// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'ControlAvio.php',
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
			
			$('num_cia').addEvents({
				'change': ObtenerCia,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('num_cia').select();
		}
	}).send();
}

var ObtenerCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ControlAvio.php',
			'data': 'accion=obtenerCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo o no la tiene asignada');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia').set('value', '');
	}
}

var Consultar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
		
		return;
	}
	
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'ControlAvio.php',
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
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				s = new Sortables('#controles tbody', {
					//'clone': true,
					'revert':true,
					'opacity': 0.5,
					'handle': 'td.dragme',
					'onComplete': function(el) {
						$$('#controles tbody')[0].getElements('tr').each(function(row, i) {
							row.removeClass('linea_off').removeClass('linea_on').addClass('linea_' + (i % 2 == 0 ? 'off' : 'on'));
						});
					}
				});
				
				$('cancelar').addEvent('click', Inicio);
				
				$('actualizar').addEvent('click', Actualizar);
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var Actualizar = function() {
	if (confirm('¿Desea actualizar los controles?')) {
		var orden = s.serialize(0, function(el, i) {
			return 'orden[]=' + JSON.encode({
				'num_cia': $('num_cia').get('value').getNumericValue(),
				'codmp': el.getProperty('id').replace('row_', '').getNumericValue(),
				'orden': i + 1
			})
		}).join('&');
		
		new Request({
			'url': 'ControlAvio.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString() + (orden != '' ? '&' + orden : ''),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Actualizando controles...', 'Control de av&iacute;o', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				Inicio.run();
			}
		}).send();
	}
}
