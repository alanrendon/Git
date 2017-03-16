// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('local').addEvents({
		'change': cambiaLocal,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('anio').select();
			}
		}
	});
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('concepto').focus();
			}
		}
	});
	
	$('concepto').addEvents({
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
				
				$('local').focus();
			}
		}
	});
	
	$('aplicar_iva').addEvent('change', calcularTotal);
	$('aplicar_retenciones').addEvent('change', calcularTotal);
	
	$('registrar').addEvent('click', Registrar);
	
	$('local').select();
});

var cambiaLocal = function() {
	if ($('local').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaElectronicaRenta.php',
			'data': 'accion=obtenerLocal&local=' + $('local').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('id').set('value', data.id);
					$('nombre_local').set('value', data.nombre_local);
					$('inmobiliaria').set('html', data.inmobiliaria);
					$('arrendatario').set('html', data.arrendatario);
					
					$('renta').set('value', data.renta != 0 ? data.renta.numberFormat(2, '.', ',') : '');
					$('mantenimiento').set('value', data.mantenimiento != 0 ? data.mantenimiento.numberFormat(2, '.', ',') : '');
					$('subtotal').set('value', data.subtotal.numberFormat(2, '.', ','));
					$('aplicar_iva').set('checked', data.iva != 0 ? true : false);
					$('iva').set('value', data.iva != 0 ? data.iva.numberFormat(2, '.', ',') : '');
					$('iva_renta').set('value', data.iva_renta != 0 ? data.iva_renta.numberFormat(2, '.', ',') : '');
					$('iva_mantenimiento').set('value', data.iva_mantenimiento != 0 ? data.iva_mantenimiento.numberFormat(2, '.', ',') : '');
					$('agua').set('value', data.agua != 0 ? data.agua.numberFormat(2, '.', ',') : '');
					$('aplicar_retenciones').set('checked', data.retencion_iva != 0 || data.retencion_isr != 0 ? true : false);
					$('retencion_iva').set('value', data.retencion_iva != 0 ? data.retencion_iva.numberFormat(2, '.', ',') : '');
					$('retencion_isr').set('value', data.retencion_isr != 0 ? data.retencion_isr.numberFormat(2, '.', ',') : '');
					$('total').set('value', data.total.numberFormat(2, '.', ','));
				}
				else {
					alert('El Local no esta en el catálogo');
					
					$('local').set('value', $('local').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#id, #local, #nombre_local, #renta, #mantenimiento, #subtotal, #iva, #iva_renta, #iva_mantenimiento, #agua, #retencion_iva, #retencion_isr, #total').set('value', '');
		$$('#inmobiliaria, #arrendatario').set('html', '&nbsp;');
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
	iva = $('aplicar_iva').get('checked') ? (renta * 0.16).round(2) + (mantenimiento * 0.16).round(2) : 0;
	iva_renta = $('aplicar_iva').get('checked') ? (renta * 0.16).round(2) : 0;
	iva_mantenimiento = $('aplicar_iva').get('checked') ? (mantenimiento * 0.16).round(2) : 0;
	iva = $('aplicar_iva').get('checked') ? iva_renta + iva_mantenimiento : 0;
	retencion_iva = $('aplicar_retenciones').get('checked') ? (subtotal * 0.10666666667).round(2) : 0;
	retencion_isr = $('aplicar_retenciones').get('checked') ? (subtotal * 0.10).round(2) : 0;
	total = subtotal + iva + agua - retencion_iva - retencion_isr;
	
	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('iva_renta').set('value', iva_renta > 0 ? iva_renta.numberFormat(2, '.', '') : '');
	$('iva_mantenimiento').set('value', iva_mantenimiento > 0 ? iva_mantenimiento.numberFormat(2, '.', '') : '');
	$('retencion_iva').set('value', retencion_iva > 0 ? retencion_iva.numberFormat(2, '.', ',') : '');
	$('retencion_isr').set('value', retencion_iva > 0 ? retencion_isr.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var Registrar = function() {
	if (!$chk($('local').get('value').getNumericValue())) {
		alert('Debe especificar el local');
		$('local').focus();
	}
	else if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año');
		$('anio').focus();
	}
//	else if ($('concepto').get('value') == '') {
//		alert('Debe especificar el concepto de renta');
//		$('concepto').focus();
//	}
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
			'url': 'FacturaElectronicaRenta.php',
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
		}
		
		popup.Close();
	});
}
