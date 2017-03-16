// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	styles = new FormStyles($('Datos'));

	$('xml_file').addEvents({
		'change': validar_cfd
	});

	$('tipo_pdf_generar').addEvent('click', function() {
		$('pdf_file').set({
			value: '',
			disabled: true
		})
	});

	$('tipo_pdf_cargar').addEvent('click', function() {
		$('pdf_file').set({
			value: '',
			disabled: $('xml_file').get('value') != '' ? false : true
		})
	});

	$('tipo_5').addEvent('click', obtener_arrendadores);

	$('arrendador').addEvent('change', obtener_arrendatarios);

	$('anio_renta').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('nombre_cliente').focus();
			}
		}
	});

	$$('#tipo_1, #tipo_2, #tipo_6').addEvent('click', function() {
		updSelect($('arrendador'), []);
		updSelect($('arrendatario'), []);

		$('renta_block').setStyle('display', 'none');
	});

	$('num_cia').addEvents({
		'change': obtener_cia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('serie').select();
			}
		}
	});

	$('serie').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('folio').focus();
			}
		}
	});

	$('folio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('fecha').focus();
			}
		}
	});

	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('hora').focus();
			}
		}
	});

	$('hora').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('cuenta_pago').focus();
			}
		}
	});

	$('cuenta_pago').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				if ($('tipo_5').get('checked'))
				{
					$('anio_renta').focus();
				}
				else
				{
					$('nombre_cliente').focus();
				}
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

	$$('input[id=descripcion]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();

				$$('input[id=cantidad]')[i].select();
			}
		});
	});

	$('expand').addEvent('click', cambiarDescripcion);

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

				if (!$chk($$('[id=descripcion]')[i + 1])) {
					newRow(i + 1);
				}

				$$('[id=descripcion]')[i + 1].select();
			}
		});
	});

	// $('aplicar_iva').addEvent('change', calcularTotal);

	$('registrar').addEvent('click', Registrar);

	$('num_cia').select();

	expand = false;
});

var validar_cfd = function()
{
	if ($('xml_file').get('value') == '')
	{
		alert('Debe seleccionar un documento XML');
	}
	else
	{
		var request = new Request.File({
			url: 'FacturaElectronicaManualSoloRegistrar.php',
			onRequest: function() {},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status < 0)
				{
					alert(data.error);

					limpiar_formulario();
				}
				else
				{
					if ($('tipo_pdf_cargar').get('checked'))
					{
						$('pdf_file').set('disabled', false).set('value', '');
					}
					else
					{
						$('pdf_file').set('disabled', true).set('value', '');
					}

					$('fecha_timbrado').set('value', data.timbre.fecha_timbrado);
					$('uuid').set('value', data.timbre.uuid);
					$('no_certificado_digital').set('value', data.timbre.no_certificado_digital);
					$('no_certificado_sat').set('value', data.timbre.no_certificado_sat);
					$('sello_cfd').set('value', data.timbre.sello_cfd);
					$('sello_sat').set('value', data.timbre.sello_sat);
					$('cadena_original').set('value', data.timbre.cadena_original);

					$('num_cia').set('value', data.emisor.num_cia);
					$('nombre_cia').set('value', data.emisor.nombre_cia);
					$('rfc_cia').set('value', data.emisor.rfc);
					$('serie').set('value', data.emisor.serie);
					$('folio').set('value', data.emisor.folio);
					$('fecha').set('value', data.emisor.fecha);
					$('hora').set('value', data.emisor.hora);

					if (data.cliente.rfc == 'XAXX010101000')
					{
						$('tipo_1').set('checked', true);

						$$('#tipo_1, #tipo_5').set('disabled', false);
						$$('#tipo_2, #tipo_6').set('disabled', true);
					}
					else
					{
						$('tipo_2').set('checked', true);

						$$('#tipo_1').set('disabled', true);
						$$('#tipo_2, #tipo_5, #tipo_6').set('disabled', false);
					}

					$A($('tipo_pago').options).each(function(option, i)
					{
						if (option.get('text').toLowerCase() == data.emisor.metodo_pago.toLowerCase())
						{
							$('tipo_pago').selectedIndex = i;
						}
					});

					$A($('condiciones_pago').options).each(function(option, i)
					{
						if (option.get('text').toLowerCase() == data.emisor.condiciones_pago.toLowerCase())
						{
							$('condiciones_pago').selectedIndex = i;
						}
					});

					$('nombre_cliente').set('value', data.cliente.nombre);
					$('rfc').set('value', data.cliente.rfc);
					$('calle').set('value', data.cliente.calle);
					$('no_exterior').set('value', data.cliente.no_exterior);
					$('no_interior').set('value', data.cliente.no_interior);
					$('colonia').set('value', data.cliente.colonia);
					$('municipio').set('value', data.cliente.municipio);
					$('estado').set('value', data.cliente.estado);
					$('pais').set('value', data.cliente.pais);
					$('codigo_postal').set('value', data.cliente.codigo_postal);

					$('Conceptos').empty();

					data.conceptos.each(function(row, i)
					{
						newRow(i);

						$$('input[id=descripcion]')[i].set('value', row.descripcion);
						$$('input[id=cantidad]')[i].set('value', row.cantidad);
						$$('input[id=precio]')[i].set('value', row.precio);
						$$('input[id=unidad]')[i].set('value', row.unidad);
						$('aplicar_iva' + i).set('checked', /*data.emisor.iva > 0 ? true : */false);
						$('aplicar_ieps' + i).set('checked', /*data.emisor.ieps > 0 ? true : */false);
						$$('input[id=importe]')[i].set('value', row.importe.numberFormat(2, '.', ','));
					});

					$('subtotal').set('value', data.emisor.subtotal.numberFormat(2, '.', ','));
					$('ieps').set('value', data.emisor.ieps != 0 ? data.emisor.ieps.numberFormat(2, '.', ',') : '');
					$('iva').set('value', data.emisor.iva != 0 ? data.emisor.iva.numberFormat(2, '.', ',') : '');
					$('retencion_iva').set('value', data.emisor.retencion_iva != 0 ? data.emisor.retencion_iva.numberFormat(2, '.', ',') : '');
					$('retencion_isr').set('value', data.emisor.retencion_isr != 0 ? data.emisor.retencion_isr.numberFormat(2, '.', ',') : '');
					$('total').set('value', data.emisor.total.numberFormat(2, '.', ','));
				}
			}
		});

		request.append('accion', 'validar_xml');
		request.append('xml_file', $('xml_file').files[0]);

		request.send();
	}
}

