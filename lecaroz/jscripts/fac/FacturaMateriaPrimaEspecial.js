// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': validarCia,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_pro').select();
			}
		}
	});
	
	$('num_pro').addEvents({
		'change': validarPro,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('num_fact').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('num_fact').addEvents({
		'change': validarFac,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				$('fecha').select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_pro').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'change': validarFecha,
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'down') {
				e.stop();
				
				if (!$chk($('cantidad'))) {
					alert('No ha seleccionado un proveedor');
					
					$('num_pro').focus();
				}
				else {
					$('codmp').focus();
				}
			}
			else if (e.key == 'up') {
				e.stop();
				
				$('num_fact').select();
			}
		}
	});
	
	$('aclaracion').addEvent('change', function() {
		if (this.get('checked')) {
			$('observaciones').set('disabled', false).focus();
		}
		else {
			$('observaciones').set('disabled', true);
		}
	});
	
	$('observaciones').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if ($chk($('cantidad'))) {
					$('cantidad').focus();
				}
			}
		}
	});
	
	$('ingresar').addEvents({
		'click': Ingresar,
		'dblclick': function() {
			alert('Al hacer doble-click sobre este boton corre el riesgo de duplicar la entrada');
		}
	});
	
	$('num_cia').select();
});

var validarCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=validarCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
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
	}
}

var validarPro = function() {
	if ($('num_pro').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=validarPro&num_pro=' + $('num_pro').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_pro').set('value', result);
					
					$$('#num_fact, #fecha, #observaciones').set('value', '');
					
					$$('#subtotal, #iva_total, #total').set('value', '0.00');
					
					$('aclaracion').set('checked', false);
					$('observaciones').set('disabled', true);
					
					$('productos').empty();
					
					nuevoProducto(0);
				}
			}
		}).send();
	}
	else {
		$('productos').empty();
		
		$$('#num_pro, #nombre_pro, #num_fact, #fecha, #observaciones').set('value', '');
		
		$('aclaracion').set('checked', false);
		$('observaciones').set('disabled', true);
		
		$$('#subtotal, #iva_total, #total').set('value', '0.00');
	}
}

