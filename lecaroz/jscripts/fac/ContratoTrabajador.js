// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('Datos'));

	$('num_cia').addEvents({
		'change': obtenerDatos,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('num_cia_sec').select();
			}
		}
	});

	$('num_cia_sec').addEvents({
		'change': obtenerCiaSec,
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

				$('ap_paterno').select();
			}
		}
	});

	$('empleado').addEvents({
		'change': obtenerDatosEmpleado
	});

	$('ap_paterno').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('ap_materno').select();
			}
		}
	});

	$('ap_materno').addEvents({
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

				$('fecha_nacimiento').select();
			}
		}
	});

	$('fecha_nacimiento').addEvents({
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

				$('codigo_postal').select();
			}
		}
	});

	$('codigo_postal').addEvents({
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

				$('hora_inicio').select();
			}
		}
	});

	$('hora_inicio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('hora_termino').select();
			}
		}
	});

	$('hora_termino').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('salario').select();
			}
		}
	});

	$('salario').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				if ( ! $('fecha_vencimiento_licencia_manejo').get('readonly')) {
					$('fecha_vencimiento_licencia_manejo').select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});

	$('doc_licencia_manejo').addEvent('change', function() {
		if (this.get('checked')) {
			$('fecha_vencimiento_licencia_manejo').set('readonly', false).focus();
		}
		else {
			$('fecha_vencimiento_licencia_manejo').set('readonly', true).set('value', '');
		}
	});

	$('fecha_vencimiento_licencia_manejo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('num_cia').select();
			}
		}
	});

	$('firma_contrato').addEvent('click', ActualizarFirmaContrato);

	$('num_cia').select();

	$('generar').addEvents({
		'click': Generar,
		'dblclick': function(e) {
			e.stop();

			alert('No se permite el doble click en esta función');

			return false;
		}
	});

	$('actualizar').addEvents({
		'click': Actualizar,
		'dblclick': function(e) {
			e.stop();

			alert('No se permite el doble click en esta función');

			return false;
		}
	});

	$('alta').addEvents({
		'click': Alta,
		'dblclick': function(e) {
			e.stop();

			alert('No se permite el doble click en esta función');

			return false;
		}
	});
});

