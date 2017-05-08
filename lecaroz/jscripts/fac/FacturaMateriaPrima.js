// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': validarCia,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_pro').select();
			}
		}
	});
	
	$('num_pro').addEvents({
		'change': validarPro,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_fact').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('num_fact').addEvents({
		'change': validarFac,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_pro').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'change': validarFecha,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				if (!$chk($('cantidad'))) {
					alert('No ha seleccionado un proveedor');
					
					$('num_pro').focus();
				}
				else {
					$('cantidad').focus();
				}
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_fact').select();
			}
		}
	});
	
	$('aclaracion').addEvent('change', function() {
		if (this.get('checked')) {
			$('observaciones').set('disabled', false).focus();
		}
		else {
			$('observaciones').set('disabled', true);
		}
	});
	
	$('observaciones').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if ($chk($('cantidad'))) {
					$('cantidad').focus();
				}
			}
		}
	});
	
	$('ingresar').addEvents({
		'click': Ingresar,
		'dblclick': function() {
			alert('Al hacer doble-click sobre este boton corre el riesgo de duplicar la entrada');
		}
	});
	
	$('num_cia').select();
	
	new Request({
		'url': 'FacturaMateriaPrima.php',
		'data': 'accion=obtenerUnidades',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			unidades = result;
		}
	}).send();
});

var validarCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=validarCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia').set('value', '');
	}
}

