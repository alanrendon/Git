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
				$('importe').select();
				e.stop();
			}
		}
	});
	
	$$('input[id=importe]').each(function(el, i) {
		if (i < 9) {
			el.addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					$$('input[id=importe]')[i + 1].select();
				}
			});
		}
		else {
			el.addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					$('cias').select();
				}
			});
		}
	});
	
	$('buscar').addEvent('click', Buscar);
	
	$('cias').focus();
});

var Buscar = function() {
	var url = 'BusquedaImportes.php',
		arg = '?accion=buscar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + arg, 'resultado', opt);
	win.focus();
}
