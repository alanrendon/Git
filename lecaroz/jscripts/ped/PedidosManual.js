// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'PedidosManual.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			validator = new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			styles = new FormStyles($('Datos'));
			
			$$('input[id=num_cia]').each(function(el, i) {
				el.addEvents({
					'change': obtenerCia.pass(i),
					'keydown': function(e) {
						if (e.key == 'enter' || e.key == 'right') {
							$$('input[id=codmp]')[i].select();
						}
						else if (e.key == 'up' && i > 0) {
							$$('input[id=num_cia]')[i - 1].select();
						}
						else if (e.key == 'down' && i < $$('input[id=num_cia]').length - 1) {
							$$('input[id=num_cia]')[i + 1].select();
						}
					}
				});
			});
			
			$$('input[id=codmp]').each(function(el, i) {
				el.addEvents({
					'change': obtenerMP.pass(i),
					'keydown': function(e) {
						if (e.key == 'enter' || e.key == 'right') {
							$$('input[id=cantidad]')[i].select();
						}
						else if (e.key == 'left') {
							$$('input[id=num_cia]')[i].select();
						}
						else if (e.key == 'up' && i > 0) {
							$$('input[id=codmp]')[i - 1].select();
						}
						else if (e.key == 'down' && i < $$('input[id=codmp]').length - 1) {
							$$('input[id=codmp]')[i + 1].select();
						}
					}
				});
			});
			
			$$('select[id=num_pro]').each(function(el, i) {
				el.addEvent('change', obtenerPre.pass(i));
			});
			
			$$('input[id=cantidad]').each(function(el, i) {
				el.addEvents({
					//'change': obtenerMP.pass(i),
					'keydown': function(e) {
						if (e.key == 'enter') {
							if (i + 1 > $$('input[id=num_cia]').length - 1) {
								newRow.run(i + 1);
							}
							
							$$('input[id=num_cia]')[i + 1].select();
						}
						else if (e.key == 'left') {
							$$('input[id=codmp]')[i].select();
						}
						else if (e.key == 'up' && i > 0) {
							$$('input[id=cantidad]')[i - 1].select();
						}
						else if (e.key == 'down' && i < $$('input[id=cantidad]').length - 1) {
							$$('input[id=cantidad]')[i + 1].select();
						}
					}
				});
			});
			
			$('cancelar').addEvent('click', Inicio);
			
			$('registrar').addEvent('click', Anotaciones);
			
			$('num_cia').select();
		}
	}).send();
}

var newRow = function() {
	var i = arguments[0],
		tr = new Element('tr', {
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
		num_cia = new Element('input', {
			'id': 'num_cia',
			'name': 'num_cia[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt right'
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
			'class': 'valid Focus toPosInt right'
		}).inject(td2),
		nombre_mp = new Element('input', {
			'id': 'nombre_mp',
			'name': 'nombre_mp[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td2),
		existencia = new Element('input', {
			'id': 'existencia',
			'name': 'existencia[]',
			'type': 'hidden'
		}).inject(td2),
		consumo = new Element('input', {
			'id': 'consumo',
			'name': 'consumo[]',
			'type': 'hidden'
		}).inject(td2),
		num_pro = new Element('select', {
			'id': 'num_pro',
			'name': 'num_pro[]',
			'styles': {
				'width': '98%'
			}
		}).inject(td3);
		presentacion = new Element('select', {
			'id': 'presentacion',
			'name': 'presentacion[]',
			'styles': {
				'width': '98%'
			}
		}).inject(td4),
		cantidad = new Element('input', {
			'id': 'cantidad',
			'name': 'cantidad[]',
			'type': 'text',
			'size': 8,
			'class': 'valid Focus numberPosFormat right',
			'precision': 2
		}).inject(td5);
	
	validator.addElementEvents(num_cia);
	validator.addElementEvents(codmp);
	validator.addElementEvents(cantidad);
	
	styles.addElementEvents(num_cia);
	styles.addElementEvents(codmp);
	styles.addElementEvents(cantidad);
	
	num_cia.addEvents({
		'change': obtenerCia.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				$$('input[id=codmp]')[i].select();
			}
			else if (e.key == 'up' && i > 0) {
				$$('input[id=num_cia]')[i - 1].select();
			}
			else if (e.key == 'down' && i < $$('input[id=num_cia]').length - 1) {
				$$('input[id=num_cia]')[i + 1].select();
			}
		}
	});
	
	codmp.addEvents({
		'change': obtenerMP.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				$$('input[id=cantidad]')[i].select();
			}
			else if (e.key == 'left') {
				$$('input[id=num_cia]')[i].select();
			}
			else if (e.key == 'up' && i > 0) {
				$$('input[id=codmp]')[i - 1].select();
			}
			else if (e.key == 'down' && i < $$('input[id=codmp]').length - 1) {
				$$('input[id=codmp]')[i + 1].select();
			}
		}
	});
	
	num_pro.addEvent('change', obtenerPre.pass(i));
	
	cantidad.addEvents({
		//'change': obtenerMP.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				if (i + 1 > $$('input[id=num_cia]').length - 1) {
					newRow.run(i + 1);
				}
				
				$$('input[id=num_cia]')[i + 1].select();
			}
			else if (e.key == 'left') {
				$$('input[id=codmp]')[i].select();
			}
			else if (e.key == 'up' && i > 0) {
				$$('input[id=cantidad]')[i - 1].select();
			}
			else if (e.key == 'down' && i < $$('input[id=cantidad]').length - 1) {
				$$('input[id=cantidad]')[i + 1].select();
			}
		}
	});
	
	tr.inject($('Tabla'));
}

