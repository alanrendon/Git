// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function () {
	new Request({
		'url': 'ProveedoresConsulta.php',
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
			
			$('pros').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('nombre').focus();
					}
				}
			});
			
			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('pros').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('pros').focus();
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
		'url': 'ProveedoresConsulta.php',
		'data': 'accion=consultar&' + param,
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
			if (result != '') {
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
				
				
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'ProveedoresConsulta.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando programa...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
		}
	}).send();
}
