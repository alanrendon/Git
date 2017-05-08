// JavaScript Document

var meses = [
	'ENERO',
	'FEBRERO',
	'MARZO',
	'ABRIL',
	'MAYO',
	'JUNIO',
	'JULIO',
	'AGOSTO',
	'SEPTIEMBRE',
	'OCTUBRE',
	'NOVIEMBRE',
	'DICIEMBRE'
];

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	styles = new FormStyles($('Datos'));

	$('arrendador').addEvents({
		'change': ObtenerArrendador,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('anio').select();
			}
		}
	});

	$('idarrendatario').addEvent('change', ObtenerDatosArrendatario);

	$('anio').addEvents({
		'change': function() {
			var d1 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue() - 1, 1);
			var d2 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue(), 0);

			var fecha1 = String('00' + d1.getDate()).slice(-2) + '/' + String('00' + (d1.getMonth() + 1)).slice(-2) + '/' + String('0000' + d1.getFullYear()).slice(-4);
			var fecha2 = String('00' + d2.getDate()).slice(-2) + '/' + String('00' + (d2.getMonth() + 1)).slice(-2) + '/' + String('0000' + d2.getFullYear()).slice(-4);

			// $('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL MES DE', 0) >= 0) ? 'RENTA DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_renta').get('value'));
			$('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL', 0) >= 0) ? 'RENTA DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_renta').get('value'));
			// $('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL MES DE', 0) >= 0) ? 'MANTENIMIENTO DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_mantenimiento').get('value'));
			$('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL', 0) >= 0) ? 'MANTENIMIENTO DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_mantenimiento').get('value'));
		},
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('concepto_renta').focus();
			}
		}
	});

	$('mes').addEvent('change', function() {
		var d1 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue() - 1, 1);
			var d2 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue(), 0);

			var fecha1 = String('00' + d1.getDate()).slice(-2) + '/' + String('00' + (d1.getMonth() + 1)).slice(-2) + '/' + String('0000' + d1.getFullYear()).slice(-4);
			var fecha2 = String('00' + d2.getDate()).slice(-2) + '/' + String('00' + (d2.getMonth() + 1)).slice(-2) + '/' + String('0000' + d2.getFullYear()).slice(-4);

			// $('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL MES DE', 0) >= 0) ? 'RENTA DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_renta').get('value'));
			$('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL', 0) >= 0) ? 'RENTA DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_renta').get('value'));
			// $('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL MES DE', 0) >= 0) ? 'MANTENIMIENTO DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_mantenimiento').get('value'));
			$('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL', 0) >= 0) ? 'MANTENIMIENTO DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_mantenimiento').get('value'));
	});

	$('concepto_renta').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('concepto_mantenimiento').focus();
			}
		}
	});

	$('concepto_mantenimiento').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('renta').focus();
			}
		}
	});

	$('renta').addEvents({
		'change': calcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('mantenimiento').focus();
			}
		}
	});

	$('mantenimiento').addEvents({
		'change': calcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('agua').focus();
			}
		}
	});

	$('agua').addEvents({
		'change': calcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('arrendador').focus();
			}
		}
	});

	$('aplicar_iva').addEvent('change', calcularTotal);
	$('aplicar_retencion_iva').addEvent('change', calcularTotal);
	$('aplicar_retencion_isr').addEvent('change', calcularTotal);

	$('registrar').addEvent('click', Registrar);

	$('arrendador').select();
});

