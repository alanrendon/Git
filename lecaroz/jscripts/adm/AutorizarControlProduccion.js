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
					alert('La compañía no se encuentra en el catálogo');
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
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if (confirm('¿Desea autorizar la modificación del control de producción de la compañía especificada?')) {
		new Request({
			'url': 'AutorizarControlProduccion.php',
			'data': 'accion=autorizar&num_cia=' + $('num_cia').get('value'),
			'onRequest': $empty,
			'onSuccess': function() {
				alert('Autorización realizada');
				
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
