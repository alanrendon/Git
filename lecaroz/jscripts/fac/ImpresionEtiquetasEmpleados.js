// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('etiqueta').select();
			}
		}
	});
	
	$('etiqueta').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').focus();
			}
		}
	});
	
	$('buscar').addEvent('click', Buscar);
	
	$('cias').select();
});

var Buscar = function() {
	new Request({
		'url': 'ImpresionEtiquetasEmpleados.php',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('empleados').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('empleados'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('empleados'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('empleados').empty().set('html', result);
				
				$('checkall').addEvent('change', checkAll.pass($('checkall')));
				
				$$('input[id=checkblock]').each(function(el) {
					el.addEvent('change', checkBlock.pass(el));
				});
				
				$$('tr[class^=linea]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					},
				});
				
				$('nuevo').addEvent('click', function() {
					$('empleados').empty();
					
					$('cias').select();
				});
				
				$('imprimir').addEvent('click', Imprimir);
			}
			else {
				alert('No hay resultados');
				
				$('empleados').empty();
				
				$('cias').select();
			}
		}
	}).send();
}

var checkAll = function() {
	var checked = arguments[0].get('checked');
	
	$$('input[id=checkblock]').set('checked', checked);
	$$('input[id=id]').set('checked', checked);
}

var checkBlock = function() {
	var checked = arguments[0].get('checked'),
		cia = arguments[0].get('value');
	
	$$('input[id=id][cia=' + cia + ']').set('checked', checked);
}

var Imprimir = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('\xbfDesea imprimir las etiquetas de los empleados seleccionados?')) {
		alert('Coloque las etiquetas en la impresora');
		
		new Request({
			'url': 'ImpresionEtiquetasEmpleados.php',
			'data': 'accion=imprimir&etiqueta=' + $('etiqueta').get('value') + '&' + $('empleados').toQueryString(),
			'onRequest': function() {
				$('empleados').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('empleados'));
				
				new Element('span', {
					'text': ' Imprimiendo etiquetas...'
				}).inject($('empleados'));
			},
			'onSuccess': function() {
				alert('Se han mandado las etiquetas a la cola de impresi\xf3n, favor de revisar la impresora');
				
				$('empleados').empty();
			}
		}).send();
	}
}

