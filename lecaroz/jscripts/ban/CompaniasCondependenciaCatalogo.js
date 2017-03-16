// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'CompaniasCondependenciaCatalogo.php',
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
			
			$('cias_pri').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias_sec').focus();
					}
				}
			});
			
			$('cias_sec').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias_pri').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('cias_pri').focus();
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
		'url': 'CompaniasCondependenciaCatalogo.php',
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
				
				$$('img[id=baja]').each(function(el) {
					var id = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'default');
						},
						'click': Baja.pass(id)
					});
				});
				
				$('regresar').addEvent('click', Inicio);
				
				$('alta').addEvent('click', Alta);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'CompaniasCondependenciaCatalogo.php',
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
			
			validator = new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			styles = new FormStyles($('Datos'));
			
			newRow(0);
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('alta').addEvent('click', doAlta);
			
			$('num_cia_pri').focus();
		}
	}).send();
}

var newRow = function() {
	var i = arguments[0],
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject($('tabla')),
		td1 = new Element('td').inject(tr),
		td2 = new Element('td').inject(tr),
		num_cia_pri = new Element('input', {
			'type': 'text',
			'id': 'num_cia_pri',
			'name': 'num_cia_pri[]',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).inject(td1),
		nombre_cia_pri = new Element('input', {
			'type': 'text',
			'id': 'nombre_cia_pri',
			'name': 'nombre_cia_pri[]',
			'size': '40',
			'disabled': true
		}).inject(td1),
		num_cia_sec = new Element('input', {
			'type': 'text',
			'id': 'num_cia_sec',
			'name': 'num_cia_sec[]',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).inject(td2),
		nombre_cia_sec = new Element('input', {
			'type': 'text',
			'id': 'nombre_cia_sec',
			'name': 'nombre_cia_sec[]',
			'size': '40',
			'disabled': true
		}).inject(td2);
	
	validator.addElementEvents(num_cia_pri);
	validator.addElementEvents(num_cia_sec);
	
	styles.addElementEvents(num_cia_pri);
	styles.addElementEvents(num_cia_sec);
	
	num_cia_pri.addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=num_cia_sec]')[i].select();
			}
			else if (e.key == 'up' && i > 0) {
				e.stop();
				
				$$('input[id=num_cia_pri]')[i - 1].select();
			}
			else if (e.key == 'down' && i < $$('input[id=num_cia_pri]').length - 1) {
				e.stop();
				
				$$('input[id=num_cia_pri]')[i + 1].select();
			}
		},
		'change': ObtenerCia.pass([i, 'pri'])
	});
	
	num_cia_sec.addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (i + 1 > $$('input[id=num_cia_pri]').length - 1) {
					newRow(i + 1);
				}
				
				$$('input[id=num_cia_pri]')[i + 1].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=num_cia_pri]')[i].select();
			}
			else if (e.key == 'up' && i > 0) {
				e.stop();
				
				$$('input[id=num_cia_sec]')[i - 1].select();
			}
			else if (e.key == 'down' && i < $$('input[id=num_cia_sec]').length - 1) {
				e.stop();
				
				$$('input[id=num_cia_sec]')[i + 1].select();
			}
		},
		'change': ObtenerCia.pass([i, 'sec'])
	});
}

var ObtenerCia = function() {
	var i = arguments[0],
		tipo = arguments[1],
		num_cia = $$('input[id=num_cia_' + tipo + ']')[i],
		nombre_cia = $$('input[id=nombre_cia_' + tipo + ']')[i];
	
	if (num_cia.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CompaniasCondependenciaCatalogo.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					nombre_cia.set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					num_cia.set('value', num_cia.retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		num_cia.set('value', '');
		nombre_cia.set('value', '');
	}
}

var doAlta = function() {
	var queryString = [];
	
	$('Datos').getElements('input').each(function(el) {
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
		'url': 'CompaniasCondependenciaCatalogo.php',
		'data': 'accion=doAlta&' + queryString.join('&'),
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Registrando nuevo producto...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty();
			
			Consultar.run(param);
		}
	}).send();
}

var Baja = function() {
	var id = arguments[0];
	
	if (confirm('¿Desea dar de baja la asociación de cuentas?')) {
		new Request({
			'url': 'CompaniasCondependenciaCatalogo.php',
			'data': 'accion=doBaja&id=' + id,
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Dando de baja la asociación de cuentas...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}
