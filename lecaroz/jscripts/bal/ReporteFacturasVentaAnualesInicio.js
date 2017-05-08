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

				document.id('rfc').select();
			}
		}
	}).select();

	document.id('rfc').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('anio').focus();
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

var reporte = function()
{
	if ($('anio').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el años de consulta');
		$('anio').select();
	}
	else
	{
		var url = 'ReporteFacturasVentaAnuales.php',
			arg = '?accion=reporte&' + $('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;

		win = window.open(url + arg, '', opt);
		win.focus();
	}
}
