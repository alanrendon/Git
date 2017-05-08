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
				
				$('sueldo').select();
			}
		}
	});
	
	$('sueldo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('neto').select();
			}
		}
	});
	
	$('neto').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('concepto').select();
			}
		}
	});
	
	$('concepto').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('num_cia').select();
	
	$('generar').addEvent('click', Generar);
});

var obtenerDatos = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CartaIncapacidades.php',
			'data': 'accion=obtenerDatos&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(json) {
				if (json != '') {
					var data = JSON.decode(json);
					
					$('nombre_cia').set('value', data.nombre_cia);
					
					updSelect($('empleado'), data.empleados);
				}
				else {
					alert('La compañía no esta en el catálogo o no tiene empleados');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		
		updSelect($('empleado'), []);
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
	}
	else {
		Select.length = 1;
		$each(Select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});
	}
}

var Generar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía de origen del trabajador');
		$('num_cia').select();
	}
	else if ($('empleado').get('value').getNumericValue() == null) {
		alert('Debe seleccionar un empleado');
	}
	else if ($('sueldo').get('value').getNumericValue() <= 0) {
		alert('Debe especificar el salario');
		$('sueldo').select();
	}
	else if ($('neto').get('value').getNumericValue() <= 0) {
		alert('Debe especificar el importe neto a pagar');
		$('neto').select();
	}
	else if ($('concepto').get('value').clean() == '') {
		alert('Debe especificar el concepto');
	}
	else {
		var url = 'CartaIncapacidades.php',
			arg = '?accion=recibo&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'carta', opt);
		win.focus();
	}
}
