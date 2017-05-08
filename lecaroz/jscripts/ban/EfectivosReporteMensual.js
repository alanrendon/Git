// JavaScript Document

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
				
				$('omitir_cias').select();
			}
		}
	});
	
	$('omitir_cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
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
	if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de corte');
		
		$('fecha').select();
	}
	else {
		var url = 'EfectivosReporteMensual.php',
		param = '?' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
		
		win = window.open(url + param, $('accion').get('value'), opt);
		
		win.focus();
	}
}
