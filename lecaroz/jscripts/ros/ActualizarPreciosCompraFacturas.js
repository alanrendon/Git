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
				$('fecha1').focus();
			}
		}
	});
	
	$('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha2').focus();
			}
		}
	});
	
	$('fecha2').addEvents({
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
	
	$('result').setStyle('display', 'none');
	
	$('loading').setStyle('display', 'none');
	
	$('nuevo').addEvent('click', Nuevo);
	
	$('buscar').addEvent('click', Buscar);
	
	$('cias').focus();
});

var Nuevo = function() {
	$('result').set('html', '');
	$('result').setStyle('display', 'none');
	$('Datos').reset();
}

var Buscar = function() {
	if ($('fecha1').get('value').length < 8) {
		alert('Debe especificar el día o periodo de búsqueda');
		$('fecha1').focus();
		return false;
	}
	
	if ($('codmp').get('value').getVal() == 0) {
		alert('Debe especificar el código de producto');
		$('codmp').focus();
		return false;
	}
	
	new Request({
		'url': 'ActualizarPreciosCompraFacturas.php',
		'method': 'post',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('buscar').set({
				'value': 'Buscando...',
				'disabled': true
			});
			$('loading').setStyle('display', 'inline');
		},
		'onSuccess': function(data) {
			$('loading').setStyle('display', 'none');
			$('buscar').set({
				'value': 'Buscar Facturas',
				'disabled': false
			});
			
			if (data == '') {
				alert('No hay resultados');
				$('result').set('html', '');
				$('result').setStyle('display', 'none');
			}
			else {
				$('result').set('html', data);
				$('result').setStyle('display', 'block');
				
				$('checkall').addEvent('click', CheckAll);
				$('actualizar').addEvent('click', Actualizar);
				
				$('loading_upd').setStyle('display', 'none');
			}
		}
	}).send();
}

var Actualizar = function() {
	if ($('precio_compra').get('value').getVal() == 0) {
		alert('Debe especificar el nuevo precio de compra');
		$('precio_compra').focus();
		return false;
	}
	
	if (!confirm('¿Desea cambiar el precio de compra a ' + $('precio_compra').get('value') + ' en todas las facturas seleccionadas?')) {
		return false;
	}
	
	new Request({
		'url': 'ActualizarPreciosCompraFacturas.php',
		'method': 'post',
		'data': 'accion=actualizar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('loading_upd').setStyle('display', 'inline');
			$('actualizar').set({
				'value': 'Actualizando...',
				'disabled': true
			});
		},
		'onSuccess': function(data) {
			$('result').set('html', '');
			$('result').setStyle('display', 'none');
			
			$('Datos').reset();
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
			'url': 'ActualizarPreciosCompraFacturas.php',
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

var CheckAll = function() {
	$$('input[id=id]').set('checked', $('checkall').get('checked'));
}