var obtenerDatos = function() {
	var id = arguments[0];

	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=obtenerDatos&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(json) {
				if (json != '') {
					var data = JSON.decode(json);

					$('nombre_cia').set('value', data.nombre_cia);

					updSelect($('empleado'), data.empleados);

					updSelect($('num_cia_emp'), data.cias);

					updSelect($('puesto'), data.puestos);

					updSelect($('turno'), data.turnos);

					$$('#nombre, #ap_paterno, #ap_materno, #rfc, #curp, #fecha_nacimiento, #calle, #colonia, #municipio, #estado, #codigo_postal, #email, #fecha_inicio, #fecha_termino, #hora_inicio, #hora_termino, #salario, #fecha_vencimiento_licencia_manejo').set('value', '');

					$('alta').set('disabled', false);
					$('actualizar').set('disabled', true);

					if (id > 0) {
						$each($('empleado').options, function(opt, i) {
							if (opt.get('value').getNumericValue() == id) {
								$('empleado').selectedIndex = i;
							}
						});

						obtenerDatosEmpleado();
					}
				}
				else {
					alert('La compañía no esta en el catálogo o no tiene empleados');

					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('input[name=tipo]')[0].set('checked', true);
		$$('input[name=tipo]')[1].set('checked', false);

		updSelect($('empleado'), []);

		updSelect($('num_cia_emp'), []);

		updSelect($('puesto'), []);

		updSelect($('turno'), []);

		$$('#num_cia, #nombre_cia, #num_cia_sec, #nombre_cia_sec, #nombre, #ap_paterno, #ap_materno, #rfc, #curp, #fecha_nacimiento, #calle, #colonia, #municipio, #estado, #codigo_postal, #email, #fecha_inicio, #fecha_termino, #hora_inicio, #hora_termino, #salario, #fecha_vencimiento_licencia_manejo').set('value', '');

		$$('[name=estado_civil]')[0].set('checked', true);
		$$('[name=sexo]')[0].set('checked', true)
		$('doc_acta_nacimiento').set('checked', false);
		$('doc_comprobante_domicilio').set('checked', false);
		$('doc_curp').set('checked', false);
		$('doc_ife').set('checked', false);
		$('doc_num_seguro_social').set('checked', false);
		$('doc_solicitd_trabajo').set('checked', false);
		$('doc_comprobante_estudios').set('checked', false);
		$('doc_referencias').set('checked', false);
		$('doc_no_antecedentes_penales').set('checked', false);
		$('doc_licencia_manejo').set('checked', false);
		$('doc_rfc').set('checked', false);
		$('doc_no_adeudo_infonavit').set('checked', false);
		$('firma_contrato').set('checked', false);

		$('alta').set('disabled', true);
		$('actualizar').set('disabled', false);
	}
}

var obtenerCiaSec = function() {
	var id = arguments[0];

	if ($('num_cia_sec').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=obtenerCiaSec&num_cia=' + $('num_cia_sec').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia_sec').set('value', result);
				}
				else {
					alert('La compañía no esta en el catálogo');

					$('num_cia_sec').set('value', $('num_cia_sec').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_cia_sec').set('value', '');
		$('nombre_cia_sec').set('value', '');
	}
}

var obtenerDatosEmpleado = function() {
	if ($('empleado').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=obtenerDatosEmpleado&id=' + $('empleado').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(json) {
				if (json != '') {
					var data = JSON.decode(json);

					$$('#cia_emp_1_row, #cia_emp_2_row').destroy();

					if (data.num_cia_emp.getNumericValue() != $('num_cia').get('value').getNumericValue()) {
						var tr1 = new Element('tr', {
								id: 'cia_emp_1_row',
								class: 'linea_on'
							}).inject($('nombre_row'), 'before'),
							td1 = new Element('td', {
								colspan: 2,
								html: 'Este empleado labora en la compa&ntilde;&iacute;a ' + data.num_cia_emp + ' ' + data.nombre_cia_emp,
								class: 'bold blue'
							}).inject(tr1),
							tr2 = new Element('tr', {
								id: 'cia_emp_2_row',
								class: 'linea_off'
							}).inject(tr1, 'after'),
							td2 = new Element('td', {
								colspan: 2,
								html: '&nbsp;'
							}).inject(tr2);
					}

					if (data.fecha_inicio != '' && data.fecha_termino == '') {
						$$('input[name=tipo]')[0].set('checked', false);
						$$('input[name=tipo]')[1].set('checked', true);
					} else {
						$$('input[name=tipo]')[0].set('checked', true);
						$$('input[name=tipo]')[1].set('checked', false);
					}

					$('num_cia_sec').set('value', data.num_cia_sec);
					$('nombre_cia_sec').set('value', data.nombre_cia_sec.clean());
					$('nombre').set('value', data.nombre.clean());
					$('ap_paterno').set('value', data.ap_paterno.clean());
					$('ap_materno').set('value', data.ap_materno.clean());
					$('rfc').set('value', data.rfc.clean());
					$('curp').set('value', data.curp.clean());
					$('fecha_nacimiento').set('value', data.fecha_nacimiento);
					$$('[name=estado_civil]')[data.estado_civil - 1].set('checked', true);
					$$('[name=sexo]')[data.sexo].set('checked', true)
					$('calle').set('value', data.calle.clean());
					$('colonia').set('value', data.colonia.clean());
					$('municipio').set('value', data.municipio.clean());
					$('estado').set('value', data.estado.clean());
					$('codigo_postal').set('value', data.codigo_postal.clean());
					$('email').set('value', data.email.clean());
					$each($('num_cia_emp').options, function(el) {
						if (el.get('value') == data.num_cia_emp) {
							el.set('selected', 'selected');
						}
					});
					$each($('puesto').options, function(el) {
						if (el.get('value') == data.puesto) {
							el.set('selected', 'selected');
						}
					});
					$each($('turno').options, function(el) {
						if (el.get('value') == data.turno) {
							el.set('selected', 'selected');
						}
					});
					$('salario').set('value', data.salario.getNumericValue().numberFormat(2, '.', ','));
					$('fecha_inicio').set('value', data.fecha_inicio);
					$('fecha_termino').set('value', data.fecha_termino);
					$('hora_inicio').set('value', data.hora_inicio);
					$('hora_termino').set('value', data.hora_termino);

					$('doc_acta_nacimiento').set('checked', data.doc_acta_nacimiento == 't' ? true : false);
					$('doc_comprobante_domicilio').set('checked', data.doc_comprobante_domicilio == 't' ? true : false);
					$('doc_curp').set('checked', data.doc_curp == 't' ? true : false);
					$('doc_ife').set('checked', data.doc_ife == 't' ? true : false);
					$('doc_num_seguro_social').set('checked', data.doc_num_seguro_social == 't' ? true : false);
					$('doc_solicitud_trabajo').set('checked', data.doc_solicitud_trabajo == 't' ? true : false);
					$('doc_comprobante_estudios').set('checked', data.doc_comprobante_estudios == 't' ? true : false);
					$('doc_referencias').set('checked', data.doc_referencias == 't' ? true : false);
					$('doc_no_antecedentes_penales').set('checked', data.doc_no_antecedentes_penales == 't' ? true : false);
					$('doc_licencia_manejo').set('checked', data.doc_licencia_manejo == 't' ? true : false);
					$('fecha_vencimiento_licencia_manejo').set('value', data.fecha_vencimiento_licencia_manejo).set('readonly', data.doc_licencia_manejo == 't' ? false : true);
					$('doc_rfc').set('checked', data.doc_rfc == 't' ? true : false);
					$('doc_no_adeudo_infonavit').set('checked', data.doc_no_adeudo_infonavit == 't' ? true : false);
					$('firma_contrato').set('checked', data.firma_contrato == 't' ? true : false);

					$('alta').set('disabled', true);
					$('actualizar').set('disabled', false);
				}
			}
		}).send();
	}
	else {
		$$('input[name=tipo]')[0].set('checked', true);
		$$('input[name=tipo]')[1].set('checked', false);

		$$('#cia_emp_1_row, #cia_emp_2_row').destroy();

		$$('#num_cia_sec, #nombre_cia_sec, #nombre, #ap_paterno, #ap_materno, #rfc, #curp, #fecha_nacimiento, #calle, #colonia, #municipio, #estado, #codigo_postal, #email, #fecha_inicio, #fecha_termino, #hora_inicio, #hora_termino, #salario, #fecha_vencimiento_licencia_manejo').set('value', '');

		$$('[name=estado_civil]')[0].set('checked', true);
		$$('[name=sexo]')[0].set('checked', true)

		$('doc_acta_nacimiento').set('checked', false);
		$('doc_comprobante_domicilio').set('checked', false);
		$('doc_curp').set('checked', false);
		$('doc_ife').set('checked', false);
		$('doc_num_seguro_social').set('checked', false);
		$('doc_solicitud_trabajo').set('checked', false);
		$('doc_comprobante_estudios').set('checked', false);
		$('doc_referencias').set('checked', false);
		$('doc_no_antecedentes_penales').set('checked', false);
		$('doc_licencia_manejo').set('checked', false);
		$('doc_rfc').set('checked', false);
		$('doc_no_adeudo_infonavit').set('checked', false);
		$('firma_contrato').set('checked', false);

		$('alta').set('disabled', false);
		$('actualizar').set('disabled', true);
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
		Select.length = 1;
		$each(Select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});

		Select.selectedIndex = 0;
	}
}