var validarPro = function() {
	if ($('num_pro').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=validarPro&num_pro=' + $('num_pro').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('nombre_pro').set('value', data.nombre_pro);
					
					$$('#num_fact, #fecha, #observaciones').set('value', '');
					
					$$('#subtotal, #iva_total, #total').set('value', '0.00');
					
					$('aclaracion').set('checked', false);
					$('observaciones').set('disabled', true);
					
					$('productos').empty();
					
					data.productos.each(function(el, i) {
						var tr = new Element('tr', {
								'id': 'row' + i,
								'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
							}).addEvents({
								'mouseover': function() {
									this.addClass('highlight');
								},
								'mouseout': function() {
									this.removeClass('highlight');
								}
							}),
							td1 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td2 = new Element('td', {
								'html': el.codmp + ' ' + el.producto
							}).inject(tr),
							td3 = new Element('td', {
								'align': 'right',
								'html': el.contenido.numberFormat(2, '.', ',')
							}).inject(tr),
							td4 = new Element('td', {
								'html': el.unidad
							}).inject(tr),
							td5 = new Element('td', {
								'align': 'right',
								'html': el.precio.numberFormat(2, '.', ',')
							}).inject(tr),
							td6 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td7 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td8 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td9 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td10 = new Element('td', {
								'align': 'right'
							}).inject(tr),
							td11 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							td12 = new Element('td', {
								'align': 'center'
							}).inject(tr),
							json = JSON.encode({
								'id': el.id,
								'codmp': el.codmp,
								'producto': el.producto,
								'contenido': el.contenido,
								'idunidad': el.idunidad,
								'unidad': el.unidad,
								'precio': el.precio,
								'desc1': el.desc1,
								'desc2': el.desc2,
								'desc3': el.desc3,
								'iva': el.iva,
								'pieps': el.pieps
							}),
							producto = new Element('input', {
								'id': 'producto',
								'name': 'producto[]',
								'type': 'hidden',
								'value': json
							}).inject(td1),
							cantidad = new Element('input', {
								'id': 'cantidad',
								'name': 'cantidad[]',
								'type': 'text',
								'size': 5,
								'class': 'valid Focus numberPosFormat right',
								'precision': 2
							}).inject(td1),
							desc1 = new Element('input', {
								'id': 'desc1',
								'name': 'desc1[]',
								'type': 'text',
								'size': 6,
								'class': 'right' + (el.desc1 > 0 ? ' gray' : ''),
								'readonly': true,
								'styles': {
									'border-color': el.desc1 > 0 ? '#00C' : null
								},
								'value': el.desc1 > 0 ? el.desc1.numberFormat(2, '.', ',') + '%' : ''
							}).inject(td6),
							desc2 = new Element('input', {
								'id': 'desc2',
								'name': 'desc2[]',
								'type': 'text',
								'size': 6,
								'class': 'right' + (el.desc1 > 0 ? ' gray' : ''),
								'readonly': true,
								'styles': {
									'border-color': el.desc2 > 0 ? '#00C' : null
								},
								'value': el.desc2 > 0 ? el.desc2.numberFormat(2, '.', ',') + '%' : ''
							}).inject(td7),
							desc3 = new Element('input', {
								'id': 'desc3',
								'name': 'desc3[]',
								'type': 'text',
								'size': 6,
								'class': 'right' + (el.desc3 > 0 ? ' gray' : ''),
								'readonly': true,
								'styles': {
									'border-color': el.desc3 > 0 ? '#00C' : null
								},
								'value': el.desc3 > 0 ? el.desc3.numberFormat(2, '.', ',') + '%' : ''
							}).inject(td8),
							ieps = new Element('input', {
								'id': 'ieps',
								'name': 'ieps[]',
								'type': 'text',
								'size': 6,
								'class': 'valid Focus numberPosFormat right red',
								'precision': 2,
								'value': el.ieps > 0 ? el.ieps.numberFormat(2, '.', ',') : '',
								'placeholder': el.pieps > 0 ? el.pieps + '%' : ''
							}).inject(td9),
							iva = new Element('input', {
								'id': 'iva',
								'name': 'iva[]',
								'type': 'text',
								'size': 6,
								'class': 'right' + (el.iva > 0 ? ' gray' : ''),
								'readonly': true,
								'styles': {
									'border-color': el.iva > 0 ? '#C00' : null
								},
								'value': el.iva > 0 ? el.iva.numberFormat(2, '.', ',') + '%' : ''
							}).inject(td10),
							importe = new Element('input', {
								'id': 'importe',
								'name': 'importe[]',
								'type': 'text',
								'size': 10,
								'class': 'right bold',
								'styles': {
									'width': '98%'
								},
								'readonly': true
							}).inject(td11),
							regalado = new Element('input', {
								'id': 'regalado',
								'name': 'regalado[]',
								'type': 'checkbox',
								'value': i
							}).inject(td12);
						
						validator.addElementEvents(cantidad);
						validator.addElementEvents(ieps);
						
						styles.addElementEvents(cantidad);
						styles.addElementEvents(ieps);
						
						td3.addEvents({
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							},
							'click': popupModificarProducto.pass(i)
						});
						
						td4.addEvents({
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							},
							'click': popupModificarProducto.pass(i)
						});
						
						td5.addEvents({
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							},
							'click': popupModificarProducto.pass(i)
						});
						
						cantidad.addEvents({
							'change': calcularImporte.pass(i),
							'keydown': function(e) {
								if (e.key == 'enter' || e.key == 'down') {
									e.stop();
									
									if ($chk($$('input[id=cantidad]')[i + 1])) {
										$$('input[id=cantidad]')[i + 1].select();
									}
									else if ($$('input[id=cantidad]').length > 1) {
										$('num_cia').select();
									}
									else {
										$$('input[id=ieps]')[0].select();
									}
								}
								else if (e.key == 'right') {
									e.stop();
									
									$$('input[id=ieps]')[i].select();
								}
								else if (e.key == 'up') {
									e.stop();
									
									if ((i - 1) >= 0) {
										$$('input[id=cantidad]')[i - 1].select();
									}
									else {
										$('fecha').select();
									}
								}
							}
						});
						
						ieps.addEvents({
							'change': calcularImporte.pass(i),
							'keydown': function(e) {
								if (e.key == 'enter') {
									e.stop();
									
									if ($chk($$('input[id=cantidad]')[i + 1])) {
										$$('input[id=cantidad]')[i + 1].select();
									}
									else {
										$('num_cia').select();
									}
								}
								else if (e.key == 'left') {
									e.stop();
									
									$$('input[id=cantidad]')[i].select();
								}
								else if (e.key == 'up') {
									e.stop();
									
									if ((i - 1) >= 0) {
										$$('input[id=ieps]')[i - 1].select();
									}
									else {
										$('fecha').select();
									}
								}
								else if (e.key == 'down') {
									e.stop();
									
									if ($chk($$('input[id=ieps]')[i + 1])) {
										$$('input[id=ieps]')[i + 1].select();
									}
									else {
										$('num_cia').select();
									}
								}
							}
						});
						
						regalado.addEvent('change', calcularImporte.pass(i));
						
						tr.inject($('productos'));
					});
				}
			}
		}).send();
	}
	else {
		$('productos').empty();
		
		$$('#num_pro, #nombre_pro, #num_fact, #fecha, #observaciones').set('value', '');
		
		$('aclaracion').set('checked', false);
		$('observaciones').set('disabled', true);
		
		$$('#subtotal, #iva_total, #total').set('value', '0.00');
	}
}

