// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('pros').focus();
			}
		}
	});
	
	$('pros').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha1').focus();
			}
		}
	});
	
	$('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha2').focus();
			}
		}
	});
	
	$('fecha2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('cias').focus();
			}
		}
	});
	
	$('result').setStyle('display', 'none');
	
	$('loading').setStyle('display', 'none');
	
	$('nuevo').addEvent('click', Nuevo);
	
	$('buscar').addEvent('click', Buscar);
	
	$('cias').focus();
});

var Nuevo = function() {
	$('result').set('html', '');
	$('result').setStyle('display', 'none');
	$('Datos').reset();
}

var Buscar = function() {
	if (!$('pendientes').get('checked') && !$('aclaradas').get('checked')) {
		alert('Debe seleccionar al menos un filtro de búsqueda');
		return false;
	}
	
	new Request({
		'url': 'AclaracionFacturasProveedores.php',
		'method': 'post',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('buscar').set({
				'value': 'Buscando...',
				'disabled': true
			});
			$('loading').setStyle('display', 'inline');
			$('result').set('html', '');
			$('result').setStyle('display', 'none');
		},
		'onSuccess': function(data) {
			$('loading').setStyle('display', 'none');
			$('buscar').set({
				'value': 'Buscar Facturas',
				'disabled': false
			});
			
			if (data == '') {
				alert('No hay resultados');
				$('result').set('html', '');
				$('result').setStyle('display', 'none');
			}
			else {
				$('result').set('html', data);
				$('result').setStyle('display', 'block');
				
				var proveedores = $$('div[id=proveedor]');
				
				proveedores.each(function(el, i) {
					var img = el.getElement('img[id=show_pro]');
					var fac = el.getElement('div[id=facturas]');
					
					img.addEvents({
						'click': ShowPro.pass([fac, img]),
						'mouseover': function() {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							this.setStyle('cursor', 'default');
						}
					});
					
					var facturas = el.getElements('div[id=factura]');
					
					facturas.each(function(f) {
						var img = f.getElement('img[id=show_fac]');
						var det = f.getElement('div[id=detalle]');
						var queryString = det.getElement('table[id=t1]').getElement('td').toQueryString();
						var data = det.getElement('table[id=t2]').getElement('td');
						
						img.addEvents({
							'click': ShowPro.pass([det, img]),
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							}
						});
						
						if ($defined(f.getElement('input[id=actualizar]'))) {
							f.getElement('input[id=actualizar]').addEvent('click', Actualizar.pass(data));
							f.getElement('input[id=aclarar]').addEvent('click', Aclarar.pass(f));
						}
						
						f.getElement('tr[id=desglose]').addEvents({
							'click': DesgloseFactura.pass(queryString),
							'mouseover': function() {
								this.setStyle('cursor', 'pointer');
							},
							'mouseout': function() {
								this.setStyle('cursor', 'default');
							}
						});
					});
					
					$$('textarea').addEvent('change', function() {
						this.set('value', this.get('value').replace(/[^\w\s\-\.,;¿\?\$]/g, '').clean().trim().toUpperCase());
					});
					
					
				});
				
				$('loading').setStyle('display', 'none');
			}
		}
	}).send();
}

var ShowPro = function() {
	var facturas = arguments[0];
	var img = arguments[1];
	
	if (facturas.getStyle('display') == 'none') {
		facturas.setStyle('display', 'block');
		img.set('src', 'imagenes/arrow_up.png');
	}
	else {
		facturas.setStyle('display', 'none');
		img.set('src', 'imagenes/arrow_down.png');
	}
}

var ShowFac = function() {
	var detalle = arguments[0];
	var img = arguments[1];
	
	if (detalle.getStyle('display') == 'none') {
		detalle.setStyle('display', 'block');
		img.set('src', 'imagenes/arrow_up.png');
	}
	else {
		detalle.setStyle('display', 'none');
		img.set('src', 'imagenes/arrow_down.png');
	}
}

var DesgloseFactura = function() {
	var queryString = arguments[0];
	
	new Request({
		'url': 'AclaracionFacturasProveedores.php',
		'data': 'accion=desglose&' + queryString,
		'onSuccess': function(result) {
			if (result != '-1') {
				var url = 'AclaracionFacturasProveedores.php?accion=showDesglose&' + result;
				var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=300';
				var win = window.open(url, 'desglose', opt);
				
				win.focus();
			}
		}
	}).send();
}

var Actualizar = function() {
	var queryString = arguments[0].toQueryString();
	
	new Request({
		'url': 'AclaracionFacturasProveedores.php',
		'data': 'accion=actualizar&' + queryString,
		'onRequest': function() {
			this.getElement('div[id=bloque_comentarios]').set('html', '<img src="imagenes/_loading.gif" width="16" height="16" /> <strong>Actualizando...</strong>');
		}.bind(arguments[0]),
		'onSuccess': function(result) {
			this.getElement('textarea').set('value', '');
			this.getElement('div[id=bloque_comentarios]').set('html', result);
		}.bind(arguments[0]),
		'onFailure': function(xhr) {
			alert('Error: ' + xhr);
		}
	}).send();
}

var Aclarar = function() {
	var queryString = arguments[0].getElement('div[id=detalle]').getElement('table[id=t2]').getElement('td').toQueryString();
	
	new Request({
		'url': 'AclaracionFacturasProveedores.php',
		'data': 'accion=aclarar&' + queryString,
		'onRequest': function() {
			this.getElement('div[id=bloque_comentarios]').set('html', '<img src="imagenes/_loading.gif" width="16" height="16" /> <strong>Aclarando...</strong>');
		}.bind(arguments[0].getElement('div[id=detalle]')),
		'onSuccess': function(result) {
			this.destroy();
		}.bind(arguments[0]),
		'onFailure': function(xhr) {
			alert('Error: ' + xhr);
		}
	}).send();
}
