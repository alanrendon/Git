// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha1').select();
				e.stop();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha2').select();
				e.stop();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('cias').select();
				e.stop();
			}
		}
	});
	
	$('buscar').addEvent('click', Buscar);
	
	$('cias').focus();
});

var Buscar = function() {
	new Request({
		'url': 'ErroresClientes.php',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('result').set('html', '');
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('result'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('result'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('result').set('html', result);
				
				new Formulario('Result');
				
				$('checkall').addEvents({
					'click': function() {
						$$('input[id=id]').set('checked', this.get('checked'));
					}
				});
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('cancelar').addEvent('click', Cancelar);
				$('autorizar').addEvent('click', Autorizar);
			}
			else {
				alert('No hay resultados');
				$('result').set('html', '');
				$('cias').select();
			}
		}
	}).send();
}

var Cancelar = function() {
	$('result').set('html', '');
	$('cias').select();
}

var Autorizar = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea autorizar todos los registros seleccionados?')) {
		new Request({
			'url': 'ErroresClientes.php',
			'data': 'accion=autorizar&' + $('result').toQueryString(),
			'onRequest': function() {
				$('result').set('html', '');
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('result'));
				
				new Element('span', {
					'text': ' Autorizando...'
				}).inject($('result'));
			},
			'onSuccess': function() {
				$('result').set('html', '');
				
				alert('Todos los registros seleccionados han sido autorizados');
				
				$('cias').select();
			}
		}).send();
	}
}
