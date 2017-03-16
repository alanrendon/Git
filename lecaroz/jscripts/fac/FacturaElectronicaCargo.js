// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': cambiaCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'change': $empty,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('nombre_cliente').focus();
			}
		}
	});
	
	$('nombre_cliente').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('rfc').focus();
			}
		}
	});
	
	$('rfc').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('calle').focus();
			}
		}
	});
	
	$('calle').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('no_exterior').focus();
			}
		}
	});
	
	$('no_exterior').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('no_interior').focus();
			}
		}
	});
	
	$('no_interior').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('colonia').focus();
			}
		}
	});
	
	$('colonia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('localidad').focus();
			}
		}
	});
	
	$('localidad').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('referencia').focus();
			}
		}
	});
	
	$('referencia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('municipio').focus();
			}
		}
	});
	
	$('municipio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('estado').focus();
			}
		}
	});
	
	$('estado').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('pais').focus();
			}
		}
	});
	
	$('pais').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('codigo_postal').focus();
			}
		}
	});
	
	$('codigo_postal').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('email_cliente').focus();
			}
		}
	});
	
	$('email_cliente').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('observaciones').focus();
			}
		}
	});
	
	$('observaciones').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('descripcion').focus();
			}
		}
	});
	
	$$('input[id=descripcion]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=iva]')[i].select();
			}
		});
	});
	
	$$('input[id=iva]').each(function(el, i) {
		el.addEvents({
			'change': calcularTotal,
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (!$chk($$('input[id=descripcion]')[i + 1])) {
						newRow(i + 1);
					}
					
					$$('input[id=descripcion]')[i + 1].select();
				}
			}
		});
	});
	
	$('registrar').addEvent('click', Registrar);
	
	$('num_cia').select();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturaElectronicaCargo.php',
			'data': 'accion=cambiaCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('nombre_cia').set('value', data.nombre);
					$('fecha').set('value', data.fecha);
				}
				else {
					alert('La compañía no esta en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		$('fecha').set('value', '');
	}
}

var calcularTotal = function() {
	var iva = 0,
		total = 0;
	
	$$('input[id=iva]').each(function(el) {
		iva += el.get('value').getNumericValue();
	});
	
	total = iva;
	
	$('total_iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 ? total.numberFormat(2, '.', ',') : '');
}

var newRow = function(i) {
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	var td1 = new Element('td', {
		'align': 'center'
	});
	var td2 = new Element('td', {
		'align': 'center'
	});
	
	var descripcion = new Element('input', {
		'id': 'descripcion',
		'name': 'descripcion[]',
		'type': 'text',
		'class': 'valid toText toUpper cleanText',
		'size': 80,
		'maxlength': 200
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('input[id=iva]')[i].select();
		}
	}).inject(td1);
	
	var iva = new Element('input', {
		'id': 'iva',
		'name': 'iva[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'precision': 2,
		'size': 10
	}).addEvents({
		'change': calcularTotal,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (!$chk($$('input[id=descripcion]')[i + 1])) {
					newRow(i + 1);
				}
				
				$$('input[id=descripcion]')[i + 1].select();
			}
		}
	}).inject(td2);
	
	validator.addElementEvents(descripcion);
	validator.addElementEvents(iva);
	
	styles.addElementEvents(descripcion);
	styles.addElementEvents(iva);
	
	td1.inject(tr);
	td2.inject(tr);
	
	tr.inject($('Conceptos'));
}

var Registrar = function() {
	if (!$chk($('num_cia').get('value').getNumericValue())) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de la factura');
		$('fecha').focus();
	}
	else if ($('nombre_cliente').get('value') == '') {
		alert('Debe especificar el nombre del cliente');
		$('nombre_cliente').focus();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el RFC del cliente');
		$('rfc').focus();
	}
	else if ($('calle').get('value') == '') {
		alert('Debe especificar la calle');
		$('calle').focus();
	}
	else if ($('colonia').get('value') == '') {
		alert('Debe especificar la colonia');
		$('colonia').focus();
	}
	else if ($('municipio').get('value') == '') {
		alert('Debe especificar la delegación o municipio');
		$('municipio').focus();
	}
	else if ($('estado').get('value') == '') {
		alert('Debe especificar el estado');
		$('estado').focus();
	}
	else if ($('pais').get('value') == '') {
		alert('Debe especificar el pais');
		$('pais').focus();
	}
	else if ($('codigo_postal').get('value') == '') {
		alert('Debe especificar el código postal');
		$('codigo_postal').focus();
	}
	else if ($('observaciones').get('value').length > 1000) {
		alert('El texto en observaciones no puede ser mayor a 1000 caracteres');
		$('observaciones').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, textarea, radio').each(function(el) {
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
			'url': 'FacturaElectronicaCargo.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Generando CFD...', 'Facturas Electr&oacute;nicas', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				popup = new Popup(result, 'Facturas Electr&oacute;nicas', 500, 200, popupOpen, null);
			}
		}).send();
	}
}

var popupOpen = function() {
	$('cerrar').addEvent('click', function() {
		if (!$chk($('error'))) {
			$('Datos').reset();
		}
		
		popup.Close();
	});
}
