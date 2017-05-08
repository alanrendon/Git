// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function () {
	new Request({
		'url': 'DepositosComplemento.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
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
						
						$('cias').focus();
					}
				}
			});
			
			$('buscar').addEvent('click', Buscar);
			
			$('cias').focus();
		}
	}).send();
}

var Buscar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'DepositosComplemento.php',
		'data': 'accion=buscar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$('regresar').addEvent('click', Inicio);
				
				$('registrar').addEvent('click', Comprobante);
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var Comprobante = function() {
	if ($$('input[id=data]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else {
		new Request({
			'url': 'DepositosComplemento.php',
			'data': 'accion=comprobante',
			'onSuccess': function(content) {
				popup = new Popup(content, 'Número de comprobante', 450, 250, IngresarComprobante, null);
			}
		}).send();
	}
}

var IngresarComprobante = function() {
	new FormValidator($('DatosComprobante'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('DatosComprobante'));
	
	$('comprobante').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
				this.focus();
			}
		}
	}).focus();
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aceptar').addEvent('click', ValidarComprobante);
}

var ValidarComprobante = function() {
	if ($('comprobante').get('value').getNumericValue() == 0) {
		alert('Debe ingresar el número de comprobante de cometra');
		
		$('comprobante').select();
	}
	else {
		new Request({
			'url': 'DepositosComplemento.php',
			'data': 'accion=validarComprobante&comprobante=' + $('comprobante').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert('El número de comprobante ya se encuentra registrado');
				}
				else {
					Registrar.run();
				}
			}
		}).send();
	}
}

var Registrar = function() {
	new Request({
		'url': 'DepositosComplemento.php',
		'data': 'accion=registrar&' + $('DatosComprobante').toQueryString() + '&' + $('Datos').toQueryString(),
		'onRequest': function() {
			popup.Close();
			
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Registrando comprobante...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			Inicio.run();
		}
	}).send();
}
