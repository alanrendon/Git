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
			document.id('reporte_frame').set('src', 'FacturasElectronicasVentaDiariaAutomatico.php?accion=reporte&' + param);
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
		url: 'FacturasElectronicasVentaDiariaAutomatico.php',
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

						document.id('omitir').select();
					}
				}
			});

			document.id('omitir').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha_corte').select();
					}
				}
			});

			document.id('fecha_corte').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('diferencia_maxima').select();
					}
				}
			});

			document.id('diferencia_maxima').addEvents(
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

			document.id('generar').addEvent('click', generar);

			boxProcessing.close();

			document.id('cias').focus();
		}
	}).send();
}

var generar = function()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		if (document.id('fecha_corte').get('value') == '')
		{
			alert('Debe especificar la fecha de corte');

			document.id('fecha_corte').select();

			return false;
		}
		else if ( ! confirm('¿Son correctos todos los datos?'))
		{
			return false;
		}

		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'FacturasElectronicasVentaDiariaAutomatico.php',
		data: 'accion=generar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(response)
		{
			if (response != '')
			{
				document.id('captura').empty().set('html', response);

				document.id('regresar').addEvent('click', inicio);

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
