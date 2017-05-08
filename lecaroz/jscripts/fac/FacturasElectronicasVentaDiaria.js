// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('omitir').select();
					}
				}
			});
			
			$('omitir').addEvents({
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
						
						$('dia').select();
					}
				}
			});
			
			$('dia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').select();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('cias').select();
		}
	}).send();
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	if ($chk($('anio')) && $('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el anio de consulta');
		$('anio').select();
	}
	else {
		new Request({
			'url': 'FacturasElectronicasVentaDiaria.php',
			'data': 'accion=consultar&' + param,
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Consultando...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('captura').empty().set('html', result);
					
					new FormValidator($('Datos'), {
						showErrors: true,
						selectOnFocus: true
					});
					
					new FormStyles($('Datos'));
					
					$$('input[id=checkblock]').each(function(el) {
						el.addEvent('click', CheckBlock.pass(el));
					});
					
					$$('input[id=datos]').each(function(el, index) {
						el.addEvent('click', CalcularFacturaDia.pass([el, index]));
					});
					
					$$('a[id^=dep]').each(function(el, index) {
						var data = el.get('title').split('|'),
							cia = data[0].toInt(),
							dia = data[1].toInt();
						
						el.addEvents({
							'click': ImponerEfectivo.pass([cia, dia])
						});
						
						el.removeProperty('title');
					});
					
					$$('a[id^=tran]').each(function(el, index) {
						var param = el.get('title'),
							data = JSON.decode($$('input[id=datos]')[index].get('value'));
						
						if (!$$('input[id=datos]')[index].get('disabled') || data.depositos == 0) {
							el.addEvents({
								'click': ModificarFacturasTransito.pass(param)
							});
						}
						
						el.removeProperty('title');
					});
					
					$$('a[id^=cli]').each(function(el, index) {
						var param = el.get('title');
						
						if (!$$('input[id=datos]')[index].get('disabled')) {
							el.addEvents({
								'click': ModificarFacturasClientes.pass(param)
							});
						}
						
						el.removeProperty('title');
					});
					
					$$('input[id^=nota-ini]').each(function(el, index) {
						var cia = el.get('cia').toInt(),
							dia = el.get('dia').toInt();
						
						el.addEvents({
							'keydown': function(e) {
								if (e.key == 'enter' || e.key == 'right') {
									e.stop();
									
									$('nota-fin-' + cia + '-' + dia).select();
								}
								else if (e.key == 'up' && dia > 1) {
									e.stop();
									
									$('nota-ini-' + cia + '-' + (dia - 1)).select();
								}
								else if (e.key == 'down' && $('nota-ini-' + cia + '-' + (dia + 1))) {
									e.stop();
									
									$('nota-ini-' + cia + '-' + (dia + 1)).select();
								}
							}
						});
					});
					
					$$('input[id^=nota-fin]').each(function(el, index) {
						var cia = el.get('cia').toInt(),
							dia = el.get('dia').toInt();
						
						el.addEvents({
							'keydown': function(e) {
								if (e.key == 'enter' && $('nota-ini-' + cia + '-' + (dia + 1))) {
									e.stop();
									
									$('nota-ini-' + cia + '-' + (dia + 1)).select();
								}
								else if (e.key == 'left') {
									e.stop();
									
									$('nota-ini-' + cia + '-' + dia).select();
								}
								else if (e.key == 'up' && dia > 1) {
									e.stop();
									
									$('nota-fin-' + cia + '-' + (dia - 1)).select();
								}
								else if (e.key == 'down' && $('nota-fin-' + cia + '-' + (dia + 1))) {
									e.stop();
									
									$('nota-fin-' + cia + '-' + (dia + 1)).select();
								}
							}
						});
					});
					
					$('regresar').addEvent('click', Inicio);
					
					$('generar').addEvent('dblclick', function() {
						alert('BEATRIZ NO LE DES DOBLE CLICK');
						
						return false;
					});
					
					$('generar').addEvent('click', Generar);
				}
				else {
					Inicio();
					
					alert('No hay resultados');
				}
			}
		}).send();
	}
}