var Generar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía de origen del trabajador');
		$('num_cia').select();
	}
	else if ($('empleado').get('value').getNumericValue() == 0) {
		alert('El trabajador aun no ha sido dado de alta en el sistema');
	}
	else if ($('nombre').get('value').clean() == '') {
		alert('Debe especificar el nombre del trabajador');
		$('nombre').select();
	}
	else if ($('ap_paterno').get('value').clean() == '') {
		alert('Debe especificar el apellido paterno del trabajador');
		$('ap_paterno').select();
	}
	else if ($('fecha_nacimiento').get('value').clean() == '') {
		alert('Debe especificar la fecha de nacimiento del trabajador');
		$('fecha_nacimiento').select();
	}
	else if ($('calle').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la calle y el número');
		$('calle').select();
	}
	else if ($('colonia').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la colonia');
		$('colonia').select();
	}
	else if ($('municipio').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la delegación o municipio');
		$('municipio').select();
	}
	else if ($('estado').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la entidad o estado');
		$('estado').select();
	}
	else if ($('codigo_postal').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador el código postal');
		$('codigo_postal').select();
	}
	else if ($('email').get('value').clean() == '') {
		alert('Debe especificar el correo electrónico del trabajador');
		$('email').select();
	}
	else if ($('puesto').get('value').getNumericValue() == 0) {
		alert('Debe especificar el puesto');
	}
	else if ($('turno').get('value').getNumericValue() == 0) {
		alert('Debe especificar el turno');
	}
	else if ($('fecha_inicio').get('value').clean() == '') {
		alert('Debe especificar la fecha de inicio de contrato');
		$('fecha_inicio').select();
	}
	else if ($$('[name=tipo]')[0].get('checked') && $('fecha_termino').get('value').clean() == '') {
		alert('Debe especificar la fecha de termino de contrato');
		$('fecha_termino').select();
	}
	else if ($('hora_inicio').get('value').clean() == '') {
		alert('Debe especificar la hora de inicio');
		$('hora_inicio').select();
	}
	else if ($('hora_termino').get('value').clean() == '') {
		alert('Debe especificar la hora de termino');
		$('hora_termino').select();
	}
	else if ($('salario').get('value').getNumericValue() == 0) {
		alert('Debe especificar el salario');
		$('salario').select();
	}
	else if (confirm('Los datos del trabajador se actualizaran, ¿Desea continuar?')) {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('generar').set('disabled', true);
			},
			'onSuccess': function(result) {
				$('generar').set('disabled', false);

				if (result == '') {
					var url = 'ContratoTrabajador.php',
						arg = '?accion=contrato&' + $('Datos').toQueryString(),
						opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
						win;

					win = window.open(url + arg, 'contrato', opt);
					win.focus();
				}
				else {
					alert('Ha ocurrido un error, avisar al programador');

					console.log(result);
				}
			}
		}).send();
	}
}

