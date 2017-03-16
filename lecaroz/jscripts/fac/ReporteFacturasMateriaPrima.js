window.addEvent('domready', function() {
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	$('exportar').addEvent('click', function() {
		var url = 'ReporteFacturasMateriaPrima.php',
			param = '?accion=exportar&anio=' + $('anio').get('value') + '&mes=' + $('mes').get('value') + '&num_pro=' + $('num_pro').get('value') + '&codmp=' + $('codmp').get('value'),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win = window.open(url + param, 'reporte_facturas_materia_prima', opt);
		
		win.focus();
	});
});
