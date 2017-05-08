var tipo_graficas = null;

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
			document.id('reporte_frame').set('src', 'ReporteGastosAnualesV2.php?accion=reporte&' + param);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
		}
	});

	boxGraficas = new mBox.Modal(
	{
		id: 'box_graficas',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Gr&aacute;ficas para imprimir',
		content: 'graficas_wrapper',
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
			document.id('graficas_frame').set('src', 'ReporteGastosAnualesV2.php?accion=graficas&tipo=' + tipo_graficas + '&' + param);
		},
		onCloseComplete: function()
		{
			document.id('graficas_frame').set('src', '');
		}
	});

	inicio();

});

var inicio = function()
{
	new Request(
	{
		url: 'ReporteGastosAnualesV2.php',
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

			document.id('gasto').addEvents(
			{
				change: obtener_gasto,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').select();
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

						document.id('anio').select();
					}
				}
			});

			$$('input[id=anio]')[0].addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						$$('input[id=anio]')[1].select();
					}
				}
			});

			$$('input[id=anio]')[1].addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						$$('input[id=anio]')[2].select();
					}
				}
			});

			$$('input[id=anio]')[2].addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						$$('input[id=anio]')[3].select();
					}
				}
			});

			$$('input[id=anio]')[3].addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						$$('input[id=anio]')[4].select();
					}
				}
			});

			$$('input[id=anio]')[4].addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('gasto').select();
					}
				}
			});

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();

			document.id('gasto').focus();
		}
	}).send();
}

var obtener_gasto = function()
{
	if (document.id('gasto').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ReporteGastosAnualesV2.php',
			data: 'accion=obtener_gasto&gasto=' + document.id('gasto').get('value'),
			onRequest: function() {},
			onSuccess: function(response) {
				if (response != '')
				{
					document.id('nombre_gasto').set('value', response);
				}
				else
				{
					document.id('gasto').set('value', document.id('gasto').retrieve('tmp'));

					alert('El código de gasto no se encuentra en el catálogo.');

					document.id('gasto').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#gasto, #nombre_gasto').set('value', '');
	}
}

var consultar = function()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		if (document.id('gasto').get('value').getNumericValue() == 0)
		{
			alert('Debes especificar el código de gasto');

			document.id('gasto').focus();

			return false;
		}
		else if ($$('input[id=anio]')[0].get('value').getNumericValue() <= 0)
		{
			alert('Debe especificar el primer año de consulta.');

			$$('input[id=anio]')[0].focus();

			return false;
		}

		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'ReporteGastosAnualesV2.php',
		data: 'accion=consulta&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			if (result != '')
			{
				document.id('captura').empty().set('html', result);

				$$('span[id=tooltip-info]').each(function(el)
				{
					el.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Desglose de gastos');
					el.store('tip:text', el.get('data-tooltip'));
				});

				tips_info = new Tips($$('span[id=tooltip-info]'),
				{
					'fixed': true,
					'className': 'Tip',
					'showDelay': 50,
					'hideDelay': 50
				});

				document.id('regresar').addEvent('click', inicio);

				document.id('reporte').addEvent('click', reporte);

				document.id('graficas_barras').addEvent('click', graficas_barras);

				document.id('graficas_lineas').addEvent('click', graficas_lineas);

				document.id('exportar').addEvent('click', exportar);

				boxProcessing.close();
			}
			else
			{
				inicio();

				boxProcessing.close();

				alert('No hay resultados');
			}
		}
	}).send();
}

var reporte = function()
{
	boxReporte.open();
}

var graficas_barras = function()
{
	tipo_graficas = 'barras';

	boxGraficas.open();
}

var graficas_lineas = function()
{
	tipo_graficas = 'lineas';

	boxGraficas.open();
}

var exportar = function()
{
	var url = 'ReporteGastosAnualesV2.php',
		url_param = '?accion=exportar&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
		win;

	win = window.open(url + url_param, 'exportar', opt);

	win.focus();
}
