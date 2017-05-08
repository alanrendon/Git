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
				
				$('fecha').focus();
			}
		}
	});
	
	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').focus();
			}
		}
	});
	
	$('generar').addEvent('click', Generar);
	
	$('cias').select();
});

var Generar = function() {
	if (confirm('¿Desea generar las facturas electrónicas?')) {
		new Request({
			'url': 'FacturasElectronicasCondimento.php',
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
