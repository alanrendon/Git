window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('anio').focus();
			}
		}
	}).focus();
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').focus();
			}
		}
	});
	
	$('exportar').addEvent('click', Exportar);
	
	$('reporte').addEvent('click', Reporte);
});

var Reporte = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el años de consulta');
		$('anio').select();
	}
	else {
		var url = 'ReporteUtilidadesNetas.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}

var Exportar = function() {
	if ($('anio').get('value') == '') {
		alert('Debe especificar el año de consulta');
		$('anio').select();
	}
	else {
		var url = 'ReporteUtilidadesNetas.php',
			arg = '?accion=exportar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
		
		win = window.open(url + arg, 'exportar', opt);
		
		win.focus();
	}
}
