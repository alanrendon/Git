window.addEvent('domready', function()
{

	boxProcessing = new mBox({
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

	boxDetalle = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Detalle',
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
		closeInTitle: true,
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
			document.id('reporte_frame').set('src', 'ComparativoSaldos.php?accion=reporte&' + param);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
		}
	});

	inicio();

});

var inicio = function ()
{
	new Request({
		url: 'ComparativoSaldos.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').empty().set('html', result);

			new FormValidator(document.id('inicio'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('cias').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio1').select();
					}
				}
			});

			document.id('anio1').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio2').select();
					}
				}
			});

			document.id('anio2').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').select();
					}
				}
			});

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();

			document.id('cias').focus();
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
		param = document.id('inicio').toQueryString();
	}

	new Request(
	{
		url: 'ComparativoSaldos.php',
		data: 'accion=consultar&' + param,
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

				$$('a[id=detalle]').each(function(el, i)
				{
					var data = JSON.decode(el.get('alt'));

					el.addEvent('click', function()
					{
						detalle(data.id, data.tipo);
					});

					el.removeProperty('alt');

				});

				$$('img[id=visualizar][src!=/lecaroz/iconos/magnify_gray.png]').each(function(el)
				{
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'click': visualizar_cfd.pass(id)
					});
				});

				$$('img[id=imprimir][src!=/lecaroz/iconos/printer_gray.png]').each(function(el)
				{
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'click': imprimir_cfd.pass(id)
					});
				});

				$$('img[id=descargar][src!=/lecaroz/iconos/download_gray.png]').each(function(el)
				{
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'click': descargar_cfd.pass(id)
					});
				});

				document.id('regresar').addEvent('click', inicio);

				document.id('reporte').addEvent('click', reporte);

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

var detalle = function(id, tipo)
{
	new Request({
		url: 'ComparativoSaldos.php',
		data: 'accion=detalle&id=' + id + '&tipo=' + tipo,
		onRequest: function() {},
		onSuccess: function(result)
		{
			boxDetalle.setContent(result).open();
		}
	}).send();
}

var reporte = function()
{
	boxReporte.open();
}

var exportar = function()
{
	var url = 'ComparativoSaldos.php',
		url_param = '?accion=exportar&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
		win;

	win = window.open(url + url_param, 'exportar', opt);

	win.focus();
}
