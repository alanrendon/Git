// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'CatalogoArrendatarios.php',
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

			$('arrendadores').addEvents({
				'change': obtenerArrendatarios,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
						this.select();
					}
				}
			});

			$$('[id=bloque]').addEvent('change', obtenerArrendatarios);

			$$('[id=categoria]').addEvent('change', obtenerArrendatarios);

			$('consultar').addEvent('click', Consultar);

			$('arrendadores').select();
		}
	}).send();
}

var obtenerArrendatarios = function() {
	if ($('arrendadores').get('value') != '' && $$('[id=bloque]').get('checked').some(function(checked) { return checked; })) {
		var queryString = [];

		$('Datos').getElements('input, #categoria').each(function(el) {
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
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=obtenerArrendatarios&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var arrendatarios = JSON.decode(result);

					updSelect($('arrendatarios'), arrendatarios);

					$each($('arrendatarios').options, function(op) {
						op.set('selected', true);
					});
				}
				else {
					updSelect($('arrendatarios'), []);
				}
			}
		}).send();
	}
	else {
		updSelect($('arrendatarios'), []);
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

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}

	new Request({
		'url': 'CatalogoArrendatarios.php',
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

				$('alta').addEvent('click', Alta);

				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});

				$$('img[id=alta_inm]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': Alta.pass(el.get('alt'))
					});
				});

				$$('img[id=mod][src!=/lecaroz/imagenes/pencil16x16_gray.png]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': Modificar.pass(el.get('alt'))
					});

					el.removeProperty('alt');
				});

				$$('img[id=baja][src!=/lecaroz/iconos/cancel_round_gray.png]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': Baja.pass(el.get('alt'))
					});

					el.removeProperty('alt');
				});

				s = new Sortables('tbody[id^=bloque_]', {
					'constrain': true,
					//'clone': true,
					'revert':true,
					'opacity': 0.5,
					'handle': 'td.dragme',
					'onComplete': function(el) {
						$$('tbody[id^=bloque_' + el.getParent('tbody').get('index') + ']')[0].getElements('tr').each(function(row, i) {
							row.removeClass('linea_off').removeClass('linea_on').addClass('linea_' + (i % 2 == 0 ? 'off' : 'on'));
						});

						ActualizarOrden(this.serialize(el.getParent('tbody').get('index').getNumericValue(), function(el, i) {
							return 'orden[]=' + JSON.encode({
								'id': el.getProperty('id').replace('row', '').getNumericValue(),
								'orden': i + 1
							});
						}).join('&'));

						/*console.log(el.getParent('tbody').get('id'), this.serialize(el.getParent('tbody').get('index').getNumericValue(), function(el, i) {
							return 'orden[]=' + JSON.encode({
								'id': el.getProperty('id').replace('row', '').getNumericValue(),
								'orden': i + 1
							});
						}).join('&'));*/
					}
				});

				$('regresar').addEvent('click', Inicio);

				$('reporte').addEvent('click', Reporte);
			}
			else {
				alert('No hay resultados');

				Inicio();
			}
		}
	}).send();
}

var ActualizarOrden = function(orden) {
	new Request({
		url: 'CatalogoArrendatarios.php',
		data: 'accion=orden&' + orden,
		onRequest: function() {},
		onSuccess: function(result) {}
	}).send();
}

var Reporte = function() {
	var url = 'CatalogoArrendatarios.php',
		_param = '?accion=reporte&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;

	win = window.open(url + _param, 'reporte_arrendatarios', opt);

	win.focus();
}

