// JavaScript Document

var popup, codigos = [];

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	obtenerCodigos();
	
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
			'change': cambiaCia.pass([el, $$('input[id=nombre_cia]')[i]]),
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
			'change': cambiaCod.pass([$$('input[id=num_cia]')[i], el, $$('input[id=descripcion]')[i], $$('input[id=importe]')[i], $$('input[id=local]')[i], $$('input[id=fecha_renta]')[i], $$('input[id=es_cheque]')[i], $$('input[id=concepto]')[i]]),
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
					else if (el.get('value') == '2' && $$('input[id=num_cia]')[i].get('value').getNumericValue() > 0) {
						new Request({
							'url': 'CapturaMovimientosCometraSinRestricciones.php',
							//'data': 'accion=obtenerLocales&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
							'data': 'accion=obtenerRentas&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
							'onRequest': function() {
							},
							'onSuccess': function(html) {
								//popup = new Popup(html, 'Locales de "' + $$('input[id=num_cia]')[i].get('value') + ' ' + $$('input[id=nombre_cia]')[i].get('value') + '"', 900, 200, openLocales.pass(i), closeLocales.pass(i));
								popup = new Popup(html, 'Recibos pendientes de "' + $$('input[id=num_cia]')[i].get('value') + ' ' + $$('input[id=nombre_cia]')[i].get('value') + '"', 900, 200, openLocales.pass(i), closeLocales.pass(i));
							},
							'onFailure': function(xhr) {
								alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
							}
						}).send();
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
						'url': 'CapturaMovimientosCometraSinRestricciones.php',
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

var obtenerCodigos = function() {
	new Request({
		'url': 'CapturaMovimientosCometraSinRestricciones.php',
		'data': 'accion=codigos',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			codigos = JSON.decode(result);
		}
	}).send();
}

var openLocales = function() {
	var i = arguments[0];
	
	new FormValidator($('Locales'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Locales'));
	
//	$('anio').addEvent('keydown', function(e) {
//		if (e.key == 'enter') {
//			e.stop();
//			
//			this.blur();
//		}
//	});
	
	$('cancelar').addEvent('click', function() {
//		$$('input[id=cod_mov]')[i].set('value', '');
//		$$('input[id=local]')[i].set('value', '');
//		$$('input[id=fecha_renta]')[i].set('value', '');
//		$$('input[id=es_cheque]')[i].set('value', 'FALSE');
		
		$$('input[id=cod_mov]')[i].set('value', '');
		$$('input[id=idreciborenta]')[i].set('value', '');
		$$('input[id=idarrendatario]')[i].set('value', '');
		$$('input[id=fecha_renta]')[i].set('value', '');
		$$('input[id=es_cheque]')[i].set('value', 'FALSE');
		
		popup.Close();
	});
	
	$('aceptar').addEvent('click', function() {
		/*if (!$chk($('anio').get('value'))) {
			alert('Debe especificar el año');
			$('anio').focus();
		}
		else if (!$chk($('locales').get('value').getNumericValue())) {
			alert('Debe seleccionar un local');
			$('locales').focus();
		}*/
		if ($('recibos').get('value') == '') {
			alert('Debe seleccionar un recibo');
		}
		else {
			var data = JSON.decode($('recibos').get('value'));
			
			$$('input[id=idreciborenta]')[i].set('value', data.idreciborenta);
			$$('input[id=idarrendatario]')[i].set('value', data.idarrendatario);
			$$('input[id=fecha_renta]')[i].set('value', data.fecha);
			$$('input[id=es_cheque]')[i].set('value', $('cheque').get('checked') ? 'TRUE' : 'FALSE');
			$$('input[id=concepto]')[i].set({
				'value': data.mes + ' ' + data.anio + ' ' + data.nombre_arrendatario,
				'readonly': true
			});
			$$('input[id=importe]')[i].set('value', data.renta.numberFormat(2, '.', ','));
			
			//$$('input[id=local]')[i].set('value', $('locales').get('value'));
			//$$('input[id=fecha_renta]')[i].set('value', '01/' + $('mes').get('value') + '/' + $('anio').get('value'));
			//$$('input[id=es_cheque]')[i].set('value', $('cheque').get('checked') ? 'TRUE' : 'FALSE');
			/*$$('input[id=concepto]')[i].set({
				'value': $('mes').getSelected()[0].get('text') + ' ' + $('anio').get('value') + ' ' + $('locales').getSelected()[0].get('text').split(' - ')[0].substr(5),
				'readonly': true
			});*/
			//$$('input[id=importe]')[i].set('value', $('locales').getSelected()[0].get('text').split(' - ')[1]).fireEvent('change');
			
			popup.Close();
		}
	});
	
	//$('anio').select();
}

var closeLocales = function() {
	var i = arguments[0];
	
	if (!$chk($$('input[id=cod_mov]')[i].get('value'))) {
		$$('input[id=cod_mov]')[i].fireEvent('change').focus();
	}
	else {
		$$('input[id=fecha]')[i].select();
	}
}

var validarComprobante = function() {
	new Request({
		'url': 'CapturaMovimientosCometraSinRestricciones.php',
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
		nombre_cia = arguments[1];
	
	if ($chk(num_cia.get('value'))) {
		new Request({
			'url': 'CapturaMovimientosCometraSinRestricciones.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value'),
			'onSuccess': function(result) {
				nombre_cia.set('value', result);
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	}
	else {
		num_cia.set('value', '');
		nombre_cia.set('value', '');
	}
}

var cambiaCod = function() {
	var num_cia = arguments[0]
		cod_mov = arguments[1],
		descripcion = arguments[2],
		importe = arguments[3],
		local = arguments[4],
		fecha_renta = arguments[5],
		es_cheque = arguments[6],
		concepto = arguments[7];
	
	$$('input[id=concepto]').set('readonly', false);
	
	if ($chk(cod_mov.get('value'))) {
		if (codigos.filter(function(el) { return $H(el).hasValue(cod_mov.get('value')); }).length > 0) {
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
				
				case'2':
					descripcion.set('value', 'RENTA');
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
					descripcion.set('value', codigos.filter(function(el) { return $H(el).hasValue(cod_mov.get('value')); })[0].descripcion);
			}
			
			if (cod_mov.get('value') != 2) {
				local.set('value', '');
				fecha_renta.set('value', '');
				es_cheque.set('value', 'FALSE');
				concepto.set('value', '');
			}
		}
		else {
			alert('El código no se encuentra en el catálogo');
			cod_mov.set('value', cod_mov.retrieve('tmp', '')).select();
		}
	}
	else {
		descripcion.set('value', '');
		local.set('value', '');
		fecha_renta.set('value', '');
		es_cheque.set('value', 'FALSE');
		concepto.set('value', '');
	}
	
	importe.setStyle('color', ['19', '48'].contains(cod_mov.get('value')) ? '#C00' : '#00C');
	actualizaTotal();
}

var validarFecha = function() {
	var fecha = arguments[0];
	
	if ($chk(fecha.get('value'))) {
		new Request({
			'url': 'CapturaMovimientosCometraSinRestricciones.php',
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
			'url': 'CapturaMovimientosCometraSinRestricciones.php',
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