var validarFac = function() {
	if ($('num_pro').get('value').getNumericValue() == 0) {
		alert('Debe especificar el proveedor');
		
		$('num_pro').focus();
	}
	else if ($('num_fact').get('value').clean() != '') {
		new Request({
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=validarFac&num_pro=' + $('num_pro').get('value') + '&num_fact=' + $('num_fact').get('value').toUpperCase(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					alert('La factura ' + data.num_fact + ' ya esta registrada en la compañía ' + data.num_cia + ' ' + data.nombre_cia + ' con fecha ' + data.fecha);
					
					$('num_fact').set('value', $('num_fact').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var validarFecha = function() {
	if ($('fecha').get('value') != '') {
		new Request({
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=validarFecha&num_cia=' + $('num_cia').get('value') + '&fecha=' + $('fecha').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result.getNumericValue() == -1) {
					alert('No puede capturar facturas del mes dado');
					
					$('fecha').set('value', $('fecha').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
}

var nuevoProducto = function() {
	var i = arguments[0];
	
	var tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).addEvents({
			'mouseover': function() {
				this.addClass('highlight');
			},
			'mouseout': function() {
				this.removeClass('highlight');
			}
		}),
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
		td9 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td10 = new Element('td', {
			'align': 'right'
		}).inject(tr),
		td11 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td12 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td13 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td14 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		codmp = new Element('input', {
			'id': 'codmp',
			'name': 'codmp[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt right'
		}).inject(td1),
		nombre_mp = new Element('input', {
			'id': 'nombre_mp',
			'name': 'nombre_mp[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td1),
		producto = new Element('input', {
			'id': 'producto',
			'name': 'producto[]',
			'type': 'hidden',
			'value': ''
		}).inject(td6),
		cantidad = new Element('input', {
			'id': 'cantidad',
			'name': 'cantidad[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td2),
		contenido = new Element('input', {
			'id': 'contenido',
			'name': 'contenido[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td3),
		unidad = new Element('input', {
			'id': 'unidad',
			'name': 'unidad[]',
			'type': 'text',
			'size': 5,
			'disabled': true
		}).inject(td4),
		precio = new Element('input', {
			'id': 'precio',
			'name': 'precio[]',
			'type': 'text',
			'size': 5,
			'class': 'valid Focus numberPosFormat right',
			'precision': 10
		}).inject(td5),
		costales = new Element('input', {
			'id': 'costales',
			'name': 'costales[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right',
			'precision': 0,
			'dec_point': ''
		}).inject(td6),
		precio_costal = new Element('input', {
			'id': 'precio_costal',
			'name': 'precio_costal[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td7),
		pdesc1 = new Element('input', {
			'id': 'pdesc1',
			'name': 'pdesc1[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right blue',
			'styles': {
				'border-color':'#00C'
			},
			'precision': 2
		}).inject(td8),
		desc1 = new Element('input', {
			'id': 'desc1',
			'name': 'desc1[]',
			'type': 'hidden',
			'value': 0
		}).inject(td8),
		pdesc2 = new Element('input', {
			'id': 'pdesc2',
			'name': 'pdesc2[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right blue',
			'styles': {
				'border-color': '#00C'
			},
			'precision': 2
		}).inject(td9),
		desc2 = new Element('input', {
			'id': 'desc2',
			'name': 'desc2[]',
			'type': 'hidden',
			'value': 0
		}).inject(td9),
		pdesc3 = new Element('input', {
			'id': 'pdesc3',
			'name': 'pdesc3[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right blue',
			'styles': {
				'border-color': '#00C'
			},
			'precision': 2
		}).inject(td10),
		desc3 = new Element('input', {
			'id': 'desc3',
			'name': 'desc3[]',
			'type': 'hidden',
			'value': 0
		}).inject(td10),
		ieps = new Element('input', {
			'id': 'ieps',
			'name': 'ieps[]',
			'type': 'text',
			'size': 6,
			'class': 'valid Focus numberPosFormat right red',
			'styles': {
				'border-color': '#C00'
			},
			'precision': 2
		}).inject(td11),
		piva = new Element('input', {
			'id': 'piva',
			'name': 'piva[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus numberPosFormat right red',
			'styles': {
				'border-color': '#C00'
			},
			'precision': 2
		}).inject(td12),
		iva = new Element('input', {
			'id': 'iva',
			'name': 'iva[]',
			'type': 'hidden',
			'value': 0
		}).inject(td12),
		importe = new Element('input', {
			'id': 'importe',
			'name': 'importe[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus numberPosFormat right bold',
			'precision': 2,
			'styles': {
				'width': '98%'
			}
		}).inject(td13),
		regalado = new Element('input', {
			'id': 'regalado',
			'name': 'regalado[]',
			'type': 'checkbox',
			'value': i
		}).inject(td14);
	
	validator.addElementEvents(codmp);
	validator.addElementEvents(cantidad);
	validator.addElementEvents(contenido);
	validator.addElementEvents(precio);
	validator.addElementEvents(costales);
	validator.addElementEvents(precio_costal);
	validator.addElementEvents(precio);
	validator.addElementEvents(pdesc1);
	validator.addElementEvents(pdesc2);
	validator.addElementEvents(pdesc3);
	validator.addElementEvents(ieps);
	validator.addElementEvents(piva);
	validator.addElementEvents(importe);
	
	styles.addElementEvents(codmp);
	styles.addElementEvents(cantidad);
	styles.addElementEvents(contenido);
	styles.addElementEvents(precio);
	styles.addElementEvents(costales);
	styles.addElementEvents(precio_costal);
	styles.addElementEvents(pdesc1);
	styles.addElementEvents(pdesc2);
	styles.addElementEvents(pdesc3);
	styles.addElementEvents(ieps);
	styles.addElementEvents(piva);
	styles.addElementEvents(importe);
	
	codmp.addEvents({
		'change': obtenerProducto.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=cantidad]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=codmp]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=codmp]')[i + 1])) {
					$$('input[id=codmp]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	cantidad.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=contenido]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=codmp]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=cantidad]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=cantidad]')[i + 1])) {
					$$('input[id=cantidad]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	contenido.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=cantidad]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=contenido]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=contenido]')[i + 1])) {
					$$('input[id=contenido]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	precio.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				$$('input[id=costales]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=contenido]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=precio]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=precio]')[i + 1])) {
					$$('input[id=precio]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	costales.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=precio_costal]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=costales]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=costales]')[i + 1])) {
					$$('input[id=costales]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	precio_costal.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=pdesc1]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=costales]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=precio_costal]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=precio_costal]')[i + 1])) {
					$$('input[id=precio_costal]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	pdesc1.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=pdesc2]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=precio_costal]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=pdesc1]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=pdesc1]')[i + 1])) {
					$$('input[id=pdesc1]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	pdesc2.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=pdesc3]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=pdesc1]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=pdesc2]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=pdesc2]')[i + 1])) {
					$$('input[id=pdesc2]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	pdesc3.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=ieps]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=pdesc2]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=pdesc3]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=pdesc3]')[i + 1])) {
					$$('input[id=pdesc3]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	ieps.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=piva]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=pdesc3]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=ieps]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=ieps]')[i + 1])) {
					$$('input[id=ieps]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	piva.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=importe]')[i].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=ieps]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=piva]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=piva]')[i + 1])) {
					$$('input[id=piva]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	importe.addEvents({
		'change': calcularImporte.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (!$chk($$('input[id=codmp]')[i + 1])) {
					nuevoProducto(i + 1);
				}
				
				$$('input[id=codmp]')[i + 1].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=piva]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if ((i - 1) >= 0) {
					$$('input[id=importe]')[i - 1].select();
				}
				else {
					$('fecha').select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if ($chk($$('input[id=importe]')[i + 1])) {
					$$('input[id=importe]')[i + 1].select();
				}
				else {
					$('num_cia').select();
				}
			}
		}
	});
	
	regalado.addEvent('change', calcularImporte.pass(i));
	
	tr.inject($('productos'));
}