var Alta = function() {
	var inmobiliaria = $chk(arguments[0]) ? arguments[0] : 0;

	new Request({
		'url': 'CatalogoArrendatarios.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);

			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});

			new FormStyles($('Datos'));

			$('arrendador').addEvents({
				'change': obtenerArrendador,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('alias_arrendatario').select();
					}
				}
			}).set('value', inmobiliaria).fireEvent('change');

			$('idlocal').addEvent('change', obtenerDatosLocal);

			$('alias_arrendatario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre_arrendatario').select();
					}
				}
			});

			$('nombre_arrendatario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('rfc').select();
					}
				}
			});

			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('calle').select();
					}
				}
			});

			$('calle').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('no_exterior').select();
					}
				}
			});

			$('no_exterior').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('no_interior').select();
					}
				}
			});

			$('no_interior').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('colonia').select();
					}
				}
			});

			$('colonia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('municipio').select();
					}
				}
			});

			$('municipio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('estado').select();
					}
				}
			});

			$('estado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('pais').select();
					}
				}
			});

			$('pais').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('codigo_postal').select();
					}
				}
			});

			$('codigo_postal').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('contacto').select();
					}
				}
			});

			$('contacto').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('telefono1').select();
					}
				}
			});

			$('telefono1').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('telefono2').select();
					}
				}
			});

			$('telefono2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email').select();
					}
				}
			});

			$('email').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email2').select();
					}
				}
			});

			$('email2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email3').select();
					}
				}
			});

			$('email3').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('giro').select();
					}
				}
			});

			$('giro').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('representante').select();
					}
				}
			});

			$('representante').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fianza').select();
					}
				}
			});

			$('fianza').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('tipo_fianza').select();
					}
				}
			});

			$('tipo_fianza').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fecha_inicio').select();
					}
				}
			});

			$('fecha_inicio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fecha_termino').select();
					}
				}
			});

			$('fecha_termino').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('deposito_garantia').select();
					}
				}
			});

			$('deposito_garantia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('cuenta_pago').select();
					}
				}
			});

			$('cuenta_pago').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('porcentaje_incremento').select();
					}
				}
			});

			$('porcentaje_incremento').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('renta').select();
					}
				}
			});

			$('renta').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('mantenimiento').select();
					}
				}
			});

			$('mantenimiento').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('agua').select();
					}
				}
			});

			$('agua').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('arrendador').select();
					}
				}
			});

			$('aplicar_retenciones').addEvent('change', calcularTotalRenta);

			$('regresar').addEvent('click', Consultar.pass(param));

			$('alta').addEvent('click', doAlta);

			$('arrendador').select();
		}
	}).send();
}

var obtenerArrendador = function() {
	if ($('arrendador').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=obtenerArrendador&arrendador=' + $('arrendador').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					$('idarrendador').set('value', data.idarrendador);
					$('nombre_arrendador').set('value', data.nombre_arrendador);

					updSelect($('idlocal'), data.locales);

					obtenerDatosLocal.run();
				}
				else {
					alert('La inmobiliaria no se encuentra en el catálogo');

					$('arrendador').set('value', $('arrendador').retrieve('tmp', '')).focus();
				}
			}
		}).send();
	}
	else {
		$('idarrendador').set('value', '');
		$('arrendador').set('value', '');
		$('nombre_arrendador').set('value', '');

		updSelect($('idlocal'), []);

		obtenerDatosLocal.run();
	}
}

var obtenerDatosLocal = function() {
	if ($('idlocal').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=obtenerDatosLocal&id=' + $('idlocal').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);

				$('tipo').set('value', data.tipo_local);
				$('tipo_local').set('html', data.tipo_local == 1 ? 'COMERCIAL' : 'VIVIENDA');
				$('domicilio_local').set('html', data.domicilio);
				$('superficie_local').set('html', data.superficie.getNumericValue().numberFormat(2, '.', ','));

				calcularTotalRenta.run();
			}
		}).send();
	}
	else {
		$('tipo').set('value', '');
		$('tipo_local').set('html', '&nbsp;');
		$('domicilio_local').set('html', '&nbsp;');
		$('superficie_local').set('html', '&nbsp;');

		calcularTotalRenta.run();
	}
}

