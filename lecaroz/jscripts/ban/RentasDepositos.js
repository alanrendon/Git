window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	crearFila.run(0);
	
	$('cancelar').addEvent('click', Cancelar);
	
	$('registrar').addEvent('click', Registrar);
	
	$('num_cia').select();
});

var crearFila = function() {
	var i = arguments[0],
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject($('filas')),
		td1 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		num_cia = new Element('input', {
			'id': 'num_cia',
			'name': 'num_cia[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).addEvents({
			'change': obtenerCia.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					$$('input[id=fecha]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=num_cia]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=num_cia]').length - 1) {
					$$('input[id=num_cia]')[i + 1].select();
				}
			}
		}).inject(td1),
		nombre_cia = new Element('input', {
			'id': 'nombre_cia',
			'name': 'nombre_cia[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td1),
		td2 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		fecha = new Element('input', {
			'id': 'fecha',
			'name': 'fecha[]',
			'type': 'text',
			'size': 10,
			'maxlength': 10,
			'class': 'valid Focus toDate center'
		}).addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					$$('input[id=importe]')[i].select();
				}
				else if (e.key == 'left') {
					$$('input[id=num_cia]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=fecha]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=fecha]').length - 1) {
					$$('input[id=fecha]')[i + 1].select();
				}
			}
		}).inject(td2),
		td3 = new Element('td').inject(tr),
		recibo = new Element('select', {
			'id': 'recibo',
			'name': 'recibo[]'
		}).addEvent('change', seleccionarRecibo.pass(i)).inject(td3),
		td4 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		importe = new Element('input', {
			'id': 'importe',
			'name': 'importe[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2,
			'styles': {
				'width': $('total').getSize().x + 'px'
			}
		}).addEvents({
			'change': calcularTotal,
			'keydown': function(e) {
				if (e.key == 'enter') {
					if (i + 1 > $$('input[id=importe]').length - 1) {
						crearFila.run(i + 1);
					}
					
					$$('input[id=num_cia]')[i + 1].select();
				}
				else if (e.key == 'left') {
					$$('input[id=fecha]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=importe]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=importe]').length - 1) {
					$$('input[id=importe]')[i + 1].select();
				}
			}
		}).inject(td4);
	
	validator.addElementEvents(num_cia);
	validator.addElementEvents(fecha);
	validator.addElementEvents(importe);
	
	styles.addElementEvents(num_cia);
	styles.addElementEvents(fecha);
	styles.addElementEvents(importe);
	
	updSelect(recibo, []);
}

var obtenerCia = function() {
	var i = arguments[0],
		num_cia = $$('input[id=num_cia]')[i],
		nombre_cia = $$('input[id=nombre_cia]')[i],
		fecha = $$('input[id=fecha]')[i],
		recibo = $$('select[id=recibo]')[i],
		importe = $$('input[id=importe]')[i];
	
	if (num_cia.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'RentasDepositos.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);
				
				if (data.num_cia > 0) {
					num_cia.set('value', data.num_cia);
					nombre_cia.set('value', data.nombre_cia);
					fecha.set('value', data.fecha);
					importe.set('value', '0.00');
					
					updSelect(recibo, data.recibos);
					
					fecha.select();
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					num_cia.set('value', num_cia.retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		num_cia.set('value', '');
		nombre_cia.set('value', '');
		fecha.set('value', '');
		importe.set('value', '');
		
		updSelect(recibo, []);
	}
}

var seleccionarRecibo = function() {
	var i = arguments[0],
		recibo = $$('select[id=recibo]')[i],
		importe = $$('input[id=importe]')[i];
	
	if ($$('select[id=recibo]').filter(function(el) { return el.get('value') == recibo.get('value') }).length > 1) {
		alert('El recibo no puede ser especificado más de una vez');
		
		recibo.selectedIndex = 0;
		
		importe.set('value', '0.00');
		
		calcularTotal.run();
		
		return false;
	}
	
	if (recibo.get('value') != '') {
		var data = JSON.decode(recibo.get('value'));
		
		importe.set('value', data.renta.numberFormat(2, '.', ','));
	}
	else {
		importe.set('value', '0.00');
	}
	
	calcularTotal.run();
	
	if (i + 1 > $$('input[id=num_cia]').length - 1) {
		crearFila.run(i + 1);
	}
	
	$$('input[id=num_cia]')[i + 1].select();
}

var calcularTotal = function() {
	$('total').set('value', $$('input[id=importe]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
}

var Registrar = function() {
	if ($('total').get('value').getNumericValue() > 0) {
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
			'url': 'RentasDepositos.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Registrando dep&oacute;sitos...', '<img src="iconos/info.png" /> Informaci&oacute;n', 120, 80, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				$('filas').empty();
				
				crearFila.run(0);
				
				calcularTotal.run();
				
				var data = JSON.decode(result);
				
				var url = 'RentasDepositos.php',
					param = '?accion=reporte&iduser=' + data.iduser + '&ts=' + data.ts,
					opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
					win = window.open(url + param, 'rentas_depositos', opt);
				
				win.focus();
			}
		}).send();
	}
}

var Cancelar = function() {
	$('filas').empty();
	
	crearFila.run(0);
	
	calcularTotal.run();
	
	$('num_cia').select();
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