var CalcularFacturaDia = function() {
	var el = arguments[0],
		index = arguments[1],
		data = JSON.decode(el.get('value')),
		arrastre_diferencia = $$('input[id=arrastre_diferencia][cia=' + data.num_cia + ']')[0];
	
	if (el.get('checked')) {
		if (index > 0
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index - 1].get('disabled')
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index - 1].get('checked')) {
			alert('No puede seleccionar este d\xeda si no ha seleccionado los d\xedas anteriores');
			
			el.set('checked', false);
		}
		else {
			data.facturas_venta = data.depositos - data.facturas_transito - data.facturas_clientes + arrastre_diferencia.get('value').getNumericValue();
			
			if (data.facturas_venta < 0) {
				data.diferencia = data.facturas_venta;
				
				data.facturas_venta = 0;
			}
			else {
				data.diferencia = data.depositos - data.facturas_transito - data.facturas_clientes - data.facturas_venta;
			}
			
			data.arrastre = arrastre_diferencia.get('value').getNumericValue();
			
			arrastre_diferencia.set('value', data.facturas_venta > 0 ? 0 : data.diferencia);
			
			$('fp-' + data.num_cia + '-' + data.dia).set('html', data.facturas_venta != 0 ? '<span style="float:left;" class="font6">(1)</span>&nbsp;' + data.facturas_venta.numberFormat(2, '.', ',') : '');
			$('dif-' + data.num_cia + '-' + data.dia).set('html', data.diferencia != 0 ? data.diferencia.numberFormat(2, '.', ',') : '').addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}
	else {
		if (index < $$('input[id=datos][cia=' + data.num_cia + ']').length - 1
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index + 1].get('disabled')
			&& $$('input[id=datos][cia=' + data.num_cia + ']')[index + 1].get('checked')) {
				alert('No puede deseleccionar este d\xeda si no ha deseleccionado los d\xedas posteriores');
				
				el.set('checked', true);
		}
		else {
			data.facturas_venta = 0;
			
			data.diferencia = data.depositos - data.facturas_transito - data.facturas_clientes;
			
			arrastre_diferencia.set('value', data.arrastre);
			
			data.arrastre = 0;
			
			$('fp-' + data.num_cia + '-' + data.dia).set('text', '');
			$('dif-' + data.num_cia + '-' + data.dia).set('text', data.diferencia.numberFormat(2, '.', ',')).addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}
	
	CalcularTotalCia(data.num_cia);
}

var CalcularTotalCia = function() {
	var cia = arguments[0],
		depositos = 0,
		facturas_transito = 0,
		facturas_clientes = 0,
		facturas_venta = 0,
		diferencia = 0;
	
	$$('input[id=datos][cia=' + cia + ']').each(function(el) {
		data = JSON.decode(el.get('value'));
		
		depositos += data.depositos;
		facturas_transito += data.facturas_transito;
		facturas_clientes += data.facturas_clientes;
		facturas_venta += data.facturas_venta;
	});
	
	diferencia = depositos - facturas_transito - facturas_clientes - facturas_venta;
	
	$('tfp-' + cia).set('text', facturas_venta.numberFormat(2, '.', ','));
	$('tdif-' + cia).set('text', diferencia.numberFormat(2, '.', ',')).addClass(diferencia >= 0 ? 'blue' : 'red').removeClass(diferencia >= 0 ? 'red' : 'blue');
}

var ModificarFacturasTransito = function() {
	var data = JSON.decode(arguments[0]);
	
	new Request({
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=modificarFacturasTransito&num_cia=' + data.num_cia + '&anio=' + data.anio + '&mes=' + data.mes + '&dia=' + data.dia,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			var periodo_pieces = $$('input[id=periodo][cia=' + data.num_cia + ']')[0].get('value').split('|'),
				date_pieces = '';
			
			if (periodo_pieces[0] != '') {
				date_pieces = periodo_pieces[0].split('/');
				
				periodo1 = new Date(date_pieces[2].toInt(10), (date_pieces[1].toInt(10) - 1), date_pieces[0].toInt(10));
			}
			else {
				periodo1 = null;
			}
			
			if (periodo_pieces[1] != '') {
				date_pieces = periodo_pieces[1].split('/');
				
				periodo2 = new Date(/*date_pieces[2].toInt(10), (date_pieces[1].toInt(10) - 1), date_pieces[0].toInt(10)*/);
			}
			else {
				periodo2 = null;
			}
			
			popup = new Popup(result, 'Facturas en transito del ' + data.dia + '/' + data.mes + '/' + data.anio, 1000, 500, popupOpenTransito, null, {scrollBars: true});
		}
	}).send();
}

var popupOpenTransito = function() {
	new FormValidator($('Transito'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Transito'));
	
	$$('input[id=fecha]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i < $$('input[id=fecha]').length - 1) {
						$$('input[id=fecha]')[i + 1].select();
					}
					else {
						$$('input[id=fecha]')[0].select();
					}
				}
			}
		});
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aplicar').addEvent('click', AplicarCambiosTransito);
	
	$$('input[id=fecha]')[0].select();
}

