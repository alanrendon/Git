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
				
				$('mps').select();
			}
		}
	});
	
	$('mps').addEvents({
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
	if ($('cias').get('value') == '' && $('mps').get('value') == '' && !confirm('No ha especificado compañías o productos, si continua la consulta podría tardar, ¿Desea continuar?')) {
		return false;
	}
	
	var tipo = arguments[0],
		url = 'ExistenciaEstimadaInventario.php',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		param = '?accion=reporte&' + $('Datos').toQueryString(),
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}
