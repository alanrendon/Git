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
				
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha2').select();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').focus();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('cias').select();
	
});

var Consultar = function() {
	if ($('fecha1').get('value') == '' && $('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		
		$('fecha1').select();
	}
	else {
		var url = 'PastelesConsultaBases.php',
			param = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + param, 'reporte_pasteles_entregas', opt);
		
		win.focus();
	}
}