var AplicarCambiosTransito = function() {
	var el = $$('input[id=fecha]');
	
	for (i = 0; i < el.length; i++) {
		if (el[i].get('value') != '') {
			var pieces = el[i].get('value').split('/'),
				fecha = new Date(pieces[2].toInt(10), (pieces[1].toInt(10) - 1), pieces[0].toInt(10)),
				data = JSON.decode($$('input[id=datos][cia=' + $('num_cia').get('value') + '][dia=' + pieces[0].toInt(10) + ']')[0].get('value'));
				
				if (!$chk(periodo1) && fecha > periodo2) {
					alert('No puede desplazar la fecha de facturación después del día ' + periodo2.getDate() + ' del mes');
					
					el[i].select();
					
					return false;
				}
				else if (!$chk(periodo1) && data.depositos > 0) {
					alert('El día al que quiere mover la factura ya ha sido generado');
					
					el[i].select();
					
					return false;
				}
				else if (fecha < periodo1 || fecha > periodo2) {
					alert('Solo puede desplazar la fecha de facturación entre los días ' + periodo1.getDate() + ' y ' + periodo2.getDate() + ' del mes');
					
					el[i].select();
					
					return false;
				}
		}
		else {
			alert('Debe especificar la fecha de facturación');
			
			el[i].select();
			
			return false;
		}
	}
	
	var queryString = [];
	
	$('Transito').getElements('input').each(function(el) {
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
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=actualizarCambiosTransito&' + queryString.join('&'),
		'onRequest': function() {
		},
		'onSuccess': function() {
			popup.Close();
			
			Consultar.run(param);
		}
	}).send();
}

var ModificarFacturasClientes = function() {
	var data = JSON.decode(arguments[0]);
	
	new Request({
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=modificarFacturasClientes&num_cia=' + data.num_cia + '&anio=' + data.anio + '&mes=' + data.mes + '&dia=' + data.dia,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			var periodo_pieces = $$('input[id=periodo][cia=' + data.num_cia + ']')[0].get('value').split('|'),
				date_pieces = '';
			
			if (periodo_pieces[0] != '') {
				date_pieces = periodo_pieces[0].split('/');
				
				periodo1 = new Date(date_pieces[2].toInt(10), (date_pieces[1].toInt(10) - 1), date_pieces[0].toInt(10));
			}
			else {
				periodo1 = null;
			}
			
			if (periodo_pieces[1] != '') {
				date_pieces = periodo_pieces[1].split('/');
				
				periodo2 = new Date(/*date_pieces[2].toInt(10), (date_pieces[1].toInt(10) - 1), date_pieces[0].toInt(10)*/);
			}
			else {
				periodo2 = null;
			}
			
			popup = new Popup(result, 'Facturas de clientes del ' + data.dia + '/' + data.mes + '/' + data.anio, 1000, 500, popupOpenClientes, null, {scrollBars: true});
		}
	}).send();
}

var popupOpenClientes = function() {
	new FormValidator($('Clientes'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Clientes'));
	
	$$('input[id=fecha_pago]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i < $$('input[id=fecha_pago]').length - 1) {
						$$('input[id=fecha_pago]')[i + 1].select();
					}
					else {
						$$('input[id=fecha_pago]')[0].select();
					}
				}
			}
		});
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aplicar').addEvent('click', AplicarCambiosClientes);
	
	$$('input[id=fecha_pago]')[0].select();
}

var AplicarCambiosClientes = function() {
	var el = $$('input[id=fecha_pago]');
	
	for (i = 0; i < el.length; i++) {
		if (el.get('value') != '') {
			var pieces = el[i].get('value').split('/'),
				fecha = new Date(pieces[2].toInt(10), (pieces[1].toInt(10) - 1), pieces[0].toInt(10));
				
				if (fecha < periodo1 || fecha > periodo2) {
					alert('Solo puede desplazar la fecha de facturación entre los días ' + periodo1.getDate() + ' y ' + periodo2.getDate() + ' del mes');
					
					el[i].select();
					
					return false;
				}
		}
		else {
			alert('Debe especificar la fecha de pago');
			
			el.select();
			
			return false;
		}
	}
	
	var queryString = [];
	
	$('Clientes').getElements('input').each(function(el) {
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
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=actualizarCambiosClientes&' + queryString.join('&'),
		'onRequest': function() {
		},
		'onSuccess': function() {
			popup.Close();
			
			Consultar.run(param);
		}
	}).send();
}

