// JavaScript Document

var homoclaves = [];

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('comprobante').addEvents({
		'change': validarComprobante,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=num_cia]')[0].select();
			}
		}
	});
	
	$$('input[id=num_cia]').each(function(el, i) {
		el.addEvents({
			'change': cambiaCia.pass([el, $$('input[id=nombre_cia]')[i], $$('input[id=cod_mov]')[i], i]),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (el.get('value').getNumericValue() > 0) {
						$$('input[id=cod_mov]')[i].select();
					}
					else {
						alert('Debe especificar la compañía');
					}
				}
			}
		});
	});
	
	$$('input[id=cod_mov]').each(function(el, i) {
		el.addEvents({
			'change': cambiaCod.pass([$$('input[id=num_cia]')[i], el, $$('input[id=descripcion]')[i], $$('input[id=importe]')[i]]),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (!$chk(el.get('value'))) {
						if ($$('input[id=num_cia]')[i].get('value').getNumericValue() <= 300
							|| $$('input[id=num_cia]')[i].get('value').getNumericValue() >= 900
							|| ($$('input[id=num_cia]')[i].get('value').getNumericValue() >= 600 && $$('input[id=num_cia]')[i].get('value').getNumericValue() <= 699)) {
							el.set('value', 1);
						}
						else {
							el.set('value', 16);
						}
						
						el.fireEvent('change');
					}
					
					$$('input[id=fecha]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=fecha]').each(function(el, i) {
		el.addEvents({
			'focus': function() {
				if (!$chk(el.get('value')) && i == 0) {
					new Request({
						'url': 'CapturaMovimientosCometra.php',
						'data': 'accion=obtenerFecha',
						'onSuccess': function(result) {
							el.set('value', result);
							el.select();
						},
						'onFailure': function() {
							alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
						}
					}).send();
				}
				else if (!$chk(el.get('value')) && i > 0) {
					el.set('value', $$('input[id=fecha]')[i - 1].get('value'));
					el.select();
				}
			},
			'change': validarFecha.pass(el),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=concepto]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=concepto]').each(function(el, i) {
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
			'change': actualizaTotal,
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if ($defined($$('input[id=num_cia]')[i + 1])) {
						$$('input[id=num_cia]')[i + 1].select();
					}
					else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		});
	});
	
	$('registrar').addEvent('click', Registrar);
	
	$('comprobante').select();
});

