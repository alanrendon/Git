// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator(null, {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles(null);
	
	Inicio();
	
});

var Inicio = function() {
	new Request({
		'url': 'PrestamosOficinaPago.php',
		'data': 'accion=inicio',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			$('captura').set('html', result);
			
			validator.Form = $('Datos');
			validator.addEventsToElements();
			
			styles.Form = $('Datos');
			styles.addEventsToElements();
			
			$('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
					}
				}
			});
			
			$('buscar').addEvent('click', Buscar);
			
			$('cias').focus();
		}
	}).send();
}

var Buscar = function() {
	new Request({
		'url': 'PrestamosOficinaPago.php',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').set('html', result);
				
				validator.Form = $('Datos');
				validator.addEventsToElements();
				
				styles.Form = $('Datos');
				styles.addEventsToElements();
				
				$$('input[id=fecha]').each(function(el, i) {
					el.addEvents({
						'keydown': function(e) {
							if (e.key == 'enter') {
								e.stop();
								
								$$('input[id=importe]')[i].select();
							}
						}
					});
				});
				
				$$('input[id=importe]').each(function(el, i) {
					el.addEvents({
						'change': calcularSaldo.pass([$$('input[id=debe]')[i], el, $$('input[id=saldo]')[i]]),
						'keydown': function(e) {
							if (e.key == 'enter') {
								e.stop();
								
								if (!$chk($$('input[id=fecha]')[i + 1])) {
									$$('input[id=fecha]')[0].select();
								}
								else {
									$$('input[id=fecha]')[i + 1].select();
								}
							}
						}
					});
				});
				
				$('cancelar').addEvent('click', Cancelar);
				
				$('pagar').addEvent('click', Pagar);
				
				$('fecha').select();
			}
			else {
				Inicio();
				
				alert('No hay resultados');
				
				$('cias').select();
			}
		}
	}).send();
}

var calcularSaldo = function() {
	var debe = arguments[0],
		importe = arguments[1],
		saldo = arguments[2],
		total = 0;
	
	total = debe.get('value').getNumericValue() - importe.get('value').getNumericValue();
	
	if (total < 0) {
		alert('No puede pagar más de lo que debe');
		importe.set('value', importe.retrieve('tmp', '')).select();
	}
	else {
		saldo.set('value', total.numberFormat(2, '.', ','));
	}
}

var Cancelar = function() {
	Inicio();
}

var Pagar = function() {
	if (confirm('Son correctos los datos')) {
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
			'url': 'PrestamosOficinaPago.php',
			'data': 'accion=pagar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Inicio();
				
				alert(result + ' registros insertados');
				
				$('cias').select();
			}
		}).send();
	}
}
