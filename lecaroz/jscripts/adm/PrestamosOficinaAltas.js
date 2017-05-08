// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$$('input[id=num_cia]').each(function(el, i) {
		el.addEvents({
			'change': Obtener.pass([el, $$('input[id=nombre_cia]')[i], $$('select[id=empleado]')[i]]),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=fecha]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$$('input[id=importe]')[i].select();
				}
			}
		});
	});
	
	$$('input[id=importe]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (!$chk($$('input[id=num_cia]')[i + 1])) {
						newRow(i + 1);
					}
					
					$$('input[id=num_cia]')[i + 1].select();
				}
			}
		});
	});
	
	$('registrar').addEvent('click', Registrar);
	
	$('num_cia').focus();
});

var Obtener = function() {
	var num_cia = arguments[0],
		nombre_cia = arguments[1],
		empleado = arguments[2];
	
	if (num_cia.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PrestamosOficinaAltas.php',
			'data': 'accion=obtener&num_cia=' + num_cia.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					nombre_cia.set('value', data.nombre_cia);
					updSelect(empleado, data.empleados);
				}
				else {
					alert('La compañía no esta en el catálogo o no tiene empleados');
					num_cia.set('value', num_cia.retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		updSelect(empleado, []);
		nombre_cia.set('value', '');
		num_cia.set('value', '').select();
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

var newRow = function(i) {
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	
	var td1 = new Element('td');
	var td2 = new Element('td');
	var td3 = new Element('td');
	var td4 = new Element('td');
	
	var num_cia = new Element('input', {
		'name': 'num_cia[]',
		'type': 'text',
		'class': 'valid Focus toPosInt center',
		'id': 'num_cia',
		'size': 3
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('input[id=fecha]')[i].select();
		}
	}).inject(td1);
	
	var nombre_cia = new Element('input', {
		'name': 'nombre_cia[]',
		'type': 'text',
		'disabled': true,
		'id': 'nombre_cia',
		'size': 30
	}).inject(td1);
	
	var empleado = new Element('select', {
		'name': 'empleado[]',
		'id': 'empleado'
	}).inject(td2);
	
	updSelect(empleado, []);
	
	var fecha = new Element('input', {
		'name': 'fecha[]',
		'type': 'text',
		'class': 'valid Focus toDate center',
		'id': 'fecha',
		'size': 10,
		'maxlength': 10
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('input[id=importe]')[i].select();
		}
	}).inject(td3);
	
	var importe = new Element('input', {
		'name': 'importe[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'precision': 2,
		'id': 'importe',
		'size': 10
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			if (!$chk($$('input[id=num_cia]')[i + 1])) {
				newRow(i + 1);
			}
			
			$$('input[id=num_cia]')[i + 1].select();
		}
	}).inject(td4);
	
	validator.addElementEvents(num_cia);
	validator.addElementEvents(fecha);
	validator.addElementEvents(importe);
	
	styles.addElementEvents(num_cia);
	styles.addElementEvents(empleado);
	styles.addElementEvents(fecha);
	styles.addElementEvents(importe);
	
	num_cia.addEvents({
		'change': Obtener.pass([num_cia, nombre_cia, empleado])
	});
	
	td1.inject(tr);
	td2.inject(tr);
	td3.inject(tr);
	td4.inject(tr);
	
	tr.inject($('TablaCaptura'));
}

var Registrar = function() {
	if (confirm('Son correctos los datos')) {
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
			'url': 'PrestamosOficinaAltas.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				$('TablaCaptura').empty();
				
				new Element('th', {
					'html': 'Compa&ntilde;&iacute;a'
				}).inject($('TablaCaptura'));
				
				new Element('th', {
					'html': 'Empleado'
				}).inject($('TablaCaptura'));
				
				new Element('th', {
					'html': 'Fecha'
				}).inject($('TablaCaptura'));
				
				new Element('th', {
					'html': 'Importe'
				}).inject($('TablaCaptura'));
				
				newRow(0);
				
				alert('Se agregaron ' + result + ' prestamos de oficina');
				
				$('num_cia').select();
			}
		}).send();
	}
}
