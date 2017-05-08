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
	
});

var inicio = function ()
{
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
				
				document.id('codmp').select();
			}
		}
	});

	document.id('codmp').addEvents(
	{
		'change': obtener_producto,
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

var consultar = function()
{
	if (document.id('anio').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el año de consulta');
		return false;
	}

	if (document.id('codmp').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el producto a consultar');
		return false;
	}
	
	var url = 'ReporteProductosMensual.php',
		data = '?accion=consultar&' + document.id('inicio').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + data, '', opt);
	win.focus();
}

var obtener_producto = function()
{
	if (document.id('codmp').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ReporteProductosMensual.php',
			data: 'accion=obtener_producto&codmp=' + document.id('codmp').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('producto').set('text', result);
				}
				else
				{
					document.id('codmp').set('value', document.id('codmp').retrieve('tmp', ''));

					alert('El producto no existe en el catálogo');

					document.id('codmp').select();
				}
			}
		}).send();
	}
	else
	{
		document.id('codmp').set('value', '');
		document.id('producto').set('text', '');
	}
}
