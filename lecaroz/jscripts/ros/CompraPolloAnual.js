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
			document.id('reporte_frame').set('src', 'CompraPolloAnual.php?accion=reporte_' + tipo + '&' + param);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
		}
	});

	inicio();

});

var inicio = function()
{
	new Request(
	{
		url: 'CompraPolloAnual.php',
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

			document.id('cias').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio').select();
					}
				}
			});

			document.id('anio').addEvents(
			{
				'keydown': function(e)
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
		if (document.id('anio').get('value') == '')
		{
			alert('Debe especificar el año de consulta');

			document.id('anio').select();

			return false;
		}

		param = document.id('inicio_form').toQueryString();
		tipo = $$('input[id^=tipo_]:checked').get('value');
	}

	new Request(
	{
		url: 'CompraPolloAnual.php',
		data: 'accion=consulta_' + $$('input[id^=tipo_]:checked').get('value') + '&' + param,
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

var reporte = function()
{
	boxReporte.open();
}

var exportar = function()
{
	var url = 'CompraPolloAnual.php',
		url_param = '?accion=exportar_' + tipo + '&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
		win;

	win = window.open(url + url_param, 'exportar', opt);

	win.focus();
}

var obtener_codigos = function()
{
	new Request(
	{
		url: 'CompraPolloAnual.php',
		data: 'accion=obtener_codigos&banco=' + document.id('banco').get('value'),
		onRequest: function() {},
		onSuccess: function(result)
		{
			if (result != '')
			{
				update_select(document.id('cod_mov'), JSON.decode(result));
			}
			else
			{
				update_select(document.id('cod_mov'), []);
			}
		}
	}).send();
}

var update_select = function()
{
	var select = arguments[0],
		options = arguments[1];

	select.length = 0;

	if (options.length > 0)
	{
		select.length = options.length;

		Array.each(select.options, function(el, i)
		{
			el.set(options[i]);
		});
	}
	else
	{
		select.length = 1;
		Array.each(select.options, function(el, i)
		{
			el.set({
				'value': '',
				'text': ''
			});
		});

		select.selectedIndex = 0;
	}
}