var Actualizar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía de origen del trabajador');
		$('num_cia').select();
	}
	else if ($('empleado').get('value').getNumericValue() == 0) {
		alert('Debe seleccionar un empleado del listado');
		$('empleado').focus();
	}
	else if ($('nombre').get('value').clean() == '') {
		alert('Debe especificar el nombre del trabajador');
		$('nombre').select();
	}
	else if ($('ap_paterno').get('value').clean() == '') {
		alert('Debe especificar el apellido paterno del trabajador');
		$('ap_paterno').select();
	}
	else if ($('fecha_nacimiento').get('value').clean() == '') {
		alert('Debe especificar la fecha de nacimiento del trabajador');
		$('fecha_nacimiento').select();
	}
	else if ($('calle').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la calle y el número');
		$('calle').select();
	}
	else if ($('colonia').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la colonia');
		$('colonia').select();
	}
	else if ($('municipio').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la delegación o municipio');
		$('municipio').select();
	}
	else if ($('estado').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la entidad o estado');
		$('estado').select();
	}
	else if ($('codigo_postal').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador el código postal');
		$('codigo_postal').select();
	}
	else if ($('email').get('value').clean() == '') {
		alert('Debe especificar el correo electrónico del trabajador');
		$('email').select();
	}
	else if ($('puesto').get('value').getNumericValue() == 0) {
		alert('Debe especificar el puesto');
	}
	else if ($('turno').get('value').getNumericValue() == 0) {
		alert('Debe especificar el turno');
	}
	else if ($('fecha_inicio').get('value').clean() == '') {
		alert('Debe especificar la fecha de inicio de contrato');
		$('fecha_inicio').select();
	}
	else if ($$('[name=tipo]')[0].get('checked') && $('fecha_termino').get('value').clean() == '') {
		alert('Debe especificar la fecha de termino de contrato');
		$('fecha_termino').select();
	}
	else if ($('hora_inicio').get('value').clean() == '') {
		alert('Debe especificar la hora de inicio');
		$('hora_inicio').select();
	}
	else if ($('hora_termino').get('value').clean() == '') {
		alert('Debe especificar la hora de termino');
		$('hora_termino').select();
	}
	else if ([5, 915].contains($('puesto').get('value').getNumericValue()) && $('fecha_vencimiento_licencia_manejo').get('value') == '') {
		alert('Debe especificar la fecha de vencimiento de la licencia de manejo del empleado');

		$('fecha_vencimiento_licencia_manejo').focus();
	}
	else if (confirm('¿Desea actualizar los datos del trabajador?')) {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('actualizar').set('disabled', true);
			},
			'onSuccess': function(result) {
				$('actualizar').set('disabled', false);

				if (result == '') {
					alert('Datos del trabajador actualizados');
				}
				else {
					alert('Ha ocurrido un error, avisar al programador');

					console.log(result);
				}
			}
		}).send();
	}
}

