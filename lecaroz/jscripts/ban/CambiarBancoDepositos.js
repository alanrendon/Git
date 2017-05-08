// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('fecha_cap').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				this.blur();
				e.stop();
			}
		}
	});
	
	$('buscar').addEvent('click', Buscar);
	
	$('fecha_cap').focus();
});

var Buscar = function() {
	new Request({
		'url': 'CambiarBancoDepositos.php',
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
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('cancelar').addEvent('click', Cancelar);
				$('cambiar').addEvent('click', Cambiar);
			}
			else {
				alert('No hay resultados');
				$('result').set('html', '');
				$('fecha_cap').select();
			}
		}
	}).send();
}

var Cancelar = function() {
	$('result').set('html', '');
	$('fecha_cap').select();
}

var Cambiar = function() {
	if ($$('input[id^=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea cambiar todos los registros seleccionados?')) {
		new Request({
			'url': 'CambiarBancoDepositos.php',
			'data': 'accion=cambiar&' + $('result').toQueryString(),
			'onRequest': function() {
				$('result').set('html', '');
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('result'));
				
				new Element('span', {
					'text': ' Cambiando...'
				}).inject($('result'));
			},
			'onSuccess': function(result) {
				$('result').set('html', '');
				
				alert('Todos los registros seleccionados han sido cambiados de banco');
				
				$('fecha_cap').select();
			}
		}).send();
	}
}
