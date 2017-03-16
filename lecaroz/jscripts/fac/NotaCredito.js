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
		'change': $empty,
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
			if (e.key == 'enter') {
				e.stop();

				$('num_cia_factura').focus();
			}
		}
	});

	$('num_cia_factura').addEvents({
		'change': cambiaCiaFactura,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('num_factura').select();
			}
		}
	}).addEvent('change', obtenerFactura).addEvent('change', aplicarDescuentoFactura);

	$('num_factura').addEvents({
		'change': obtenerFactura,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('porcentaje_descuento_factura').select();
			}
		}
	}).addEvent('change', aplicarDescuentoFactura);

	$('porcentaje_descuento_factura').addEvents({
		'change': aplicarDescuentoFactura,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('descripcion').select();
			}
		}
	});

	$$('input[id=descripcion]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();

				$$('input[id=cantidad]')[i].select();
			}
		});
	});

	$$('input[id=cantidad]').each(function(el, i) {
		el.addEvents({
			'change': calcularImporte.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();

					$$('input[id=precio]')[i].select();
				}
			}
		});
	});

	$$('input[id=precio]').each(function(el, i) {
		el.addEvents({
			'change': calcularImporte.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();

					$$('input[id=unidad]')[i].select();
				}
			}
		});
	});

	$$('input[id=unidad]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();

				if (!$chk($$('input[id=descripcion]')[i + 1])) {
					newRow(i + 1);
				}

				$$('input[id=descripcion]')[i + 1].select();
			}
		});
	});

	$('aplicar_iva').addEvent('change', calcularTotal);

	$('registrar').addEvent('click', Registrar);

	$('num_cia').select();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'NotaCredito.php',
			'data': 'accion=cambiaCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					$('nombre_cia').set('value', data.nombre);
					$('fecha').set('value', data.fecha);

					if ($('num_cia').get('value').getNumericValue() == 900 || $('num_cia').get('value').getNumericValue() == 992 || $('num_cia').get('value').getNumericValue() == 925) {
						$('fecha').set('readonly', false);
					}
					else {
						$('fecha').set('readonly', true);
					}
				}
				else {
					alert('La compañía no esta en el catálogo');

					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();

					if ($('num_cia').get('value').getNumericValue() == 900 || $('num_cia').get('value').getNumericValue() == 992 || $('num_cia').get('value').getNumericValue() == 925) {
						$('fecha').set('readonly', false);
					}
					else {
						$('fecha').set('readonly', true);
					}
				}
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		$('fecha').set({
			'readonly': true,
			'value': ''
		});
	}
}

var cambiaCiaFactura = function() {
	if ($('num_cia_factura').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'NotaCredito.php',
			'data': 'accion=cambiaCiaFactura&num_cia=' + $('num_cia_factura').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					$('nombre_cia_factura').set('value', data.nombre);
				}
				else {
					alert('La compañía no esta en el catálogo');

					$('num_cia_factura').set('value', $('num_cia_factura').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_cia_factura').set('value', '');
		$('nombre_cia_factura').set('value', '');
	}
}

var obtenerFactura = function() {
	if ($('num_cia_factura').get('value').getNumericValue() > 0 && $('num_factura').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'NotaCredito.php',
			'data': {
				'accion': 'obtenerFactura',
				'num_cia': $('num_cia_factura').get('value'),
				'consecutivo': $('num_factura').get('value')
			},
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);

				if (data.status >= 0) {
					if (data.status == 1 || confirm('La factura con fecha "' + data.fecha + '" por un total de "' + data.total.numberFormat(2, '.', ',') + '" esta cancelada, \xbfDesea acreditar la nota de cr\xe9dito a esta factura?')) {
						$('fecha_factura').set('value', data.fecha);
						$('total_factura').set('value', data.total.numberFormat(2, '.', ','));
					}
					else {
						$('num_factura').set('value', $('num_factura').retrieve('tmp', '')).focus();
					}
				}
				else {
					alert('La factura solicitada no existe');
				}
			}
		}).send();
	}
	else {
		$('fecha_factura').set('value', '');
		$('total_factura').set('value', '');
		$('porcentaje_descuento_factura').set('value', '');
	}
}

var aplicarDescuentoFactura = function() {
	if ($('total_factura').get('value').getNumericValue() > 0 && $('porcentaje_descuento_factura').get('value').getNumericValue() > 0) {
		var total = $('total_factura').get('value').getNumericValue(),
			porcentaje_descuento = $('porcentaje_descuento_factura').get('value').getNumericValue(),
			descuento = 0;

		descuento = (total * porcentaje_descuento / 100).round(2);

		$('observaciones').set('value', 'ESTA NOTA DE CREDITO APLICA A LA FACTURA ' + $('num_factura').get('value') + ' CON FECHA ' + $('fecha_factura').get('value') + ' Y UN MONTO DE $' + $('total_factura').get('value'));

		$('descripcion').set('value', 'DESCUENTO');
		$('cantidad').set('value', 1);
		$('precio').set('value', descuento.numberFormat(2, '.', ','));
		$('unidad').set('value', 'SIN UNIDAD');
		$('importe').set('value', descuento.numberFormat(2, '.', ','));

		calcularTotal();
	}
}

var calcularImporte = function() {
	var index = arguments[0],
		cantidad = $$('input[id=cantidad]')[index].get('value').getNumericValue(),
		precio = $$('input[id=precio]')[index].get('value').getNumericValue();

	importe = cantidad * precio;

	$$('input[id=importe]')[index].set('value', importe > 0 ? importe.numberFormat(2, '.', ',') : '');

	calcularTotal();
}

var calcularTotal = function() {
	var subtotal = 0,
		iva = 0,
		total = 0;

	$$('input[id=importe]').each(function(el) {
		subtotal += el.get('value').getNumericValue();
	});

	iva = $('aplicar_iva').get('checked') ? (subtotal * 0.16).round(2) : 0;

	total = subtotal + iva;

	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
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

			if (!$chk($$('input[id=descripcion]')[i + 1])) {
				newRow(i + 1);
			}

			$$('input[id=descripcion]')[i + 1].select();
		}
	}).inject(td4);

	var importe = new Element('input', {
		'id': 'importe',
		'name': 'importe[]',
		'type': 'text',
		'class': 'right',
		'size': 10,
		'readonly': true
	}).inject(td5);

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
	else if ($('calle').get('value') == '') {
		alert('Debe especificar la calle');
		$('calle').focus();
	}
	else if ($('colonia').get('value') == '') {
		alert('Debe especificar la colonia');
		$('colonia').focus();
	}
	else if ($('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio');
		$('municipio').focus();
	}
	else if ($('estado').get('value') == '') {
		alert('Debe especificar el estado');
		$('estado').focus();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el pais');
		$('pais').focus();
	}
	else if ($('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal');
		$('codigo_postal').focus();
	}
	else if ($('observaciones').get('value').length > 1000) {
		alert('El texto en observaciones no puede ser mayor a 1000 caracteres');
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
			'url': 'NotaCredito.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Generando CFD...', 'Notas de Cr&eacute;dito', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();

				popup = new Popup(result, 'Notas de Cr&eacute;dito', 500, 200, popupOpen, null);
			}
		}).send();
	}
}

var popupOpen = function() {
	$('cerrar').addEvent('click', function() {
		if (!$chk($('error'))) {
			$('Datos').reset();
		}

		popup.Close();
	});
}