var obtenerCia = function() {
	var i = arguments[0];
	
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PedidosManual.php',
			'data': 'accion=obtenerCia&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$$('input[id=nombre_cia]')[i].set('value', result);
					
					obtenerMP.run(i);
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

var obtenerMP = function() {
	var i = arguments[0];
	
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0 && $$('input[id=codmp]')[i].get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PedidosManual.php',
			'data': 'accion=obtenerMP&codmp=' + $$('input[id=codmp]')[i].get('value') + '&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			'onRequest': function() {
				$$('input[id=nombre_mp]')[i].store('tmp', $$('input[id=nombre_mp]')[i].get('value')).set('value', 'BUSCANDO PRODUCTO...');
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$$('input[id=nombre_mp]')[i].set('value', data.nombre_mp);
					$$('input[id=existencia]')[i].set('value', data.existencia);
					$$('input[id=consumo]')[i].set('value', data.consumo);
					
					obtenerPro.run(i);
				}
				else {
					alert('El producto no se encuentra en el catálogo');
					
					$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', ''));
					$$('input[id=nombre_mp]')[i].set('value', $$('input[id=nombre_mp]')[i].retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		$$('input[id=codmp]')[i].set('value', '');
		$$('input[id=nombre_mp]')[i].set('value', '');
		$$('input[id=existencia]')[i].set('value', '');
		$$('input[id=consumo]')[i].set('value', '');
		
		updSelect($$('select[id=num_pro]')[i], []);
		updSelect($$('select[id=presentacion]')[i], []);
	}
}

var obtenerPro = function() {
	var i = arguments[0];
	
	new Request({
		'url': 'PedidosManual.php',
		'data': 'accion=obtenerPro&codmp=' + $$('input[id=codmp]')[i].get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);
				
				updSelect($$('select[id=num_pro]')[i], data);
				
				obtenerPre.run(i);
			}
			else {
				alert('No hay proveedores que trabajen el producto');
				
				updSelect($$('select[id=num_pro]')[i], []);
				updSelect($$('select[id=presentacion]')[i], []);
				
				$$('input[id=codmp]')[i].select();
			}
		}
	}).send();
}

var obtenerPre = function() {
	var i = arguments[0];
	
	new Request({
		'url': 'PedidosManual.php',
		'data': 'accion=obtenerPre&codmp=' + $$('input[id=codmp]')[i].get('value') + '&num_pro=' + $$('select[id=num_pro]')[i].get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);
				
				updSelect($$('select[id=presentacion]')[i], data);
			}
			else {
				alert('El proveedor no tiene este producto');
				
				updSelect($$('input[id=presentacion]')[i], []);
				
				$$('input[id=codmp]')[i].select();
			}
		}
	}).send();
}

