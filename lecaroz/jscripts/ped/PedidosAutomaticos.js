// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'PedidosAutomaticos.php',
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
						
						$('dias').select();
					}
				}
			});
			
			$('dias').addEvents({
				'change': function() {
					this.removeClass('red');
				},
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').select();
					}
				}
			});
			
			$('cias').select();
			
			$('siguiente').addEvent('click', calculoInicial);
		}
	}).send();
}

var calculoInicial = function() {
	new Request({
		'url': 'PedidosAutomaticos.php',
		'data': 'accion=calculoInicial&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Realizando calculos para pedidos...'
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
				
				$('cancelar').addEvent('click', Inicio);
				
				$('siguiente').addEvent('click', distribuirPedidos);
			}
			else {
				alert('No hay pedidos por realizar');
				
				Inicio.run();
			}
		}
	}).send();
}

var distribuirPedidos = function() {
	new Request({
		'url': 'PedidosAutomaticos.php',
		'data': 'accion=distribuirPedidos&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Distribuyendo pedidos...'
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
				
				$('cancelar').addEvent('click', Inicio);
				
				$('siguiente').addEvent('click', Anotaciones);
			}
			else {
				alert('No hay pedidos por realizar');
				
				Inicio.run();
			}
		}
	}).send();
}

var Anotaciones = function() {
	new Request({
		'url': 'PedidosAutomaticos.php',
		'data': 'accion=anotaciones&' + $$('input[id=num_pro]').get('value').map(function(value) { return 'num_pro[]=' + value; }).join('&'),
		'onRequest': function() {
		},
		'onSuccess': function(result) { 
			popup = new Popup(result, 'Anotaciones', 800, 600, popupOpen, null, {scrollBars: true});
		}
	}).send();
}

var popupOpen = function() {
	new FormValidator($('Anotaciones'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Anotaciones'));
	
	$$('textarea[id=anotacion]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i < $$('textarea[id=anotacion]').length - 1) {
						$$('textarea[id=anotacion]')[i + 1].select();
					}
					else {
						$$('textarea[id=anotacion]')[0].select();
					}
				}
			}
		});
	});
	
	$('popup_close').addEvent('click', function() {
		popup.Close();
	});
	
	$('terminar').addEvent('click', terminarProceso);
}

var terminarProceso = function() {
	var queryString = [];
	
	$('Anotaciones').getElements('input, textarea').each(function(el) {
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
		'url': 'PedidosAutomaticos.php',
		'data': 'accion=terminarProceso&' + $('Datos').toQueryString() + '&' + queryString.join('&'),
		'onRequest': function() {
			popup.Close();
			
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Registrando pedidos...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			$('reporte_cia').addEvent('click', reporte.pass(['cia', $('folio').get('value')]));
			$('reporte_mp').addEvent('click', reporte.pass(['mp', $('folio').get('value')]));
			$('reporte_pro').addEvent('click', reporte.pass(['pro', $('folio').get('value')]));
			
			$('memo').addEvent('click', reporte.pass(['memo', $('folio').get('value')]));
			
			$('email').addEvent('click', email.pass($('folio').get('value')));
			
			$('terminar').addEvent('click', Inicio);
		}
	}).send();
}

var reporte = function() {
	var tipo = arguments[0],
		folio = arguments[1],
		url = 'ReportePedidos.php',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		param = '?tipo=' + tipo + '&folios=' + folio,
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}

var email = function() {
	new Request({
		'url': 'EmailPedidos.php',
		'data': 'folios=' + $('folio').get('value'),
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

var checkAll = function() {
	var checkbox = arguments[0];
	
	$$('input[id=checkblock]').set('checked', checkbox.get('checked'));
	$$('input[id=pedido]').set('checked', checkbox.get('checked'));
}

var checkBlock = function() {
	var num_cia = arguments[0].retrieve('num_cia'),
		checkbox = arguments[0];
	
	$$('input[id=pedido][num_cia=' + num_cia + ']').set('checked', checkbox.get('checked'));
}
