// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'CatalogoLocales.php',
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
			
			$('arrendadores').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.select();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('arrendadores').select();
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
		'url': 'CatalogoLocales.php',
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
			if (result != '') {
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
				
				$$('img[id=alta_inm]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': Alta.pass(el.get('alt'))
					});
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
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var Alta = function() {
	var inmobiliaria = $chk(arguments[0]) ? arguments[0] : 0;
	
	new Request({
		'url': 'CatalogoLocales.php',
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
			
			$('arrendador').addEvents({
				'change': obtenerArrendador,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('alias_local').select();
					}
				}
			}).set('value', inmobiliaria).fireEvent('change');
			
			$('alias_local').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('domicilio').select();
					}
				}
			});
			
			$('domicilio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('superficie').select();
					}
				}
			});
			
			$('superficie').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cuenta_predial').select();
					}
				}
			});
			
			$('cuenta_predial').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('arrendador').select();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('alta').addEvent('click', doAlta);
			
			$('arrendador').select();
		}
	}).send();
}

var obtenerArrendador = function() {
	if ($('arrendador').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CatalogoLocales.php',
			'data': 'accion=obtenerArrendador&arrendador=' + $('arrendador').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('idarrendador').set('value', data.idarrendador);
					$('nombre_arrendador').set('value', data.nombre_arrendador);
				}
				else {
					alert('La inmobiliaria no se encuentra en el catálogo');
					
					$('arrendador').set('value', $('arrendador').retrieve('tmp', '')).focus();
				}
			}
		}).send();
	}
	else {
		$('idarrendador').set('value', '');
		$('arrendador').set('value', '');
		$('nombre_arrendador').set('value', '');
	}
}

var doAlta = function() {
	if ($('arrendador').get('value').getNumericValue() == 0) {
		alert('Debe especificar la inmobiliaria');
		
		$('arrendador').select();
	}
	else if ($('alias_local').get('value') == '') {
		alert('Debe especificar un alias para el nuevo arrendatario');
		
		$('alias_local').select();
	}
	else if ($('domicilio').get('value') == '') {
		alert('Debe especificar el domicilio del local');
		
		$('domicilio').select();
	}
	else {
		var queryString = [];
		
		$('Datos').getElements('input, select, textarea').each(function(el) {
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') {
				return;
			}
			
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt) {
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			
			$splat(value).each(function(val) {
				if (typeof val != 'undefined') {
					queryString.push(el.name + '=' + encodeURIComponent(val));
				}
			});
		});
		
		new Request({
			'url': 'CatalogoLocales.php',
			'data': 'accion=doAlta&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Registrando nuevo arrendatario...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var Modificar = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'CatalogoLocales.php',
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
			
			$('arrendador').addEvents({
				'change': obtenerArrendador,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('alias_local').select();
					}
				}
			});
			
			$('alias_local').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('domicilio').select();
					}
				}
			});
			
			$('domicilio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('superficie').select();
					}
				}
			});
			
			$('superficie').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cuenta_predial').select();
					}
				}
			});
			
			$('cuenta_predial').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('arrendador').select();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('modificar').addEvent('click', doModificar);
			
			$('alias_local').select();
		}
	}).send();
}

var doModificar = function() {
	if ($('arrendador').get('value').getNumericValue() == 0) {
		alert('Debe especificar la inmobiliaria');
		
		$('arrendador').select();
	}
	else if ($('alias_local').get('value') == '') {
		alert('Debe especificar un alias para el nuevo arrendatario');
		
		$('alias_local').select();
	}
	else if ($('domicilio').get('value') == '') {
		alert('Debe especificar el domicilio del local');
		
		$('domicilio').select();
	}
	else {
		var queryString = [];
		
		$('Datos').getElements('input, select, textarea').each(function(el) {
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') {
				return;
			}
			
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt) {
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			
			$splat(value).each(function(val) {
				if (typeof val != 'undefined') {
					queryString.push(el.name + '=' + encodeURIComponent(val));
				}
			});
		});
		
		new Request({
			'url': 'CatalogoLocales.php',
			'data': 'accion=doModificar&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Modificando arrendatario...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var Baja = function() {
	var id = arguments[0];
	
	if (id.getNumericValue() > 0) {
		if (confirm('¿Desea dar de baja al arrendatario seleccionado?')) {
			new Request({
				'url': 'CatalogoLocales.php',
				'data': 'accion=doBaja&id=' + id,
				'onRequest': function() {
					$('captura').empty();
					
					new Element('img', {
						'src': 'imagenes/_loading.gif'
					}).inject($('captura'));
					
					new Element('span', {
						'text': ' Dando de baja al arrendatario...'
					}).inject($('captura'));
				},
				'onSuccess': function(result) {
					$('captura').empty();
					
					Consultar.run(param);
				}
			}).send();
		}
	}
	else {
		alert('El local no puede ser borrado debido a que esta asociado con un arrendatario');
	}
}
