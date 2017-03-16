// JavaScript Document

window.addEvent('domready', function() {
	
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': CambiarCia,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('importe').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('importe').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_cia').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('scan1').addEvent('click', EscanearDocumento.pass(1));
	
	$('scan2').addEvent('click', EscanearDocumento.pass(2));
	
	$('scan3').addEvent('click', EscanearDocumento.pass(3));
	
	$('registrar').addEvent('click', Registrar);
	
	$('num_cia').select();
	
});

var CambiarCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'BilletesFalsos.php',
			'data': 'accion=cambiarCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia, #fecha, #importe').set('value', '');
	}
}

var EscanearDocumento = function() {
	var doc = arguments[0];
	
	new Request({
		'url': 'BilletesFalsos.php',
		'data': 'accion=escanear&doc=' + doc,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			popup = new Popup(result, '<img src="/lecaroz/iconos/pictures.png" /> Escanear documento ' + doc, 630, 550, popupOpen, popupClose);
		}
	}).send();
}

var popupOpen = function() {
	$('cancelar').addEvent('click', function(e) {
		e.stop();
		
		popup.Close();
	});
}

var popupClose = function() {
}

var ObtenerMiniaturas = function() {
	var doc = arguments[0];
	
	$('documento' + doc).empty();
	
	new Element('img', {
		'id': 'img' + doc,
		'name': 'img' + doc,
		'src': 'BilletesFalsos.php?accion=obtenerMiniatura&doc=' + doc + '&width=128'
	}).inject($('documento' + doc));
}

var Registrar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha');
		
		$('fecha').select();
	}
	else if ($('importe').get('value').getNumericValue() == 0) {
		alert('Debe especificar el importe');
		
		$('importe').select();
	}
	else if (confirm('¿Son correctos los datos?')) {
		new Request({
			'url': 'BilletesFalsos.php',
			'data': 'accion=registrar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				popup = new Popup('<img src="/lecaroz/imagenes/_loading.gif" /> Registrando y enviando por correo electr&oacute;nico...', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n', 300, 50, null, null);
			},
			'onSuccess': function() {
				popup.Close();
				
				alert('Registrado y enviado por correo electrónico');
				
				$$('#num_cia, #nombre_cia, #fecha, #importe').set('value', '');
				
				$$('#documento1, #documento2, #documento3').set('html', 'No hay documento<br />escaneado');
				
				$('Datos').reset();
				
				$('num_cia').select();
			}
		}).send();
	}
}
