// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$$('input[id=importe]').each(function(el, i, els) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=porcentaje]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					e.stop();
					
					els[i - 1].select();
				}
				else if (e.key == 'down' && i < els.length - 1) {
					e.stop();
					
					els[i + 1].select();
				}
			}
		});
	});
	
	$$('input[id=porcentaje]').each(function(el, i, els) {
		el.addEvents({
			'change': function() {
				if (el.get('value').getNumericValue() > 100) {
					alert('Solo se permiten porcentajes entre 0 y 100');
					
					el.set('value', el.retrieve('tmp', '')).select();
				}
			},
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i < els.length - 1) {
						$$('input[id=importe]')[i + 1].select();
					}
					else {
						$$('input[id=importe]')[0].select();
					}
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=importe]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					e.stop();
					
					els[i - 1].select();
				}
				else if (e.key == 'down' && i < els.length - 1) {
					e.stop();
					
					els[i + 1].select();
				}
			}
		});
	});
	
	$('actualizar').addEvent('click', Actualizar);
	
	$('importe').select();
});

var Actualizar = function() {
	$$('input[id=num_cia]').every(function(cia, i) {
		if ($$('input[id=importe]')[i].get('value').getNumericValue() > 0 && $$('input[id=porcentaje]')[i].get('value').getNumericValue() > 0) {
			alert('Solo puede tener importe o porcentaje pero no ambos');
			
			$$('input[id=importe]')[i].select();
			
			return false;
		}
		else {
			return true;
		}
	});
	
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
			'url': 'ImportesSeparacion.php',
			'data': 'accion=actualizar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result),
					rows_affected = 0;
				
				$H(data).each(function(value, key) {
					rows_affected += value;
				});
				
				if (rows_affected > 0) {
					var msg = 'Resultado de los cambios:\n';
					
					$H(data).each(function(value, key) {
						msg += '\n' + key.capitalize() + ': ' + value;
					});
					
					alert(msg);
				}
				else {
					alert('No se realizo ningun cambio');
				}
			}
		}).send();
	}
}
