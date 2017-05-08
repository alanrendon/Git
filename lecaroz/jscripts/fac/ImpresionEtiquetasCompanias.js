// JavaScript Document// JavaScript Document

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
				
				$('copias').focus();
			}
		}
	});
	
	$('copias').addEvents({
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
		'url': 'ImpresionEtiquetasCompanias.php',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('companias').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('companias'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('companias'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('companias').empty().set('html', result);
				
				$('checkall').addEvent('change', checkAll.pass($('checkall')));
				
				$$('tr[class^=linea]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					},
				});
				
				$('nuevo').addEvent('click', function() {
					$('companias').empty();
					
					$('cias').select();
				});
				
				$('imprimir').addEvent('click', Imprimir);
			}
			else {
				alert('No hay resultados');
				
				$('companias').empty();
				
				$('cias').select();
			}
		}
	}).send();
}

var checkAll = function() {
	var checked = arguments[0].get('checked');
	
	$$('input[id=checkblock]').set('checked', checked);
	$$('input[id=num_cia]').set('checked', checked);
}

var Imprimir = function() {
	if ($$('input[id=num_cia]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('\xbfDesea imprimir las etiquetas de las compa\xf1\xedas seleccionadas?')) {
		alert('Coloque las etiquetas en la impresora');
		
		new Request({
			'url': 'ImpresionEtiquetasCompanias.php',
			'data': 'accion=imprimir&etiqueta=' + $('etiqueta').get('value') + '&campo=' + $$('[id=campo]:checked')[0].get('value') + '&copias=' + $('copias').get('value') + ($('intercalar').get('checked') ? '&intercalar=1' : '') + '&' + $('companias').toQueryString(),
			'onRequest': function() {
				$('companias').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('companias'));
				
				new Element('span', {
					'text': ' Imprimiendo etiquetas...'
				}).inject($('companias'));
			},
			'onSuccess': function() {
				alert('Se han mandado las etiquetas a la cola de impresi\xf3n, favor de revisar la impresora');
				
				$('companias').empty();
			}
		}).send();
	}
}

