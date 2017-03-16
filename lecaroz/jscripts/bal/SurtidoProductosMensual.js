window.addEvent('domready', function()
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
	}).select();
	
	document.id('codmp').addEvents(
	{
		change: obtener_mp,
		keydown: function(e)
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
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('cias').focus();
			}
		}
	});
	
	document.id('reporte').addEvent('click', reporte);
	
});

var obtener_mp = function()
{
	if (document.id('codmp').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'SurtidoProductosMensual.php',
			data: 'accion=obtener_mp&codmp=' + document.id('codmp').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_mp').set('value', result);
				}
				else
				{
					document.id('codmp').set('value', document.id('codmp').retrieve('tmp'));
					
					alert('El código de producto no se encuentra en el catálogo.');
					
					document.id('codmp').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#codmp, #nombre_mp').set('value', '');
	}
}

var reporte = function() {
	if (document.id('codmp').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar código de producto a consultar');
		document.id('codmp').select();
	}
	else if (document.id('anio').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el año de consulta');
		document.id('anio').select();
	}
	else
	{
		var url = 'SurtidoProductosMensual.php',
			arg = '?accion=reporte&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, '', opt);
		win.focus();
	}
}
