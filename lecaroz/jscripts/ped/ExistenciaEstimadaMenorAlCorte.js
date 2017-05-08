// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('mps').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('dias').select();
			}
		}
	});
	
	$('dias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('mps').select();
			}
		}
	});
	
	$('reporte').addEvent('click', Reporte);
	
	$('mps').select();
});

var Reporte = function() {
	if ($('dias').get('value').getNumericValue() == 0) {
		alert('Debe especificar los días');
		
		$('dias').select();
	}
	else {
		if ($('mps').get('value') == '' && !confirm('No ha especificado productos, si continua la consulta podría tardar, ¿Desea continuar?')) {
			return false;
		}
		
		var tipo = arguments[0],
			url = 'ExistenciaEstimadaMenorAlCorte.php',
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			param = '?accion=reporte&' + $('Datos').toQueryString(),
			win;
		
		win = window.open(url + param, '', opt);
		
		win.focus();
	}
}
