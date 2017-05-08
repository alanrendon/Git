window.addEvent('domready', function()
{

	boxProcessing = new mBox(
	{
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles:
		{
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
		buttons:
		[
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
		buttons:
		[
			{ title: 'Aceptar' }
		],
		overlay: true,
		overlayStyles:
		{
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: false,
	});

	boxModificar = new mBox.Modal({
		id: 'box_modificar_reserva',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Modificar reserva',
		content: 'modificar_reserva_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					modificar_reserva();
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
		onBoxReady: function() {
			new FormValidator(document.id('modificar_reserva'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nuevo_importe_reserva').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
						this.focus();
					}
				}
			});
		},
		onOpenComplete: function() {
			document.id('nuevo_importe_reserva').select();
		}
	});

	inicio();

});

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ReservasControl.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value') + '&anio=' + document.id('anio').get('value') + '&reserva=' + document.id('reserva').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result)
				}
				else
				{
					alert('La compañía no se encuentra en el catálogo.');

					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia, #nombre_cia').set('value', '');
	}

}

var obtener_cia_next = function()
{
	if (document.id('num_cia_next').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ReservasControl.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia_next').get('value') + '&anio=' + document.id('anio').get('value') + '&reserva=' + document.id('reserva').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia_next').set('value', result)
				}
				else
				{
					alert('La compañía no se encuentra en el catálogo.');

					document.id('num_cia_next').set('value', document.id('num_cia_next').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia_next, #nombre_cia_next').set('value', '');
	}

}

var inicio = function ()
{
	new Request(
	{
		url: 'ReservasControl.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			document.id('num_cia').addEvents({
				change: obtener_cia,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio').select();
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

						document.id('num_cia').select();
					}
				}
			});

			document.id('consultar').addEvent('click', consultar.pass('inicio'));

			boxProcessing.close();
		}
	}).send();
}

var consultar = function(tipo)
{
	if (document.id('anio').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el año de consulta');

		document.id('anio').focus();
	}
	else
	{
		var num_cia;

		if (tipo == 'inicio')
		{
			num_cia = document.id('num_cia').get('value');
		}
		else if (tipo == 'ir_a')
		{
			num_cia = document.id('num_cia_next').get('value');
		}
		else if (tipo == 'siguiente')
		{
			num_cia = '+' + document.id('num_cia').get('value');
		}
		else if (tipo == 'anterior')
		{
			num_cia = '-' + document.id('num_cia').get('value');
		}

		new Request(
		{
			url: 'ReservasControl.php',
			data:
			{
				accion: 'consultar',
				reserva: document.id('reserva').get('value'),
				anio: document.id('anio').get('value'),
				num_cia: num_cia
			},
			onRequest: function()
			{
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function(result)
			{
				document.id('captura').set('html', result);

				new FormValidator(document.id('consulta'),
				{
					showErrors: true,
					selectOnFocus: true
				});

				if ($$('a[id^=reserva_anchor_]').length > 0)
				{
					$$('a[id^=reserva_anchor_]').addEvent('click', function()
					{
						var index = $$('a[id^=reserva_anchor_]').get('id')[0].replace('reserva_anchor_', '').getNumericValue();
						var reserva = $$('input[id=reserva_input]')[index].get('value').getNumericValue();

						document.id('nuevo_importe_reserva').set('value', reserva.numberFormat(2, '.', ','));

						boxModificar.open();
					});
				}

				if ($$('a[id^=promedio_anchor_]').length > 0)
				{
					$$('a[id^=promedio_anchor_]').addEvent('click', asignar_promedio);
				}

				document.id('num_cia_next').addEvents(
				{
					change: obtener_cia_next,
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							this.blur();
							this.focus();
						}
					}
				});

				document.id('terminar').addEvent('click', inicio);

				document.id('ir_a').addEvent('click', function()
				{
					if (document.id('num_cia_next').get('value').getNumericValue() > 0)
					{
						consultar('ir_a');
					}
				});

				document.id('siguiente').addEvent('click', consultar.pass('siguiente'));

				document.id('anterior').addEvent('click', consultar.pass('anterior'));

				calcular_totales();

				boxProcessing.close();
			}
		}).send();
	}
}

var calcular_totales = function()
{
	var total_reserva = 0;
	var total_pagado = 0;
	var diferencia = 0;

	$$('input[id=reserva_input]').each(function(el, i)
	{
		if ($$('input[id=status]')[i].get('value').getNumericValue() >= 0)
		{
			total_reserva += el.get('value').getNumericValue();
		}
	});

	$$('input[id=pagado_input]').each(function(el, i)
	{
		if ($$('input[id=status]')[i].get('value').getNumericValue() >= 0)
		{
			total_pagado += el.get('value').getNumericValue();
		}
	});

	diferencia = total_reserva - total_pagado;

	document.id('total_reserva').set('html', total_reserva != 0 ? total_reserva.numberFormat(2, '.', ',') : '&nbsp;');

	document.id('diferencia').set(
	{
		html: diferencia != 0 ? diferencia.numberFormat(2, '.', ',') : '&nbsp;',
		class: diferencia > 0 ? 'blue' : 'red'
	});
}

var asignar_promedio = function()
{
	if (confirm('¿Desea asignar el importe promedio al importe reservado del mes?'))
	{
		var index = $$('a[id^=promedio_anchor_]').get('id')[0].replace('promedio_anchor_', '').getNumericValue();
		var promedio = $$('input[id=promedio_input]')[index].get('value').getNumericValue();

		$$('input[id=reserva_input]').each(function(el, i)
		{
			if (i >= index && i < 11)
			{
				el.set('value', promedio.round(-2));

				document.id('reserva_anchor_' + i).set('html', promedio != 0 ? promedio.round(-2).numberFormat(2, '.', ',') : (i == index ? '-----' : ''));
			}
		});

		calcular_totales();

		if (document.id('distribuir_diferencia').get('value') == 't')
		{
			var diferencia = (document.id('diferencia').get('html').getNumericValue() / (12 -  index)).round(2);
			var promedio = $$('input[id=promedio_input]')[index].get('value').getNumericValue() - diferencia;

			$$('input[id=reserva_input]').each(function(el, i)
			{
				if (i >= index && i < 11)
				{
					el.set('value', promedio.round(-2));

					document.id('reserva_anchor_' + i).set('html', promedio != 0 ? promedio.round(-2).numberFormat(2, '.', ',') : (i == index ? '-----' : ''));
				}
			});
		}

		calcular_totales();

		actualizar_reservas();
	}
}

var modificar_reserva = function()
{
	var index = $$('a[id^=reserva_anchor_]').get('id')[0].replace('reserva_anchor_', '').getNumericValue();
	var reserva = document.id('nuevo_importe_reserva').get('value').getNumericValue();

	$$('input[id=reserva_input]').each(function(el, i)
	{
		if (i >= index && i < 11)
		{
			el.set('value', reserva);

			document.id('reserva_anchor_' + i).set('html', reserva != 0 ? reserva.numberFormat(2, '.', ',') : (i == index ? '-----' : ''));
		}
	});

	calcular_totales();

	actualizar_reservas();

	boxModificar.close();
}

var actualizar_reservas = function()
{
	new Request(
	{
		url: 'ReservasControl.php',
		data: 'accion=actualizar&' + document.id('consulta').toQueryString(),
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			boxProcessing.close();
		}
	}).send();
}
