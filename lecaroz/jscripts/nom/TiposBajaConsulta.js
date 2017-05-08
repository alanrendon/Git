// JavaScript Document

window.addEvent('domready', function() {
	Consulta.run();
});

var Consulta = function() {
	new Request({
		'url': 'TiposBajaConsulta.php',
		'data': 'accion=consulta',
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
			$('captura').empty().set('html', result);
			
			$$('tr[class^=linea_]').each(function(row, i) {
				row.addEvents({
					'mouseover': function(e) {
						e.stop();
						
						row.addClass('highlight');
					},
					'mouseout': function(e) {
						e.stop();
						
						row.removeClass('highlight');
					}
				});
			});
			
			$('alta').addEvent('click', AltaTipo);
			
			$$('img[id=modificar][src!=/lecaroz/iconos/pencil_gray.png]').each(function(el) {
				var id = el.get('alt').getNumericValue();
				
				el.removeProperty('alt').addEvents({
					'mouseover': function(e) {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function(e) {
						el.setStyle('cursor', 'default');
					},
					'click': ModificarTipo.pass(id)
				});
			});
			
			$$('img[id=baja][src!=/lecaroz/iconos/cancel_round_gray.png]').each(function(el) {
				var id = el.get('alt').getNumericValue();
				
				el.removeProperty('alt').addEvents({
					'mouseover': function(e) {
						el.setStyle('cursor', 'pointer');
					},
					'mouseout': function(e) {
						el.setStyle('cursor', 'default');
					},
					'click': BajaTipo.pass(id)
				});
			});
		}
	}).send();
}

var AltaTipo = function() {
	var num_cia = $chk(arguments[0]) ? arguments[0] : 0;
	
	new Request({
		'url': 'TiposBajaConsulta.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando pantalla...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('nombre_tipo_baja').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			}).focus();
			
			$('cancelar').addEvent('click', Consulta);
			
			$('alta').addEvent('click', Actualizar.pass('alta'));
		}
	}).send();
}

var ModificarTipo = function() {
	new Request({
		'url': 'TiposBajaConsulta.php',
		'data': 'accion=modificar&id=' + arguments[0],
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Obteniendo datos del trabajador...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('nombre_tipo_baja').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			}).focus();
			
			$('cancelar').addEvent('click', Consulta);
			
			$('modificar').addEvent('click', Actualizar.pass('modificar'));
		}
	}).send();
}

var Actualizar = function() {
	if ($('nombre_tipo_baja').get('value').clean() == '') {
		alert('Debe escribir un nombre descriptivo para el tipo de baja');
		
		$('nombre_tipo_baja').focus();
	} else if (arguments[0] == 'alta') {
		new Request({
			'url': 'TiposBajaConsulta.php',
			'data': 'accion=insertar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Registrando nuevo tipo de baja...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consulta.run();
			}
		}).send();
	} else if (arguments[0] == 'modificar') {
		new Request({
			'url': 'TiposBajaConsulta.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Actualizando tipo de baja...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consulta.run();
			}
		}).send();
	}
}

var BajaTipo = function() {
	if (confirm('¿Esta seguro de dar de baja al trabajador?')) {
		new Request({
			'url': 'TiposBajaConsulta.php',
			'data': 'accion=baja&id=' + arguments[0],
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Borando tipo de baja...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consulta.run();
			}
		}).send();
	}
}
