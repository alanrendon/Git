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

	boxCartas = new mBox.Modal(
	{
		id: 'box_reporte',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Cartas para imprimir',
		content: 'cartas_wrapper',
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
			document.id('cartas_frame').set('src', 'CorreoAdeudoProveedores.php?accion=generar_cartas&' + param);
		},
		onCloseComplete: function()
		{
			document.id('cartas_frame').set('src', '');
		}
	});

	boxCorreos = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Env&iacute;o de correos',
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

	new FormValidator(document.id('inicio_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});

	document.id('pros').addEvents(
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

				document.id('pros').select();
			}
		}
	});

	document.id('generar_cartas').addEvent('click', generar_cartas);

	document.id('enviar_correos').addEvent('click', enviar_correos);

	document.id('pros').focus();

});

var enviar_correos = function()
{
	if (document.id('anio').get('value') == '')
	{
		alert('Debe especificar el año de cierre contable.');

		document.id('anio').select();

		return false;
	}

	param = document.id('inicio_form').toQueryString();

	new Request(
	{
		url: 'CorreoAdeudoProveedores.php',
		data: 'accion=enviar_correos&' + param,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			boxProcessing.close();

			boxCorreos.setContent('<pre style="width:800px; height:400px; overflow:auto;">' + result + '</pre>').open();
		}
	}).send();
}

var generar_cartas = function()
{
	if (document.id('anio').get('value') == '')
	{
		alert('Debe especificar el año de cierre contable.');

		document.id('anio').select();

		return false;
	}

	param = document.id('inicio_form').toQueryString();

	boxCartas.open();
}
