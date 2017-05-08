// JavaScript Document

window.addEvent('domready', function() {
	new Formulario('Datos');
	
	obtenerCias();
	
	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				this.blur();
			}
		}
	});
	
	$('registrar').addEvent('click', Registrar);
	
	$('fecha').focus();
});

var obtenerCias = function() {
	new Request({
		'url': 'RegistroEfectivosCompletos.php',
		'data': 'accion=obtener',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result == '') {
				updSelect($('cias'), [{ 'value':null, 'text':'No hay compañías por actualizar' }]);
			}
			else {
				var data = JSON.decode(result);
				updSelect($('cias'), data.cias);
			}
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

var Registrar = function() {
	if ($A($('cias').options).filter(function(el, i) { return el.selected; }).length == 0) {
		alert('Debe seleccionar al menos una compañía');
	}
	else {
		new Request({
			'url': 'RegistroEfectivosCompletos.php',
			'data': 'accion=registrar&' + $('Datos').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function() {
				obtenerCias();
			}
		}).send();
	}
}
