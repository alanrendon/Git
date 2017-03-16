// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function () {
	new Request({
		'url': 'TrabajadoresConsultaSimple.php',
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
						
						$('trabajadores').focus();
					}
				}
			});
			
			$('trabajadores').addEvents({
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
						
						$('ap_paterno').focus();
					}
				}
			});
			
			$('ap_paterno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('ap_materno').focus();
					}
				}
			});
			
			$('ap_materno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('rfc').focus();
					}
				}
			});
			
			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').focus();
					}
				}
			});
			
			$('buscar').addEvent('click', Buscar);
			
			$('cias').focus();
		}
	}).send();
}

var Buscar = function() {
	BuscarTrabajadores.run();
}

var BuscarTrabajadores = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'TrabajadoresConsultaSimple.php',
		'data': 'accion=buscar&' + param,
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
				
				$$('img[id=cia_emp]').each(function(img) {
					img.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Labora en');
					img.store('tip:text', img.get('alt'));
					
					img.removeProperty('alt');
				});
				
				tips = new Tips($$('img[id=cia_emp]'), {
					'fixed': true,
					'className': 'Tip',
					'showDelay': 50,
					'hideDelay': 50
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
