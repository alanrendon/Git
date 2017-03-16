// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('anios').select();
				e.stop();
			}
		}
	});
	
	$('anios').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('cias').select();
				e.stop();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('cias').focus();
});

var Consultar = function() {
	if ($('anios').get('value').clean() == '') {
		alert('Debe especificar el año de consulta');
		$('anios').select();
	}
	else if (!$$('input[id=codmp]')[0].get('checked') && !$$('input[id=codmp]')[1].get('checked') && !$$('input[id=codmp]')[2].get('checked')) {
		alert('Debe seleccionar al menos un producto');
	}
	else {
		var url = 'VentaPollosAnual.php',
			arg = '?accion=consultar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		
		var win = window.open(url + arg, 'consulta', opt);
		win.focus();
	}
}
