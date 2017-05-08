// JavaScript Document

window.addEvent('domready', function() {
	new Formulario('Datos');
	
	$('num_cia').addEvents({
		'change': cambiaCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				this.blur();
				e.stop();
			}
		}
	});
	
	$('autorizar').addEvent('click', Autorizar);
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
	}
	else {
		new Request({
			'url': 'AutorizarControlProduccion.php',
			'data': 'accion=cia&num_cia=' + $('num_cia').get('value'),
			'onRequest': $empty,
			'onSuccess': function(result) {
				if (result == '') {
					alert('La compa��a no se encuentra en el cat�logo');
					$('num_cia').set('value', '');
					$('nombre_cia').set('value', '');
					$('num_cia').select();
				}
				else {
					$('nombre_cia').set('value', result);
					$('num_cia').select();
				}
			}
		}).send();
	}
}

var Autorizar = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compa��a');
		$('num_cia').focus();
	}
	else if (confirm('�Desea autorizar la modificaci�n del control de producci�n de la compa��a especificada?')) {
		new Request({
			'url': 'AutorizarControlProduccion.php',
			'data': 'accion=autorizar&num_cia=' + $('num_cia').get('value'),
			'onRequest': $empty,
			'onSuccess': function() {
				alert('Autorizaci�n realizada');
				
				$('num_cia').set('value', '');
				$('nombre_cia').set('value', '');
				$('num_cia').focus();
			}
		}).send();
	}
	else {
		$('num_cia').select();
	}
}
