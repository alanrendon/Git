// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'FacturasElectronicasConsulta.php',
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

			if ($('cias').get('tag') == 'select') {
				$('fecha1').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('fecha2').select();
						}
					}
				});

				$('fecha2').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('folios').select();
						}
					}
				});

				$('folios').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('fecha1').select();
						}
					}
				});

				$('consultar').addEvent('click', Consultar);

				new Calendar(
					{
						'fecha1': 'd/m/Y',
						'fecha2': 'd/m/Y'
					},
					{
						'days': ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
						'months': ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
					}
				);
			}
			else {
				$('cias').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('fecha1').select();
						}
					}
				});

				$('fecha1').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('fecha2').select();
						}
					}
				});

				$('fecha2').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('folios').select();
						}
					}
				});

				$('folios').addEvents({
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
		'url': 'FacturasElectronicasConsulta.php',
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

				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});

				$('checkall').addEvent('change', checkAll.pass($('checkall')));

				$$('input[id=checkemisor]').each(function(el) {
					el.store('emisor', el.get('value')).removeProperty('value').addEvent('change', checkEmisor.pass(el));
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

				$$('img[id=reimpresion][src!=iconos/refresh_gray.png]').each(function(el) {
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

				$$('img[id=cancelar][src!=iconos/cancel_round_gray.png]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': cancelarFactura.pass(el.get('alt'))
					});

					el.removeProperty('alt');
				});

				$('imprimir_seleccion').addEvent('click', imprimirSeleccion);

				$('descargar_seleccion').addEvent('click', descargarSeleccion);

				$('reporte_seleccion').addEvent('click', reporteSeleccion);

				$('csv_seleccion').addEvent('click', csvSeleccion);

				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');

				Inicio();
			}
		}
	}).send();
}

var checkAll = function() {
	var checkbox = arguments[0];

	$$('input[id=checkemisor]').set('checked', checkbox.get('checked'));
	$$('input[id=id]').set('checked', checkbox.get('checked'));
}

var checkEmisor = function() {
	var emisor = arguments[0].retrieve('emisor'),
		checkbox = arguments[0];

	$$('input[id=id][emisor=' + emisor + ']').set('checked', checkbox.get('checked'));
}

var visualizarFactura = function() {
	var url,
		data,
		win,
		opt,
		id = arguments[0];

	url = 'FacturasElectronicasConsulta.php';
	data = '?accion=visualizar&id=' + id;
	opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';

	win = window.open(url + data, 'CFD', opt);

	win.focus();
}

