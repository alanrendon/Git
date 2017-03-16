window.addEvent('domready', function() {
	
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
	new Request({
		url: 'FacturasProductosConsulta.php',
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
						
						document.id('pros').select();
					}
				}
			});
			
			document.id('pros').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();
						
						document.id('mps').select();
					}
				}
			});

			document.id('mps').addEvents(
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

			document.id('fecha1').addEvents(
			{
				keydown: function(e)
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
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();
						
						document.id('facturas').focus();
					}
				}
			});
			
			document.id('facturas').addEvents(
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

			$$('#status_0, #status_1').addEvent('click', function()
			{
				$$('#pag_0, #pag_1, #pag_2').set('disabled', true);
			});

			document.id('status_2').addEvent('click', function()
			{
				$$('#pag_0, #pag_1, #pag_2').set('disabled', false);
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
		url: 'FacturasProductosConsulta.php',
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