var ImponerEfectivo = function() {
	var cia = arguments[0],
		dia = arguments[1];
	
	new Request({
		'url': 'FacturasElectronicasVentaDiaria.php',
		'data': 'accion=imponerEfectivo&num_cia=' + cia + '&dia=' + dia,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			popup = new Popup(result, 'Imponer efectivo', 300, 150, popupOpenImponerEfectivo, null, {scrollBars: true});
		}
	}).send();
}

var popupOpenImponerEfectivo = function() {
	new FormValidator($('Efectivo'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Efectivo'));
	
	$('efectivo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
			}
		}
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('imponer').addEvent('click', AplicarEfectivo);
	
	$('efectivo').select();
}

var AplicarEfectivo = function() {
	var num_cia = $('num_cia').get('value').toInt(),
		dia = $('dia').get('value').toInt(),
		porcentajes = $('porcentajes').get('value') != '' ? JSON.decode($('porcentajes').get('value')) : null,
		efectivo = $('efectivo').get('value').getNumericValue();
	
	if ($chk(porcentajes)) {
		var sucursales = 0,
			importe = 0,
			data,
			input,
			a;
		
		porcentajes.each(function(por) {
			if (por.tipo == 1) {
				importe = (efectivo * por.porcentaje / 100).round(2);
				
				sucursales += importe;
			}
			else {
				importe = efectivo - sucursales;
			}
			
			data = JSON.decode($$('input[id=datos][cia=' + por.num_cia + '][dia=' + dia + ']')[0].get('value'));
			input = $$('input[id=datos][cia=' + por.num_cia + '][dia=' + dia + ']')[0];
			a = $('dep-' + por.num_cia + '-' + dia);
			
			data.depositos = importe;
			
			input.set({
				'value': JSON.encode(data),
				'checked': data.depositos > 0 ? true : false
			});
			
			a.set('html', data.depositos > 0 ? '<span style="float:left;">*&nbsp;</span>' + data.depositos.numberFormat(2, '.', ',') : '----------').removeClass(data.depositos > 0 ? 'blue' : 'aqua').addClass(data.depositos > 0 ? 'aqua' : 'blue');
			
			CalcularFacturaDia.run([input, input.get('index').toInt()]);
		});
	}
	else {
		var data = JSON.decode($$('input[id=datos][cia=' + num_cia + '][dia=' + dia + ']')[0].get('value')),
			input = $$('input[id=datos][cia=' + num_cia + '][dia=' + dia + ']')[0],
			a = $('dep-' + num_cia + '-' + dia);
		
		data.depositos = $('efectivo').get('value').getNumericValue();
		
		input.set({
			'value': JSON.encode(data),
			'checked': data.depositos > 0 ? true : false
		});
		
		a.set('text', data.depositos > 0 ? $('efectivo').get('value') : '----------').removeClass(data.depositos > 0 ? 'blue' : 'aqua').addClass(data.depositos > 0 ? 'aqua' : 'blue');;
		
		CalcularFacturaDia.run([input, input.get('index').toInt()]);
	}
	
	popup.Close();
}

var CheckBlock = function() {
	var cia = arguments[0].get('cia'),
		checked = arguments[0].get('checked');
	
	if (!checked) {
		$$('input[id=datos][cia=' + cia + ']').filter(function(el) {
			return !el.get('disabled');
		}).reverse().each(function(el) {
			el.set('checked', false);
			el.fireEvent('click');
		});
	}
	else {
		$$('input[id=datos][cia=' + cia + ']').filter(function(el) {
			return !el.get('disabled');
		}).each(function(el) {
			el.set('checked', true);
			el.fireEvent('click');
		});
	}
}

var Generar = function() {
	/*if ($$('input[id=datos]:checked').length == 0) {
		alert('Debe seleccionar al menos un día');
	}
	else */if (confirm('¿Desea generar las facturas electrónicas de los días seleccionados?')) {
		new Request({
			'url': 'FacturasElectronicasVentaDiaria.php',
			'data': 'accion=generar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'html': ' Generando facturas electr&oacute;nicas, por favor espere a que el proceso termine...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result == '') {
					Inicio();
				}
				else if (result == '-1') {
					alert('Error al conectar al servidor de CFD');
					
					Inicio();
				}
				else {
					$('captura').empty().set('html', result);
					
					$('terminar').addEvent('click', Inicio);
				}
			}
		}).send();
	}
}
