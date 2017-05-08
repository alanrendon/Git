// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$$('tr[id=row]').addEvents({
		'mouseover': function() {
			this.addClass('highlight');
		},
		'mouseout': function() {
			this.removeClass('highlight');
		}
	});
	
	$$('input[id=pesada_1]').each(function(el, i) {
		el.addEvents({
			'keyup': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=pesada_2]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=pesada_2]').each(function(el, i) {
		el.addEvents({
			'keyup': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=pesada_3]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=pesada_3]').each(function(el, i) {
		el.addEvents({
			'keyup': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=pesada_4]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=pesada_4]').each(function(el, i) {
		el.addEvents({
			'keyup': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if ($chk($$('input[id=pesada_1]')[i + 1])) {
						$$('input[id=pesada_1]')[i + 1].select();
					}
					else {
						$$('input[id=pesada_1]')[0].select();
					}
				}
			}
		});
	});
	
	$('actualizar').addEvent('click', Actualizar);
	
	$('pesada_1').select();
});

var Actualizar = function() {
	if (confirm('¿Desea actualizar los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input').each(function(el) {
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') {
				return;
			}
			
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt) {
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			
			$splat(value).each(function(val) {
				if (typeof val != 'undefined') {
					queryString.push(el.name + '=' + encodeURIComponent(val));
				}
			});
		});
		
		new Request({
			'url': 'Pesadas.php',
			'data': 'accion=actualizar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == '') {
					alert('Datos actualizados correctamente');
				}
				else {
					alert('Ha ocurrido un error al actualizar los datos, avisar al administrador');
				}
			}
		}).send();
	}
}
