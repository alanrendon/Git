var rows = [];

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	new_row(0);
	
	$('registrar').addEvent('click', validar);
	
	$('num_cia').select();
});

var new_row = function(i) {
	var tbody = $('ventas'),
		tr = new Element('tr', {
			class: i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject(tbody),
		td1 = new Element('td', {
			align: 'center'
		}).inject(tr),
		td2 = new Element('td', {
			align: 'center'
		}).inject(tr),
		td3 = new Element('td', {
			align: 'center'
		}).inject(tr),
		td4 = new Element('td', {
			align: 'center'
		}).inject(tr),
		num_cia = new Element('input', {
			id: 'num_cia',
			name: 'num_cia[]',
			type: 'text',
			size: 3,
			class: 'valid Focus toPosInt right'
		}).addEvents({
			change: nombreCia.pass(i),
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				} else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=num_cia]')[i - 1].select();
					} else {
						$$('input[id=num_cia]')[$$('input[id=num_cia]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=num_cia]').length - 1) {
						$$('input[id=num_cia]')[i + 1].select();
					} else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		}).inject(td1),
		nombre_cia = new Element('input', {
			id: 'nombre_cia',
			name: 'nombre_cia[]',
			type: 'text',
			size: 30,
			disabled: true
		}).inject(td1),
		fecha = new Element('input', {
			id: 'fecha',
			name: 'fecha[]',
			type: 'text',
			size: 10,
			maxlength: 10,
			class: 'valid Focus toDate center'
		}).addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=folio]')[i].select();
				} else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=num_cia]')[i].select();
				} else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=fecha]')[i - 1].select();
					} else {
						$$('input[id=fecha]')[$$('input[id=fecha]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=fecha]').length - 1) {
						$$('input[id=fecha]')[i + 1].select();
					} else {
						$$('input[id=fecha]')[0].select();
					}
				}
			}
		}).inject(td2),
		folio = new Element('input', {
			id: 'folio',
			name: 'folio[]',
			type: 'text',
			size: 8,
			class: 'valid Focus onlyNumbersAndLetters toUpper right'
		}).addEvents({
			change: function() {
				if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0
					&& $$('input[id=folio]')[i].get('value') != ''
					&& rows.filter(function(el) {
						return el.num_cia == $$('input[id=num_cia]')[i].get('value').getNumericValue() && el.folio == $$('input[id=folio]')[i].get('value').replace(/[^a-zA-Z0-9\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, '').toUpperCase()
					}).length > 0) {
					alert('El folio ' + $$('input[id=folio]')[i].get('value') + ' ya ha sido capturado');
					
					$$('input[id=folio]')[i].set('value', $$('input[id=folio]')[i].retrieve('tmp', '')).select();
				} else {
					rows[i].num_cia = $$('input[id=num_cia]')[i].get('value').getNumericValue();
					rows[i].folio = $$('input[id=folio]')[i].get('value').replace(/[^a-zA-Z0-9\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, '').toUpperCase();
					
					$$('input[id=folio]')[i].select();
				}
			},
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=importe]')[i].select();
				} else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				} else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=folio]')[i - 1].select();
					} else {
						$$('input[id=folio]')[$$('input[id=folio]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=folio]').length - 1) {
						$$('input[id=folio]')[i + 1].select();
					} else {
						$$('input[id=folio]')[0].select();
					}
				}
			}
		}).inject(td3),
		importe = new Element('input', {
			id: 'importe',
			name: 'importe[]',
			type: 'text',
			size: 10,
			class: 'valid Focus numberPosFormat right',
			precision: 2
		}).addEvents({
			change: calcularTotal,
			keydown: function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i + 1 > $$('input[id=num_cia]').length - 1) {
						new_row(i + 1);
					}
					
					$$('input[id=num_cia]')[i + 1].select();
				} else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=folio]')[i].select();
				} else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=importe]')[i - 1].select();
					} else {
						$$('input[id=importe]')[$$('input[id=importe]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=importe]').length - 1) {
						$$('input[id=importe]')[i + 1].select();
					} else {
						$$('input[id=importe]')[0].select();
					}
				}
			}
		}).inject(td4);
	
	validator.addElementEvents(num_cia);
	validator.addElementEvents(fecha);
	validator.addElementEvents(folio);
	validator.addElementEvents(importe);
	
	styles.addElementEvents(num_cia);
	styles.addElementEvents(fecha);
	styles.addElementEvents(folio);
	styles.addElementEvents(importe);
	
	rows[i] = {
		num_cia: null,
		folio: null
	};
}

var nombreCia = function(i) {
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0) {
		new Request({
			url: 'VentasImdisco.php',
			data: 'accion=get_cia&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			onSuccess: function(result) {
				if (result != '') {
					if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0
						&& $$('input[id=folio]')[i].get('value') != ''
						&& rows.filter(function(el) {
							return el.num_cia == $$('input[id=num_cia]')[i].get('value').getNumericValue() && el.folio == $$('input[id=folio]')[i].get('value').replace(/[^a-zA-Z0-9\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, '').toUpperCase()
						}).length > 0) {
						alert('El folio ' + $$('input[id=folio]')[i].get('value') + ' ya ha sido capturado');
						
						$$('input[id=num_cia]')[i].set('value', $$('input[id=num_cia]')[i].retrieve('tmp', '')).select();
					} else {
						$$('input[id=nombre_cia]')[i].set('value', result);
						
						rows[i].num_cia = $$('input[id=num_cia]')[i].get('value').getNumericValue();
						rows[i].folio = $$('input[id=folio]')[i].get('value').replace(/[^a-zA-Z0-9\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, '').toUpperCase();
					}
				} else {
					alert('La compañía no se encuentra en el catálogo');
					
					$$('input[id=num_cia]')[i].set('value', $$('input[id=num_cia]')[i].retrieve('tmp', '')).select();
				}
			}
		}).send();
	} else {
		$$('input[id=num_cia]')[i].set('value', '').select();
		$$('input[id=nombre_cia]')[i].set('value', '');
		
		rows[i].num_cia = null;
	}
}

var calcularTotal = function() {
	$('total').set('html', $$('input[id=importe]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
}

var validar = function() {
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
		url: 'VentasImdisco.php',
		data: 'accion=validar&' + queryString.join('&'),
		onRequest: function() {
			popup = new Popup('<img src="/lecaroz/imagenes/_loading.gif" width="16" height="16" /> Validando informaci&oacute;n de ventas', 'Validando informaci&oacute;n', 300, 200, null, null);
		},
		onSuccess: function(result) {
			popup.Close();
			
			if (result != '') {
				popup = new Popup(result, 'Error en los datos', 500, 400, function() {
					$('cerrar').addEvent('click', function() {
						popup.Close();
					});
				}, null);
			} else {
				registrar();
			}
		}
	}).send();
}

var registrar = function() {
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
		url: 'VentasImdisco.php',
		data: 'accion=registrar&' + queryString.join('&'),
		onRequest: function() {
			popup = new Popup('<img src="/lecaroz/imagenes/_loading.gif" width="16" height="16" /> Registrando informaci&oacute;n de ventas', 'Registrando informaci&oacute;n', 300, 200, null, null);
		},
		onSuccess: function(result) {
			popup.Close();
			
			rows = [];
			
			$('ventas').empty();
			
			new_row(0);
			
			$('num_cia').focus();
		}
	}).send();
}