var validarFac = function() {
	if ($('num_pro').get('value').getNumericValue() == 0) {
		alert('Debe especificar el proveedor');
		
		$('num_pro').focus();
	}
	else if ($('num_fact').get('value').clean() != '') {
		new Request({
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=validarFac&num_pro=' + $('num_pro').get('value') + '&num_fact=' + $('num_fact').get('value').toUpperCase(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					alert('La factura ' + data.num_fact + ' ya esta registrada en la compañía ' + data.num_cia + ' ' + data.nombre_cia + ' con fecha ' + data.fecha);
					
					$('num_fact').set('value', $('num_fact').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var validarFecha = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('fecha').get('value') != '') {
		new Request({
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=validarFecha&num_cia=' + $('num_cia').get('value') + '&fecha=' + $('fecha').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result.getNumericValue() == -1) {
					alert('No puede capturar facturas del mes dado');
					
					$('fecha').set('value', $('fecha').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var calcularImporte = function() {
	var i = arguments[0],
		data = JSON.decode($$('input[id=producto]')[i].get('value'))
		cantidad = $$('input[id=cantidad]')[i].get('value').getNumericValue(),
		precio = data.precio,
		pdesc1 = data.desc1,
		pdesc2 = data.desc2,
		pdesc3 = data.desc3,
		ieps = 0,
		piva = data.iva,
		subimporte = 0,
		desc1 = 0,
		desc2 = 0,
		desc3 = 0,
		iva = 0,
		importe = 0;
	
	if ($$('input[id=cantidad]')[i].get('value').getNumericValue() > 0) {
		if (!$$('input[id=regalado]')[i].get('checked')) {
			importe = (cantidad * precio).round(2);
			
			desc1 = (importe * (pdesc1 / 100)).round(2);
			importe = importe - desc1;
			
			desc2 = (importe * (pdesc2 / 100)).round(2);
			importe = importe - desc2;
			
			desc3 = (importe * (pdesc3 / 100)).round(2);
			importe = importe - desc3;

			ieps = data.pieps > 0 ? (importe * data.pieps).round(2) / 100 : $$('input[id=ieps]')[i].get('value').getNumericValue();
			
			importe = importe + ieps;
			
			iva = (importe * (piva / 100)).round(2);
			
			$$('input[id=desc1]')[i].set('value', desc1 > 0 ? desc1.numberFormat(2, '.', ',') : '').removeClass('gray').addClass('blue');
			$$('input[id=desc2]')[i].set('value', desc2 > 0 ? desc2.numberFormat(2, '.', ',') : '').removeClass('gray').addClass('blue');
			$$('input[id=desc3]')[i].set('value', desc3 > 0 ? desc3.numberFormat(2, '.', ',') : '').removeClass('gray').addClass('blue');
			$$('input[id=ieps]')[i].set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
			$$('input[id=iva]')[i].set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '').removeClass('gray').addClass('red');
			$$('input[id=importe]')[i].set('value', importe > 0 ? importe.numberFormat(2, '.', ',') : '');
		}
		else {
			$$('input[id=desc1]')[i].set('value', '').removeClass('gray').addClass('blue');
			$$('input[id=desc2]')[i].set('value', '').removeClass('gray').addClass('blue');
			$$('input[id=desc3]')[i].set('value', '').removeClass('gray').addClass('blue');
			$$('input[id=ieps]')[i].set('value', '');
			$$('input[id=iva]')[i].set('value', '').removeClass('gray').addClass('red');
			$$('input[id=importe]')[i].set('value', '');
		}
	}
	else {
		$$('input[id=cantidad]')[i].set('value', '');
		$$('input[id=desc1]')[i].set('value', '').removeClass('blue').addClass('gray').set('value', pdesc1 > 0 ? pdesc1.numberFormat(2, '.', ',') + '%' : '');
		$$('input[id=desc2]')[i].set('value', '').removeClass('blue').addClass('gray').set('value', pdesc2 > 0 ? pdesc2.numberFormat(2, '.', ',') + '%' : '');
		$$('input[id=desc3]')[i].set('value', '').removeClass('blue').addClass('gray').set('value', pdesc3 > 0 ? pdesc3.numberFormat(2, '.', ',') + '%' : '');
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', '').removeClass('red').addClass('gray').set('value', piva > 0 ? piva.numberFormat(2, '.', ',') + '%' : '');
		$$('input[id=importe]')[i].set('value', '');
	}
	
	calcularTotal.run();
}

var calcularTotal = function() {
	$('subtotal').set('value', $$('input[id=importe]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
	$('iva_total').set('value', $$('input[id=iva]').get('value').filter(function(el) { return el.indexOf('%', 0) < 0; }).getNumericValue().sum().numberFormat(2, '.', ','));
	$('total').set('value', ($('subtotal').get('value').getNumericValue() + $('iva_total').get('value').getNumericValue()).numberFormat(2, '.', ','));
}

var Ingresar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').focus();
	}
	else if ($('num_pro').get('value').getNumericValue() == 0) {
		alert('Debe especificar el proveedor');
		
		$('num_pro').focus();
	}
	else if ($('num_fact').get('value').clean() == '') {
		alert('Debe especificar el número de factura');
		
		$('num_fact').focus();
	}
	else if ($('fecha').get('value').length < 10) {
		alert('Debe especificar la fecha de la factura');
		
		$('fecha').focus();
	}
//	else if ($('total').get('value').getNumericValue() == 0) {
//		alert('El importe de la factura debe ser mayor a 0');
//		
//		$('cantidad').select();
//	}
	else if ($('aclaracion').get('checked') && $('observaciones').get('value').length == 0) {
		alert('Debe especificar el porque se debe aclarar la factura');
		
		$('observaciones').focus();
	}
	else if ($('aclaracion').get('checked') && $('observaciones').get('value').length > 1000) {
		alert('Las observaciones para la aclaración son demasiado largas');
		
		$('observaciones').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, textarea').each(function(el) {
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
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=ingresar&' + queryString.join('&'),
			'onRequest': function() {
				$('ingresar').set('disabled', true);
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert('Error al ingresar la factura, avisar al administrador');
				}
				else {
					$$('#num_cia, #nombre_cia, #num_fact, #fecha, #observaciones').set('value', '');
					
					$('aclaracion').set('checked', false);
					$('observaciones').set('disabled', true);
					
					validarPro.run();
					
					$('ingresar').set('disabled', false);
					
					$('num_cia').focus();
				}
			}
		}).send();
	}
}

var popupModificarProducto = function() {
	var i = arguments[0],
		data = JSON.decode($$('input[id=producto]')[i].get('value')),
		html = '<form action="" method="get" name="Modificar" class="FormValidator FormStyles" id="Modificar"><table class="tabla_captura"><tr><th colspan="7" align="left" scope="col"><input name="id" type="hidden" id="id" value="' + data.id + '" />' + data.codmp + ' ' + data.producto + '</th></tr><tr><th>Precio</th><th>Contenido</th><th>Unidad</th><th>% Desc. 1</th><th>% Desc. 2</th><th>% Desc. 3</th><th>% I.V.A.</th></tr><tr><td align="center"><input name="precio" type="text" class="valid Focus numberPosFormat right" id="precio" value="' + data.precio.numberFormat(4, '.', ',') + '" size="5" precision="4" /></td><td align="center"><input name="contenido" type="text" class="valid Focus numberPosFormat right" id="contenido" value="' + data.contenido.numberFormat(2, '.', ',') + '" size="5" precision="2" /></td><td align="center"><select name="unidad" id="unidad">' + unidades + '</select></td><td align="center"><input name="pdesc1" type="text" class="valid Focus numberPosFormat right blue" id="pdesc1" style="border-color:#00C;" value="' + (data.desc1 > 0 ? data.desc1.numberFormat(2, '.', ',') : '') + '" size="5" precision="2" /></td><td align="center"><input name="pdesc2" type="text" class="valid Focus numberPosFormat right blue" id="pdesc2" style="border-color:#00C;" value="' + (data.desc2 > 0 ? data.desc2.numberFormat(2, '.', ',') : '') + '" size="5" precision="2" /></td><td align="center"><input name="pdesc3" type="text" class="valid Focus numberPosFormat right blue" id="pdesc3" style="border-color:#00C;" value="' + (data.desc3 > 0 ? data.desc3.numberFormat(2, '.', ',') : '') + '" size="5" precision="2" /></td><td align="center"><input name="piva" type="text" class="valid Focus numberPosFormat right red" id="piva" style="border-color:#C00;" value="' + (data.iva > 0 ? data.iva.numberFormat(2, '.', ',') : '') + '" size="5" precision="2" /></td></tr></table><p><input type="button" name="cancelar" id="cancelar" value="Cancelar" />&nbsp;&nbsp;<input type="button" name="modificar" id="modificar" value="Modificar" /></p></form>';
	
	popup = new Popup(html, '<img src="/lecaroz/imagenes/pencil16x16.png" width="16" height="16" /> Modificar informaci&oacute;n del producto', 800, 150, popupOpen.pass(i), null);
}

var popupOpen = function() {
	var i = arguments[0],
	data = JSON.decode($$('input[id=producto]')[i].get('value'));
	
	new FormValidator($('Modificar'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Modificar'));
	
	$each($('unidad').options, function(el, i) {
		if (el.get('value') == data.idunidad) {
			$('unidad').selectedIndex = i;
		}
	});
	
	$('precio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$('contenido').select();
			}
		}
	});
	
	$('contenido').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$('pdesc1').select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$('precio').select();
			}
		}
	});
	
	$('pdesc1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$('pdesc2').select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$('contenido').select();
			}
		}
	});
	
	$('pdesc2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$('pdesc3').select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$('pdesc1').select();
			}
		}
	});
	
	$('pdesc3').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$('piva').select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$('pdesc2').select();
			}
		}
	});
	
	$('piva').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('precio').select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$('pdesc3').select();
			}
		}
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('modificar').addEvent('click', modificarProducto.pass(i));
	
	$('precio').select();
}

