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

	boxReporte = new mBox.Modal(
	{
		id: 'box_reporte',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Reporte para imprimir',
		content: 'reporte_wrapper',
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
		closeInTitle: true,
		onOpenComplete: function()
		{
			document.id('reporte_frame').set('src', 'ReporteNominaV2.php?accion=reporte_pdf&folio=' + folio);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
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
			url: 'ReporteNominaV2.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result);

					obtener_periodos();
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));

					alert('La compañía no está en el catálogo');

					document.id('num_cia').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia, #nombre_cia').set('value', '');

		obtener_periodos();
	}
}

var obtener_periodos = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			'url': 'ReporteNominaV2.php',
			'data': 'accion=obtener_periodos&num_cia=' + document.id('num_cia').get('value'),
			'onRequest': function() {},
			'onSuccess': function(result)
			{
				if (result != '')
				{
					var periodos = JSON.decode(result);

					update_select(document.id('periodo'), periodos);
				}
				else
				{
					update_select(document.id('periodo'), []);
				}
			}
		}).send();
	}
	else
	{
		update_select(document.id('periodo'), []);
	}
}

var inicio = function ()
{
	new Request(
	{
		url: 'ReporteNominaV2.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').empty().set('html', result);

			new FormValidator(document.id('inicio_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia').addEvents(
			{
				change: obtener_cia,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nombre_extra').select();
					}
				}
			});

			document.id('nombre_extra').addEvents(
			{
				change: obtener_cia,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('leyenda_extra').select();
					}
				}
			});

			document.id('leyenda_extra').addEvents(
			{
				change: obtener_cia,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('num_cia').select();
					}
				}
			});

			document.id('cargar_datos').addEvent('click', cargar_datos);

			boxProcessing.close();

			document.id('num_cia').select();
		}
	}).send();
}

var cargar_datos = function()
{
	if (document.id('num_cia').get('value').getNumericValue() <= 0)
	{
		alert('Debe especificar la compañía');

		document.id('num_cia').focus();
	}
	else if (document.id('archivo_carga').get('value') == '')
	{
		alert('Debe seleccionar el archivo de carga de datos');

		document.id('archivo_carga').focus();
	}
	else
	{
		var request = new Request.File(
		{
			url: 'ReporteNominaV2.php',
			onRequest: function()
			{
				boxProcessing.open();

				document.id('captura').set('html', 'Validando datos...');
			},
			onSuccess: function(result)
			{
				document.id('captura').empty().set('html', result);

				document.id('cancelar').addEvent('click', inicio)

				document.id('registrar').addEvent('click', registrar)

				boxProcessing.close();
			}
		});

		request.append('accion', 'cargar_datos');
		request.append('num_cia', document.id('num_cia').get('value'));
		request.append('periodo', document.id('periodo').get('value'));
		request.append('archivo_carga', document.id('archivo_carga').files[0]);
		request.append('nombre_extra', document.id('nombre_extra').get('value'));
		request.append('leyenda_extra', document.id('leyenda_extra').get('value'));

		request.send();
	}
}

var reporte = function()
{
	boxReporte.open();
}

var registrar = function()
{
	new Request(
	{
		url: 'ReporteNominaV2.php',
		data:
		{
			accion: 'registrar_datos'
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			folio = result;

			inicio();

			reporte();

			boxProcessing.close();
		}
	}).send();
}

var update_select = function()
{
	var select = arguments[0];
	var options = arguments[1];

	if (options.length > 0)
	{
		select.length = options.length;

		Array.each(select.options, function(el, i)
		{
			el.set(options[i]);
		});

		select.selectedIndex = 0;
	}
	else
	{
		select.length = 0;

		select.selectedIndex = -1;
	}
}
