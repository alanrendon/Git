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
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'html': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('arrendadores').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('arrendatarios').focus();
					}
				}
			});
			
			$('arrendatarios').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('anios').focus();
					}
				}
			});
			
			$('anios').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('recibos').focus();
					}
				}
			});
			
			$('recibos').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('arrendadores').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('arrendadores').select();
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
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'html': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('checkall').addEvent('change', CheckAll.pass($('checkall')));
				
				$$('input[id=checkarrendador]').each(function(el) {
					el.store('arrendador', el.get('value')).removeProperty('value').addEvent('change', CheckArrendador.pass(el));
				});
				
				$$('img[id=visualizar]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': visualizarFactura.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=imprimir]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': imprimirFactura.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=descargar]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': descargarFactura.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=email]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': emailFactura.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=reimpresion][src!=/lecaroz/iconos/refresh_gray.png]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': reimprimirFactura.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=cancelar][src!=/lecaroz/iconos/cancel_round_gray.png]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': motivoCancelacion.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$('reporte').addEvent('click', reporte);
				
				$('regresar').addEvent('click', Inicio);
				
				$('imprimir_seleccion').addEvent('click', imprimirSeleccion);
				
				$('descargar_seleccion').addEvent('click', descargarSeleccion);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var CheckAll = function() {
	var checkbox = arguments[0];
	
	$$('input[id=checkarrendador]').set('checked', checkbox.get('checked'));
	$$('input[id=id]').set('checked', checkbox.get('checked'));
}

var CheckArrendador = function() {
	var arrendador = arguments[0].retrieve('arrendador'),
		checkbox = arguments[0];
	
	$$('input[id=id][arrendador=' + arrendador + ']').set('checked', checkbox.get('checked'));
}

var visualizarFactura = function() {
	var url,
		data,
		win,
		opt,
		id = arguments[0];
	
	url = 'RentasConsulta.php';
	data = '?accion=visualizar&id=' + id;
	opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	win = window.open(url + data, 'CFD', opt);
	
	win.focus();
}

var imprimirFactura = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=imprimir&id=' + id,
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Imprimiendo factura...', '<img src="iconos/printer.png" /> Imprimiendo', 100, 50, null, null);
		},
		'onSuccess': function() {
			popup.Close();
		}
	}).send();
}

var descargarFactura = function() {
	var url,
		data,
		win,
		opt,
		id = arguments[0];
	
	url = 'obtenerCFD.php';
	data = '?renta=1&id[]=' + id;
	opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
	
	win = window.open(url + data, 'CFDdownload', opt);
}

var emailFactura = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=email&id=' + id,
		'onRequest': function() {
			
		},
		'onSuccess': function(result) {
			popup = new Popup(result, '<img src="iconos/envelope.png" /> Correo electr&oacute;nico', 450, 200, popupEmail, null);
		}
	}).send();
}

var popupEmail = function() {
	validator = new FormValidator($('EmailForm'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('EmailForm'));
	
	$$('input[id=email]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					if (i < $$('input[id=email]').length - 1) {
						$$('input[id=email]')[i + 1].focus();
					}
					else {
						$$('input[id=email]')[0].focus();
					}
				}
			}
		})
	});
	
	$('cancelar_email').addEvent('click', function() {
		popup.Close();
	});
	
	$('enviar_email').addEvent('click', function() {
		new Request({
			'url': 'RentasConsulta.php',
			'data': 'accion=enviarEmail&' + $('EmailForm').toQueryString(),
			'onRequest': function() {
				popup.Close();
				
				popup = new Popup('<img src="imagenes/_loading.gif" /> Enviado comprobante a destinatarios...', '<img src="iconos/envelope.png" /> Correo electr&oacute;nico', 250, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				popup = new Popup(result, '<img src="iconos/envelope.png" /> Correo electr&oacute;nico', 250, 150, popupEmailClose, null);
			}
		}).send();
	});
	
	$$('input[id=email]')[0].select();
}

var popupEmailClose = function() {
	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}

var motivoCancelacion = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=motivoCancelacion',
		'onRequest': function() {
		},
		'onSuccess': function(content) {
			popup = new Popup(content, 'Recibo de arrendamiento', 500, 200, function() {
				validator = new FormValidator($('motivo'), {
					showErrors: true,
					selectOnFocus: true
				});
				
				styles = new FormStyles($('motivo'));
				
				$('popup_cancelar').addEvent('click', function() {
					popup.Close();
				});
				
				$('popup_aceptar').addEvent('click', function() {
					if ($('motivo_cancelacion').get('value') != '') {
						cancelarFactura.run([id, $('motivo_cancelacion')]);
					}
					else {
						alert('Debe especificar el motivo por el cual se esta cancelando el recibo de arrendamiento');
						
						$('motivo_cancelacion').focus();
					}
				});
				
				$('motivo_cancelacion').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();
							
							this.blur();
							this.focus();
						}
					}
				}).focus();
				
			}, null);
		}
	}).send();
}