var obtenerProducto = function() {
	var i = arguments[0];
	
	if ($$('input[id=codmp]')[i].get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=obtenerProducto&num_pro=' + $('num_pro').get('value') + '&codmp=' + $$('input[id=codmp]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$$('input[id=nombre_mp]')[i].set('value', data.nombre_mp);
					$$('input[id=producto]')[i].set('value', result);
					$$('input[id=cantidad]')[i].set('value', '');
					$$('input[id=contenido]')[i].set('value', data.contenido.numberFormat(2, '.', ','));
					$$('input[id=unidad]')[i].set('value', data.unidad);
					$$('input[id=precio]')[i].set('value', data.precio.numberFormat(10, '.', ','));
					$$('input[id=costales]')[i].set('value', '');
					$$('input[id=precio_costal]')[i].set('value', '');
					$$('input[id=pdesc1]')[i].set('value', data.pdesc1 > 0 ? data.pdesc1.numberFormat(2, '.', ',') : '');
					$$('input[id=pdesc2]')[i].set('value', data.pdesc2 > 0 ? data.pdesc2.numberFormat(2, '.', ',') : '');
					$$('input[id=pdesc3]')[i].set('value', data.pdesc3 > 0 ? data.pdesc3.numberFormat(2, '.', ',') : '');
					$$('input[id=desc1]')[i].set('value', 0);
					$$('input[id=desc2]')[i].set('value', 0);
					$$('input[id=desc3]')[i].set('value', 0);
					$$('input[id=ieps]')[i].set('value', '');
					$$('input[id=piva]')[i].set('value', data.piva > 0 ? data.piva.numberFormat(2, '.', ',') : '');
					$$('input[id=iva]')[i].set('value', 0);
					$$('input[id=importe]')[i].set('value', '');
					$$('input[id=regalado]')[i].set('checked', false);
				}
				else {
					alert('El producto no se encuentra en el catálogo del proveedor');
					
					$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', '')).focus();
				}
			}
		}).send();
	}
	else {
		$$('input[id=nombre_mp]')[i].set('value', '');
		$$('input[id=producto]')[i].set('value', '');
		$$('input[id=cantidad]')[i].set('value', '');
		$$('input[id=contenido]')[i].set('value', '');
		$$('input[id=unidad]')[i].set('value', '');
		$$('input[id=precio]')[i].set('value', '');
		$$('input[id=contenido]')[i].set('value', '');
		$$('input[id=precio_costal]')[i].set('value', '');
		$$('input[id=pdesc1]')[i].set('value', '');
		$$('input[id=desc1]')[i].set('value', '');
		$$('input[id=pdesc2]')[i].set('value', '');
		$$('input[id=desc2]')[i].set('value', '');
		$$('input[id=pdesc3]')[i].set('value', '');
		$$('input[id=desc3]')[i].set('value', '');
		$$('input[id=piva]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', '');
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=importe]')[i].set('value', '');
		$$('input[id=regalado]')[i].set('checked', false);
	}
	
	calcularTotal.run();
}