var calcularTotalRenta = function() {
	var renta = $('renta').get('value').getNumericValue(),
		mantenimiento = $('mantenimiento').get('value').getNumericValue(),
		subtotal = renta + mantenimiento,
		iva = $('tipo').get('value') == '1' ? ((renta * 0.16) + (mantenimiento * 0.16)).round(2) : 0,
		agua = $('agua').get('value').getNumericValue(),
		retencion_iva = $('aplicar_retenciones').get('checked') ? ((renta * 0.10666666667) + (mantenimiento * 0.10666666667)).round(2) : 0,
		retencion_isr = $('aplicar_retenciones').get('checked') ? ((renta * 0.10) + (mantenimiento * 0.10)).round(2) : 0,
		total = subtotal + iva + agua - retencion_iva - retencion_isr;

	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('retencion_iva').set('value', retencion_iva > 0 ? retencion_iva.numberFormat(2, '.', ',') : '');
	$('retencion_isr').set('value', retencion_isr > 0 ? retencion_isr.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var doAlta = function() {
	if ($('arrendador').get('value').getNumericValue() == 0) {
		alert('Debe especificar la inmobiliaria');

		$('arrendador').select();
	}
	else if ($('idlocal').get('value').getNumericValue() == 0) {
		alert('Debe especificar el local a arrendar');

		$('idlocal').focus();
	}
	else if ($('alias_arrendatario').get('value') == '') {
		alert('Debe especificar un alias para el nuevo arrendatario');

		$('alias_arrendatario').select();
	}
	else if ($('nombre_arrendatario').get('value') == '') {
		alert('Debe especificar el nombre o razón social del arrendatario');

		$('nombre_arrendatario').select();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el rfc del arrendatario');

		$('rfc').select();
	}
	else if ($('calle').get('value') == '') {
		alert('Debe especificar la calle del domicilio fiscal del arrendatario');

		$('calle').select();
	}
	else if ($('colonia').get('value') == '') {
		alert('Debe especificar la colonia del domicilio fiscal del arrendatario');

		$('colonia').select();
	}
	else if ($('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio del domicilio fiscal del arrendatario');

		$('municipio').select();
	}
	else if ($('estado').get('value') == '') {
		alert('Debe especificar el estado del domicilio fiscal del arrendatario');

		$('estado').select();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el país del domicilio fiscal del arrendatario');

		$('pais').select();
	}
	else if ($('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal del domicilio fiscal del arrendatario');

		$('codigo_postal').select();
	}
	else if ($('contacto').get('value') == '') {
		alert('Debe especificar al menos una persona para contaco');

		$('contacto').select();
	}
	else if ($('telefono1').get('value') == '') {
		alert('Debe especificar al menos un teléfono de contacto');

		$('telefono1').select();
	}
	else if ($('fecha_inicio').get('value') == '') {
		alert('Debe especificar la fecha en la que inicia el contrato de arrendamiento');

		$('fecha_inicio').select();
	}
	else if ($('fecha_termino').get('value') == '') {
		alert('Debe especificar la fecha en la que termina el contrato de arrendamiento');

		$('fecha_termino').select();
	}
	else if ($('total').get('value').getNumericValue() == 0) {
		alert('El importe total de la renta debe ser mayor a cero');

		$('renta').select();
	}
	else {
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
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=doAlta&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Registrando nuevo arrendatario...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();

				Consultar.run(param);
			}
		}).send();
	}
}

