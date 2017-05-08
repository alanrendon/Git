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
				
				$('alza_precio').select();
			}
		}
	});
	
	$('alza_precio').addEvents({
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
				
				$('cias').focus();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('cias').focus();
});

var Consultar = function() {
	if ($('alza_precio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el alza de precio');
		
		$('alza_precio').select();
	}
	if ($('anio').get('value').getNumericValue() == 0)  {
		alert('Debe especificar el año');
		
		$('anio').select();
	}
	else {
		var url = 'EstimacionAlzaPreciosProduccion.php',
			param = '?accion=consultar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + param, 'reporte', opt);
		
		win.focus();
	}
}
