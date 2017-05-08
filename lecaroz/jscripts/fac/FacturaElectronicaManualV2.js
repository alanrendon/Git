// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': cambiaCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'change': validarFecha,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cuenta_pago').focus();
			}
		}
	});
	
	$$('[id=tipo]').addEvent('change', validarFecha);
	
	$('cuenta_pago').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('nombre_cliente').focus();
			}
		}
	});
	
	$('nombre_cliente').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('rfc').focus();
			}
		}
	});
	
	$('rfc').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('calle').focus();
			}
		}
	});
	
	$('calle').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('no_exterior').focus();
			}
		}
	});
	
	$('no_exterior').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('no_interior').focus();
			}
		}
	});
	
	$('no_interior').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('colonia').focus();
			}
		}
	});
	
	$('colonia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('localidad').focus();
			}
		}
	});
	
	$('localidad').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('referencia').focus();
			}
		}
	});
	
	$('referencia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('municipio').focus();
			}
		}
	});
	
	$('municipio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('estado').focus();
			}
		}
	});
	
	$('estado').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('pais').focus();
			}
		}
	});
	
	$('pais').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('codigo_postal').focus();
			}
		}
	});
	
	$('codigo_postal').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('email_cliente').focus();
			}
		}
	});
	
	$('email_cliente').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('observaciones').focus();
			}
		}
	});
	
	$('observaciones').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' && e.control) {
				e.stop();
				
				$('descripcion').focus();
			}
		}
	});
	
	$('expand').addEvent('click', cambiarDescripcion);
	
	// $$('input[id=descripcion]').each(function(el, i) {
	// 	el.addEvent('keydown', function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();
				
	// 			$$('input[id=cantidad]')[i].select();
	// 		}
	// 	});
	// });
	
	// $$('input[id=cantidad]').each(function(el, i) {
	// 	el.addEvents({
	// 		'change': calcularImporte.pass(i),
	// 		'keydown': function(e) {
	// 			if (e.key == 'enter') {
	// 				e.stop();
					
	// 				$$('input[id=precio]')[i].select();
	// 			}
	// 		}
	// 	});
	// });
	
	// $$('input[id=precio]').each(function(el, i) {
	// 	el.addEvents({
	// 		'change': calcularImporte.pass(i),
	// 		'keydown': function(e) {
	// 			if (e.key == 'enter') {
	// 				e.stop();
					
	// 				$$('input[id=unidad]')[i].select();
	// 			}
	// 		}
	// 	});
	// });
	
	// $$('input[id=unidad]').each(function(el, i) {
	// 	el.addEvent('keydown', function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();
				
	// 			if (!$chk($$('[id=descripcion]')[i + 1])) {
	// 				newRow(i + 1);
	// 			}
				
	// 			$$('[id=descripcion]')[i + 1].select();
	// 		}
	// 	});
	// });

	expand = false;

	$('Conceptos').empty();

	newRow(0);
	
	$('aplicar_iva').addEvent('change', calcularTotal);
	
	$('registrar').addEvent('click', Registrar);
	
	$('num_cia').select();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaElectronicaManualV2.php',
			'data': 'accion=cambiaCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('nombre_cia').set('value', data.nombre);
					$('fecha').set('value', data.fecha);
					
//					if ($('num_cia').get('value').getNumericValue() == 900 || $('num_cia').get('value').getNumericValue() == 992 || ($('num_cia').get('value').getNumericValue() >= 600 && $('num_cia').get('value').getNumericValue() <= 699)) {
//						$('fecha').set('readonly', false);
//					}
//					else {
//						$('fecha').set('readonly', true);
//					}
				}
				else {
					alert('La compañía no esta en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
					
//					if ($('num_cia').get('value').getNumericValue() == 900 || $('num_cia').get('value').getNumericValue() == 992 || ($('num_cia').get('value').getNumericValue() >= 600 && $('num_cia').get('value').getNumericValue() <= 699)) {
//						$('fecha').set('readonly', false);
//					}
//					else {
//						$('fecha').set('readonly', true);
//					}
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia, #fecha').set('value', '');
//		$('fecha').set({
//			'readonly': true,
//			'value': ''
//		});
	}
}