var Modificar = function() {
	var id = arguments[0];

	new Request({
		'url': 'CatalogoArrendatarios.php',
		'data': 'accion=modificar&id=' + id,
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);

			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});

			new FormStyles($('Datos'));

			$('arrendador').addEvents({
				'change': obtenerArrendador,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('alias_arrendatario').select();
					}
				}
			});

			$('idlocal').addEvent('change', obtenerDatosLocal);

			$('alias_arrendatario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre_arrendatario').select();
					}
				}
			});

			$('nombre_arrendatario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('rfc').select();
					}
				}
			});

			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('calle').select();
					}
				}
			});

			$('calle').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('no_exterior').select();
					}
				}
			});

			$('no_exterior').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('no_interior').select();
					}
				}
			});

			$('no_interior').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('colonia').select();
					}
				}
			});

			$('colonia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('municipio').select();
					}
				}
			});

			$('municipio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('estado').select();
					}
				}
			});

			$('estado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('pais').select();
					}
				}
			});

			$('pais').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('codigo_postal').select();
					}
				}
			});

			$('codigo_postal').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('contacto').select();
					}
				}
			});

			$('contacto').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('telefono1').select();
					}
				}
			});

			$('telefono1').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('telefono2').select();
					}
				}
			});

			$('telefono2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email').select();
					}
				}
			});

			$('email').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email2').select();
					}
				}
			});

			$('email2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email3').select();
					}
				}
			});

			$('email3').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('giro').select();
					}
				}
			});

			$('giro').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('representante').select();
					}
				}
			});

			$('representante').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fianza').select();
					}
				}
			});

			$('fianza').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('tipo_fianza').select();
					}
				}
			});

			$('tipo_fianza').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fecha_inicio').select();
					}
				}
			});

			$('fecha_inicio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('fecha_termino').select();
					}
				}
			});

			$('fecha_termino').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('deposito_garantia').select();
					}
				}
			});

			$('deposito_garantia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('cuenta_pago').select();
					}
				}
			});

			$('cuenta_pago').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('porcentaje_incremento').select();
					}
				}
			});

			$('porcentaje_incremento').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('renta').select();
					}
				}
			});

			$('renta').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('mantenimiento').select();
					}
				}
			});

			$('mantenimiento').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('agua').select();
					}
				}
			});

			$('agua').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('alias_arrendatario').select();
					}
				}
			});

			$('aplicar_retenciones').addEvent('change', calcularTotalRenta);

			$('regresar').addEvent('click', Consultar.pass(param));

			$('modificar').addEvent('click', doModificar);

			$('alias_arrendatario').select();
		}
	}).send();
}

var doModificar = function() {
	if ($('arrendador').get('value').getNumericValue() == 0) {
		alert('Debe especificar la inmobiliaria');

		$('arrendador').select();
	}
	else if ($('idlocal').get('value').getNumericValue() == 0) {
		alert('Debe especificar el local a arrendar');

		$('idlocal').focus();
	}
	else if ($('alias_arrendatario').get('value') == '') {
		alert('Debe especificar un alias para el arrendatario');

		$('alias_arrendatario').select();
	}
	else if ($('nombre_arrendatario').get('value') == '') {
		alert('Debe especificar el nombre o razón social del arrendatario');

		$('nombre_arrendatario').select();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el rfc del arrendatario');

		$('rfc').select();
	}
	else if ($('calle').get('value') == '') {
		alert('Debe especificar la calle del domicilio fiscal del arrendatario');

		$('calle').select();
	}
	else if ($('colonia').get('value') == '') {
		alert('Debe especificar la colonia del domicilio fiscal del arrendatario');

		$('colonia').select();
	}
	else if ($('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio del domicilio fiscal del arrendatario');

		$('municipio').select();
	}
	else if ($('estado').get('value') == '') {
		alert('Debe especificar el estado del domicilio fiscal del arrendatario');

		$('estado').select();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el país del domicilio fiscal del arrendatario');

		$('pais').select();
	}
	else if ($('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal del domicilio fiscal del arrendatario');

		$('codigo_postal').select();
	}
	else if ($('contacto').get('value') == '') {
		alert('Debe especificar al menos una persona para contaco');

		$('contacto').select();
	}
	else if ($('telefono1').get('value') == '') {
		alert('Debe especificar al menos un teléfono de contacto');

		$('telefono1').select();
	}
	else if ($('fecha_inicio').get('value') == '') {
		alert('Debe especificar la fecha en la que inicia el contrato de arrendamiento');

		$('fecha_inicio').select();
	}
	else if ($('fecha_termino').get('value') == '') {
		alert('Debe especificar la fecha en la que termina el contrato de arrendamiento');

		$('fecha_termino').select();
	}
	else if ($('total').get('value').getNumericValue() == 0) {
		alert('El importe total de la renta debe ser mayor a cero');

		$('renta').select();
	}
	else {
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
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=doModificar&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Modificando arrendatario...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();

				Consultar.run(param);
			}
		}).send();
	}
}

var Baja = function() {
	var id = arguments[0];

	if (confirm('¿Desea dar de baja al arrendatario seleccionado?')) {
		new Request({
			'url': 'CatalogoArrendatarios.php',
			'data': 'accion=doBaja&id=' + id,
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Dando de baja al arrendatario...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();

				Consultar.run(param);
			}
		}).send();
	}
}
