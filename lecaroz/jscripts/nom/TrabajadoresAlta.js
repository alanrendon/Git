// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': obtenerCia.pass([$('num_cia'), $('nombre_cia')]),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_cia_emp').select();
			}
		}
	});
	
	$('num_cia_emp').addEvents({
		'change': obtenerCia.pass([$('num_cia_emp'), $('nombre_cia_emp')]),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('nombre').focus();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('nombre').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('ap_paterno').select();
			}
			else if (e.key == 'up') {
				$('num_cia_emp').select();
			}
		}
	});
	
	$('ap_paterno').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('ap_materno').select();
			}
			else if (e.key == 'up') {
				$('nombre').select();
			}
		}
	});
	
	$('ap_materno').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('rfc').select();
			}
			else if (e.key == 'up') {
				$('ap_paterno').select();
			}
		}
	});
	
	$('rfc').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('curp').select();
			}
			else if (e.key == 'up') {
				$('ap_materno').select();
			}
		}
	});
	
	$('curp').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha_nac').select();
			}
			else if (e.key == 'up') {
				$('rfc').select();
			}
		}
	});
	
	$('fecha_nac').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('lugar_nac').select();
			}
			else if (e.key == 'up') {
				$('curp').select();
			}
		}
	});
	
	$('lugar_nac').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('calle').select();
			}
			else if (e.key == 'up') {
				$('fecha_nac').select();
			}
		}
	});
	
	$('calle').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('colonia').select();
			}
			else if (e.key == 'up') {
				$('lugar_nac').select();
			}
		}
	});
	
	$('colonia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('del_mun').select();
			}
			else if (e.key == 'up') {
				$('calle').select();
			}
		}
	});
	
	$('del_mun').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('entidad').select();
			}
			else if (e.key == 'up') {
				$('colonia').select();
			}
		}
	});
	
	$('entidad').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('cod_postal').select();
			}
			else if (e.key == 'up') {
				$('del_mun').select();
			}
		}
	});
	
	$('cod_postal').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('telefono_casa').select();
			}
			else if (e.key == 'up') {
				$('entidad').select();
			}
		}
	});
	
	$('telefono_casa').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('telefono_movil').select();
			}
			else if (e.key == 'up') {
				$('cod_postal').select();
			}
		}
	});
	
	$('telefono_movil').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('email').select();
			}
			else if (e.key == 'up') {
				$('telefono_casa').select();
			}
		}
	});
	
	$('email').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha_alta').select();
			}
			else if (e.key == 'up') {
				$('telefono_movil').select();
			}
		}
	});
	
	$('fecha_alta').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('salario').focus();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('email').select();
			}
		}
	});
	
	$('salario').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('salario_integrado').select();
			}
			else if (e.key == 'up') {
				$('fecha_alta').select();
			}
		}
	});
	
	$('salario_integrado').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha_alta_imss').select();
			}
			else if (e.key == 'up') {
				$('salario').select();
			}
		}
	});
	
	$('fecha_alta_imss').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_afiliacion').select();
			}
			else if (e.key == 'up') {
				$('salario_integrado').select();
			}
		}
	});
	
	$('num_afiliacion').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('no_infonavit').select();
			}
			else if (e.key == 'up') {
				$('fecha_alta_imss').select();
			}
		}
	});
	
	$('no_infonavit').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('aguinaldo').select();
			}
			else if (e.key == 'up') {
				$('num_afiliacion').select();
			}
		}
	});
	
	$('aguinaldo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('observaciones').select();
			}
			else if (e.key == 'up') {
				$('no_infonavit').select();
			}
		}
	});
	
	$('observaciones').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('uniforme').select();
			}
			else if (e.key == 'up') {
				$('aguinaldo').select();
			}
		}
	});
	
	$('uniforme').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('deposito_bata').select();
			}
			else if (e.key == 'up') {
				$('observaciones').select();
			}
		}
	});
	
	$('deposito_bata').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_cia').select();
			}
			else if (e.key == 'up') {
				$('uniforme').select();
			}
		}
	});
	
	$('borrar').addEvent('click', Borrar);
	
	$('alta').addEvent('click', ValidarDatos);
	
	$('num_cia').select();
});