var validarFecha = function() {
	if ($('fecha').get('value') != '' && $('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('fecha').set('value', '');
		
		$('num_cia').select();
	}
	else if ($('fecha').get('value') != '' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 2) {
		new Request({
			'url': 'FacturaElectronicaManualV2.php',
			'data': 'accion=validarFecha&num_cia=' + $('num_cia').get('value') + '&fecha=' + $('fecha').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result.getNumericValue() != 0) {
					switch (result.getNumericValue()) {
						case -1:
							alert('La fecha de emisión no puede ser anterior a el último día de efectivo');
						break;
						
						case 1:
							alert('La fecha de emisión no puede ser posterior al día de hoy');
						break;
					}
					
					$('fecha').set('value', $('fecha').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var cambiarDescripcion = function() {
	expand = !expand;
	
	if (expand) {
		$('expand').set('src', '/lecaroz/imagenes/minus16x16.png');
		
		$('expand_desc').set('html', 'Contraer descripciones');
		
		$$('[id=descripcion]').each(function(el, i) {
			expandirDescripcion.run(i);
		});
		
		$('tipo_reporte').set('value', 2);
	}
	else {
		$('expand').set('src', '/lecaroz/imagenes/plus16x16.png');
		
		$('expand_desc').set('html', 'Expandir descripciones');
		
		$$('[id=descripcion]').each(function(el, i) {
			contraerDescripcion.run(i);
		});
		
		$('tipo_reporte').set('value', 1);
	}
}

var expandirDescripcion = function() {
	var i = arguments[0],
		value = $$('[id=descripcion]')[i].get('value'),
		el = new Element('textarea', {
			'id': 'descripcion',
			'name': 'descripcion[]',
			'class': 'valid toText toUpper',
			'cols': 70,
			'rows': 5,
			'wrap': 'physical',
			'value': value
		}).addEvent('keydown', function(e) {
			if (e.key == 'enter' && e.control) {
				e.stop();
				
				$$('[id=cantidad]')[i].select();
			}
		}).replaces($$('[id=descripcion]')[i]);
	
	validator.addElementEvents(el);
	styles.addElementEvents(el);
}

var contraerDescripcion = function() {
	var i = arguments[0],
		value = $$('[id=descripcion]')[i].get('value'),
		el = new Element('input', {
			'id': 'descripcion',
			'name': 'descripcion[]',
			'type': 'text',
			'class': 'valid toText toUpper cleanText',
			'size': 30,
			'maxlength': 100,
			'value': value.clean()
		}).addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('[id=cantidad]')[i].select();
			}
		}).replaces($$('[id=descripcion]')[i]);
	
	validator.addElementEvents(el);
	styles.addElementEvents(el);
}

var calcularImporte = function() {
	var index = arguments[0],
		cantidad = $$('input[id=cantidad]')[index].get('value').getNumericValue(),
		precio = $$('input[id=precio]')[index].get('value').getNumericValue(),
		ieps = 0;
	
	importe = cantidad * precio;

	if ($$('input[id=aplicar_ieps]')[index].get('checked') && cantidad > 0 && precio > 0)
	{
		ieps = (importe - importe / 1.08).round(2);
	}

	$$('input[id=ieps]')[index].set('value', ieps);
	$$('input[id=importe]')[index].set('value', importe > 0 || cantidad > 0 ? (importe - ieps).numberFormat(2, '.', ',') : '');
	
	calcularTotal();
}

var calcularTotal = function() {
	var cantidad = 0,
		subtotal = 0,
		ieps = 0,
		iva = 0,
		total = 0;
	
	$$('input[id=importe]').each(function(el, i) {
		subtotal += el.get('value').getNumericValue();

		ieps += $$('input[id=ieps]')[i].get('value').getNumericValue();
		
		iva += $$('input[id=aplicar_iva]')[i].get('checked') ? (el.get('value').getNumericValue() * 0.16).round(2) : 0;
	});
	
	cantidad = $$('input[id=cantidad]').get('value').getNumericValue().sum();
	
	//iva = $('aplicar_iva').get('checked') ? (subtotal * 0.16).round(2) : 0;
	
	total = subtotal + ieps + iva;
	
	$('subtotal').set('value', subtotal > 0 || cantidad > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('total_ieps').set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 || cantidad > 0 ? total.numberFormat(2, '.', ',') : '');
}

var newRow = function(i) {
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	var td1 = new Element('td', {
		'align': 'center'
	});
	var td2 = new Element('td', {
		'align': 'center'
	});
	var td3 = new Element('td', {
		'align': 'center'
	});
	var td4 = new Element('td', {
		'align': 'center'
	});
	var td5 = new Element('td', {
		'align': 'center'
	});
	var td6 = new Element('td', {
		'align': 'center'
	});
	var td7 = new Element('td', {
		'align': 'center'
	});
	
	if ( ! expand) {
		var descripcion = new Element('input', {
			'id': 'descripcion',
			'name': 'descripcion[]',
			'type': 'text',
			'class': 'valid toText toUpper cleanText',
			'size': 30,
			'maxlength': 100
		}).addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=cantidad]')[i].select();
			}
		}).inject(td1);
	}
	else {
		var descripcion = new Element('textarea', {
			'id': 'descripcion',
			'name': 'descripcion[]',
			'class': 'valid toText toUpper',
			'cols': 70,
			'rows': 5,
			'wrap': 'physical'
		}).addEvent('keydown', function(e) {
			if (e.key == 'enter' && e.control) {
				e.stop();
				
				$$('input[id=cantidad]')[i].select();
			}
		}).inject(td1);
	}
	
	var cantidad = new Element('input', {
		'id': 'cantidad',
		'name': 'cantidad[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'size': 5,
		'precision': 2
	}).addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
		}
	}).inject(td2);
	
	var precio = new Element('input', {
		'id': 'precio',
		'name': 'precio[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'size': 8,
		'precision': 2
	}).addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=unidad]')[i].select();
			}
		}
	}).inject(td3);
	
	var unidad = new Element('input', {
		'id': 'unidad',
		'name': 'unidad[]',
		'type': 'text',
		'class': 'valid onlyText toUpper cleanText',
		'size': 10,
		'maxlength': 50
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			if (!$chk($$('[id=descripcion]')[i + 1])) {
				newRow(i + 1);
			}
			
			$$('[id=descripcion]')[i + 1].select();
		}
	}).inject(td4);

	var aplicar_ieps = new Element('input',
	{
		'id': 'aplicar_ieps',
		'name': 'aplicar_ieps[]',
		'type': 'checkbox',
		'value': i,
		'checked': false
	}).addEvent('change', calcularImporte.pass(i)).inject(td5);

	var ieps = new Element('input',
	{
		'id': 'ieps',
		'name': 'ieps[]',
		'type': 'hidden',
		'value': 0
	}).inject(td5);
	
	var aplicar_iva = new Element('input', {
		'id': 'aplicar_iva',
		'name': 'aplicar_iva[]',
		'type': 'checkbox',
		'value': i,
		'checked': $('num_cia').get('value').getNumericValue() <= 300 ? false : true
	}).addEvent('change', calcularTotal).inject(td6);
	
	var importe = new Element('input', {
		'id': 'importe',
		'name': 'importe[]',
		'type': 'text',
		'class': 'right',
		'size': 10,
		'readonly': true
	}).inject(td7);
	
	validator.addElementEvents(descripcion);
	validator.addElementEvents(cantidad);
	validator.addElementEvents(precio);
	validator.addElementEvents(unidad);
	
	styles.addElementEvents(descripcion);
	styles.addElementEvents(cantidad);
	styles.addElementEvents(precio);
	styles.addElementEvents(unidad);
	
	td1.inject(tr);
	td2.inject(tr);
	td3.inject(tr);
	td4.inject(tr);
	td5.inject(tr);
	td6.inject(tr);
	td7.inject(tr);
	
	tr.inject($('Conceptos'));
}