var calcularImporte = function() {
	var i = arguments[0],
		cantidad = $$('input[id=cantidad]')[i].get('value').getNumericValue(),
		contenido = $$('input[id=contenido]')[i].get('value').getNumericValue(),
		precio = $$('input[id=precio]')[i].get('value').getNumericValue(),
		costales = $$('input[id=costales]')[i].get('value').getNumericValue(),
		precio_costal = $$('input[id=precio_costal]')[i].get('value').getNumericValue(),
		pdesc1 = $$('input[id=pdesc1]')[i].get('value').getNumericValue(),
		pdesc2 = $$('input[id=pdesc2]')[i].get('value').getNumericValue(),
		pdesc3 = $$('input[id=pdesc3]')[i].get('value').getNumericValue(),
		ieps = $$('input[id=ieps]')[i].get('value').getNumericValue(),
		piva = $$('input[id=piva]')[i].get('value').getNumericValue(),
		desc1 = 0,
		desc2 = 0,
		desc3 = 0,
		iva = 0,
		importe = $$('input[id=importe]')[i].get('value').getNumericValue();
	
	if ($$('input[id=codmp]')[i].get('value').getNumericValue() == 0) {
		$$('input[id=cantidad]')[i].set('value', '');
		$$('input[id=contenido]')[i].set('value', '');
		$$('input[id=precio]')[i].set('value', '');
		$$('input[id=costales]')[i].set('value', '');
		$$('input[id=precio_costal]')[i].set('value', '');
		$$('input[id=pdesc1]')[i].set('value', '');
		$$('input[id=pdesc2]')[i].set('value', '');
		$$('input[id=pdesc3]')[i].set('value', '');
		$$('input[id=desc1]')[i].set('value', 0);
		$$('input[id=desc2]')[i].set('value', 0);
		$$('input[id=desc3]')[i].set('value', 0);
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=piva]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', 0);
		$$('input[id=importe]')[i].set('value', '');
	}
	else if ($$('input[id=regalado]')[i].get('checked')) {
		$$('input[id=desc1]')[i].set('value', 0);
		$$('input[id=desc2]')[i].set('value', 0);
		$$('input[id=desc3]')[i].set('value', 0);
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', 0);
		$$('input[id=importe]')[i].set('value', '');
	}
	else if (cantidad == 0 && importe > 0) {
		cantidad = importe;
		
		contenido = $$('input[id=codmp]')[i].get('value').getNumericValue() == 148 ? 360 : 1;
		
		precio = 1;
		
		iva = importe * (piva / 100).round(2);
		
		$$('input[id=cantidad]')[i].set('value', cantidad.numberFormat(2, '.', ','));
		$$('input[id=contenido]')[i].set('value', contenido.numberFormat(2, '.', ','));
		$$('input[id=precio]')[i].set('value', precio.numberFormat(10, '.', ','));
		$$('input[id=pdesc1]')[i].set('value', '');
		$$('input[id=pdesc2]')[i].set('value', '');
		$$('input[id=pdesc3]')[i].set('value', '');
		$$('input[id=desc1]')[i].set('value', 0);
		$$('input[id=desc2]')[i].set('value', 0);
		$$('input[id=desc3]')[i].set('value', 0);
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', iva > 0 ? iva : '');
	}
	else if (cantidad > 0 && contenido > 0 && precio == 0 && importe > 0) {
		precio = (importe / cantidad).round(10);
		
		iva = (importe * (piva / 100)).round(2);
		
		$$('input[id=precio]')[i].set('value', precio.numberFormat(10, '.', ','));
		$$('input[id=pdesc1]')[i].set('value', '');
		$$('input[id=pdesc2]')[i].set('value', '');
		$$('input[id=pdesc3]')[i].set('value', '');
		$$('input[id=desc1]')[i].set('value', 0);
		$$('input[id=desc2]')[i].set('value', 0);
		$$('input[id=desc3]')[i].set('value', 0);
		$$('input[id=ieps]')[i].set('value', '');
		$$('input[id=iva]')[i].set('value', iva > 0 ? iva : '');
		$$('input[id=importe]')[i].set('value', importe > 0 ? importe.numberFormat(2, '.', ',') : '');
	}
	else if (cantidad > 0 && contenido > 0 && precio > 0) {
		var data = JSON.decode($$('input[id=producto]')[i].get('value'));

		importe = (cantidad * precio).round(2);
		
		importe = (importe - costales * precio_costal).round(2);
		
		desc1 = (importe * (pdesc1 / 100)).round(2);
		importe = importe - desc1;
		
		desc2 = (importe * (pdesc2 / 100)).round(2);
		importe = importe - desc2;
		
		desc3 = (importe * (pdesc3 / 100)).round(2);
		importe = importe - desc3;

		ieps = data.pieps > 0 ? (importe * data.pieps).round(2) / 100 : $$('input[id=ieps]')[i].get('value').getNumericValue();
		
		importe = importe + ieps;
		
		iva = (importe * (piva / 100)).round(2);
		
		$$('input[id=desc1]')[i].set('value', desc1 > 0 ? desc1 : '');
		$$('input[id=desc2]')[i].set('value', desc2 > 0 ? desc2 : '');
		$$('input[id=desc3]')[i].set('value', desc3 > 0 ? desc3 : '');
		$$('input[id=ieps]')[i].set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
		$$('input[id=iva]')[i].set('value', iva > 0 ? iva : '');
		$$('input[id=importe]')[i].set('value', importe > 0 ? importe.numberFormat(2, '.', ',') : '');
	}