var Anotaciones = function() {
	var num_cia = $$('input[id=num_cia]'),
		codmp = $$('input[id=codmp]'),
		nombre_mp = $$('input[id=nombre_mp]'),
		existencia = $$('input[id=existencia]'),
		consumo = $$('input[id=consumo]'),
		num_pro = $$('select[id=num_pro]'),
		presentacion = $$('select[id=presentacion]'),
		cantidad = $$('input[id=cantidad]');
	
	for (i = 0; i < num_cia.length; i++) {
		if (num_cia[i].get('value').getNumericValue() > 0) {
			var data = presentacion[i].get('value').split('|'),
				exi = existencia[i].get('value').getNumericValue(),
				ped = cantidad[i].get('value').getNumericValue() * ($chk(data[1]) ? data[1].getNumericValue() : 0),
				con = consumo[i].get('value').getNumericValue(),
				est = (exi + ped).round();
			
			if (codmp[i].get('value').getNumericValue() == 0) {
				alert('No ha especificado el producto');
				
				codmp[i].select();
				
				return false;
			}
			else if (num_pro[i].get('value').getNumericValue() == 0) {
				alert('No ha selecionado un proveedor');
				
				num_pro[i].focus();
				
				return false;
			}
			else if (presentacion[i].get('value') == '') {
				alert('No ha seleccionado la presentación del producto');
				
				presentacion[i].focus();
				
				return false;
			}
			else if (cantidad[i].get('value').getNumericValue() == 0) {
				alert('No ha especificado la cantidad a pedir');
				
				cantidad[i].select();
				
				return false;
			}
			else if (est > (con * 1.25).round()) {
				var text = codmp[i].get('value') + ' ' + nombre_mp[i].get('value') + ': El inventario estimado excede el consumo mensual.\n\nInventario: ' + exi.numberFormat(2, '.', ',') + ' + Pedido: ' + ped.numberFormat(2, '.', ',') + ' = Inventario estimado: ' + est.numberFormat(2, '.', ',') + '\n\nConsumo mensual = ' + con.numberFormat(2, '.', ',') + ' + (7 días: ' + (con * 0.25). round().numberFormat(2, '.', ',') + ') = ' + (con * 1.25).round().numberFormat(2, '.', ',') + '\n\n¿Es correcto este pedido?';
				
				if (!confirm(text)) {
					cantidad[i].select();
					
					return false;
				}
			}
		}
	}
	
	new Request({
		'url': 'PedidosManual.php',
		'data': 'accion=anotaciones&' + $$('select[id=num_pro]').get('value').getNumericValue().filter(function(val) { return val > 0; }).map(function(val) { return 'num_pro[]=' + val; }).join('&'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			popup = new Popup(result, 'Anotaciones', 800, 600, popupOpen, null, {scrollBars: true});
		}
	}).send();
}

var popupOpen = function() {
	new FormValidator($('Anotaciones'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Anotaciones'));
	
	$$('textarea[id=anotacion]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i < $$('textarea[id=anotacion]').length - 1) {
						$$('textarea[id=anotacion]')[i + 1].select();
					}
					else {
						$$('textarea[id=anotacion]')[0].select();
					}
				}
			}
		});
	});
	
	$('popup_close').addEvent('click', function() {
		popup.Close();
	});
	
	$('terminar').addEvent('click', Registrar);
}

var Registrar = function() {
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
	
	$('Anotaciones').getElements('input, textarea').each(function(el) {
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
	
	popup.Close();
	
	if (confirm('¿Desea registrar los pedidos?')) {
		new Request({
			'url': 'PedidosManual.php',
			'data': 'accion=registrar&' + queryString.join('&'),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Realizando pedidos...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty().set('html', result);
				
				$('reporte_cia').addEvent('click', reporte.pass(['cia', $('folio').get('value')]));
				$('reporte_mp').addEvent('click', reporte.pass(['mp', $('folio').get('value')]));
				$('reporte_pro').addEvent('click', reporte.pass(['pro', $('folio').get('value')]));
				
				$('memo').addEvent('click', reporte.pass(['memo', $('folio').get('value')]));
				
				$('email').addEvent('click', email.pass($('folio').get('value')));
				
				$('terminar').addEvent('click', Inicio);
			}
		}).send();
	}
}

var reporte = function() {
	var tipo = arguments[0],
		folio = arguments[1],
		url = 'ReportePedidos.php',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		param = '?tipo=' + tipo + '&folios=' + folio,
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}

var email = function() {
	new Request({
		'url': 'EmailPedidos.php',
		'data': 'folios=' + $('folio').get('value'),
		'onRequest': function() {
			new Element('img', {
				'id': 'loading',
				'src': 'imagenes/_loading.gif'
			}).inject($('email'), 'after');
			
			new Element('span', {
				'id': 'leyenda',
				'text': ' Enviando pedidos por email...'
			}).inject($('loading'), 'after');
		},
		'onSuccess': function(result) {
			$('loading').dispose();
			$('leyenda').dispose();
			
			alert('Pedidos enviados por email');
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
		
		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;
		
		Select.selectedIndex = -1;
	}
}
