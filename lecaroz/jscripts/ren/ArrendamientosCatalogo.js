// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'ArrendamientosCatalogo.php',
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

						this.blur();
						this.select();
					}
				}
			});

			$('consultar').addEvent('click', Consultar);

			$('cias').select();
		}
	}).send();
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
		'url': 'ArrendamientosCatalogo.php',
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

				$$('img[id=alta_arr]').each(function(el) {
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

				$$('img[id=mod]').each(function(el) {
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

				$$('img[id=baja]').each(function(el) {
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

				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');

				Inicio();
			}
		}
	}).send();
}

var Alta = function() {
	var num_cia = $chk(arguments[0]) ? arguments[0] : 0;

	new Request({
		'url': 'ArrendamientosCatalogo.php',
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

			$('num_cia').addEvents({
				'change': obtenerCia,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('alias_arrendamiento').select();
					}
				}
			}).set('value', num_cia).fireEvent('change');

			$('alias_arrendamiento').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre_arrendador').select();
					}
				}
			});

			$('nombre_arrendador').addEvents({
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

						$('curp').select();
					}
				}
			});

			$('curp').addEvents({
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

						$('renta_efectivo').select();
					}
				}
			});

			$('renta_efectivo').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('observaciones').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('num_cia').select();
					}
				}
			});

			$('aplicar_iva').addEvent('change', calcularTotalRenta);
			$('aplicar_retencion_iva').addEvent('change', calcularTotalRenta);
			$('aplicar_retencion_isr').addEvent('change', calcularTotalRenta);

			$('regresar').addEvent('click', Consultar.pass(param));

			$('alta').addEvent('click', doAlta);

			$('num_cia').select();
		}
	}).send();
}

var obtenerCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ArrendamientosCatalogo.php',
			'data': 'accion=obtenerCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');

					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).focus();
				}
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
	}
}

var calcularTotalRenta = function() {
	var renta = $('renta').get('value').getNumericValue(),
		mantenimiento = $('mantenimiento').get('value').getNumericValue(),
		subtotal = renta + mantenimiento,
		iva = $('aplicar_iva').get('checked') ? (subtotal * 0.16).round(2) : 0,
		agua = $('agua').get('value').getNumericValue(),
		retencion_iva = $('aplicar_retencion_iva').get('checked') ? (subtotal * 0.10666666667).round(2) : 0,
		retencion_isr = $('aplicar_retencion_isr').get('checked') ? (subtotal * 0.10).round(2) : 0,
		total = subtotal + iva + agua - retencion_iva - retencion_isr,
		renta_efectivo = $('renta_efectivo').get('value').getNumericValue(),
		gran_total = total + renta_efectivo;

	$('subtotal').set('value', subtotal > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('retencion_iva').set('value', retencion_iva > 0 ? retencion_iva.numberFormat(2, '.', ',') : '');
	$('retencion_isr').set('value', retencion_isr > 0 ? retencion_isr.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
	$('gran_total').set('value', gran_total > 0 ? gran_total.numberFormat(2, '.', ',') : '');
}

var doAlta = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');

		$('num_cia').select();
	}
	else if ($('alias_arrendamiento').get('value') == '') {
		alert('Debe especificar un alias para el nuevo arrendamiento');

		$('alias_arrendamiento').select();
	}
	else if ($('nombre_arrendador').get('value') == '') {
		alert('Debe especificar el nombre o razón social del arrendador');

		$('nombre_arrendador').select();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el rfc del arrendador');

		$('rfc').select();
	}
	else if ($('curp').get('value') == '') {
		alert('Debe especificar el curp del arrendador');

		$('curp').select();
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
		alert('El importe de renta debe ser mayor a cero');

		$('renta').select();
	}
	else {
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
			'url': 'ArrendamientosCatalogo.php',
			'data': 'accion=doAlta&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Registrando nuevo arrendamiento...'
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
		'url': 'ArrendamientosCatalogo.php',
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

			$('alias_arrendamiento').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre_arrendador').select();
					}
				}
			});

			$('nombre_arrendador').addEvents({
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

						$('curp').select();
					}
				}
			});

			$('curp').addEvents({
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

						$('renta_efectivo').select();
					}
				}
			});

			$('renta_efectivo').addEvents({
				'change': calcularTotalRenta,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('observaciones').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('num_cia').select();
					}
				}
			});

			$('aplicar_iva').addEvent('change', calcularTotalRenta);
			$('aplicar_retencion_iva').addEvent('change', calcularTotalRenta);
			$('aplicar_retencion_isr').addEvent('change', calcularTotalRenta);

			$('regresar').addEvent('click', Consultar.pass(param));

			$('modificar').addEvent('click', doModificar);

			$('alias_arrendamiento').select();
		}
	}).send();
}

var doModificar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');

		$('num_cia').select();
	}
	else if ($('alias_arrendamiento').get('value') == '') {
		alert('Debe especificar un alias para el nuevo arrendamiento');

		$('alias_arrendamiento').select();
	}
	else if ($('nombre_arrendador').get('value') == '') {
		alert('Debe especificar el nombre o razón social del arrendador');

		$('nombre_arrendador').select();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el rfc del arrendador');

		$('rfc').select();
	}
	else if ($('curp').get('value') == '') {
		alert('Debe especificar el curp del arrendador');

		$('curp').select();
	}
	else if ($('fecha_inicio').get('value') == '') {
		alert('Debe especificar la fecha en la que inicia el contrato de arrendamiento');

		$('fecha_inicio').select();
	}
	else if ($('fecha_termino').get('value') == '') {
		alert('Debe especificar la fecha en la que termina el contrato de arrendamiento');

		$('fecha_termino').select();
	}
	else if ($('renta').get('value').getNumericValue() == 0) {
		alert('El importe de renta debe ser mayor a cero');

		$('renta').select();
	}
	else {
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
			'url': 'ArrendamientosCatalogo.php',
			'data': 'accion=doModificar&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Modificando arrendamiento...'
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
			'url': 'ArrendamientosCatalogo.php',
			'data': 'accion=doBaja&id=' + id,
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Dando de baja el arrendamiento...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();

				Consultar.run(param);
			}
		}).send();
	}
}
