// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'ProveedoresModificacion.php',
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
			$('captura').set('html', result);

			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});

			new FormStyles($('Datos'));

			$('num_pro').addEvents({
				'change': obtenerPro,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
					}
				}
			});

			$('modificar').addEvent('click', function() {
				if ($('num_pro').get('value').getNumericValue() > 0) {
					Modificar($('num_pro').get('value'));
				}
				else {
					Listado();
				}
			});

			$('num_pro').select();
		}
	}).send();
}

var obtenerPro = function() {
	if ($('num_pro').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ProveedoresModificacion.php',
			'data': 'accion=obtenerPro&num_pro=' + $('num_pro').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre').set('value', result);
				}
				else {
					alert('El proveedor no se encuentra en el catálogo');
					$('num_pro').set('value', $('num_pro').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_pro').set('value', '');
		$('nombre').set('value', '');
	}
}

var Listado = function() {
	new Request({
		'url': 'ProveedoresModificacion.php',
		'data': 'accion=listado',
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Generando listado de proveedores...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').set('html', result);

				$$('tr[class^=linea_]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					},
				});

				$$('a[id=proveedor]').each(function(el) {
					el.addEvent('click', Modificar.pass(el.get('title')));
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

var Modificar = function() {
	var num_pro = arguments[0];

	new Request({
		'url': 'ProveedoresModificacion.php',
		'data': 'accion=modificar&num_pro=' + num_pro,
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Obteniendo datos del proveedor...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').set('html', result);

			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});

			new FormStyles($('Datos'));

			$('clave_seguridad').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre').select();
					}
				}
			});

			$('nombre').addEvents({
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

						$('localidad').select();
					}
				}
			});

			$('localidad').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('referencia').select();
					}
				}
			});

			$('referencia').addEvents({
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

						$('fax').select();
					}
				}
			});

			$('fax').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('email1').select();
					}
				}
			});

			$('email1').addEvents({
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

						$('observaciones').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('diascredito').select();
					}
				}
			});

			$('diascredito').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('facturas_por_mes').select();
					}
				}
			});

			$('facturas_por_mes').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('facturas_por_pago').select();
					}
				}
			});

			$('facturas_por_pago').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('sucursal').select();
					}
				}
			});

			$('sucursal').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('plaza_banxico').select();
					}
				}
			});

			$('plaza_banxico').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('referencia_bancaria').select();
					}
				}
			});

			$('referencia_bancaria').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('cuenta').select();
					}
				}
			});

			$('cuenta').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('clabe').select();
					}
				}
			});

			$('clabe').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('pass_site').select();
					}
				}
			});

			$('pass_site').addEvents({
				'blur': ValidarPass,
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						if ($chk($('contacto1'))) {
							$('contacto1').select();
						}
						else {
							$('nombre').select();
						}
					}
				}
			});

			$('pass_reload').addEvents({
				'click': function() {
					$('pass_site').set('value', PassReload());
				},
				'mouseover': function() {
					this.setStyle('cursor', 'pointer');
				},
				'mouseout': function() {
					this.setStyle('cursor', 'default');
				}
			});

			$('pass_reload').store('tip:title', '<img src="imagenes/info.png" /> Contraseña aleatoria');
			$('pass_reload').store('tip:text', 'Haga click en el icono para generar una contrase&ntilde;a aleatoria v&aacute;lida');

			tips = new Tips($('pass_reload'), {
				'fixed': true,
				'className': 'Tip',
				'showDelay': 50,
				'hideDelay': 50
			});

			if ($chk($('contacto1'))) {
				$('contacto1').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('contacto2').select();
						}
					}
				});

				$('contacto2').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('contacto3').select();
						}
					}
				});

				$('contacto3').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('contacto4').select();
						}
					}
				});

				$('contacto4').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('desc1').select();
						}
					}
				});

				$('desc1').addEvents({

					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('cod_desc1').select();
						}
					}
				});

				$('cod_desc1').addEvents({
					'change': Descuento.pass([$('cod_desc1'), $('con_desc1'), $('tipo_desc1')]),
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('desc2').select();
						}
					}
				});

				$('desc2').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('cod_desc2').select();
						}
					}
				});

				$('cod_desc2').addEvents({
					'change': Descuento.pass([$('cod_desc2'), $('con_desc2'), $('tipo_desc2')]),
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('desc3').select();
						}
					}
				});

				$('desc3').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('cod_desc3').select();
						}
					}
				});

				$('cod_desc3').addEvents({
					'change': Descuento.pass([$('cod_desc3'), $('con_desc3'), $('tipo_desc3')]),
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('desc4').select();
						}
					}
				});

				$('desc4').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('cod_desc4').select();
						}
					}
				});

				$('cod_desc4').addEvents({
					'change': Descuento.pass([$('cod_desc4'), $('con_desc4'), $('tipo_desc4')]),
					'keydown': function(e) {
						if (e.key == 'enter') {
							e.stop();

							$('nombre').select();
						}
					}
				});
			}

			$('regresar').addEvent('click', Inicio);
			$('actualizar').addEvent('click', Actualizar);

			$('telefono1').fireEvent('blur');
			$('telefono2').fireEvent('blur');
			$('fax').fireEvent('blur');

			if ($chk($('contacto1'))) {
				$('cod_desc1').fireEvent('change');
				$('cod_desc2').fireEvent('change');
				$('cod_desc3').fireEvent('change');
				$('cod_desc4').fireEvent('change');
			}

			$('nombre').focus();
		}
	}).send();
}

