// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
	
	$(window).addEvent('unload', Desautorizar);
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre').set('value', '');
		$('num_cia').focus();
	}
	else {
		new Request({
			'url': 'ControlProduccion.php',
			'data': {
				'accion': 'getCia',
				'num_cia': $('num_cia').get('value')
			},
			'onSuccess': function(result) {
				if (result == '') {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', '');
					$('nombre').set('value', '');
					$('num_cia').focus();
				}
				else {
					$('nombre').set('value', result);
					$('num_cia').focus();
				}
			}
		}).send();
	}
}

var Inicio = function() {
	new Request({
		'url': 'ControlProduccion.php',
		'data': 'accion=inicio',
		'onRquest': function() {
			
		},
		'onSuccess': function(result) {
			$('captura').set('html', result);
			
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
			
			$('consultar').addEvent('click', Consultar);
			
			$('num_cia').focus();
		}
	}).send();
}

var Consultar = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else {
		new Request({
			'url': 'ControlProduccion.php',
			'data': {
				'accion': 'buscar',
				'num_cia': $('num_cia').get('value')
			},
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == '') {
					Inicio();
					
					alert('No hay resultados');
				}
				else {
					$('captura').set('html', result);
					
					new Formulario('Datos');
					
					$('agregar').addEvent('click', Agregar);
					
					$$('img[id=mod]').each(function(el) {
						el.addEvents({
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							},
							'click': Modificar.pass(el.get('alt'))
						});
					});
					
					$$('img[id=del]').each(function(el) {
						el.addEvents({
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							},
							'click': Borrar.pass(el.get('alt'))
						});
					});
					
					$('regresar').addEvent('click', function() {
						Desautorizar();
						Inicio();
					});
				}
			}
		}).send();
	}
}

var Agregar = function() {
	new Request({
		'url': 'ControlProduccion.php',
		'data': {
			'accion': 'agregar',
			'num_cia': $('num_cia').get('value')
		},
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result == '-1') {
				alert('Necesita autorización para modificar este registro');
			}
			else {
				$('captura').set('html', result);
				
				new Formulario('Datos');
				
				$('cod_turno').addEvent('change', ultimoOrden);
				
				$('cod_producto').addEvents({
					'change': cambiaProducto,
					'keydown': function(e) {
						if (e.key == 'enter') {
							$('num_orden').select();
							e.stop();
						}
					}
				});
				
				$('num_orden').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('precio_raya').select();
						e.stop();
					}
				});
				
				$('precio_raya').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('porc_raya').select();
						e.stop();
					}
				});
				
				$('porc_raya').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('precio_venta').select();
						e.stop();
					}
				});
				
				$('precio_venta').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('tantos').select();
						e.stop();
					}
				});
				
				$('tantos').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('cod_producto').select();
						e.stop();
					}
				});
				
				$('regresar').addEvent('click', Consultar);
				
				$('agregarControl').addEvent('click', agregarControl);
				
				$('cod_turno').fireEvent('change');
				
				$('cod_producto').focus();
			}
		}
	}).send();
}

var ultimoOrden = function() {
	new Request({
		'url': 'ControlProduccion.php',
		'data': 'accion=ultimoOrden&num_cia=' + $('num_cia').get('value') + '&cod_turno=' + $('cod_turno').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			$('num_orden').set('value', result);
		}
	}).send();
}

var Modificar = function() {
	new Request({
		'url': 'ControlProduccion.php',
		'data': {
			'accion': 'modificar',
			'id': arguments[0]
		},
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result == '-1') {
				alert('Necesita autorización para modificar este registro');
			}
			else {
				$('captura').set('html', result);
				
				new Formulario('Datos');
				
				$('num_orden').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('precio_raya').select();
						e.stop();
					}
				});
				
				$('precio_raya').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('porc_raya').select();
						e.stop();
					}
				});
				
				$('porc_raya').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('precio_venta').select();
						e.stop();
					}
				});
				
				$('precio_venta').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('tantos').select();
						e.stop();
					}
				});
				
				$('tantos').addEvent('keydown', function(e) {
					if (e.key == 'enter') {
						$('num_orden').select();
						e.stop();
					}
				});
				
				$('regresar').addEvent('click', Consultar);
				
				$('actualizarControl').addEvent('click', actualizarControl);
				
				$('num_orden').focus();
			}
		}
	}).send();
}

var Borrar = function() {
	if (confirm('¿Desea borrar el registro?')) {
		new Request({
			'url': 'ControlProduccion.php',
			'data': {
				'accion': 'borrar',
				'id': arguments[0],
				'num_cia': $('num_cia').get('value')
			},
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == '-1') {
					alert('Necesita autorización para modificar este registro');
				}
				else {
					Consultar();
				}
			}
		}).send();
	}
}

