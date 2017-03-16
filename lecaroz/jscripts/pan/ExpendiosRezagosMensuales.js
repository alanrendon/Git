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
				
				$('anio1').select();
			}
		}
	});
	
	$('anio1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('anio2').select();
			}
		}
	});
	
	$('anio2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('cias').select();
	
	$('exportar').addEvent('click', Exportar);
	
	$('reporte').addEvent('click', Reporte);
});

var Reporte = function() {
	if ($('anio1').get('value').getNumericValue() == 0 || $('anio2').get('value').getNumericValue() == 0) {
		alert('Debe especificar el periodo de consulta');
		$('anio1').select();
	}
	else {
		var url = 'ExpendiosRezagosMensuales.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}

var Exportar = function() {
	if ($('anio1').get('value').getNumericValue() == 0 || $('anio2').get('value').getNumericValue() == 0) {
		alert('Debe especificar el periodo de consulta');
		$('anio').select();
	}
	else {
		var url = 'ExpendiosRezagosMensuales.php',
			arg = '?accion=exportar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
		
		win = window.open(url + arg, 'exportar', opt);
		
		win.focus();
	}
}
