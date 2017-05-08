// JavaScript Document

var param = '';

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'PedidosPanaderiasMes.php',
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
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('pedido').addEvent('change', function() {
				if (this.length > 0) {
					this.setStyle('color', this.options[this.selectedIndex].getStyle('color'));
				}
			}).fireEvent('change');
			
			$('registrar').addEvent('click', Anotaciones);
			
			$('consultar').addEvent('click', Consultar);
		}
	}).send();
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result) {
				$('captura').empty().set('html', result);
				
				new FormValidator($('Datos'), {
					showErrors: true,
					selectOnFocus: true
				});
				
				new FormStyles($('Datos'));
				
				$('checkall').addEvent('change', checkAll.pass($('checkall')));
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('input[id^=codmp]').each(function(el, i, array) {
					el.addEvents({
						'change': obtenerMP.pass(i),
						'keydown': function(e) {
							if (e.key == 'enter' || e.key == 'right') {
								e.stop();
								
								$('cantidad' + i).select();
							}
							else if (e.key == 'up') {
								e.stop();
								
								if (i > 0) {
									$('codmp' + (i - 1)).select();
								}
							}
							else if (e.key == 'down') {
								e.stop();
								
								if (i < array.length - 1) {
									$('codmp' + (i + 1)).select();
								}
							}
						}
					});
				});
				
				$$('input[id^=cantidad]').each(function(el, i, array) {
					el.addEvents({
						'change': calcularEntrega.pass(i),
						'keydown': function(e) {
							if (e.key == 'enter') {
								e.stop();
								
								if (i < array.length - 1) {
									$('codmp' + (i + 1)).select();
								}
								else {
									$('codmp0').select();
								}
							}
							else if (e.key == 'left') {
								$('codmp' + i).select();
							}
							else if (e.key == 'up') {
								e.stop();
								
								if (i > 0) {
									$('cantidad' + (i - 1)).select();
								}
							}
							else if (e.key == 'down') {
								e.stop();
								
								if (i < array.length - 1) {
									$('cantidad' + (i + 1)).select();
								}
							}
						}
					});
				});
				
				
				
				$$('input[id^=tomar_consumo]').each(function(el, i) {
					el.addEvent('change', calcularEntrega.pass(i));
				});
				
				$$('select[id^=num_pro]').each(function(el, i) {
					el.addEvent('change', obtenerPre.pass(i));
				});
				
				$$('select[id^=presentacion]').each(function(el, i) {
					el.addEvent('change', calcularEntrega.pass(i));
				});
				
				$('regresar').addEvent('click', Inicio);
				
				$('borrar').addEvent('click', borrarRegistros);
				
				$('guardar').addEvent('click', Guardar);
				
				$('codmp0').select();
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var obtenerMP = function() {
	var i = arguments[0];
	
	if ($('codmp' + i).get('value').getNumericValue() > 0) {
		new Request({
			'url': 'PedidosPanaderiasMes.php',
			'data': 'accion=obtenerMP&codmp=' + $('codmp' + i).get('value') + '&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			'onRequest': function() {
				$('nombre_mp' + i).store('tmp', $('nombre_mp' + i).get('value')).set('value', 'BUSCANDO PRODUCTO...');
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					$('nombre_mp' + i).set('value', data.nombre_mp);
					$('existencia' + i).set('value', data.existencia);
					$('consumo' + i).set('value', data.consumo);
					
					obtenerPro.run(i);
				}
				else {
					alert('El producto no se encuentra en el catálogo');
					
					$('codmp' + i).set('value', $('codmp' + i).retrieve('tmp', ''));
					$('nombre_mp' + i).set('value', $('nombre_mp' + i).retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		$('codmp' + i).set('value', '');
		$('nombre_mp' + i).set('value', '');
		$('existencia' + i).set('value', '');
		$('consumo' + i).set('value', '');
		
		updSelect($('num_pro' + i), []);
		updSelect($('presentacion' + i), []);
		
		$('entregar' + i).set('value', '');
	}
}

var obtenerPro = function() {
	var i = arguments[0];
	
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=obtenerPro&codmp=' + $('codmp' + i).get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);
				
				updSelect($('num_pro' + i), data);
				
				obtenerPre.run(i);
			}
			else {
				alert('No hay proveedores que trabajen el producto');
				
				updSelect($('num_pro' + i), []);
				updSelect($('presentacion' + i), []);
				
				$('codmp' + i).select();
			}
		}
	}).send();
}

