// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('omitir').focus();
			}
		}
	});
	
	$('omitir').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('pros').focus();
			}
		}
	});
	
	$('pros').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('codmp').focus();
			}
		}
	});
	
	$('codmp').addEvents({
		change: cambiaMP,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('precio_compra').focus();
			}
		}
	});
	
	$('precio_compra').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('cias').focus();
			}
		}
	});
	
	$('loading').setStyle('display', 'none');
	
	$('actualizar').addEvent('click', Actualizar);
	
	$('cias').focus();
});

var Actualizar = function() {
	if ($('codmp').get('value').getVal() == 0) {
		alert('Debe especificar el código de producto');
		$('codmp').focus();
		return false;
	}
	if ($('precio_compra').get('value').getVal() == 0) {
		alert('El nuevo precio de compra debe ser mayor a 0');
		$('precio_compra').focus();
		return false;
	}
	
	new Request({
		'url': 'ActualizarPreciosCompra.php',
		'method': 'post',
		'data': 'accion=actualizar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('actualizar').set('disabled', true);
			$('loading').setStyle('display', 'inline');
		},
		'onSuccess': function(data) {
			$('loading').setStyle('display', 'none');
			$('actualizar').set('disabled', false);
			
			alert('Los precios de compra han sido actualizados');
		}
	}).send();
}

var cambiaMP = function() {
	if ($('codmp').get('value').getVal() == 0) {
		$('codmp').set('value', '');
		$('nombre_mp').set('value', '');
	}
	else {
		new Request({
			'url': 'ActualizarPreciosCompra.php',
			'method': 'post',
			'data': {
				'accion': 'mp',
				'codmp': $('codmp').get('value')
			},
			'onSuccess': function(data) {
				if (data == '') {
					alert('El producto no se encuentra en el catálogo para cambio de precio');
					$('codmp').set('value', '');
					$('nombre_mp').set('value', '');
					$('codmp').focus();
				}
				else
					$('nombre_mp').set('value', data);
			}
		}).send();
	}
}
