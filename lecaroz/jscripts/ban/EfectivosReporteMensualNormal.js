window.addEvent('domready', function() {
	var height = $$('.reporte').getHeight().max(),
		width = $$('.reporte').getWidth().max();
	
	/*$$('.hoja').setStyles({
		'height': height + 'px',
		'min-width': (width * 2 + 50) + 'px'
	});
	
	$$('.reporte').setStyle('width', width + 'px');*/
	
	$('screen-style').sheet.cssRules["0"].style.height = height + 'px';
	$('screen-style').sheet.cssRules["0"].style.width = width * 2 + 50 + 'px';
	
	$('screen-style').sheet.cssRules["1"].style.width = width + 'px';
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});

	$('email').addEvent('click', function() {
		new Request({
			url: 'EfectivosReporteMensual.php',
			data: $('email').get('data-request'),
			onRequest: function() {
				alert('Los reportes se están generando para enviarlos por correo, por favor espere...');
			},
			onSuccess: function() {
				alert('Reportes enviados por correo con exito.');
			}
		}).send();
	});
});