var cancelarFactura = function() {
	var id = arguments[0],
		motivo = arguments[1].get('value');
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=cancelar&id=' + id + '&motivo=' + motivo,
		'onRequest': function() {
			popup.Close();
		},
		'onSuccess': function(result) {
			data = JSON.decode(result);
			
			if (data.status < 1) {
				content = '<div id="error" align="center" style="color:#C00;font-size:12pt;font-weight:bold;">' + data.error + '</div>';
			}
			else {
				$('row' + id).removeClass('linea_off').removeClass('linea_on').addClass('cancelada');
				
				content = '<div align="center" style="font-size:12pt;font-weight:bold;">Factura cancelada</div>';
			}
			
			content += '<div id="ok" align="center" style="margin-top:20px;"><input name="cerrar" type="button" id="cerrar" value="Cerrar" /></div>';
			
			popup = new Popup(content, 'Recibo de arrendamiento', 500, 200, function() {
				$('cerrar').addEvent('click', function() {
					popup.Close();
				});
			}, null);
			
			$('row' + id).removeClass('linea_off').removeClass('linea_on').addClass('linea_red');
			$('row' + id).getElement('img[id=cancelar]').set('src', '/lecaroz/iconos/cancel_round_gray.png').removeEvents();
		}
	}).send();
}

var reimprimirFactura = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'RentasConsulta.php',
		'data': 'accion=reimpresion&id=' + id,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Solicitando datos para reimpresi&oacute;n...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result == '-1') {
				alert('No tiene autorización para reimprimir recibos de arrendamiento');
				
				Consultar(param);
			}
			else {
				$('captura').empty().set('html', result);
				
				validator = new FormValidator($('Datos'), {
					showErrors: true,
					selectOnFocus: true
				});
				
				styles = new FormStyles($('Datos'));
				
				$('anio').addEvents({
					'change': function() {
						$('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL MES DE', 0) >= 0) ? 'RENTA DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_renta').get('value'));
						$('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL MES DE', 0) >= 0) ? 'MANTENIMIENTO DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_mantenimiento').get('value'));
					},
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();
							
							$('concepto_renta').focus();
						}
					}
				});
				
				$('mes').addEvent('change', function() {
					$('concepto_renta').set('value', $('renta').get('value').getNumericValue() != 0 && ($('concepto_renta').get('value') == '' || $('concepto_renta').get('value').indexOf('RENTA DEL MES DE', 0) >= 0) ? 'RENTA DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_renta').get('value'));
					$('concepto_mantenimiento').set('value', $('mantenimiento').get('value').getNumericValue() != 0 && ($('concepto_mantenimiento').get('value') == '' || $('concepto_mantenimiento').get('value').indexOf('MANTENIMIENTO DEL MES DE', 0) >= 0) ? 'MANTENIMIENTO DEL MES DE ' + meses[$('mes').get('value').getNumericValue() - 1] + ' DE ' + $('anio').get('value') : $('concepto_mantenimiento').get('value'));
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
							
							$('motivo_cancelacion').focus();
						}
					}
				});
				
				$('motivo_cancelacion').addEvents({
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
				$('aplicar_retenciones').addEvent('change', calcularTotal);
				
				$('cancelar').addEvent('click', Consultar.pass(param));
				
				$('reimprimir').addEvent('click', Reimprimir);
				
				$('anio').select();
			}
		}
	}).send();
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
	retencion_iva = $('aplicar_retenciones').get('checked') ? ((renta * 0.10666666667) + (mantenimiento * 0.10666666667)).round(2) : 0;
	retencion_isr = $('aplicar_retenciones').get('checked') ? ((renta * 0.10) + (mantenimiento * 0.10)).round(2) : 0;
	total = subtotal + iva + agua - retencion_iva - retencion_isr;
	
	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('iva_renta').set('value', iva_renta > 0 ? iva_renta.numberFormat(2, '.', '') : '');
	$('iva_mantenimiento').set('value', iva_mantenimiento > 0 ? iva_mantenimiento.numberFormat(2, '.', '') : '');
	$('retencion_iva').set('value', retencion_iva > 0 ? retencion_iva.numberFormat(2, '.', ',') : '');
	$('retencion_isr').set('value', retencion_iva > 0 ? retencion_isr.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var Reimprimir = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
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
	else if (confirm('El recibo de arrendamiento original se cancelara y será reemplazada por un nuevo recibo, ¿Desea continuar?')) {
		var queryString = [];
		
		$('Datos').getElements('input, select, textarea').each(function(el) {
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
			'url': 'RentasConsulta.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Generando CFD...', 'Recibo de arrendamiento', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				 
				popup = new Popup(result, 'Recibo de arrendamiento', 500, 200, popupOpen, null);
			}
		}).send();
	}
}

var popupOpen = function() {
	$('cerrar').addEvent('click', function() {
		if (!$chk($('error'))) {
			Consultar(param);
		}
		
		popup.Close();
	});
}

var reporte = function() {
	var url,
		data,
		win,
		opt,
		id = arguments[0];
	
	url = 'RentasConsulta.php';
	data = '?accion=reporte&' + param;
	opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	win = window.open(url + data, 'reporteCFD', opt);
	
	win.focus();
}

var imprimirSeleccion = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else {
		new Request({
			'url': 'RentasConsulta.php',
			'data': 'accion=imprimirSeleccion&' + $$('input[id=id]:checked').get('value').map(function(id) { return 'id[]=' + id; }).join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Imprimiendo selecci&oacute;n...', '<img src="iconos/printer.png" /> Imprimiendo', 150, 50, null, null);
			},
			'onSuccess': function() {
				popup.Close();
			}
		}).send();
	}
}

var descargarSeleccion = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else {
		var url,
			data,
			win,
			opt;
		
		url = 'obtenerCFD.php';
		data = '?renta=1&' + $$('input[id=id]:checked').get('value').map(function(value) {
			return 'id[]=' + value;
		}).join('&'),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
		
		win = window.open(url + data, 'CFDdownload', opt);
	}
}
