// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('fecha').addEvents({
		'change': Obtener,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('importe').focus();
			}
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
	
	$$('input[id=importe]').each(function(el, i) {
		el.addEvents({
			'focus': function() {
				$$('tr[id=row]')[i].addClass('highlight');
			},
			'blur': function() {
				$$('tr[id=row]')[i].removeClass('highlight');
			},
			'change': calcularTotal,
			'keydown': function(e) {
				if (e.key == 'enter') {
					if (i < $$('input[id=importe]').length - 1) {
						$$('input[id=importe]')[i + 1].select();
					}
					else {
						$('fecha').select();
					}
				}
			}
		});
	});
	
	$('registrar').addEvent('click', Registrar);
	
	$('fecha').select();
});

var Obtener = function() {
	if ($('fecha').get('value') != '') {
		new Request({
			'url': 'ZapateriasVentasDiarias.php',
			'data': 'accion=obtener&fecha=' + encodeURIComponent($('fecha').get('value')),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					data.each(function(el) {
						$$('input[id=importe][cia=' + el.num_cia + ']').set('value', el.importe != 0 ? el.importe.numberFormat(2, '.', ',') : '');
					});
				}
				else {
					$$('input[id=importe]').set('value', '');
				}
			}
		}).send();
	}
	else {
		$$('input[id=importe]').set('value', '');
	}
	
	calcularTotal.run();
}

var calcularTotal = function() {
	var total = 0;
	
	$$('input[id=importe]').each(function(el) {
		total += el.get('value').getNumericValue();
	});
	
	$('total').set('value', total.numberFormat(2, '.', ','));
}

var Registrar = function() {
	if (confirm('¿Son correctos los datos?')) {
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
			'url': 'ZapateriasVentasDiarias.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
			}
		}).send();
	}
}