var ObtenerArrendador = function() {
	if ($('arrendador').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'RentasFacturasManual.php',
			'data': 'accion=obtenerArrendador&arrendador=' + $('arrendador').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					$('nombre_arrendador').set('value', data.nombre_arrendador);

					updSelect($('idarrendatario'), data.arrendatarios);

					$('idarrendatario').fireEvent('change');
				}
				else {
					alert('El arrendador no esta en el catálogo');

					$('arrendador').set('value', $('arrendador').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#arrendador, #nombre_arrendador, #renta, #mantenimiento, #subtotal, #iva, #iva_renta, #iva_mantenimiento, #agua, #retencion_iva, #retencion_isr, #total').set('value', '');

		updSelect($('idarrendatario'), []);
	}
}

var ObtenerDatosArrendatario = function() {
	if ($('idarrendatario').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'RentasFacturasManual.php',
			'data': 'accion=obtenerDatosArrendatario&id=' + $('idarrendatario').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					var d1 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue() - 1, 1);
					var d2 = new Date($('anio').get('value').getNumericValue(), $('mes').get('value').getNumericValue(), 0);

					var fecha1 = String('00' + d1.getDate()).slice(-2) + '/' + String('00' + (d1.getMonth() + 1)).slice(-2) + '/' + String('0000' + d1.getFullYear()).slice(-4);
					var fecha2 = String('00' + d2.getDate()).slice(-2) + '/' + String('00' + (d2.getMonth() + 1)).slice(-2) + '/' + String('0000' + d2.getFullYear()).slice(-4);

					// $('concepto_renta').set('value', data.renta != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL MES DE', 0) >= 0) ? 'RENTA DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_renta').get('value'));
					$('concepto_renta').set('value', data.renta != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL', 0) >= 0) ? 'RENTA DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_renta').get('value'));
					// $('concepto_mantenimiento').set('value', data.mantenimiento != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL MES DE', 0) >= 0) ? 'MANTENIMIENTO DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_mantenimiento').get('value'));
					$('concepto_mantenimiento').set('value', data.mantenimiento != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL', 0) >= 0) ? 'MANTENIMIENTO DEL ' + fecha1 + ' AL ' + fecha2 : $('concepto_mantenimiento').get('value'));

					$('renta').set('value', data.renta != 0 ? data.renta.numberFormat(2, '.', ',') : '');
					$('mantenimiento').set('value', data.mantenimiento != 0 ? data.mantenimiento.numberFormat(2, '.', ',') : '');
					$('subtotal').set('value', data.subtotal.numberFormat(2, '.', ','));
					$('aplicar_iva').set('checked', data.iva != 0 ? true : false);
					$('iva').set('value', data.iva != 0 ? data.iva.numberFormat(2, '.', ',') : '');
					$('iva_renta').set('value', data.iva_renta != 0 ? data.iva_renta.numberFormat(2, '.', ',') : '');
					$('iva_mantenimiento').set('value', data.iva_mantenimiento != 0 ? data.iva_mantenimiento.numberFormat(2, '.', ',') : '');
					$('agua').set('value', data.agua != 0 ? data.agua.numberFormat(2, '.', ',') : '');
					$('aplicar_retencion_iva').set('checked', data.retencion_iva != 0 ? true : false);
					$('retencion_iva').set('value', data.retencion_iva != 0 ? data.retencion_iva.numberFormat(2, '.', ',') : '');
					$('aplicar_retencion_isr').set('checked', data.retencion_isr != 0 ? true : false);
					$('retencion_isr').set('value', data.retencion_isr != 0 ? data.retencion_isr.numberFormat(2, '.', ',') : '');
					$('total').set('value', data.total.numberFormat(2, '.', ','));
				}
				else {
					$$('#renta, #mantenimiento, #subtotal, #iva, #iva_renta, #iva_mantenimiento, #agua, #retencion_iva, #retencion_isr, #total').set('value', '');
				}
			}
		}).send();
	}
	else {
		$$('#renta, #mantenimiento, #subtotal, #iva, #iva_renta, #iva_mantenimiento, #agua, #retencion_iva, #retencion_isr, #total').set('value', '');
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

var calcularTotal = function() {
	var renta = $('renta').get('value').getNumericValue(),
		mantenimiento = $('mantenimiento').get('value').getNumericValue(),
		subtotal = 0,
		iva = 0,
		iva_renta = 0,
		iva_mantenimiento = 0,
		agua = $('agua').get('value').getNumericValue(),
		retencion_iva = 0,
		retencion_isr = 0,
		total = 0;

	subtotal = renta + mantenimiento;
	iva = $('aplicar_iva').get('checked') ? ((renta * 0.16) + (mantenimiento * 0.16)).round(2) : 0;
	iva_renta = $('aplicar_iva').get('checked') ? (renta * 0.16) : 0;
	iva_mantenimiento = $('aplicar_iva').get('checked') ? (mantenimiento * 0.16) : 0;
	iva = $('aplicar_iva').get('checked') ? (iva_renta + iva_mantenimiento).round(2) : 0;
	retencion_iva = $('aplicar_retencion_iva').get('checked') ? ((renta * 0.106666667) + (mantenimiento * 0.106666667)).round(2) : 0;
	retencion_isr = $('aplicar_retencion_isr').get('checked') ? ((renta * 0.10) + (mantenimiento * 0.10)).round(2) : 0;
	total = subtotal + iva + agua - retencion_iva - retencion_isr;

	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('iva_renta').set('value', iva_renta > 0 ? iva_renta.numberFormat(2, '.', '') : '');
	$('iva_mantenimiento').set('value', iva_mantenimiento > 0 ? iva_mantenimiento.numberFormat(2, '.', '') : '');
	$('retencion_iva').set('value', retencion_iva > 0 ? retencion_iva.numberFormat(2, '.', ',') : '');
	$('retencion_isr').set('value', retencion_isr > 0 ? retencion_isr.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var Registrar = function() {
	if (!$chk($('idarrendatario').get('value').getNumericValue())) {
		alert('Debe especificar el arrendatario');
		$('local').focus();
	}
	else if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año');
		$('anio').focus();
	}
	else if ($('concepto_renta').get('value') == '' && $('renta').get('value').getNumericValue() > 0) {
		alert('Debe especificar el concepto de renta');
		$('concepto_renta').focus();
	}
	else if ($('concepto_renta').get('value') != '' && $('renta').get('value').getNumericValue() <= 0) {
		alert('Debe especificar el importe de renta');
		$('renta').focus();
	}
	else if ($('concepto_mantenimiento').get('value') == '' && $('mantenimiento').get('value').getNumericValue() > 0) {
		alert('Debe especificar el concepto de mantenimiento');
		$('concepto_mantenimiento').focus();
	}
	else if ($('concepto_mantenimiento').get('value') != '' && $('mantenimiento').get('value').getNumericValue() <= 0) {
		alert('Debe especificar el importe de mantenimiento');
		$('mantenimiento').focus();
	}
	else if ($('subtotal').get('value').getNumericValue() == 0) {
		alert('Debe especificar el importe de renta o mantenimiento');
		$('renta').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
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
			'url': 'RentasFacturasManual.php',
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
			$('Datos').reset();

			updSelect($('idarrendatario'), []);
		}

		popup.Close();
	});
}