var validarComprobante = function() {
	new Request({
		'url': 'CapturaMovimientosCometra.php',
		'data': 'accion=validarComprobante&comprobante=' + $('comprobante').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result == '-1') {
				alert('El comprobante ya esta registrado');
				$('comprobante').set('value', $('comprobante').retrieve('tmp', '')).select();
			}
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var cambiaCia = function() {
	var num_cia = arguments[0],
		nombre_cia = arguments[1],
		cod_mov = arguments[2],
		index = arguments[3];
	
	if ($chk(num_cia.get('value'))) {
		new Request({
			'url': 'CapturaMovimientosCometra.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value') + (index == 0 ? '&primaria=true' : ''),
			'onSuccess': function(result) {
				if (result == '-1') {
					alert('La compañía no se encuentra en el catálogo');
					num_cia.set('value', num_cia.retrieve('tmp', ''));
					num_cia.select();
				}
				else {
					var data = JSON.decode(result);
					
					if ($defined(data.homoclaves)
						&& !homoclaves.contains(num_cia.get('value'))) {
						
						if (homoclaves.length > 0
							&& !confirm('Al cambiar la primera compañía cambiaran todas las homoclaves y tendra que capturar todo de nuevo. ¿Desea continuar?')) {
							num_cia.set('value', num_cia.retrieve('tmp', ''));
							num_cia.select();
							return false;
						}
						
						var num_cia_value = num_cia.get('value');
						
						$$('input.valid[id!=comprobante]', 'input[id=nombre_cia]', 'input[id=descripcion]').set('value', '');
						
						homoclaves.empty();
						
						homoclaves.extend(data.homoclaves);
						
						num_cia.set('value', num_cia_value);
						nombre_cia.set('value', data.nombre_cia);
					}
					else if (homoclaves.contains(num_cia.get('value'))) {
						nombre_cia.set('value', data.nombre_cia);
					}
					else {
						alert('La compañía debe estar dentro de las siguientes claves:\n\n' + homoclaves.join(', '));
						num_cia.set('value', num_cia.retrieve('tmp', ''));
						num_cia.select();
					}
				}
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	}
	else {
		if (index == 0 && confirm('Al borrar la primera compañía se borraran todas las homoclaves y tendra que capturar todo de nuevo. ¿Desea continuar?')) {
			$$('input.valid', 'input[id=nombre_cia]', 'input[id=descripcion]').set('value', '');
			
			actualizaTotal();
		}
		else if (index > 0 && confirm('Al borrar la compañía se borrara todo el registro. ¿Desea continuar?')) {
			num_cia.set('value', '');
			nombre_cia.set('value', '');
		}
	}
}

var cambiaCod = function() {
	var num_cia = arguments[0]
		cod_mov = arguments[1],
		descripcion = arguments[2],
		importe = arguments[3];
	
	if ($chk(cod_mov.get('value'))) {
		switch (cod_mov.get('value')) {
			case '1':
				if (!(num_cia.get('value').getNumericValue() <= 300
					|| num_cia.get('value').getNumericValue() >= 900
					|| (num_cia.get('value').getNumericValue() >= 600 && num_cia.get('value').getNumericValue() <= 699))) {
					alert('El código 1 es solo para panaderías, zapaterías e inmobiliarias');
					cod_mov.set('value', cod_mov.retrieve('tmp', ''));
					cod_mov.select();
				}
				else {
					descripcion.set('value', num_cia.get('value').getNumericValue() <= 300 ? 'PAN' : (num_cia.get('value').getNumericValue() < 900 ? 'INMOBILIARIA' : 'ZAPATERIA'));
				}
			break;
			
			case '16':
				if (num_cia.get('value').getNumericValue() <= 300
					|| num_cia.get('value').getNumericValue() >= 900
					|| (num_cia.get('value').getNumericValue() >= 600 && num_cia.get('value').getNumericValue() <= 699)) {
					alert('El código 16 es solo para rosticerías');
					cod_mov.set('value', cod_mov.retrieve('tmp', ''));
					cod_mov.select();
				}
				else {
					descripcion.set('value', 'POLLOS');
				}
			break;
			
			case '7':
				descripcion.set('value', 'PAGO FALT.');
			break;
			
			case '13':
				descripcion.set('value', 'SOBRANTE');
			break;
			
			case '19':
				descripcion.set('value', 'FALTANTE');
			break;
			
			case '48':
				descripcion.set('value', 'FALSO');
			break;
			
			case '99':
				descripcion.set('value', 'CHEQUE');
			break;

			case '21':
				descripcion.set('value', 'CANC. DEP.');
			break;
			
			default:
				alert('El código debe estar dentro de los siguientes valores:\n\n[ 1] PAN, ZAPATERIA ó INMOBILIARIA\n[16] POLLOS\n[13] SOBRANTE\n[19] FALTANTE\n[48] FALSO\n[99] CHEQUE\n[21] CANCELACION DE DEPOSITO');
				cod_mov.set('value', cod_mov.retrieve('tmp', ''));
				cod_mov.select();
		}
	}
	else {
		descripcion.set('value', '');
	}
	
	importe.setStyle('color', ['19', '48'].contains(cod_mov.get('value')) ? '#C00' : '#00C');
	actualizaTotal();
}

var validarFecha = function() {
	var fecha = arguments[0];
	
	if ($chk(fecha.get('value'))) {
		new Request({
			'url': 'CapturaMovimientosCometra.php',
			'data': 'accion=validarFecha&fecha=' + fecha.get('value'),
			'onSuccess': function(result) {
				if (result == '-1') {
					alert('La fecha no puede ser mayor al día de hoy ni menor a hace 15 días');
					fecha.set('value', fecha.retrieve('tmp', ''));
					fecha.select();
				}
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	}
	else {
		fecha.set('value', '');
	}
}

var actualizaTotal = function() {
	var total = 0;
	
	$$('input[id=importe]').each(function(el, i) {
		total += ['19', '48', '21'].contains($$('input[id=cod_mov]')[i].get('value')) ? -el.get('value').getNumericValue() : el.get('value').getNumericValue();
	});
	
	$('total').set('value', total.numberFormat(2, '.', ','));
}

var Registrar = function() {
	if (!$chk($('comprobante').get('value').getNumericValue())) {
		alert('Debe especificar el número de comprobante');
		$('comprobante').select();
	}
	else if (confirm('¿Desea registrar el nuevo comprobante?')) {
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
			'url': 'CapturaMovimientosCometra.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function() {
				$$('input.valid', 'input[id=nombre_cia]', 'input[id=descripcion]').set('value', '');
				
				$('total').set('value', '0.00');
				
				$('comprobante').select();
			},
			'onFailure': function() {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	}
}