var Registrar = function() {
	if (!$chk($('num_cia').get('value').getNumericValue())) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de la factura');
		$('fecha').focus();
	}
	else if ($('nombre_cliente').get('value') == '') {
		alert('Debe especificar el nombre del cliente');
		$('nombre_cliente').focus();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el RFC del cliente');
		$('rfc').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 6) && $('calle').get('value') == '') {
		alert('Debe especificar la calle');
		$('calle').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 6) && $('colonia').get('value') == '') {
		alert('Debe especificar la colonia');
		$('colonia').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 6) && $('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio');
		$('municipio').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 6) && $('estado').get('value') == '') {
		alert('Debe especificar el estado');
		$('estado').focus();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el pais');
		$('pais').set('value', 'MEXICO').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000' && $$('[id=tipo]:checked')[0].get('value').getNumericValue() == 6) && $('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal');
		$('codigo_postal').focus();
	}
	else if ($('observaciones').get('value').length > 1000) {
		alert('El texto en observaciones no puede ser mayor a 1000 caracteres');
		$('observaciones').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, textarea, radio, select').each(function(el) {
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
			'url': 'FacturaElectronicaManualV2.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Generando CFD...', 'Facturas Electr&oacute;nicas', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				popup = new Popup(result, 'Facturas Electr&oacute;nicas', 500, 200, popupOpen, null);
			}
		}).send();
	}
}

var popupOpen = function() {
	$('cerrar').addEvent('click', function() {
		if (!$chk($('error'))) {
			expand = false;

			$('Datos').reset();
			
			$('Conceptos').empty();
			
			newRow(0);
		}
		
		popup.Close();
	});
}
