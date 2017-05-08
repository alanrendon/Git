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
				
				$('pros').select();
			}
		}
	});
	
	$('pros').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha_corte').select();
			}
		}
	});
	
	$('fecha_corte').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('reporte').addEvent('click', Reporte);
	
	$('cias').select();
});

var Reporte = function() {
	var url = 'FacturasPendientesPagoCorte.php',
		arg = '?accion=reporte&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + arg, 'reporte', opt);
	win.focus();
}
