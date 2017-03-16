// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'SimulacionPedidosAutomaticosV2.php',
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
			
			if ($('cias').get('tag') == 'select') {
				$('dias').addEvents({
					'change': function() {
						this.removeClass('red');
					},
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();
							
							this.blur();
						}
					}
				});
			}
			else {
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
			}
			
			$('simular').addEvent('click', Simular);
		}
	}).send();
}

var Simular = function() {
	new Request({
		'url': 'SimulacionPedidosAutomaticosV2.php',
		'data': 'accion=simular&' + $('Datos').toQueryString(),
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
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay pedidos por realizar');
				
				Inicio.run();
			}
		}
	}).send();
}