var Alta = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía de origen del trabajador');
		$('num_cia').select();
	}
	else if ($('empleado').get('value').getNumericValue() > 0) {
		alert('No debe tener seleccionado a ningun trabajador mientras realiza una alta');
		$('empleado').focus();
	}
	else if ($('nombre').get('value').clean() == '') {
		alert('Debe especificar el nombre del trabajador');
		$('nombre').select();
	}
	else if ($('ap_paterno').get('value').clean() == '') {
		alert('Debe especificar el apellido paterno del trabajador');
		$('ap_paterno').select();
	}
	else if ($('fecha_nacimiento').get('value').clean() == '') {
		alert('Debe especificar la fecha de nacimiento del trabajador');
		$('fecha_nacimiento').select();
	}
	else if ($('calle').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la calle y el número');
		$('calle').select();
	}
	else if ($('colonia').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la colonia');
		$('colonia').select();
	}
	else if ($('municipio').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la delegación o municipio');
		$('municipio').select();
	}
	else if ($('estado').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador la entidad o estado');
		$('estado').select();
	}
	else if ($('codigo_postal').get('value').clean() == '') {
		alert('Debe especificar en la información del domicilio del trabajador el código postal');
		$('codigo_postal').select();
	}
	else if ($('email').get('value').clean() == '') {
		alert('Debe especificar el correo electrónico del trabajador');
		$('email').select();
	}
	else if ($('puesto').get('value').getNumericValue() == 0) {
		alert('Debe especificar el puesto');
	}
	else if ($('turno').get('value').getNumericValue() == 0) {
		alert('Debe especificar el turno');
	}
	else if ($('fecha_inicio').get('value').clean() == '') {
		alert('Debe especificar la fecha de inicio de contrato');
		$('fecha_inicio').select();
	}
	else if ($$('[name=tipo]')[0].get('checked') && $('fecha_termino').get('value').clean() == '') {
		alert('Debe especificar la fecha de termino de contrato');
		$('fecha_termino').select();
	}
	else if ($('hora_inicio').get('value').clean() == '') {
		alert('Debe especificar la hora de inicio');
		$('hora_inicio').select();
	}
	else if ($('hora_termino').get('value').clean() == '') {
		alert('Debe especificar la hora de termino');
		$('hora_termino').select();
	}
	else {
		$('alta').set('disabled', true);

		ValidarListaNegra();
	}
}

var ValidarListaNegra = function() {
	new Request({
		'url': 'ContratoTrabajador.php',
		'data': 'accion=validarListaNegra&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('alta').set('disabled', false);

				var data = JSON.decode(result);

				alert('El empleado esta en la lista negra con el folio ' + data.folio + ' y no puede ser ingresado/modificado por las siguientes razones:\n\n' + data.observaciones);

				$('nombre').focus();

				/*var data = JSON.decode(result);

				if (confirm('El empleado esta en la lista negra con el folio ' + data.folio + ' por las siguientes razones:\n\n' + data.observaciones + '\n\n¿Desea continuar con la actualización de los datos del trabajador?')) {
					ValidarNombre();
				}
				else {
					$('nombre').focus();
				}*/
			}
			else {
				ValidarNombre();
			}
		}
	}).send();
}