var obtener_cia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			url: 'FacturaElectronicaManualSoloRegistrar.php',
			data: {
				accion: 'obtener_cia',
				num_cia: $('num_cia').get('value'),
				rfc: $('rfc_cia').get('value')
			},
			onRequest: function() {},
			onSuccess: function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);

					if ($('tipo_5').get('checked'))
					{
						obtener_arrendatarios();
					}
				}
				else {
					alert('La compañía no esta en el catálogo o no tiene el mismo R.F.C. que el comprobante');

					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia, #fecha').set('value', '');
	}
}

var obtener_arrendadores = function()
{
	new Request({
		url: 'FacturaElectronicaManualSoloRegistrar.php',
		data: {
			accion: 'obtener_arrendadores',
			num_cia: $('num_cia').get('value')
		},
		onRequest: function() {},
		onSuccess: function(result) {
			if (result != '') {
				var data = JSON.decode(result);

				updSelect($('arrendador'), data.arrendadores);

				obtener_arrendatarios();

				$('renta_block').setStyle('display', 'table-row-group');
			}
			else {
				updSelect($('arrendador'), []);
				updSelect($('arrendatario'), []);

				alert('La compañía no tiene inmobiliarias asociadas');
			}
		}
	}).send();
}

var obtener_arrendatarios = function()
{
	new Request({
		url: 'FacturaElectronicaManualSoloRegistrar.php',
		data: {
			accion: 'obtener_arrendatarios',
			arrendador: $('arrendador').get('value')
		},
		onRequest: function() {},
		onSuccess: function(result) {
			if (result != '') {
				var data = JSON.decode(result);

				updSelect($('arrendatario'), data.arrendatarios);
			}
			else {
				updSelect($('arrendatario'), []);

				alert('La inmobiliaria no tiene arrendatarios');
			}
		}
	}).send();
}

