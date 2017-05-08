// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': ObtenerCia,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'change': ValidarFecha,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha_precio').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('fecha_precio').addEvents({
		'change': ValidarFechaPrecio,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_rem').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('num_pro').addEvent('change', CambiarPrecio);
	
	$('num_rem').addEvents({
		'change': ValidarRemision,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('cajas').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('cajas').addEvents({
		'change': CalcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('peso_bruto_remision').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_rem').select();
			}
		}
	});
	
	$('peso_bruto_remision').addEvents({
		'change': CalcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$$('input[id=pesada]')[0].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('cajas').select();
			}
		}
	});
	
	$$('input[id=pesada]').each(function(el, i) {
		el.addEvents({
			'change': CalcularPesadas,
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					e.stop();
					
					if (e.key == 'enter' && (i + 1) > ($$('input[id=pesada]').length - 1)) {
						InsertarPesada.run(i + 1);
					}
					
					if (i < $$('input[id=pesada]').length - 1) {
						$$('input[id=pesada]')[i + 1].select();
					}
					else {
						$$('input[id=pesada]')[0].select();
					}
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=pesada]')[i - 1].select();
					}
					else {
						$$('input[id=pesada]')[$$('input[id=pesada]').length - 1].select();
					}
				}
			}
		});
	});
	
	$('ingresar').addEvent('click', IngresarRemision);
	
	$('num_cia').select();
});

var InsertarPesada = function() {
	var i = arguments[0],
		tabla = $('tabla_pesadas'),
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject(tabla),
		td = new Element('td', {
			'align': 'center'
		}).inject(tr),
		input = new Element('input', {
			'id': 'pesada',
			'name': 'pesada[]',
			'type': 'text',
			'class': 'valid Focus numberPosFormat right',
			'precision': 2,
			'size': 12
		}).inject(td);
	
	validator.addElementEvents(input);
	styles.addElementEvents(input);
	
	input.addEvents({
		'change': CalcularPesadas,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				if (e.key == 'enter' && (i + 1) > ($$('input[id=pesada]').length - 1)) {
					InsertarPesada.run(i + 1);
				}
				
				if (i < $$('input[id=pesada]').length - 1) {
					$$('input[id=pesada]')[i + 1].select();
				}
				else {
					$$('input[id=pesada]')[0].select();
				}
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=pesada]')[i - 1].select();
				}
				else {
					$$('input[id=pesada]')[$$('input[id=pesada]').length - 1].select();
				}
			}
		}
	});
}

var ObtenerCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'HuevoCapturaRemisiones.php',
			'data': 'accion=obtenerCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
					
					ValidarFecha.run();
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia').set('value', '');
		
		ValidarFecha.run();
	}
}

var ValidarFecha = function() {
	if ($('num_cia').get('value').getNumericValue() > 0 && $('fecha').get('value') != '') {
		new Request({
			'url': 'HuevoCapturaRemisiones.php',
			'data': 'accion=validarFecha&num_cia=' + $('num_cia').get('value') + '&fecha=' + $('fecha').get('value'),
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
						
						case -3:
							alert('No hay precios para el día especificado');
						break;
					}
					
					$('fecha').set('value', $('fecha').retrieve('tmp', '')).select();
				}
				else {
					if ($('fecha_precio').get('value') == '') {
						updSelect($('num_pro'), data.options);
						
						CambiarPrecio.run();
					}
				}
			}
		}).send();
	}
	else {
		updSelect($('num_pro'), []);
		
		CambiarPrecio.run();
	}
}

var ValidarFechaPrecio = function() {
	if ($('fecha_precio').get('value') != '') {
		new Request({
			'url': 'HuevoCapturaRemisiones.php',
			'data': 'accion=validarFechaPrecio&fecha=' + $('fecha').get('value') + '&fecha_precio=' + $('fecha_precio').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);
				
				if (data.status < 0) {
					switch(data.status) {
						case -1:
							alert('La fecha del precio no puede tener 5 días de antigüedad');
						break;
						
						case -2:
							alert('La fecha del precio no puede ser mayor a la fecha de la remisión');
						break;
						
						case -3:
							alert('No hay precios para el día especificado');
						break;
					}
					
					$('fecha_precio').set('value', $('fecha_precio').retrieve('tmp', '')).select();
				}
				else {
					updSelect($('num_pro'), data.options);
					
					CambiarPrecio.run();
				}
			}
		}).send();
	}
	else {
		ValidarFecha.run();
	}
}