var ValidarPass = function() {
	if ($('pass_site').get('value').clean().length == 0) {
		return true;
	}
	else if (!(/^[A-F0-9]{10}$/.test($('pass_site').get('value').clean()))) {
		alert('La contraseña debe de ser de 10 caracteres y solo puede contener los caracteres de la A a la F y del 0 al 9');

		$('pass_site').set('value', $('pass_site').retrieve('tmp', '')).select();
	}
}

var PassReload = function() {
	var char_set = 'ABCDEF0123456789',
		password_length = 10,
		password = '';

	for (var i = 0; i < password_length; i++) {
		password += char_set.charAt($random(0, char_set.length - 1));
	}

	return password;
}

var Descuento = function() {
	var cod_desc = arguments[0],
		con_desc = arguments[1],
		tipo_desc = arguments[2];

	if (cod_desc.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ProveedoresModificacion.php',
			'data': 'accion=descuento&cod=' + cod_desc.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					con_desc.set('value', data.concepto);
					tipo_desc.set('value', data.tipo);
				}
				else {
					cod_desc.set('value', cod_desc.retrieve('tmp', ''));

					alert('El código no esta en el catálogo');
				}
			}
		}).send();
	}
	else {
		cod_desc.set('value', '');
		con_desc.set('value', '');
		tipo_desc.set('value', '');
	}
}

var Actualizar = function() {
	if ($('nombre').get('value').clean() == '') {
		alert('Debe especificar el nombre del proveedor');
		$('nombre').select();
	}
	else if ($('rfc').get('value').clean() == '') {
		alert('Debe especificar el RFC del proveedor');
		$('rfc').select();
	}
	else if ($('tipopersona_t').get('checked') == true && $('curp').get('value').clean() == '') {
		alert('Para personas fisicas debe especificar el CURP');
		$('curp').select();
	}
	else if ($('calle').get('value').clean() == '') {
		alert('Debe especificar la calle');
		$('calle').select();
	}
	else if ($('colonia').get('value').clean() == '') {
		alert('Debe especificar la colonia');
		$('colonia').select();
	}
	else if ($('municipio').get('value').clean() == '') {
		alert('Debe especificar la delegación o municipio');
		$('municipio').select();
	}
	else if ($('estado').get('value').clean() == '') {
		alert('Debe especificar el estado');
		$('estado').select();
	}
	else if ($('codigo_postal').get('value').clean() == '') {
		alert('Debe especificar el código postal');
		$('codigo_postal').select();
	}
	else if ($('telefono1').get('value').clean() == '' && $('telefono2').get('value').clean() == '') {
		alert('Debe especificar al menos un teléfono de contacto');
		$('telefono1').select();
	}
	else if ($$('[name=trans]')[1].get('checked') && !$chk($('idbanco').get('value').getNumericValue())) {
		alert('Para transferencias debe especificar el banco de procedencia de la cuenta del proveedor');
		$('idbanco').focus();
	}
	else if ($$('[name=trans]')[1].get('checked') && $('sucursal').get('value').clean().length < 4) {
		alert('Para transferencias debe especificar la sucursal de procedencia de la cuenta del proveedor');
		$('sucursal').select();
	}
	else if ($$('[name=trans]')[1].get('checked') && !$chk($('IdEntidad').get('value').getNumericValue())) {
		alert('Para transferencias debe especificar la entidad de procedencia de la cuenta del proveedor');
		$('IdEntidad').focus();
	}
	else if ($$('[name=trans]')[1].get('checked') && $('plaza_banxico').get('value').clean().length < 5) {
		alert('Para transferencias debe especificar la plaza banxico de la cuenta del proveedor');
		$('plaza_banxico').select();
	}
	else if ($$('[name=trans]')[1].get('checked') && $('cuenta').get('value').clean().length < 11) {
		alert('Para transferencias debe especificar la cuenta del proveedor');
		$('cuenta').select();
	}
	else if ($$('[name=trans]')[1].get('checked') && $('clabe').get('value').clean().length < 18) {
		alert('Para transferencias debe especificar la cuenta CLABE del proveedor');
		$('clabe').select();
	}
	else if ($('pass_site').get('value').clean().length < 10) {
		alert('Para que el proveedor pueda revisar sus pagos atraves del portal de Lecaroz debe especificar la contraseña de acceso');
		$('pass_site').select();
	}
	else if ($('facturas_por_pago').get('value').getNumericValue() <= 0) {
		alert('Debe especificar la máxima cantidad de facturas dentro de un pago');
		$('facturas_por_pago').select();
	}
	else if ($('facturas_por_pago').get('value').getNumericValue() > 20) {
		alert('La máxima cantidad de facturas dentro de pago no puede ser mayor a 20');
		$('facturas_por_pago').select();
	}
	else if (confirm('¿Son correctos los datos del proveedor?')) {
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
			'url': 'ProveedoresModificacion.php',
			'data': 'accion=actualizar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Inicio();
			}
		}).send();
	}
}