var ValidarNombre = function() {
	new Request({
		'url': 'ContratoTrabajador.php',
		'data': 'accion=validarNombre&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value') + '&rfc=' + $('rfc').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('alta').set('disabled', false);

				var data = JSON.decode(result),
					msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías y no podrá ser ingresado:';

				data.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC:\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t' + rec.usuario;
				});

				alert(msg);

				$('nombre').focus();

				/*var data = JSON.decode(result),
					msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías:';

				data.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC:\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t' + rec.usuario;
				});

				msg += '\n\n¿Desea continuar con la alta del trabajador?';

				if (confirm(msg)) {
					Insertar();
				}
				else {
					$('nombre').focus();
				}*/
			}
			else if (confirm('¿Son correctos todos los datos?')) {
				Insertar();
			}
		}
	}).send();
}

var Insertar = function() {
	new Request({
		'url': 'ContratoTrabajador.php',
		'data': 'accion=alta&' + $('Datos').toQueryString(),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			$('alta').set('disabled', false);

			var data = JSON.decode(result);

			if (data.status == 1) {
				alert('Trabajador dado de alta:\n\n' + data.num_emp + ' ' + data.nombre_completo);

				obtenerDatos.run(data.id);
			}
			else {
				alert('Ha ocurrido un error, avisar al programador');

				console.log(result);
			}
		}
	}).send();
}

var ActualizarFirmaContrato = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la compañía de origen del trabajador');
		$('num_cia').select();
	}
	else if ($('empleado').get('value').getNumericValue() == 0) {
		$('firma_contrato').set('checked', false);

		alert('El trabajador aun no ha sido dado de alta en el sistema');
	}
	else if ($('nombre').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar el nombre del trabajador');
		$('nombre').select();
	}
	else if ($('ap_paterno').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar el apellido paterno del trabajador');
		$('ap_paterno').select();
	}
	else if ($('fecha_nacimiento').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la fecha de nacimiento del trabajador');
		$('fecha_nacimiento').select();
	}
	else if ($('calle').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar en la información del domicilio del trabajador la calle y el número');
		$('calle').select();
	}
	else if ($('colonia').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar en la información del domicilio del trabajador la colonia');
		$('colonia').select();
	}
	else if ($('municipio').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar en la información del domicilio del trabajador la delegación o municipio');
		$('municipio').select();
	}
	else if ($('estado').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar en la información del domicilio del trabajador la entidad o estado');
		$('estado').select();
	}
	else if ($('codigo_postal').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar en la información del domicilio del trabajador el código postal');
		$('codigo_postal').select();
	}
	else if ($('email').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar el correo electrónico del trabajador');
		$('email').select();
	}
	else if ($('fecha_inicio').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la fecha de inicio de contrato');
		$('fecha_inicio').select();
	}
	else if ($$('[name=tipo]')[0].get('checked') && $('fecha_termino').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la fecha de termino de contrato');
		$('fecha_termino').select();
	}
	else if ($('hora_inicio').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la hora de inicio');
		$('hora_inicio').select();
	}
	else if ($('hora_termino').get('value').clean() == '') {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar la hora de termino');
		$('hora_termino').select();
	}
	else if ($('salario').get('value').getNumericValue() == 0) {
		$('firma_contrato').set('checked', false);

		alert('Debe especificar el salario');
		$('salario').select();
	}
	else {
		new Request({
			'url': 'ContratoTrabajador.php',
			'data': 'accion=firma&id=' + $('empleado').get('value') + '&status=' + ($('firma_contrato').get('checked') ? 'TRUE' : 'FALSE'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert('Ha ocurrido un error, avisar al programador');

					console.log(result);
				}
			}
		}).send();
	}
}
