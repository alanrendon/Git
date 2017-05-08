// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('arrendadores').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('arrendatarios').select();
			}
		}
	});
	
	$('arrendatarios').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('meses').select();
			}
		}
	});
	
	$('meses').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('arrendadores').select();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('arrendadores').select();
});

var Consultar = function() {
	var url = 'RentasArrendatariosVencimiento.php',
		param = '?accion=reporte&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, 'ArrendamientosVencidos', opt);
	
	win.focus();
}
