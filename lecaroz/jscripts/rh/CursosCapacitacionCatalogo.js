// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'CursosCapacitacionCatalogo.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			$('consultar').addEvent('click', Consultar);
		}
	}).send();
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'CursosCapacitacionCatalogo.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			$('alta').addEvent('click', Alta);
			
			$$('tr[id^=row]').addEvents({
				'mouseover': function() {
					this.addClass('highlight');
				},
				'mouseout': function() {
					this.removeClass('highlight');
				}
			});
			
			$$('img[id=status][src!=/lecaroz/iconos/accept_blank.png]').each(function(el) {
				el.addEvents({
					'mouseover': function() {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function() {
						el.setStyle('cursor', 'default');
					},
					'click': CambiarStatus.pass(el.get('alt'))
				});
				
				el.removeProperty('alt');
			});
			
			$$('img[id=ver]').each(function(el) {
				el.addEvents({
					'mouseover': function() {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function() {
						el.setStyle('cursor', 'default');
					},
					'click': VerEmpleados.pass(el.get('alt'))
				});
				
				el.removeProperty('alt');
			});
			
			$$('img[id=mod]').each(function(el) {
				el.addEvents({
					'mouseover': function() {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function() {
						el.setStyle('cursor', 'default');
					},
					'click': Modificar.pass(el.get('alt'))
				});
				
				el.removeProperty('alt');
			});
			
			$$('img[id=baja]').each(function(el) {
				el.addEvents({
					'mouseover': function() {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function() {
						el.setStyle('cursor', 'default');
					},
					'click': Baja.pass(el.get('alt'))
				});
				
				el.removeProperty('alt');
			});
			
			$('regresar').addEvent('click', Inicio);
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'CursosCapacitacionCatalogo.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('nombre_curso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						$('fecha_inicio').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('descripcion_curso').focus();
					}
				}
			}).focus();
			
			$('fecha_inicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'right') {
						e.stop();
						
						$('fecha_termino').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('nombre_curso').focus();
					} else if (e.key == e.key == 'down') {
						e.stop();
						
						$('descripcion_curso').focus();
					}
				}
			});
			
			$('fecha_termino').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						$('descripcion_curso').focus();
					} else if (e.key == 'left') {
						e.stop();
						
						$('fecha_inicio').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('nombre_curso').focus();
					}
				}
			});
			
			$('descripcion_curso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' && e.control) {
						e.stop();
						
						$('nombre_curso').focus();
					}
				}
			});
			
			$('cancelar').addEvent('click', Consultar.pass(param));
			
			$('alta').addEvent('click', doAlta);
		}
	}).send();
}

var doAlta = function() {
	if ($('nombre_curso').get('value').clean() == '') {
		alert('Debe especificar el nombre del curso');
		
		$('nombre_curso').focus();
	} else if ($('fecha_inicio').get('value') == '' && $('fecha_termino').get('value') == '') {
		alert('Debe especificar el periodo de aplicación de curso');
		
		$('fecha_inicio').select();
	} else if ($('descripcion_curso').get('value').clean() == '') {
		alert('Debe especificar la descripcion del curso');
		
		$('descripcion_curso').focus();
	} else if ($('descripcion_curso').get('value').length > 2000) {
		alert('La descripción del curso no debe sobrepasar los 2,000 caracteres');
		
		$('descripcion_curso').focus();
	} else {
		new Request({
			'url': 'CursosCapacitacionCatalogo.php',
			'data': 'accion=doAlta&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Registrando nuevo curso...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var CambiarStatus = function(id) {
	if (confirm('¿Desea dar por terminado el curso?')) {
		new Request({
			'url': 'CursosCapacitacionCatalogo.php',
			'data': 'accion=status&id=' + id,
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Terminando el curso...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var Modificar = function(id) {
	new Request({
		'url': 'CursosCapacitacionCatalogo.php',
		'data': 'accion=modificar&id=' + id,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('nombre_curso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						$('fecha_inicio').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('descripcion_curso').focus();
					}
				}
			}).focus();
			
			$('fecha_inicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'right') {
						e.stop();
						
						$('fecha_termino').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('nombre_curso').focus();
					} else if (e.key == e.key == 'down') {
						e.stop();
						
						$('descripcion_curso').focus();
					}
				}
			});
			
			$('fecha_termino').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						$('descripcion_curso').focus();
					} else if (e.key == 'left') {
						e.stop();
						
						$('fecha_inicio').select();
					} else if (e.key == 'up') {
						e.stop();
						
						$('nombre_curso').focus();
					}
				}
			});
			
			$('descripcion_curso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' && e.control) {
						e.stop();
						
						$('nombre_curso').focus();
					}
				}
			});
			
			$('cancelar').addEvent('click', Consultar.pass(param));
			
			$('modificar').addEvent('click', doModificar);
		}
	}).send();
}

var doModificar = function() {
	if ($('nombre_curso').get('value').clean() == '') {
		alert('Debe especificar el nombre del curso');
		
		$('nombre_curso').focus();
	} else if ($('fecha_inicio').get('value') == '' && $('fecha_termino').get('value') == '') {
		alert('Debe especificar el periodo de aplicación de curso');
		
		$('fecha_inicio').select();
	} else if ($('descripcion_curso').get('value').clean() == '') {
		alert('Debe especificar la descripcion del curso');
		
		$('descripcion_curso').focus();
	} else if ($('descripcion_curso').get('value').length > 2000) {
		alert('La descripción del curso no debe sobrepasar los 2,000 caracteres');
		
		$('descripcion_curso').focus();
	} else {
		new Request({
			'url': 'CursosCapacitacionCatalogo.php',
			'data': 'accion=doModificar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Modificando curso...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var Baja = function(id) {
	if (confirm('¿Desea dar de baja el curso seleccionado?')) {
		new Request({
			'url': 'CursosCapacitacionCatalogo.php',
			'data': 'accion=doBaja&id=' + id,
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Dando de baja el curso...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var VerEmpleados = function(id) {
	new Request({
		'url': 'CursosCapacitacionCatalogo.php',
		'data': 'accion=empleados&id=' + id,
		'onSuccess': function(content) {
			popup = new Popup(content, 'Empleados que tomaron el curso', 800, 400, VerEmpleadosOpen, null);
		}
	}).send();
}

var VerEmpleadosOpen = function() {
	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}
