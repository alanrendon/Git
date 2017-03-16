// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('mps').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('anio_com').select();
			}
		}
	}).select();
	
	$('mps').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('anio').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('anio_com').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('mps').select();
			}
		}
	});
	
	$('anio_com').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('cias').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('anio').select();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
});

var Consultar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		
		$('anio').select();
	}
	else if ($('anio_com').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de comparación');
		
		$('anio_com').select();
	}
	else if ($('anio_com').get('value').getNumericValue() > $('anio').get('value').getNumericValue()) {
		alert('El año de comparación no puede ser mayor al año de consulta');
		
		$('anio_com').select();
	}
	else if ($('anio_com').get('value').getNumericValue() == $('anio').get('value').getNumericValue()
		&& $('mes_com').get('value').getNumericValue() > $('mes').get('value').getNumericValue()) {
		alert('El mes de comparación no puede ser mayor al mes de consulta');
	}
	else {
		var url = 'InventarioComparativoCostoMensual.php',
			param = '?accion=consultar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + param, '', opt);
		
		win.focus();
	}
}