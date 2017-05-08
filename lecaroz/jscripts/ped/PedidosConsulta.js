// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'PedidosConsulta.php',
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
			
			$('folios').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').select();
					}
				}
			});
			
			$('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('mps').select();
					}
				}
			});
			
			$('mps').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('pros').select();
					}
				}
			});
			
			$('pros').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('omitir_cias').select();
					}
				}
			});
			
			$('omitir_cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('omitir_mps').select();
					}
				}
			});
			
			$('omitir_mps').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('omitir_pros').select();
					}
				}
			});
			
			$('omitir_pros').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha1').select();
					}
				}
			});
			
			$('fecha1').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha2').select();
					}
				}
			});
			
			$('fecha2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('folios').select();
					}
				}
			});
			
			$('folios').select();
			
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
		'url': 'PedidosConsulta.php',
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
			if (result) {
				$('captura').empty().set('html', result);
				
				$('checkall').addEvent('change', checkAll.pass($('checkall')));
				
				$$('input[id=checkblock]').each(function(el) {
					el.store('num_cia', el.get('value')).removeProperty('value').addEvent('change', checkBlock.pass(el));
				});
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('.enlace').each(function(el, i) {
					el.store('tip:title', '<img src="imagenes/info.png" /> Informaci&oacute;n');
					el.store('tip:text', el.get('title'));
					
					el.removeProperty('title');
					
					el.addEvents({
						'mouseover': function() {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							this.setStyle('cursor', 'default');
						}
					});
				});
				
				tips = new Tips($$('.enlace'), {
					'fixed': true,
					'className': 'Tip',
					'showDelay': 50,
					'hideDelay': 50
				});
				
				$('reporte_cia').addEvent('click', Reporte.pass('cia'));
				$('reporte_mp').addEvent('click', Reporte.pass('mp'));
				$('reporte_pro').addEvent('click', Reporte.pass('pro'));
				
				$('memo').addEvent('click', Reporte.pass('memo'));
				
				$('email').addEvent('click', Email);
				
				$('borrar').addEvent('click', Borrar);
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var Reporte = function() {
	if ($$('input[id=id]:checked').length > 0) {
		var tipo = arguments[0],
			url = 'ReportePedidos.php',
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			//param = '?tipo=' + tipo + '&' + $$('input[id=id]:checked').get('value').map(function(val) { return 'id[]=' + val; }).join('&'),
			win;
		
		$('Datos').set({
			'action': url,
			'method': 'post',
			'target': 'reporte'
		});
		
		$('tipo').set('value', tipo);
		
		win = window.open('', 'reporte', opt);
		
		$('Datos').submit();
		
		win.focus();
	}
	else {
		alert('Debe seleccionar al menos un registro');
	}
}

var Email = function() {
	if ($$('input[id=id]:checked').length > 0) {
		new Request({
			'url': 'EmailPedidos.php',
			'data': $$('input[id=id]:checked').get('value').map(function(val) { return 'id[]=' + val; }).join('&'),
			'onRequest': function() {
				new Element('img', {
					'id': 'loading',
					'src': 'imagenes/_loading.gif'
				}).inject($('email'), 'after');
				
				new Element('span', {
					'id': 'leyenda',
					'text': ' Enviando pedidos por email...'
				}).inject($('loading'), 'after');
			},
			'onSuccess': function(result) {
				$('loading').dispose();
				$('leyenda').dispose();
				
				alert('Pedidos enviados por email');
			}
		}).send();
	}
	else {
		alert('Debe seleccionar al menos un registro');
	}
}

var Borrar = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea borrar los registros seleccionados?')) {
		new Request({
			'url': 'PedidosConsulta.php',
			'data': 'accion=borrar&' + $$('input[id=id]:checked').get('value').map(function(val) { return 'id[]=' + val; }).join('&'),
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
				Consultar.run(param);
			}
		}).send();
	}
}

var checkAll = function() {
	var checkbox = arguments[0];
	
	$$('input[id=checkblock]').set('checked', checkbox.get('checked'));
	$$('input[id=id]').set('checked', checkbox.get('checked'));
}

var checkBlock = function() {
	var num_cia = arguments[0].retrieve('num_cia'),
		checkbox = arguments[0];
	
	$$('input[id=id][num_cia=' + num_cia + ']').set('checked', checkbox.get('checked'));
}