var cambiaProducto = function() {
	if ($('cod_producto').get('value').getVal() == 0) {
		$('cod_producto').set('value', '');
		$('nombre_producto').set('value', '');
	}
	else {
		new Request({
			'url': 'ControlProduccion.php',
			'data': {
				'accion': 'getPro',
				'cod': $('cod_producto').get('value')
			},
			'onSuccess': function(result) {
				if (result == '') {
					alert('El producto no se encuentra en el catálogo');
					$('cod_producto').set('value', '');
					$('nombre_producto').set('value', '');
					$('cod_producto').focus();
				}
				else {
					$('nombre_producto').set('value', result);
				}
			}
		}).send();
	}
}

var agregarControl = function() {
	if ($('cod_producto').get('value').getVal() == 0) {
		alert('Debe especificar el producto');
		$('cod_producto').focus();
	}
	else if ($('num_orden').get('value').getVal() == 0) {
		alert('Debe especificar el número de orden');
		$('num_orden').focus();
	}
	else if ($('precio_raya').get('value').getVal() > 0 && $('porc_raya').get('value').getVal() > 0) {
		alert('Solo puede especificar precio de raya o porcentaje de raya, pero nunca ambos');
		$('precio_raya').focus();
	}
	else if ($('precio_raya').get('value').getVal() == 0 && $('porc_raya').get('value').getVal() == 0 && $('precio_venta').get('value').getVal() == 0) {
		alert('Debe especificar el precio de raya, porcentaje de raya o precio de venta');
		$('precio_raya').focus();
	}
	else if ($('precio_venta').get('value').getVal() > 0 && $('precio_raya').get('value').getVal() > ($('precio_venta').get('value').getVal() * 0.15).round(4)) {
		alert('El precio de raya no puede ser mayor al 15% del precio de venta (' + ($('precio_venta').get('value').getVal() * 0.15).round(4).numberFormat(4, '.', ',') + ')');
	}
	else if ($('porc_raya').get('value').getVal() > 15) {
		alert('El porcentaje de raya no puede ser mayor a 15%');
		$('porc_raya').select();
	}
	else if ($('tantos').get('value').getVal() == 0) {
		alert('Debe especificar los tantos');
		$('tantos').select();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			'url': 'ControlProduccion.php',
			'data': 'accion=grabar&' + $('Datos').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == '-1') {
					alert('El producto "' + $('cod_producto').get('value') + ' ' + $('nombre_producto').get('value') + '" ya existe para el turno "' + $('cod_turno').getSelected().get('text') + '"');
					return false;
				}
				else {
					Consultar();
				}
			}
		}).send();
	}
}

var actualizarControl = function() {
	if ($('num_orden').get('value').getVal() == 0) {
		alert('Debe especificar el número de orden');
		$('num_orden').focus();
	}
	else if ($('precio_raya').get('value').getVal() > 0 && $('porc_raya').get('value').getVal() > 0) {
		alert('Solo puede especificar precio de raya o porcentaje de raya, pero nunca ambos');
		$('precio_raya').focus();
	}
	else if ($('precio_raya').get('value').getVal() == 0 && $('porc_raya').get('value').getVal() == 0 && $('precio_venta').get('value').getVal() == 0) {
		alert('Debe especificar el precio de raya, porcentaje de raya o precio de venta');
		$('precio_raya').focus();
	}
	else if ($('precio_venta').get('value').getVal() > 0 && $('precio_raya').get('value').getVal() > ($('precio_venta').get('value').getVal() * 0.15).round(4)) {
		alert('El precio de raya no puede ser mayor al 15% del precio de venta (' + ($('precio_venta').get('value').getVal() * 0.15).round(4).numberFormat(4, '.', ',') + ')');
	}
	else if ($('porc_raya').get('value').getVal() > 15) {
		alert('El porcentaje de raya no puede ser mayor a 15%');
		$('porc_raya').select();
	}
	else if ($('tantos').get('value').getVal() == 0) {
		alert('Debe especificar los tantos');
		$('tantos').select();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			'url': 'ControlProduccion.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Consultar();
			}
		}).send();
	}
}


var Desautorizar = function() {
	if ($defined($('num_cia'))) {
		new Request({
			'url': 'ControlProduccion.php',
			'data': {
				'accion': 'desautorizar',
				'num_cia': $('num_cia').get('value')
			}
		}).send();
	}
}
