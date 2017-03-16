// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('codmp').addEvents({
		'change': obtenerMP,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
				this.select();
			}
		}
	});
	
	$('admin').addEvent('change', consultar);
	
	$('codmp').select();
});

var obtenerMP = function() {
	if ($('codmp').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PorcentajesPedidosProveedores.php',
			'data': 'accion=obtenerMP&codmp=' + $('codmp').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_mp').set('value', result).select();
					
					consultar.run();
				}
				else {
					alert('El producto no existe en el catálogo');
					
					$('codmp').set('value', $('codmp').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('consulta').empty();
		
		$('nombre_mp').set('value', '');
		$('codmp').set('value', '').select();
	}
}

var consultar = function() {
	if ($('codmp').get('value').getNumericValue() == 0) {
		return false;
	}
	
	new Request({
		'url': 'PorcentajesPedidosProveedores.php',
		'data': 'accion=consultar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('consulta').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('consulta'));
			
			new Element('span', {
				'text': ' Buscando proveedores...'
			}).inject($('consulta'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('consulta').empty().set('html', result);
				
				new FormValidator($('Tabla'), {
					showErrors: true,
					selectOnFocus: true
				});
				
				new FormStyles($('Tabla'));
				
				$$('img[id=actualizar_porcentajes]').each(function(el) {
					var num_pro = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'click': imponerPorcentaje.pass(num_pro),
						'mouseover': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'default');
						}
					});
				});
				
				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('input[id=num_cia]').each(function(el1, i) {
					$$('input[id=porcentaje_' + i + ']').each(function(el2, j) {
						el2.addEvents({
							'change': calculaTotal.pass(i),
							'keydown': function(e) {
								if (e.key == 'enter') {
									if (j < $$('input[id=porcentaje_' + i + ']').length - 1) {
										$$('input[id=porcentaje_' + i + ']')[j + 1].select();
									}
									else if ($chk($$('input[id=porcentaje_' + (i + 1) + ']')[0])) {
										$$('input[id=porcentaje_' + (i + 1) + ']')[0].select();
									}
									else {
										$$('input[id=porcentaje_0]')[0].select();
									}
								}
								else if (e.key == 'left') {
									if (j - 1 >= 0) {
										$$('input[id=porcentaje_' + i + ']')[j - 1].select();
									}
								}
								else if (e.key == 'right') {
									if (j + 1 <= $$('input[id=porcentaje_' + i + ']').length - 1) {
										$$('input[id=porcentaje_' + i + ']')[j + 1].select();
									}
								}
								else if (e.key == 'up') {
									if (i - 1 >= 0) {
										$$('input[id=porcentaje_' + (i - 1) + ']')[j].select();
									}
								}
								else if (e.key == 'down') {
									if (i + 1 <= $$('input[id=num_cia]').length - 1) {
										$$('input[id=porcentaje_' + (i + 1) + ']')[j].select();
									}
								}
							}
						});
					});
				});
				
				$('cancelar').addEvent('click', cancelar);
				
				$('actualizar').addEvent('click', actualizar);
				
				$$('input[id=porcentaje_0]')[0].select();
			}
			else {
				alert('El producto no lo trabaja ningun proveedor');
				
				$('consulta').empty();
				
				$('codmp').select();
			}
		}
	}).send();
}

var imponerPorcentaje = function() {
	var num_pro = arguments[0],
		val,
		por;
	
	val = prompt('Nuevo porcentaje:', $$('input[id=porcentaje_0][num_pro=' + num_pro + ']')[0].get('value').getNumericValue());
	
	por = val.getNumericValue();
	
	$$('input[id^=porcentaje][num_pro=' + num_pro + ']').each(function(el, i) {
		el.set('value', por > 0 ? por.numberFormat(2, '.', ',') : '');
		
		calculaTotal.run(i);
	});
}

var calculaTotal = function() {
	var i = arguments[0],
		total = $$('[id=porcentaje_' + i + ']').get('value').getNumericValue().sum();
	
	$$('[id=total]')[i].set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
	
	if (total != 100) {
		$$('[id=total]')[i].removeClass('blue').addClass('red');
	}
	else {
		$$('[id=total]')[i].removeClass('red').addClass('blue');
	}
}

var actualizar = function() {
	if ($$('input[id=total]').some(function(el) { return el.get('value').getNumericValue() != 0 && el.get('value').getNumericValue() != 100; })) {
		alert('Una o más compañías no cumplen con el 100% de distribución de pedido');
	}
	else if (confirm('¿Desea actualizar los porcentajes de distribución de pedidos?')) {
		var queryString = [];
		
		$('Tabla').getElements('input, select').each(function(el) {
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
			'url': 'PorcentajesPedidosProveedores.php',
			'data': 'accion=actualizar&codmp=' + $('codmp').get('value') + '&' + queryString.join('&'),
			'onRequest': function() {
				$('consulta').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('consulta'));
				
				new Element('span', {
					'text': ' Actualizando porcentajes de distribucion de pedidos...'
				}).inject($('consulta'));
			},
			'onSuccess': function(result) {
				$('consulta').empty();
				
				$('nombre_mp').set('value', '');
				$('codmp').set('value', '').select();
			}
		}).send();
	}
}

var cancelar = function() {
	$('consulta').empty();
	
	$('nombre_mp').set('value', '');
	$('codmp').set('value', '').select();
}
