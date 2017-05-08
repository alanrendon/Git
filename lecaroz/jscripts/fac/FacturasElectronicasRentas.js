// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'FacturasElectronicasRentas.php',
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
						
						this.blur();
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
		'url': 'FacturasElectronicasRentas.php',
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
				
				$$('input[id=checkinmobiliaria]').each(function(el) {
					el.store('inmobiliaria', el.get('value')).removeProperty('value').addEvent('change', checkBloque.pass(el));
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
	
	$$('input[id=checkinmobiliaria]').set('checked', checkbox.get('checked'));
	$$('input[id=id]').set('checked', checkbox.get('checked'));
}

var checkBloque = function() {
	var inmobiliaria = arguments[0].retrieve('inmobiliaria'),
		checkbox = arguments[0];
	
	$$('input[id=id][inmobiliaria=' + inmobiliaria + ']').set('checked', checkbox.get('checked'));
}

var Generar = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe selccionar al menos un registro');
	}
	else if (confirm('¿Desea generar los recibos de renta seleccionados?')) {
		new Request({
			'url': 'FacturasElectronicasRentas.php',
			'data': 'accion=generar&' + $$( 'input[id=anio]', 'input[id=mes]', 'input[id=id]:checked').map(function(el) { return el.get('name') + '=' + el.get('value'); }).join('&'),
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