var modificarProducto = function() {
	var i = arguments[0];
	
	if ($('precio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el precio del producto');
		
		$('precio').select();
	}
	else if ($('contenido').get('value').getNumericValue() == 0) {
		alert('Debe especificar el contenido por unidad');
		
		$('contenido').select();
	}
	else {
		new Request({
			'url': 'FacturaMateriaPrima.php',
			'data': 'accion=modificar&' + $('Modificar').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == '') {
					var data = JSON.decode($$('input[id=producto]')[i].get('value'));
					
					data.precio = $('precio').get('value').getNumericValue();
					data.contenido = $('contenido').get('value').getNumericValue();
					data.idunidad = $('unidad').getSelected()[0].get('value').getNumericValue();
					data.unidad = $('unidad').getSelected()[0].get('text');
					data.desc1 = $('pdesc1').get('value').getNumericValue();
					data.desc2 = $('pdesc2').get('value').getNumericValue();
					data.desc3 = $('pdesc3').get('value').getNumericValue();
					data.iva = $('piva').get('value').getNumericValue();
					
					$$('input[id=producto]')[i].set('value', JSON.encode(data));
					
					$('row' + i).getElements('td')[2].set('html', $('contenido').get('value'));
					$('row' + i).getElements('td')[3].set('html', $('unidad').getSelected()[0].get('text'));
					$('row' + i).getElements('td')[4].set('html', $('precio').get('value'));
					
					calcularImporte.run(i);
					
					popup.Close();
				}
				else {
					alert('Ocurrio un error al tratar de actualizar la información del producto, avisar al administrador del sistema');
				}
			}
		}).send();
	}
}
