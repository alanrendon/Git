// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'PedidosAlCorte.php',
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
			
			$('codmp').addEvents({
				'change': obtenerMP,
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
						
						$('omitir_cias').select();
					}
				}
			});
			
			$('omitir_cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha').select();
					}
				}
			});
			
			$('fecha').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha_hoja').select();
					}
				}
			});
			
			$('fecha_hoja').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('codmp').select();
					}
				}
			});
			
			$('siguiente').addEvent('click', calculoInicial);
			
			$('codmp').select();
		}
	}).send();
}

var obtenerMP = function() {
	if ($('codmp').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PedidosAlCorte.php',
			'data': 'accion=obtenerMP&codmp=' + $('codmp').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_mp').set('value', result);
				}
				else {
					alert('El producto no se encuentra en el catálogo');
					
					$('codmp').set('value', $('codmp').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('codmp').set('value', '');
		$('nombre_mp').set('');
	}
}

var calculoInicial = function() {
	if ($('codmp').get('value').getNumericValue() == 0) {
		alert('Debe especificar el producto');
		
		$('codmp').select();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de corte');
		
		$('fecha').select();
	}
	else if ($('fecha_hoja').get('value') == '') {
		alert('Debe especificar la fecha de las hojas de diario');
		
		$('fecha_hoja').select();
	}
	else {
		new Request({
			'url': 'PedidosAlCorte.php',
			'data': 'accion=calculoInicial&' + $('Datos').toQueryString(),
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
					
					if (!$chk($('checkall'))) {
						$('regresar').addEvent('click', Inicio);
					}
					else {
						$('checkall').addEvent('click', function() {
							$$('input[id=pedido]').set('checked', this.get('checked'));
						});
						
						$('cancelar').addEvent('click', Inicio);
						
						$('siguiente').addEvent('click', distribuirPedidos);
					}
				}
				else {
					alert('No hay resultados');
					
					Inicio.run();
				}
			}
		}).send();
	}
}

var distribuirPedidos = function() {
	if ($$('input[id=pedido]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else {
		new Request({
			'url': 'PedidosAlCorte.php',
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
				$('captura').empty().set('html', result);
				
				$('checkall').addEvent('click', function() {
					$$('input[id=pedido]').set('checked', this.get('checked'));
				});
				
				$('cancelar').addEvent('click', Inicio);
				
				$('registrar').addEvent('click', registrarPedidos);
			}
		}).send();
	}
}

var registrarPedidos = function() {
	if ($$('input[id=pedido]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea realizar los pedidos seleccionados?')) {
		new Request({
			'url': 'PedidosAlCorte.php',
			'data': 'accion=registrarPedidos&' + $('Datos').toQueryString(),
			'onRequest': function() {
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
