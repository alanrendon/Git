// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('anio').addEvents({
		'change': Obtener,
		'keydown': function(e) {
			if (e.key == 'enter') {
				this.blur();
			}
		}
	});
	
	$('mes').addEvents({
		'change': Obtener
	});
	
	Obtener.run();
	
	$('anio').select();
});

var Obtener = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		$('resultado').empty();
	}
	else {
		new Request({
			'url': 'ImportesSeparacionZapaterias.php',
			'data': 'accion=obtener&anio=' + $('anio').get('value') + '&mes=' + $('mes').get('value'),
			'onRequest': function() {
				$('resultado').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('resultado'));
				
				new Element('span', {
					'html': ' Cargando informaci&oacute;n...'
				}).inject($('resultado'));
			},
			'onSuccess': function(result) {
				$('resultado').empty().set('html', result);
				
				$$('input[id^=importe]').each(function(el, i, els) {
					validator.addElementEvents(el);
					styles.addElementEvents(el);
				});
				
				$$('input[id=num_cia]').each(function(cia, i, cias) {
					$$('input[id=importe' + i + ']').each(function(el, j, els) {
						el.addEvents({
							'keydown': function(e) {
								if (e.key == 'enter') {
									e.stop();
									
									if (j < els.length - 1) {
										els[j + 1].select();
									}
									else if (i < cias.length - 1) {
										$$('input[id=importe' + (i + 1) + ']')[0].select();
									}
									else {
										$$('input[id=importe0]')[0].select();
									}
								}
								else if (e.key == 'right') {
									e.stop();
									
									if (j < els.length - 1) {
										els[j + 1].select();
									}
								}
								else if (e.key == 'left') {
									e.stop();
									
									if (j > 0) {
										els[j - 1].select();
									}
								}
								else if (e.key == 'up') {
									e.stop();
									
									if (i > 0) {
										$$('input[id=importe' + (i - 1) + ']')[j].select();
									}
								}
								else if (e.key == 'down') {
									e.stop();
									
									if (i < cias.length - 1) {
										$$('input[id=importe' + (i + 1) + ']')[j].select();
									}
								}
							}
						});
					});
				});
				
				$('actualizar').addEvent('click', Actualizar);
				
				$('importe0').select();
			}
		}).send();
	}
}

var Actualizar = function() {
	if (confirm('¿Desea actualizar los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, select').each(function(el) {
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
			'url': 'ImportesSeparacionZapaterias.php',
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
