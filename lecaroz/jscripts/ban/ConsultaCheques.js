// JavaScript Document

window.addEvent('domready', function() {
	new Formulario('Datos');
			
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('pros').select();
				e.stop();
			}
		}
	});
	
	$('pros').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('folios').select();
				e.stop();
			}
		}
	});
	
	$('folios').addEvents({
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
				$('fecha_con1').select();
				e.stop();
			}
		}
	});
	
	$('fecha_con1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha_con2').select();
				e.stop();
			}
		}
	});
	
	$('fecha_con2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('gastos').select();
				e.stop();
			}
		}
	});
	
	$('gastos').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('importe').select();
				e.stop();
			}
		}
	});
	
	$('importe').addEvents({
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
	var url = 'ConsultaCheques.php',
		data = '?accion=generar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + data, 'reporte', opt);
	win.focus();
}
