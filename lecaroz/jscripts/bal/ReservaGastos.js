window.addEvent('domready', function()
{

	boxProcessing = new mBox(
	{
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		closeOnEsc: false,
		closeOnBodyClick: false
	});

	box = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{

				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
		},
		onOpenComplete: function()
		{
		}
	});

	boxFailure = new mBox.Modal(
	{
		id: 'box_failure',
		title: 'Error',
		content: '',
		buttons: [
			{ title: 'Aceptar' }
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: false,
	});

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'ReservaGastos.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('inicio_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('gasto').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio').focus();
					}
				}
			}).focus();

			document.id('anio').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').focus();
					}
				}
			});

			document.id('cias').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('gasto').focus();
					}
				}
			});

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();
		}
	}).send();
}

var consultar = function()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		if (document.id('gasto').get('value').getNumericValue() <= 0)
		{
			alert('Debe especificar el gasto');

			document.id('gasto').select();

			return false;
		}
		else if (document.id('anio').get('value').getNumericValue() <= 0)
		{
			alert('Debe especificar el año');

			document.id('anio').select();

			return false;
		}

		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'ReservaGastos.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			document.id('modificar').addEvent('click', modificar);

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var modificar = function()
{
	new Request(
	{
		url: 'ReservaGastos.php',
		data: 'accion=modificar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('modificar_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			var elements = $$('input[id^=importe_]');

			elements.each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						var row = el.get('data-row').getNumericValue();

						calcular_total(row);
					},
					keydown: function(e)
					{
						var row = el.get('data-row').getNumericValue();
						var mes = el.get('data-mes').getNumericValue();
						var index = el.get('data-index').getNumericValue();

						if (e.key == 'enter')
						{
							e.stop();

							if ( !! elements[index + 1])
							{
								elements[index + 1].select();
							}
							else
							{
								elements[0].select();
							}
						}
						else if (e.key == 'right')
						{
							e.stop();

							if (mes < 12)
							{
								elements[index + 1].select();
							}
						}
						else if (e.key == 'left')
						{
							e.stop();

							if (mes > 1)
							{
								elements[index - 1].select();
							}
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (row > 0)
							{
								elements[index - 12].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (index < elements.length - 12)
							{
								elements[index + 12].select();
							}
						}
					}
				});
			});

			document.id('regresar').addEvent('click', function()
			{
				consultar(param);
			});

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function()
{
	if ( ! confirm('¿Son correctos los datos?'))
	{
		return false;
	}

	new Request(
	{
		url: 'ReservaGastos.php',
		data: 'accion=do_modificar&' + document.id('modificar_form').toQueryString(),
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var calcular_total = function(row)
{
	var total = $$('input[id^=importe_' + row + '_]').get('value').getNumericValue().sum() + document.id('acumulado_' + row).get('value').getNumericValue();

	document.id('total_' + row).set('value', total.numberFormat(2, '.', ','));
}
