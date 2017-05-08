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
				
				$('anio').select();
			}
		}
	});
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('generar').addEvent('click', Generar);
	
	$('cias').select();
});

var Generar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el anio');
		$('anio').select();
	}
	else if ($$('input[id=emisor]:checked').length == 0) {
		alert('Debe seleccionar al menos un emisor');
	}
	else if (confirm('¿Desea generar las facturas electrónicas?')) {
		new Request({
			'url': 'FacturasOficinasTalleres.php',
			'data': 'accion=generar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('resultado').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('resultado'));
				
				new Element('span', {
					'text': 'Generando facturas...'
				}).inject($('resultado'));
			},
			'onSuccess': function(result) {
				if (result == '-1') {
					alert('No puede generar facturas electrónicas porque el servidor se encuentra ocupado, intentelo más tarde.');
					
					$('resultado').empty();
				}
				else if (result == '-2') {
					alert('Error al conectar al servidor de CFD');
					
					$('resultado').empty();
				}
				else if (result == '-3') {
					alert('Error al iniciar sesión en el servidor de CFD');
					
					$('resultado').empty();
				}
				else if (result == '-4') {
					alert('No hay facturas para el mes dado');
					
					$('resultado').empty();
				}
				else {
					$('resultado').empty().set('html', result);
				}
			}
		}).send();
	}
}
