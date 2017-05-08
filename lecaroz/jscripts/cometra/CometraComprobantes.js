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
			document.id('reporte_frame').set('src', 'CometraComprobantes.php?accion=reporte&' + param);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
		}
	});

	boxComprobantes = new mBox.Modal(
	{
		id: 'box_comprobantes',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Comprobantes para imprimir',
		content: 'comprobantes_wrapper',
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
		onOpenComplete: function() {},
		onCloseComplete: function()
		{
			document.id('comprobantes_frame').set('src', '');
		}
	});

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'CometraComprobantes.php',
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

			document.id('fecha1').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha2').select();
					}
				}
			});

			document.id('fecha2').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha1').select();
					}
				}
			});

			// document.id('cias').addEvents(
			// {
			// 	'keydown': function(e)
			// 	{
			// 		if (e.key == 'enter')
			// 		{
			// 			e.stop();

			// 			this.blur();
			// 			this.focus();
			// 		}
			// 	}
			// });

			// document.id('banco').addEvents(
			// {
			// 	change: function()
			// 	{
			// 		switch (this.get('value').getNumericValue())
			// 		{

			// 			case 1:
			// 				this.removeClass('logo_banco_2').addClass('logo_banco_1');
			// 				break;

			// 			case 2:
			// 				this.removeClass('logo_banco_1').addClass('logo_banco_2');
			// 				break;

			// 			default:
			// 				this.removeClass('logo_banco_1').removeClass('logo_banco_2');

			// 		}
			// 	}
			// });

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();

			document.id('fecha1').select();
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
		param = document.id('inicio_form').toQueryString();

		fecha1 = document.id('fecha1').get('value');
		fecha2 = document.id('fecha2').get('value');
	}

	new Request(
	{
		url: 'CometraComprobantes.php',
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

				document.id('regresar').addEvent('click', inicio);

				document.id('comprobantes').addEvent('click', comprobantes);

				document.id('imprimir').addEvent('click', imprimir);

				// document.id('reporte').addEvent('click', reporte);

				// document.id('exportar').addEvent('click', exportar);

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

var comprobantes = function()
{
	new Request(
	{
		url: 'CometraComprobantes.php',
		data:
		{
			accion: 'comprobantes',
			fecha1: fecha1,
			fecha2: fecha2,
			comprobantes: $$('input[id=comprobante]:checked').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(file)
		{
			boxProcessing.close();

			if (file != '')
			{
				document.id('comprobantes_frame').set('src', 'cometra/' + file);

				boxComprobantes.open();
			}
			else
			{
				alert('Error al procesar comprobantes');
			}
		}
	}).send();
}

var imprimir = function()
{
	new Request(
	{
		url: 'CometraComprobantes.php',
		data:
		{
			accion: 'imprimir',
			fecha1: fecha1,
			fecha2: fecha2,
			comprobantes: $$('input[id=comprobante]:checked').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function()
		{
			boxProcessing.close();
		}
	}).send();
}

var exportar = function() {
	var url = 'CometraComprobantes.php',
		url_param = '?accion=exportar&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
		win;

	win = window.open(url + url_param, 'exportar', opt);

	win.focus();
}
