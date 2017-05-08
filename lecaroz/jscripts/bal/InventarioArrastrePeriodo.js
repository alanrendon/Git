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
	new FormValidator(document.id('inicio'), {
		showErrors: true,
		selectOnFocus: true
	});

	document.id('cias').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('mps').focus();
			}
		}
	}).focus();

	document.id('mps').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('anio1').focus();
			}
		}
	});

	document.id('anio1').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('anio2').focus();
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

				document.id('cias').focus();
			}
		}
	});

	document.id('consultar').addEvent('click', consultar);
}

var consultar = function ()
{
	if (document.id('anio1').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el año de consulta');
		document.id('anio1').select();
	}
	else if (document.id('anio1').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el año de consulta');
		document.id('anio1').select();
	}
	else
	{
		var url = 'InventarioArrastrePeriodo.php';
		var arg = '?accion=reporte&' + document.id('inicio').toQueryString();
		var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		var win;

		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}
