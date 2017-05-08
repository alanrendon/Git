// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('Datos'));

	$('num_proveedor').addEvents({
		'change': Validar,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('clave_seguridad').select();
			}
		}
	});

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
					$('num_proveedor').select();
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

					$('num_proveedor').select();
				}
			}
		});
	}

	$('alta').addEvent('click', Alta);

	Ultimo();

	$('num_proveedor').focus();
});

var Ultimo = function() {
	new Request({
		'url': 'ProveedoresAlta.php',
		'data': 'accion=ultimo',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			$('num_proveedor').set('value', result).select();
		}

	}).send();
}

var Validar = function() {
	if ($('num_proveedor').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ProveedoresAlta.php',
			'data': 'accion=validar&num_pro=' + $('num_proveedor').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				switch (result) {
					case '-1':
						alert('El número de proveedor ya ha sido usado, especifique uno diferente');
						$('num_proveedor').set('value', $('num_proveedor').retrieve('tmp')).select();
					break;

					case '-2':
						alert('El número de proveedor debe estar entre 1 y 9000');
						$('num_proveedor').set('value', $('num_proveedor').retrieve('tmp')).select();
					break;

					case '-3':
						alert('El número de proveedor debe ser mayor a 9000');
						$('num_proveedor').set('value', $('num_proveedor').retrieve('tmp')).select();
					break;
				}
			}
		}).send();
	}
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
			'url': 'ProveedoresAlta.php',
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

var Alta = function() {
	if (!($('num_proveedor').get('value').getNumericValue() > 0)) {
		alert('Debe epecificar el número que se asignara al proveedor');
		$('num_proveedor').select();
	}
	else if ($('nombre').get('value').clean() == '') {
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
	else if (confirm('¿Son correctos los datos del nuevo proveedor?')) {
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
			'url': 'ProveedoresAlta.php',
			'data': 'accion=alta&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				$('Datos').reset();

				Ultimo();

				$('num_proveedor').focus();
			}
		}).send();
	}
}