var CambiarPrecio = function() {
	if ($('num_pro').get('value') != '') {
		var data = JSON.decode($('num_pro').get('value'));
		
		$('precio').set('value', data.precio.numberFormat(2, '.', ','));
	}
	else {
		$('precio').set('value', '');
	}
	
	$('num_rem').set('value', '');
	
	CalcularTotal.run();
}

var ValidarRemision = function() {
	if ($('num_pro').get('value') != '' && $('num_rem').get('value') != '') {
		var data = JSON.decode($('num_pro').get('value'));
		
		new Request({
			'url': 'HuevoCapturaRemisiones.php',
			'data': 'accion=validarRemision&num_pro=' + data.num_pro + '&num_rem=' + $('num_rem').get('value'),
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
					string += '\n\nCajas:\t\t\t' + data.cajas.numberFormat(0, '', ',');
					string += '\nPeso bruto:\t\t' + data.peso_bruto.numberFormat(2, '.', ',');
					string += '\nTara:\t\t\t' + data.tara.numberFormat(2, '.', ',');
					string += '\nPeso neto:\t\t' + data.peso_neto.numberFormat(2, '.', ',');
					string += '\nPrecio:\t\t\t' + data.precio.numberFormat(2, '.', ',');
					string += '\nTotal:\t\t\t' + data.total.numberFormat(2, '.', ',');
					
					alert(string);
					
					$('num_rem').set('value', $('num_rem').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		alert('Debe especificar el proveedor');
		
		$('num_rem').set('value', '');
		$('num_pro').focus();
	}
}

var CalcularPesadas = function() {
	var pesadas = $$('input[id=pesada]').get('value').getNumericValue().sum();
	
	$('pesadas').set('value', pesadas > 0 ? pesadas.numberFormat(2, '.', ',') : '');
	
	CalcularTotal.run();
}

var CalcularTotal = function() {
	var cajas = 0,
		peso_bruto_remision = 0,
		peso_bruto = 0,
		tara = 0,
		peso_neto = 0,
		precio = 0,
		total = 0;
	
	cajas = $('cajas').get('value').getNumericValue();
	peso_bruto_remision = $('peso_bruto_remision').get('value').getNumericValue();
	peso_bruto = $('pesadas').get('value').getNumericValue();
	tara = cajas * 2;
	peso_neto = (peso_bruto < peso_bruto_remision ? peso_bruto : peso_bruto_remision) - tara;
	precio = $('precio').get('value').getNumericValue();
	total = peso_neto * precio;
	
	$('peso_bruto').set('value', peso_bruto > 0 ? peso_bruto.numberFormat(2, '.', ',') : '');
	$('tara').set('value', tara > 0 ? tara.numberFormat(2, '.', ',') : '');
	$('peso_neto').set('value', peso_neto > 0 ? peso_neto.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var IngresarRemision = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha');
		
		$('fecha').select();
	}
	else if ($('num_pro').get('value') == '') {
		alert('Debe especificar el proveedor');
		
		$('num_pro').focus();
	}
	else if ($('num_rem').get('value') == '') {
		alert('Debe especificar el número de remisión');
		
		$('num_rem').select();
	}
	else if ($('cajas').get('value').getNumericValue() == 0) {
		alert('Debe especificar la cantidad de cajas entregadas');
		
		$('cajas').select();
	}
	else if ($('peso_bruto_remision').get('value').getNumericValue() == 0) {
		alert('Debe especificar el peso bruto de la remisión');
		
		$('peso_bruto').select();
	}
	else if ($('peso_bruto').get('value').getNumericValue() == 0) {
		alert('Debe especificar las pesadas');
		
		$$('input[id=pesada]')[0].select();
	}
	else if ($('total').get('value').getNumericValue() == 0) {
		alert('El total de la remisión no puede ser cero');
		
		$$('input[id=pesada]')[0].select();
	}
	else if (confirm('¿Son correctos los datos de la remisión?')) {
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
			'url': 'HuevoCapturaRemisiones.php',
			'data': 'accion=ingresar&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				$$('#num_cia, #nombre_cia, #fecha, #num_rem, #cajas, #peso_bruto_remision, #peso_bruto, #tara, #peso_neto, #precio, #total, #pesadas').set('value', '');
				
				updSelect($('num_pro'), []);
				
				$('tabla_pesadas').empty();
				
				InsertarPesada.run(0);
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