var imprimirFactura = function() {
	var id = arguments[0];

	new Request({
		'url': 'FacturasElectronicasConsulta.php',
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
	data = '?id[]=' + id;
	opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';

	win = window.open(url + data, 'CFDdownload', opt);
}

var emailFactura = function() {
	var id = arguments[0];

	new Request({
		'url': 'FacturasElectronicasConsulta.php',
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
			'url': 'FacturasElectronicasConsulta.php',
			'data': 'accion=enviarEmail&' + $('EmailForm').toQueryString(),
			'onRequest': function() {
				popup.Close();

				popup = new Popup('<img src="imagenes/_loading.gif" /> Enviado comprobante a destinatarios...', '<img src="iconos/envelope.png" /> Correo electr&oacute;nico', 250, 100, /*popupEmail*/null, null);
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

var reimprimirFactura = function() {
	var id = arguments[0];

	new Request({
		'url': 'FacturasElectronicasConsulta.php',
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
				alert('No tiene autorización para borrar facturas electrónicas');

				Consultar(param);
			}
			else {
				$('captura').empty().set('html', result);

				validator = new FormValidator($('Datos'), {
					showErrors: true,
					selectOnFocus: true
				});

				styles = new FormStyles($('Datos'));

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

							$('nombre_consignatario').focus();
						}
					}
				});

				$('nombre_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('rfc_consignatario').focus();
						}
					}
				});

				$('rfc_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('calle_consignatario').focus();
						}
					}
				});

				$('calle_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('no_exterior_consignatario').focus();
						}
					}
				});

				$('no_exterior_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('no_interior_consignatario').focus();
						}
					}
				});

				$('no_interior_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('colonia_consignatario').focus();
						}
					}
				});

				$('colonia_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('localidad_consignatario').focus();
						}
					}
				});

				$('localidad_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('referencia_consignatario').focus();
						}
					}
				});

				$('referencia_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('municipio_consignatario').focus();
						}
					}
				});

				$('municipio_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('estado_consignatario').focus();
						}
					}
				});

				$('estado_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('pais_consignatario').focus();
						}
					}
				});

				$('pais_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('codigo_postal_consignatario').focus();
						}
					}
				});

				$('codigo_postal_consignatario').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
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

							$$('input[id=numero_pedimento]')[i].select();
						}
					});
				});

				$$('input[id=numero_pedimento]').each(function(el, i) {
					el.addEvents({
						'keydown': function(e) {
							if (e.key == 'enter') {
								e.stop();

								$$('input[id=fecha_entrada]')[i].select();
							}
						}
					});
				});

				$$('input[id=fecha_entrada]').each(function(el, i) {
					el.addEvents({
						'keydown': function(e) {
							if (e.key == 'enter') {
								e.stop();

								$$('input[id=aduana_entrada]')[i].select();
							}
						}
					});
				});

				$$('input[id=aduana_entrada]').each(function(el, i) {
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

				$('porcentaje_descuento').addEvents({
					'change': calcularTotal,
					'keydown': function(e) {
						if (e.key == 'enter') {
							this.blur();
						}
					}
				});

				$('aplicar_iva').addEvent('change', calcularTotal);

				$('cancelar').addEvent('click', Consultar.pass(param));

				$('reimprimir').addEvent('click', Reimprimir);

				$('nombre_cliente').select();
			}
		}
	}).send();
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
		porcentaje_descuento = $('porcentaje_descuento').get('value').getNumericValue(),
		descuento = 0,
		iva = 0,
		total = 0;

	$$('input[id=importe]').each(function(el) {
		subtotal += el.get('value').getNumericValue();
	});

	descuento = (subtotal * porcentaje_descuento / 100).round(2);

	iva = $('aplicar_iva').get('checked') ? ((subtotal - descuento) * 0.16).round(2) : 0;

	total = subtotal - descuento + iva;

	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('descuento').set('value', descuento > 0 ? descuento.numberFormat(2, '.', ',') : '');
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

	var td6 = new Element('td', {
		'align': 'center'
	});

	var td7 = new Element('td', {
		'align': 'center'
	});

	var td8 = new Element('td', {
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

			$$('input[id=numero_pedimento]')[i].select();
		}
	}).inject(td4);

	var numero_pedimento = new Element('input', {
		'id': 'numero_pedimento',
		'name': 'numero_pedimento[]',
		'type': 'text',
		'class': 'valid Focus onlyNumbers',
		'size': 15,
		'maxlength': 15
	}).addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$$('input[id=fecha_entrada]')[i].select();
			}
		}
	}).inject(td5);

	var fecha_entrada = new Element('input', {
		'id': 'fecha_entrada',
		'name': 'fecha_entrada[]',
		'type': 'text',
		'class': 'valid Focus toDate',
		'size': 10,
		'maxlength': 10
	}).addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$$('input[id=aduana_entrada]')[i].select();
			}
		}
	}).inject(td6);

	var aduana_entrada = new Element('input', {
		'id': 'aduana_entrada',
		'name': 'aduana_entrada[]',
		'type': 'text',
		'class': 'valid toText cleanText toUpper',
		'size': 30,
		'maxlength': 100
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();

			if (!$chk($$('input[id=descripcion]')[i + 1])) {
				newRow(i + 1);
			}

			$$('input[id=descripcion]')[i + 1].select();
		}
	}).inject(td7);

	var importe = new Element('input', {
		'id': 'importe',
		'name': 'importe[]',
		'type': 'text',
		'class': 'right',
		'size': 10,
		'readonly': true
	}).inject(td8);

	validator.addElementEvents(descripcion);
	validator.addElementEvents(cantidad);
	validator.addElementEvents(precio);
	validator.addElementEvents(unidad);
	validator.addElementEvents(numero_pedimento);
	validator.addElementEvents(fecha_entrada);
	validator.addElementEvents(aduana_entrada);

	styles.addElementEvents(descripcion);
	styles.addElementEvents(cantidad);
	styles.addElementEvents(precio);
	styles.addElementEvents(unidad);
	styles.addElementEvents(numero_pedimento);
	styles.addElementEvents(fecha_entrada);
	styles.addElementEvents(aduana_entrada);

	td1.inject(tr);
	td2.inject(tr);
	td3.inject(tr);
	td4.inject(tr);
	td5.inject(tr);
	td6.inject(tr);
	td7.inject(tr);
	td8.inject(tr);

	tr.inject($('Conceptos'));
}

