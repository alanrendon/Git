var proceso_bloqueo = null;
var proceso_mensajes = null;

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

	inicio();

	estatus_bloqueo();

	proceso_bloqueo = estatus_bloqueo.periodical(2000);
});

var inicio = function()
{
	new Request(
	{
		url: 'FacturasElectronicasVentaDiariaAutomaticoV2.php',
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

var estatus_bloqueo = function()
{
	new Request(
	{
		url: 'FacturasElectronicasVentaDiariaAutomaticoV2.php',
		data: 'accion=estatus_bloqueo',
		onRequest: function() {},
		onSuccess: function(response)
		{
			if (response == '1')
			{
				document.id('generar').set('disabled', true);

				obtener_mensajes();

				if ( ! proceso_mensajes)
				{
					proceso_mensajes = obtener_mensajes.periodical(2000);
				}
			}
			else
			{
				clearInterval(proceso_mensajes);
				// clearInterval(proceso_bloqueo);

				proceso_mensajes = null;
				// proceso_bloqueo = null;

				document.id('mensajes').set('html', '&nbsp;');

				document.id('generar').set('disabled', false);
			}
		}
	}).send();
}

var obtener_mensajes = function()
{
	new Request(
	{
		url: 'FacturasElectronicasVentaDiariaAutomaticoV2.php',
		data: 'accion=obtener_mensajes',
		onRequest: function() {},
		onSuccess: function(response)
		{
			document.id('mensajes').set('html', response);
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

	document.id('generar').set('disabled', true);

	new Request(
	{
		url: 'FacturasElectronicasVentaDiariaAutomaticoV2.php',
		data: 'accion=generar&' + param,
		onRequest: function() {},
		onSuccess: function(response)
		{
			// proceso_bloqueo = estatus_bloqueo.periodical(2000);
		}
	}).send();
}
