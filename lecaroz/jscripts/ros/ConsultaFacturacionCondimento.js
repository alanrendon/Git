// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha1').focus();
			}
		}
	});
	
	$('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha2').focus();
			}
		}
	});
	
	$('fecha2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('cias').focus();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('cias').focus();
});

var Consultar = function() {
	if ($('fecha1').get('value').length < 8) {
		alert('Debe especificar el día o periodo de consulta');
		$('fecha1').focus();
		return false;
	}
	
	if (!$('pendientes').get('checked') && !$('facturados').get('checked')) {
		alert('Debe seleccionar al menos un filtro de búsqueda');
		return false;
	}
	
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	var win = window.open('', 'result', opt);
	
	$('Datos').submit();
	
	win.focus();
}
