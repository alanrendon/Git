// JavaScript Document

window.addEvent('domready', function() {
	new Formulario('Datos');
	
	$$('input[id=prima]').each(function(el, i, array) {
		if (i < array.length - 1) {
			el.addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					array[i + 1].select();
				}
			});
		}
		else {
			el.addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					array[0].select();
				}
			});
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
	
	$('actualizar').addEvent('click', Actualizar);
	
	$('prima').select();
});

var Actualizar = function() {
	if (confirm('¿Desea actualizar los datos?')) {
		$('Datos').set({
			'action': 'PrimaRiesgoTrabajo.php',
			'method': 'post'
		});
		
		$('Datos').submit();
	}
}