//	else {
//		var data = JSON.decode($$('input[id=producto]')[i].get('value'));
//		
//		$$('input[id=cantidad]')[i].set('value', '');
//		$$('input[id=contenido]')[i].set('value', data.contenido.numberFormat(2, '.', ','));
//		$$('input[id=precio]')[i].set('value', data.precio.numberFormat(10, '.', ','));
//		$$('input[id=pdesc1]')[i].set('value', data.pdesc1 > 0 ? data.pdesc1.numberFormat(2, '.', ',') : '');
//		$$('input[id=pdesc2]')[i].set('value', data.pdesc2 > 0 ? data.pdesc2.numberFormat(2, '.', ',') : '');
//		$$('input[id=pdesc3]')[i].set('value', data.pdesc3 > 0 ? data.pdesc3.numberFormat(2, '.', ',') : '');
//		$$('input[id=desc1]')[i].set('value', 0);
//		$$('input[id=desc2]')[i].set('value', 0);
//		$$('input[id=desc3]')[i].set('value', 0);
//		$$('input[id=piva]')[i].set('value', data.piva > 0 ? data.piva.numberFormat(2, '.', ',') : '');
//		$$('input[id=iva]')[i].set('value', 0);
//	}
	
	calcularTotal.run();
}

var calcularTotal = function() {
	$('subtotal').set('value', $$('input[id=importe]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
	$('iva_total').set('value', $$('input[id=iva]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
	$('total').set('value', ($('subtotal').get('value').getNumericValue() + $('iva_total').get('value').getNumericValue()).numberFormat(2, '.', ','));
}

var Ingresar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').focus();
	}
	else if ($('num_pro').get('value').getNumericValue() == 0) {
		alert('Debe especificar el proveedor');
		
		$('num_pro').focus();
	}
	else if ($('num_fact').get('value').clean() == '') {
		alert('Debe especificar el número de factura');
		
		$('num_fact').focus();
	}
	else if ($('fecha').get('value').length < 10) {
		alert('Debe especificar la fecha de la factura');
		
		$('fecha').focus();
	}
	else if ($('total').get('value').getNumericValue() == 0) {
		alert('El importe de la factura debe ser mayor a 0');
		
		$('cantidad').select();
	}
	else if ($('aclaracion').get('checked') && $('observaciones').get('value').length == 0) {
		alert('Debe especificar el porque se debe aclarar la factura');
		
		$('observaciones').focus();
	}
	else if ($('aclaracion').get('checked') && $('observaciones').get('value').length > 1000) {
		alert('Las observaciones para la aclaración son demasiado largas');
		
		$('observaciones').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, textarea').each(function(el) {
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
			'url': 'FacturaMateriaPrimaEspecial.php',
			'data': 'accion=ingresar&' + queryString.join('&'),
			'onRequest': function() {
				$('ingresar').set('disabled', true);
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert('Error al ingresar la factura, avisar al administrador');
				}
				else {
					$$('#num_cia, #nombre_cia, #num_fact, #fecha, #observaciones').set('value', '');
					
					$('aclaracion').set('checked', false);
					$('observaciones').set('disabled', true);
					
					$$('#subtotal, #iva_total, #total').set('value', '0.00');
					
					$('productos').empty();
					
					nuevoProducto(0);
					
					$('num_cia').focus();
					
					$('ingresar').set('disabled', false);
				}
			}
		}).send();
	}
}
