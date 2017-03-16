// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('anio').addEvents({
		'change': ConsultarPrecios,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
				this.select();
			}
		}
	});
	
	$('mes').addEvent('change', ConsultarPrecios).fireEvent('change');
});

var ConsultarPrecios = function() {
	if ($('anio').get('value').getNumericValue == 0) {
		$('result').empty();
	}
	else {
		new Request({
			'url': 'HuevoPrecios.php',
			'data': 'accion=consultar&anio=' + $('anio').get('value') + '&mes=' + $('mes').get('value'),
			'onRequest': function() {
				$('result').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('result'));
				
				new Element('span', {
					'text': ' Consultando precios...'
				}).inject($('result'));
			},
			'onSuccess': function(result) {
				if (result) {
					$('result').set('html', result);
					
					new FormValidator($('Precios'), {
						showErrors: true,
						selectOnFocus: true
					});
					
					new FormStyles($('Precios'));
					
					$$('tr[id=row]').addEvents({
						'mouseover': function() {
							this.addClass('highlight');
						},
						'mouseout': function() {
							this.removeClass('highlight');
						}
					});
					
					$$('input[id=dia]').each(function(dia) {
						$$('input[id=precio' + dia.get('value') + ']').each(function(precio, i) {
							precio.addEvents({
								'change': ActualizarVacios.pass([dia.get('value').getNumericValue(), i, precio]),
								'keydown': function(e) {
									if (e.key == 'enter') {
										if (i < $$('input[id=precio' + dia.get('value') + ']').length - 1) {
											$$('input[id=precio' + dia.get('value') + ']')[i + 1].select();
										}
										else {
											$('observaciones' + dia.get('value')).focus();
										}
									}
									else if (e.key == 'left' && i > 0) {
										$$('input[id=precio' + dia.get('value') + ']')[i - 1].select();
									}
									else if (e.key == 'right') {
										if (i < $$('input[id=precio' + dia.get('value') + ']').length - 1) {
											$$('input[id=precio' + dia.get('value') + ']')[i + 1].select();
										}
										else {
											$('observaciones' + dia.get('value')).focus();
										}
									}
									else if (e.key == 'up' && dia.get('value').getNumericValue() > 1) {
										$$('input[id=precio' + (dia.get('value').getNumericValue() - 1) + ']')[i].select();
									}
									else if (e.key == 'down' && dia.get('value').getNumericValue() + 1 < $$('input[id=dia]').length + 1) {
										$$('input[id=precio' + (dia.get('value').getNumericValue() + 1) + ']')[i].select();
									}
								}
							});
						});
						
						$('observaciones' + dia.get('value')).addEvents({
							'keydown': function(e) {
								if (e.key == 'enter') {
									if (dia.get('value').getNumericValue() + 1 < $$('input[id=dia]').length + 1) {
										$$('input[id=precio' + (dia.get('value').getNumericValue() + 1) + ']')[0].select();
									}
									else {
										$$('input[id^=precio]')[0].select();
									}
								}
								else if (e.key == 'left') {
									$$('input[id=precio' + dia.get('value') + ']')[$$('input[id=precio' + dia.get('value') + ']').length - 1].select();
								}
								else if (e.key == 'up' && dia.get('value').getNumericValue() > 1) {
									$('observaciones' + (dia.get('value').getNumericValue() - 1)).focus();
								}
								else if (e.key == 'down' && dia.get('value').getNumericValue() + 1 < $$('input[id=dia]').length + 1) {
									$('observaciones' + (dia.get('value').getNumericValue() + 1)).focus();
								}
							}
						});
					});
					
					$('actualizar').addEvent('click', ActualizarPrecios);
					
					$$('input[id^=precio]')[0].select();
				}
			}
		}).send();
	}
}

var ActualizarVacios = function() {
	var dia_tope = arguments[0],
		index = arguments[1],
		value = $$('input[id=precio1]')[index].get('value').getNumericValue();
	
	if (dia_tope > 1) {
		for (var dia = 2; dia < dia_tope; dia++) {
			if ($$('input[id=precio' + dia + ']')[index].get('value').getNumericValue() == 0) {
				$$('input[id=precio' + dia + ']')[index].set('value', value.numberFormat(2, '.', ','));
			}
			else {
				value = $$('input[id=precio' + dia + ']')[index].get('value').getNumericValue();
			}
		}
	}
}

var ActualizarPrecios = function() {
	if (confirm('¿Desea actualizar los precios de compra?')) {
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
		
		$('Precios').getElements('input').each(function(el) {
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
			'url': 'HuevoPrecios.php',
			'data': 'accion=actualizar&' + queryString.join('&'),
			'onRequest': function() {
				$('result').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('result'));
				
				new Element('span', {
					'text': ' Actualizando precios...'
				}).inject($('result'));
			},
			'onSuccess': function(result) {
				alert('Los precios para el mes seleccionado han sido actualizados con exito');
				
				$('mes').fireEvent('change');
			}
		}).send();
	}
}
