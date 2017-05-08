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
				
				$('arrendatarios').focus();
			}
		}
	});
	
	$('arrendatarios').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('anio').select();
			}
		}
	});
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('arrendadores').focus();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('arrendadores').select();
	
});

var Consultar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		
		$('anio').select();
	}
	else {
		var url = 'RentasPagadas.php',
			param = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + param, 'reporte_rentas_pagadas', opt);
		
		win.focus();
	}
}
