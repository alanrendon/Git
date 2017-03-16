// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'RentasFacturasAutomatico.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'html': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('anio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('arrendadores').focus();
					}
				}
			});
			
			$('arrendadores').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('anio').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('anio').select();
		}
	}).send();
}

var Consultar = function() {
	new Request({
		'url': 'RentasFacturasAutomatico.php',
		'data': 'accion=consultar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'html': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('checkall').addEvent('change', checkAll.pass($('checkall')));
				
				$$('input[id=checkarrendador]').each(function(el) {
					el.store('arrendador', el.get('value')).removeProperty('value').addEvent('change', checkBloque.pass(el));
				});
				
				$('generar').addEvent('click', Generar);
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var checkAll = function() {
	var checkbox = arguments[0];
	
	$$('input[id=checkarrendador]').set('checked', checkbox.get('checked'));
	$$('input[id=id]').set('checked', checkbox.get('checked'));
}

var checkBloque = function() {
	var arrendador = arguments[0].retrieve('arrendador'),
		checkbox = arguments[0];
	
	$$('input[id=id][arrendador=' + arrendador + ']').set('checked', checkbox.get('checked'));
}

var Generar = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe selccionar al menos un registro');
	}
	else if (confirm('¿Desea generar los recibos de renta seleccionados?')) {
		new Request({
			'url': 'RentasFacturasAutomatico.php',
			'data': 'accion=generar&' + $$( 'input[id=anio]', 'input[id=mes]', 'input[id=id]:checked').filter(function(el) { return !el.disabled; }).map(function(el) { return el.get('name') + '=' + el.get('value'); }).join('&'),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'html': ' Generando facturas electr&oacute;nicas, por favor espere a que el proceso termine...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result == '') {
					Inicio();
				}
				else if (result == '-1') {
					alert('No puede generar facturas electrónicas porque el servidor se encuentra ocupado, intentelo más tarde.');
					
					Inicio();
				}
				else if (result == '-2') {
					alert('Error al conectar al servidor de CFD');
					
					Inicio();
				}
				else if (result == '-3') {
					alert('Error al iniciar sesión en el servidor de CFD');
					
					Inicio();
				}
				else {
					$('captura').empty().set('html', result);
					
					$$('tr[id=row]').addEvents({
						'mouseover': function() {
							this.addClass('highlight');
						},
						'mouseout': function() {
							this.removeClass('highlight');
						}
					});
					
					$('terminar').addEvent('click', Inicio);
				}
			}
		}).send();
	}
}
