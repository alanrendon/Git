// JavaScript Document

window.addEvent('domready', function() {
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
				
				this.blur();
			}
		}
	});
	
	$('siguiente').addEvent('click', siguiente);
	
	$('num_cia').select();
});

var obtenerCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ReporteNomina.php',
			'data': 'accion=obtenerCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
					
					obtenerPeriodos.run();
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		
		obtenerPeriodos.run();
	}
}

var obtenerPeriodos = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ReporteNomina.php',
			'data': 'accion=obtenerPeriodos&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var periodos = JSON.decode(result);
					
					updSelect($('periodo'), periodos);
				}
				else {
					updSelect($('periodos'), []);
				}
			}
		}).send();
	}
	else {
		updSelect($('periodos'), []);
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

var siguiente = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('archivo1').get('value') == '') {
		alert('Debe seleccionar el primer archivo de carga de datos');
		
		$('archivo1').focus();
	}
	else if ($('archivo2').get('value') == '') {
		alert('Debe seleccionar el segundo archivo de carga de datos');
		
		$('archivo2').focus();
	}
	else if ($('archivo3').get('value') == '') {
		alert('Debe seleccionar el tercer archivo de carga de datos');
		
		$('archivo3').focus();
	}
	else {
		new Element('input', {
			'id': 'accion',
			'name': 'accion',
			'type': 'hidden',
			'value': 'cargarDatos'
		}).inject($('Datos'));
		
		$('Datos').submit();
	}
}
