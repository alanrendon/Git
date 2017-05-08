// JavaScript Document

var homoclaves = [];

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('comprobante').addEvents({
		'change': validarComprobante,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
			}
		}
	});
	
	$('checkall').addEvent('click', function() {
		$$('input[id=id]').set('checked', this.get('checked'));
		
		calcularTotal();
	});
	
	$$('input[id=id]').addEvent('click', calcularTotal);
	
	$('registrar').addEvent('click', Registrar);
	
	$('comprobante').select();
});

var validarComprobante = function() {
	new Request({
		'url': 'DepositosFaltantesCometra.php',
		'data': 'accion=validarComprobante&comprobante=' + $('comprobante').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result == '-1') {
				alert('El comprobante ya esta registrado');
				$('comprobante').set('value', $('comprobante').retrieve('tmp', '')).select();
			}
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var calcularTotal = function() {
	var total = 0;
	
	$$('input[id=id]:checked').get('importe').each(function(el) {
		total += el.getNumericValue();
	});
}

var Registrar = function() {
	if (!$chk($('comprobante').get('value').getNumericValue())) {
		alert('Debe especificar el número de comprobante');
		$('comprobante').select();
	}
	else if ($$('input[id=id]').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea agregar los registros seleccionados al comprobante?')) {
		new Request({
			'url': 'DepositosFaltantesCometra.php',
			'data': 'accion=registrar&' + $('Datos').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function() {
				document.location.reload(); 
			},
			'onFailure': function() {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	}
}