var obtenerCia = function() {
	var num_cia = arguments[0],
		nombre_cia = arguments[1];
	
	if (num_cia.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'TrabajadoresAlta.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					nombre_cia.set('value', data.nombre_cia);
					
					updSelect($('cod_puestos'), data.puestos);
					updSelect($('cod_turno'), data.turnos);
					updSelect($('cod_horario'), data.horarios);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					num_cia.set('value', num_cia.retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		num_cia.set('value', '');
		nombre_cia.set('value', '');
		
		updSelect($('cod_puestos'), []);
		updSelect($('cod_turno'), []);
		updSelect($('cod_horario'), []);
	}
}

var ValidarDatos = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('nombre').get('value') == '') {
		alert('Debe especificar el nombre');
		
		$('nombre').focus();
	}
	else if ($('ap_paterno').get('value') == '') {
		alert('Debe epecificar el apellido paterno');
		
		$('ap_paterno').focus();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el R.F.C.');
		
		$('rfc').focus();
	}
	else if ($('fecha_nac').get('value') == '') {
		alert('Debe especificar la fecha de nacimiento');
		
		$('fecha').focus();
	}
//	else if ($('fecha_alta_imss').get('value') != '' && $('num_afiliacion').get('value') == '') {
//		alert('Debe especificar el número de seguro social');
//		
//		$('num_afiliacion').focus();
//	}
//	else if ($('fecha_alta_imss').get('value') == '' && $('num_afiliacion').get('value') != '') {
//		alert('Debe especificar la fecha de alta en el I.M.S.S.');
//		
//		$('fecha_alta_imss').focus();
//	}
	else {
		ValidarEdad();
	}
	
}

var ValidarListaNegra = function() {
	var tipo = arguments[0];
	
	new Request({
		'url': 'TrabajadoresAlta.php',
		'data': 'accion=validarListaNegra&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);
				
				if (confirm('El empleado esta en la lista negra con el folio ' + data.folio + ' por las siguientes razones:\n\n' + data.observaciones + '\n\n¿Desea continuar con la actualización de los datos del trabajador?')) {
					ValidarNombre(tipo);
				}
				else {
					$('nombre').focus();
				}
			}
			else {
				ValidarNombre();
			}
		}
	}).send();
}

var ValidarNombre = function() {
	new Request({
		'url': 'TrabajadoresAlta.php',
		'data': 'accion=validarNombre&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value') + '&rfc=' + $('rfc').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result),
					msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías:';
				
				data.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC::\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t\t' + rec.usuario;
				});
				
				msg += '\n\n¿Desea continuar con registro del trabajador?';
				
				if (confirm(msg)) {
					Alta();
				}
				else {
					$('nombre').focus();
				}
			}
			else if (confirm('¿Son correctos todos los datos?')) {
				Alta();
			}
		}
	}).send();
}

var ValidarEdad = function() {
	new Request({
		'url': 'TrabajadoresAlta.php',
		'data': 'accion=validarEdad&fecha_nac=' + $('fecha_nac').get('value') + '&num_afiliacion=' + $('num_afiliacion').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result.getNumericValue() < 0) {
				alert('El trabajor es menor de edad y tiene número de afiliación del seguro social, solo administradores pueden dar de alta este tipo de trabajadores');
			}
			else {
				ValidarListaNegra();
			}
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'TrabajadoresAlta.php',
		'data': 'accion=alta&' + $('Datos').toQueryString(),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			var data = JSON.decode(result),
				msg = 'Datos de alta:\n\nCompañía:\t' + data.cia + '\nTrabajador:\t' + data.trabajador;
			
			alert(msg);
			
			Borrar();
		}
	}).send();
}

var Borrar = function() {
	$$('input[type=text], textarea').set('value', '');
	
	updSelect($('cod_puestos'), []);
	updSelect($('cod_turno'), []);
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