var limpiar_formulario = function()
{
	$$('#xml_file, #pdf_file, #fecha_timbrado, #uuid, #no_certificado_digital, #no_certificado_sat, #sello_cfd, #sello_sat, #cadena_original, #num_cia, #serie, #folio, #fecha, #hora, #cuenta_pago, #nombre_cliente, #rfc, #calle, #no_exterior, #no_interior, #colonia, #localidad, #referencia, #municipio, #estado, #pais, #codigo_postal, #email_cliente, #observaciones, #subtotal, #ieps, #iva, #total, #anio_renta').set('value', '');

	$$('#pdf_file, #tipo_1, #tipo_2, #tipo_5, #tipo_6').set('disabled', true);

	updSelect($('arrendador'), []);
	updSelect($('arrendatario'), []);

	$('renta_block').setStyle('display', 'none');

	$('tipo_pago').selectedIndex = 0;
	$('condiciones_pago').selectedIndex = 0;

	$('Conceptos').empty();

	$('tipo_pdf_generar').set('checked', true);
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
		precio = $$('input[id=precio]')[index].get('value').getNumericValue();

	importe = cantidad * precio;

	$$('input[id=importe]')[index].set('value', importe > 0 || cantidad > 0 ? importe.numberFormat(2, '.', ',') : '');

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

		ieps += $('aplicar_ieps' + i).get('checked') ? (el.get('value').getNumericValue() * 0.08).round(2) : 0;
		iva += $('aplicar_iva' + i).get('checked') ? (el.get('value').getNumericValue() * 0.16).round(2) : 0;
	});

	cantidad = $$('input[id=cantidad]').get('value').getNumericValue().sum();

	total = subtotal + ieps + iva;

	$('subtotal').set('value', subtotal > 0 || cantidad > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('ieps').set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
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

	if (!expand) {
		var descripcion = new Element('input', {
			'id': 'descripcion',
			'name': 'descripcion[]',
			'type': 'text',
			'class': 'valid toText toUpper cleanText',
			'size': 30,
			'maxlength': 100,
			'readonly': true
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
			'wrap': 'physical',
			'readonly': true
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
		'precision': 2,
		'readonly': true
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
		'precision': 2,
		'readonly': true
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
		'maxlength': 50,
		'readonly': true
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();

			if (!$chk($$('[id=descripcion]')[i + 1])) {
				newRow(i + 1);
			}

			$$('[id=descripcion]')[i + 1].select();
		}
	}).inject(td4);

	var aplicar_iva = new Element('input', {
		'id': 'aplicar_iva' + i,
		'name': 'aplicar_iva' + i,
		'type': 'checkbox',
		'value': i,
		'checked': $('num_cia').get('value').getNumericValue() <= 300 ? false : true
	})/*.addEvent('change', calcularTotal)*/.inject(td5);

	var aplicar_ieps = new Element('input', {
		'id': 'aplicar_ieps' + i,
		'name': 'aplicar_ieps' + i,
		'type': 'checkbox',
		'value': i,
		'checked': false
	})/*.addEvent('change', calcularTotal)*/.inject(td6);

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
	else if (!($('rfc').get('value') == 'XAXX010101000') && $('calle').get('value') == '') {
		alert('Debe especificar la calle');
		$('calle').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000') && $('colonia').get('value') == '') {
		alert('Debe especificar la colonia');
		$('colonia').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000') && $('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio');
		$('municipio').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000') && $('estado').get('value') == '') {
		alert('Debe especificar el estado');
		$('estado').focus();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el pais');
		$('pais').set('value', 'MEXICO').focus();
	}
	else if (!($('rfc').get('value') == 'XAXX010101000') && $('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal');
		$('codigo_postal').focus();
	}
	else if ($('observaciones').get('value').length > 1000) {
		alert('El texto en observaciones no puede ser mayor a 1000 caracteres');
		$('observaciones').focus();
	}
	else if ($('xml_file').get('value') == '') {
		alert('Debe seleccionar un archivo XML');
		$('xml_file').focus();
	}
	else if ($('tipo_pdf_cargar').get('checked') && $('pdf_file').get('value') == '') {
		alert('Debe seleccionar un archivo PDF');
		$('pdf_file').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		var queryObject = {};

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

					if (el.name.indexOf('[') === -1)
					{
						queryObject[el.name] = val;
					}
					else
					{
						if ( ! queryObject[el.name.substr(0, el.name.length - 2)]) {
							queryObject[el.name.substr(0, el.name.length - 2)] = [];
						}

						queryObject[el.name.substr(0, el.name.length - 2)].push(val);
					}
				}
			});
		});

		var request = new Request.File({
			url: 'FacturaElectronicaManualSoloRegistrar.php',
			onRequest: function()
			{
				popup = new Popup('<img src="imagenes/_loading.gif" /> Guardando archivos...', 'Facturas Electr&oacute;nicas', 200, 100, null, null);
			},
			onSuccess: function(result)
			{
				popup.Close();

				var data = JSON.decode(result);

				if (data.status < 0)
				{
					alert(data.error);

					if (data.status == -4)
					{
						$('pdf_file').set('value', '');
					}
				}
				else
				{
					new Request({
						'url': 'FacturaElectronicaManualSoloRegistrar.php',
						'data': 'accion=registrar&' + queryString.join('&'),
						'onRequest': function() {
							popup = new Popup('<img src="imagenes/_loading.gif" /> Generando datos...', 'Facturas Electr&oacute;nicas', 200, 100, null, null);
						},
						'onSuccess': function(result) {
							popup.Close();

							popup = new Popup("<p>Comprobante guardado en el sistema</p><p><button onclick=\"popup.Close()\">Cerrar</button></p>", 'Facturas Electr&oacute;nicas', 500, 200, null, null);

							limpiar_formulario();
						}
					}).send();
				}
			}
		});

		request.append('accion', 'guardar_archivos');
		request.append('num_cia', $('num_cia').get('value'));
		request.append('tipo', $$('input[name=tipo]:checked').get('value'));
		request.append('serie', $('serie').get('value'));
		request.append('folio', $('folio').get('value'));
		request.append('xml_file', $('xml_file').files[0]);
		request.append('tipo_pdf', $$('input[name=tipo_pdf]:checked').get('value'));
		request.append('json_string', JSON.encode(queryObject));

		if ($('tipo_pdf_cargar').get('checked')) {
			request.append('pdf_file', $('pdf_file').files[0]);
		}

		request.send();
	}
}

var updSelect = function() {
	var Select = arguments[0],
		Options = arguments[1];

	if (Options.length > 0) {
		Select.length = Options.length;

		$each(Select.options, function(el, i) {
			el.set(Options[i]);
		});

		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;

		Select.selectedIndex = -1;
	}
}
