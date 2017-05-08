// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha1').select();
				e.stop();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha2').select();
				e.stop();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('cias').select();
				e.stop();
			}
		}
	});
	
	$('generar').addEvent('click', Generar);
	
	$('cias').focus();
});

var Generar = function() {
	var url = 'GenerarCartasPagosLuz.php',
		arg = '?accion=generar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + arg, 'cartas', opt);
	win.focus();
}