var Reimprimir = function() {
	if ($('nombre_cliente').get('value') == '') {
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
		alert('El texto en observaciones no puede ser mayor a 100 caracteres');
		$('observaciones').focus();
	}
	else if (confirm('La factura original se cancelara y será reemplazada por una nueva factura, ¿Desea continuar?')) {
		var queryString = [];

		$('Datos').getElements('input, textarea, select').each(function(el) {
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
			'url': 'FacturasElectronicasConsulta.php',
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
			Consultar(param);
		}

		popup.Close();
	});
}

var cancelarFactura = function() {
	var id = arguments[0];

	if (confirm('Desea cancelar la factura')) {
		new Request({
			'url': 'FacturasElectronicasConsulta.php',
			'data': 'accion=cancelar&id=' + id,
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Cancelando factura...', '<img src="iconos/clock.png" /> Cancelando factura', 150, 80, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();

				data = JSON.decode(result);

				if (data.status < 1) {
					content = '<div id="error" align="center" style="color:#C00;font-size:12pt;font-weight:bold;">' + data.error + '</div>';
				}
				else {
					$('row_' + id).removeClass('linea_off').removeClass('linea_on').addClass('cancelada');

					content = '<div align="center" style="font-size:12pt;font-weight:bold;">Factura cancelada</div>';
				}

				content += '<div id="ok" align="center" style="margin-top:20px;"><input name="cerrar" type="button" id="cerrar" value="Cerrar" /></div>';

				popup = new Popup(content, 'Facturas Electr&oacute;nicas', 500, 200, function() {
						$('cerrar').addEvent('click', function() {
							popup.Close();
						});
					}, null);
			}
		}).send();
	}
}

var imprimirSeleccion = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe selccionar al menos un registro');
	}
	else {
		new Request({
			'url': 'FacturasElectronicasConsulta.php',
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
		alert('Debe selccionar al menos un registro');
	}
	else {
		var url,
			data,
			win,
			opt,
			id = arguments[0];

		url = 'obtenerCFD.php';
		data = '?' + $$('input[id=id]:checked').get('value').map(function(id) { return 'id[]=' + id; }).join('&');
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';

		win = window.open(url + data, 'descargaCFD', opt);
	}
}

var reporteSeleccion = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe selccionar al menos un registro');
	}
	else {
		var url,
			data,
			win,
			opt,
			id = arguments[0];

		url = 'FacturasElectronicasConsulta.php';
		data = '?accion=reporte&' + $$('input[id=id]:checked').get('value').map(function(id) { return 'id[]=' + id; }).join('&');
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';

		win = window.open(url + data, 'reporteCFD', opt);

		win.focus();
	}
}

var csvSeleccion = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else {
		var url,
			data,
			win,
			opt,
			id = arguments[0];

		url = 'FacturasElectronicasConsulta.php';
		data = '?accion=csv&' + $$('input[id=id]:checked').get('value').map(function(id) { return 'id[]=' + id; }).join('&');
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';

		win = window.open(url + data, 'descargaCFD', opt);
	}
}