var obtenerPre = function() {
	var i = arguments[0];
	
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=obtenerPre&codmp=' + $('codmp' + i).get('value') + '&num_pro=' + $('num_pro' + i).get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);
				
				updSelect($('presentacion' + i), data);
				
				calcularEntrega.run(i);
			}
			else {
				alert('El proveedor no tiene este producto');
				
				updSelect($('presentacion' + i), []);
				
				$('codmp' + i).select();
			}
		}
	}).send();
}

var calcularEntrega = function() {
	var i = arguments[0],
		entregar = 0,
		data;
	
	if ($('cantidad' + i).get('value').getNumericValue() > 0 && $('presentacion' + i).get('value') != '') {
		data = $('presentacion' + i).get('value').split('|');
		
		entregar = $('tomar_consumo' + i).get('checked') ? Number.ceil($('cantidad' + i).get('value').getNumericValue() / data[2].getNumericValue()) : $('cantidad' + i).get('value').getNumericValue();
		
		$('entregar' + i).set('value', entregar.numberFormat(2, '.', ','));
	}
	else {
		$('entregar' + i).set('value', '');
	}
}

var borrarRegistros = function() {
	if ($$('input[id^=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
		
		return false;
	}
	
	if (confirm('¿Desea borrar los registros seleccionados?')) {
		new Request({
			'url': 'PedidosPanaderiasMes.php',
			'data': 'accion=borrar&' + $$('input[id^=id]:checked').get('value').map(function(el) { return 'id[]=' + el; }).join('&'),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Borrando registros...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consultar.run(param);
			}
		}).send();
	}
}

var Guardar = function() {
	var id = $$('input[id^=id]'),
		cantidad = $$('input[id^=cantidad]'),
		tomar_consumo = $$('input[id^=tomar_consumo]'),
		codmp = $$('input[id^=codmp]'),
		nombre_mp = $$('input[id^=nombre_mp]'),
		existencia = $$('input[id^=existencia]'),
		consumo = $$('input[id^=consumo]'),
		entregar = $$('input[id^=entregar]');
	
	for (i = 0; i < id.length; i++) {
		if (id[i].get('checked')) {
			var data = $('presentacion' + i).get('value').split('|'),
				exi = existencia[i].get('value').getNumericValue(),
				ped = cantidad[i].get('value').getNumericValue() * (!tomar_consumo[i].get('checked') ? ($chk(data[1]) ? data[1].getNumericValue() : 0) : 1),
				con = consumo[i].get('value').getNumericValue(),
				est = (exi + ped).round();
			
			if (cantidad[i].get('value').getNumericValue() == 0) {
				alert('No hay cantidad especificada para el pedido');
				
				cantidad[i].select();
				
				return false;
			}
			else if (codmp[i].get('value').getNumericValue() == 0) {
				alert('No ha codificado el producto solicitado');
				
				codmp[i].select();
				
				return false;
			}
			else if (entregar[i].get('value').getNumericValue() == 0) {
				alert('No ha especificado el proveedor o la presentación para pedido del producto');
				
				codmp[i].select();
				 
				return false;
			}/*
			else if (est > (con * 1.25).round()) {
				var text = codmp[i].get('value') + ' ' + nombre_mp[i].get('value') + ': El inventario estimado excede el consumo mensual.\n\nInventario: ' + exi.numberFormat(2, '.', ',') + ' + Pedido: ' + ped.numberFormat(2, '.', ',') + ' = Inventario estimado: ' + est.numberFormat(2, '.', ',') + '\n\nConsumo mensual = ' + con.numberFormat(2, '.', ',') + ' + (7 días: ' + (con * 0.25). round().numberFormat(2, '.', ',') + ') = ' + (con * 1.25).round().numberFormat(2, '.', ',') + '\n\n¿Es correcto este pedido?';
				
				if (!confirm(text)) {
					cantidad[i].select();
					
					return false;
				}
			}*/
		}
	}
	
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=guardar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Guardando cambios...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			Inicio.run();
		}
	}).send();
}

var Anotaciones = function() {
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=anotaciones',
		'onRequest': function() {
		},
		'onSuccess': function(result) { 
			if (result != '') {
				popup = new Popup(result, 'Anotaciones', 800, 600, popupOpen, null, {scrollBars: true});
			}
			else {
				alert('No hay registros de pedidos por realizar');
			}
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
	
	$('terminar').addEvent('click', realizarPedido);
}

var realizarPedido = function() {
	var queryString = [];
	
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
	
	new Request({
		'url': 'PedidosPanaderiasMes.php',
		'data': 'accion=realizarPedido&' + queryString.join('&'),
		'onRequest': function() {
			popup.Close();
			
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

var checkAll = function() {
	var checkbox = arguments[0];
	
	$$('input[id^=id]').set('checked', checkbox.get('checked'));
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
