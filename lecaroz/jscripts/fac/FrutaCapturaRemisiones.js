// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$$('input[id=num_cia]').each(function(el, i) {
		el.addEvents({
			'change': ObtenerCia.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=codmp]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=num_cia]')[i - 1].select();
					}
					else {
						$$('input[id=num_cia]')[$$('input[id=num_cia]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=num_cia]').length - 1) {
						$$('input[id=num_cia]')[i + 1].select();
					}
					else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=codmp]').each(function(el, i) {
		el.addEvents({
			'change': ObtenerMP.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=num_rem]')[i].select();
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=num_cia]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=codmp]')[i - 1].select();
					}
					else {
						$$('input[id=codmp]')[$$('input[id=codmp]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=codmp]').length - 1) {
						$$('input[id=codmp]')[i + 1].select();
					}
					else {
						$$('input[id=codmp]')[0].select();
					}
				}
			}
		});
	});
	
	$$('select[id=num_pro]').each(function(el, i) {
		el.addEvent('change', ValidarRemision.pass(i));
	});
	
	$$('input[id=num_rem]').each(function(el, i) {
		el.addEvents({
			'change': ValidarRemision.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=codmp]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=num_rem]')[i - 1].select();
					}
					else {
						$$('input[id=num_rem]')[$$('input[id=num_rem]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=num_rem]').length - 1) {
						$$('input[id=num_rem]')[i + 1].select();
					}
					else {
						$$('input[id=num_rem]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=fecha]').each(function(el, i) {
		el.addEvents({
			'change': ValidarFecha.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					if ($$('input[id=codmp]')[i].get('value').getNumericValue() == 179) {
						$$('input[id=total]')[i].select();
					}
					else {
						$$('input[id=cantidad]')[i].select();
					}
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=num_rem]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=fecha]')[i - 1].select();
					}
					else {
						$$('input[id=fecha]')[$$('input[id=fecha]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=fecha]').length - 1) {
						$$('input[id=fecha]')[i + 1].select();
					}
					else {
						$$('input[id=fecha]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=cantidad]').each(function(el, i) {
		el.addEvents({
			'change': CalcularTotal.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=precio]')[i].select();
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=cantidad]')[i - 1].select();
					}
					else {
						$$('input[id=cantidad]')[$$('input[id=cantidad]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=cantidad]').length - 1) {
						$$('input[id=cantidad]')[i + 1].select();
					}
					else {
						$$('input[id=cantidad]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=precio]').each(function(el, i) {
		el.addEvents({
			'change': CalcularTotal.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i + 1 > $$('input[id=num_cia]').length - 1 && $$('input[id=codmp]')[i].get('value').getNumericValue() == 291) {
						AgregarFila(i + 1);
						
						$$('input[id=num_cia]')[i + 1].select();
					}
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=cantidad]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=precio]')[i - 1].select();
					}
					else {
						$$('input[id=precio]')[$$('input[id=precio]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=precio]').length - 1) {
						$$('input[id=precio]')[i + 1].select();
					}
					else {
						$$('input[id=precio]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=total]').each(function(el, i) {
		el.addEvents({
			'change': CalcularTotal.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i + 1 > $$('input[id=num_cia]').length - 1 && $$('input[id=codmp]').get('value').getNumericValue() == 179) {
						AgregarFila(i + 1);
						
						$$('input[id=num_cia]')[i + 1].select();
					}
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=fecha]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=total]')[i - 1].select();
					}
					else {
						$$('input[id=total]')[$$('input[id=total]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=total]').length - 1) {
						$$('input[id=total]')[i + 1].select();
					}
					else {
						$$('input[id=total]')[0].select();
					}
				}
			}
		});
	});
	
	$('ingresar').addEvent('click', IngresarRemisiones);
	
	$('num_cia').select();
});

var ObtenerCia = function() {
	var i = arguments[0];
	
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FrutaCapturaRemisiones.php',
			'data': 'accion=obtenerCia&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$$('input[id=nombre_cia]')[i].set('value', result);
					
					ValidarFecha.run(i);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$$('input[id=num_cia]')[i].set('value', $$('input[id=num_cia]')[i].retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('input[id=num_cia]')[i].set('value', '');
		$$('input[id=nombre_cia]')[i].set('value', '');
	}
}

var ObtenerMP = function() {
	var i = arguments[0];
	
	if ($$('input[id=codmp]')[i].get('value').getNumericValue() > 0 && $$('input[id=num_cia]')[i].get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$$('input[id=codmp]')[i].set('value', '');
		
		$$('input[id=num_cia]')[i].select();
	}
	else if ($$('input[id=codmp]')[i].get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FrutaCapturaRemisiones.php',
			'data': 'accion=obtenerMP&codmp=' + $$('input[id=codmp]')[i].get('value') + '&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);
				
				if (data.status > 0) {
					$$('input[id=nombre_mp]')[i].set('value', data.nombre);
					
					updSelect($$('select[id=num_pro]')[i], data.options);
				}
				else if (data.status == -1) {
					alert('El producto no se encuentra en el catálogo o la compañía no lo tiene registrado');
					
					$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', '')).select();
				}
				else if (data.status == -2) {
					alert('El producto no lo surte ningun proveedor');
					
					$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('input[id=codmp]')[i].set('value', '');
		$$('input[id=nombre_mp]')[i].set('value', '');
	}
}

var ValidarFecha = function() {
	var i = arguments[0];
	
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0 && $$('input[id=fecha]')[i].get('value') != '') {
		new Request({
			'url': 'FrutaCapturaRemisiones.php',
			'data': 'accion=validarFecha&num_cia=' + $$('input[id=num_cia]')[i].get('value') + '&fecha=' + $$('input[id=fecha]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);
				
				if (data.status < 0) {
					switch(data.status) {
						case -1:
							alert('No puede capturar facturas de meses anteriores');
						break;
						
						case -2:
							alert('No puede capturar facturas de meses posteriores');
						break;
					}
					
					$$('input[id=fecha]')[i].set('value', $('fecha').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var ValidarRemision = function() {
	var i = arguments[0];
	
	if ($$('select[id=num_pro]')[i].get('value').getNumericValue() > 0 && $$('input[id=num_rem]')[i].get('value') != '') {
		$$('select[id=num_pro]').each(function(el, index, arr) {
			if (i != index
				&& $$('select[id=num_pro]')[i].get('value').getNumericValue() == el.get('value').getNumericValue()
				&& $$('input[id=num_rem]')[i].get('value').toUpperCase() == $$('input[id=num_rem]')[index].get('value')
				&& $$('input[id=codmp]')[i].get('value').getNumericValue() == $$('input[id=codmp]')[index].get('value').getNumericValue()) {
				alert('El producto para la remisión ya esta esta capturado en otra fila');
				
				$$('input[id=num_rem]')[i].set('value', '').select();
				
				return false;
			}
			else if (i != index
				&& $$('select[id=num_pro]')[i].get('value').getNumericValue() == el.get('value').getNumericValue()
				&& $$('input[id=num_rem]')[i].get('value').toUpperCase() == $$('input[id=num_rem]')[index].get('value')
				&& $$('input[id=num_cia]')[i].get('value').getNumericValue() != $$('input[id=num_cia]')[index].get('value').getNumericValue()) {
				alert('Es la misma remisión pero las compañías no coinciden');
				
				$$('input[id=num_rem]')[i].set('value', '').select();
				
				return false;
			}
		});
		
		new Request({
			'url': 'FrutaCapturaRemisiones.php',
			'data': 'accion=validarRemision&num_pro=' + $$('select[id=num_pro]')[i].get('value') + '&num_rem=' + $$('input[id=num_rem]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result),
						string = '';
					
					string += 'La remisión ya ha sido registrada con los siguientes datos:\n';
					string += '\nCompañía:\t\t' + data.num_cia + ' ' + data.nombre_cia;
					string += '\nFecha:\t\t\t' + data.fecha;
					string += '\nProveedor:\t\t' + data.num_pro + ' ' + data.nombre_pro;
					string += '\nRemisión:\t\t' + data.num_rem;
					if (data.num_fact != null) {
						string += '\nFactura:\t\t' + data.num_fact;
					}
					string += '\nTotal:\t\t\t' + data.total.numberFormat(2, '.', ',');
					
					alert(string);
					
					$$('input[id=num_rem]')[i].set('value', $$('input[id=num_rem]')[i].retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('input[id=num_rem]')[i].set('value', '');
		$$('select[id=num_pro]')[i].focus();
	}
}

var CalcularTotal = function() {
	var i = arguments[0],
		codmp = 0,
		cantidad = 0,
		precio = 0,
		total = 0;
	
	codmp = $$('input[id=codmp]')[i].get('value').getNumericValue();
	
	if (codmp == 291) {
		cantidad = $$('input[id=cantidad]')[i].get('value').getNumericValue();
		precio = $$('input[id=precio]')[i].get('value').getNumericValue();
		total = cantidad * precio;
		
		$$('input[id=total]')[i].set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
	}
	else if (codmp == 179) {
		total = $$('input[id=total]')[i].get('value').getNumericValue();
		cantidad = total;
		precio = 1;
		
		$$('input[id=cantidad]')[i].set('value', cantidad > 0 ? cantidad : '');
		$$('input[id=precio]')[i].set('value', precio > 0 ? precio.numberFormat(2, '.', ',') : '');
	}
}

var AgregarFila = function() {
	var i = arguments[0],
		tbody = $('tbody'),
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject(tbody),
		td1 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td2 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td3 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td4 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td5 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td6 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td7 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td8 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		num_cia = new Element('input', {
			'id': 'num_cia',
			'name': 'num_cia[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).inject(td1),
		nombre_cia = new Element('input', {
			'id': 'nombre_cia',
			'name': 'nombre_cia[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td1),
		codmp = new Element('input', {
			'id': 'codmp',
			'name': 'codmp[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).inject(td2),
		nombre_mp = new Element('input', {
			'id': 'nombre_mp',
			'name': 'nombre_mp[]',
			'type': 'text',
			'size': 15,
			'disabled': true
		}).inject(td2),
		num_pro = new Element('select', {
			'id': 'num_pro',
			'name': 'num_pro[]'
		}).inject(td3),
		num_rem = new Element('input', {
			'id': 'num_rem',
			'name': 'num_rem[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus onlyNumbersAndLetters toUpper'
		}).inject(td4),
		fecha = new Element('input', {
			'id': 'fecha',
			'name': 'fecha[]',
			'type': 'text',
			'size': 10,
			'maxlength': 10,
			'class': 'valid Focus toDate center'
		}).inject(td5),
		cantidad = new Element('input', {
			'id': 'cantidad',
			'name': 'cantidad[]',
			'type': 'text',
			'size': 8,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td6),
		precio = new Element('input', {
			'id': 'precio',
			'name': 'precio[]',
			'type': 'text',
			'size': 8,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td7),
		total = new Element('input', {
			'id': 'total',
			'name': 'total[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td8);
	
	validator.addElementEvents(num_cia);
	validator.addElementEvents(codmp);
	validator.addElementEvents(num_rem);
	validator.addElementEvents(fecha);
	validator.addElementEvents(cantidad);
	validator.addElementEvents(precio);
	validator.addElementEvents(total);
	
	
	styles.addElementEvents(num_cia);
	styles.addElementEvents(codmp);
	styles.addElementEvents(num_rem);
	styles.addElementEvents(fecha);
	styles.addElementEvents(cantidad);
	styles.addElementEvents(precio);
	styles.addElementEvents(total);
	
	num_cia.addEvents({
		'change': ObtenerCia.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=codmp]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=num_cia]')[i - 1].select();
				}
				else {
					$$('input[id=num_cia]')[$$('input[id=num_cia]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=num_cia]').length - 1) {
					$$('input[id=num_cia]')[i + 1].select();
				}
				else {
					$$('input[id=num_cia]')[0].select();
				}
			}
		}
	});
	
	codmp.addEvents({
		'change': ObtenerMP.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=num_rem]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=num_cia]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=codmp]')[i - 1].select();
				}
				else {
					$$('input[id=codmp]')[$$('input[id=codmp]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=codmp]').length - 1) {
					$$('input[id=codmp]')[i + 1].select();
				}
				else {
					$$('input[id=codmp]')[0].select();
				}
			}
		}
	});
	
	num_pro.addEvent('change', ValidarRemision.pass(i));
	
	num_rem.addEvents({
		'change': ValidarRemision.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=fecha]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=codmp]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=num_rem]')[i - 1].select();
				}
				else {
					$$('input[id=num_rem]')[$$('input[id=num_rem]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=num_rem]').length - 1) {
					$$('input[id=num_rem]')[i + 1].select();
				}
				else {
					$$('input[id=num_rem]')[0].select();
				}
			}
		}
	});
	
	fecha.addEvents({
		'change': ValidarFecha.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				if ($$('input[id=codmp]')[i].get('value').getNumericValue() == 179) {
					$$('input[id=total]')[i].select();
				}
				else {
					$$('input[id=cantidad]')[i].select();
				}
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=num_rem]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=fecha]')[i - 1].select();
				}
				else {
					$$('input[id=fecha]')[$$('input[id=fecha]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=fecha]').length - 1) {
					$$('input[id=fecha]')[i + 1].select();
				}
				else {
					$$('input[id=fecha]')[0].select();
				}
			}
		}
	});
	
	cantidad.addEvents({
		'change': CalcularTotal.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=fecha]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=cantidad]')[i - 1].select();
				}
				else {
					$$('input[id=cantidad]')[$$('input[id=cantidad]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=cantidad]').length - 1) {
					$$('input[id=cantidad]')[i + 1].select();
				}
				else {
					$$('input[id=cantidad]')[0].select();
				}
			}
		}
	});
	
	precio.addEvents({
		'change': CalcularTotal.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (i + 1 > $$('input[id=num_cia]').length - 1 && $$('input[id=codmp]')[i].get('value').getNumericValue() == 291) {
					AgregarFila(i + 1);
					
					$$('input[id=num_cia]')[i + 1].select();
				}
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=cantidad]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=precio]')[i - 1].select();
				}
				else {
					$$('input[id=precio]')[$$('input[id=precio]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=precio]').length - 1) {
					$$('input[id=precio]')[i + 1].select();
				}
				else {
					$$('input[id=precio]')[0].select();
				}
			}
		}
	});
	
	total.addEvents({
		'change': CalcularTotal.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (i + 1 > $$('input[id=num_cia]').length - 1 && $$('input[id=codmp]')[i].get('value').getNumericValue() == 179) {
					AgregarFila(i + 1);
					
					$$('input[id=num_cia]')[i + 1].select();
				}
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=total]')[i - 1].select();
				}
				else {
					$$('input[id=total]')[$$('input[id=total]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=total]').length - 1) {
					$$('input[id=total]')[i + 1].select();
				}
				else {
					$$('input[id=total]')[0].select();
				}
			}
		}
	});
}

var IngresarRemisiones = function() {
	if (confirm('¿Son correctos los datos de la remisión?')) {
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
			'url': 'FrutaCapturaRemisiones.php',
			'data': 'accion=ingresar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				$('tbody').empty();
				
				AgregarFila.run(0);
			}
		}).send();
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
