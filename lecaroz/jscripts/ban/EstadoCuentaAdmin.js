window.addEvent('domready', function() {
	
	boxProcessing = new mBox({
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		closeOnEsc: false,
		closeOnBodyClick: false
	});
	
	boxModificar = new mBox.Modal({
		id: 'box_modificar',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Modificar movimiento',
		content: 'modificar_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					do_modificar();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function() {
			new FormValidator(document.id('modificar'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('num_cia').addEvents({
				change: function() {
					if (this.get('value').getNumericValue() >= 0) {
						obtener_cia();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('num_cia_sec').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('gasto').select();
					}
				}
			});
			
			document.id('num_cia_sec').addEvents({
				change: function() {
					if (this.get('value').getNumericValue() >= 0) {
						obtener_cia_sec();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('num_cia').select();
					}
				}
			});
			
			document.id('banco').addEvents({
				focus: function() {
					if (document.id('conciliado').get('value') != '' || document.id('folio').get('value').getNumericValue() > 0) {
						this.blur();
					}
				},
				change: function() {
					document.id('cod_mov').store('tmp', document.id('cod_mov').get('value').getNumericValue());
					
					switch (this.get('value').getNumericValue()) {
						
						case 1:
							this.removeClass('logo_banco_2').addClass('logo_banco_1');
							break;
						
						case 2:
							this.removeClass('logo_banco_1').addClass('logo_banco_2');
							break;
					}
					
					obtener_cia();
					
					obtener_cia_sec();
					
					obtener_cod_mov();
				}
			});
			
			document.id('fecha').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('importe').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('num_cia_sec').select();
					}
				}
			});
			
			document.id('importe').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('concepto').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha').focus();
					}
				}
			});
			
			document.id('cod_mov').addEvents({
				change: function() {
					obtener_rentas();
				}
			});
			
			document.id('concepto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('gasto').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('importe').select();
					}
				}
			});
			
			document.id('gasto').addEvents({
				change: function() {
					if (this.get('value').getNumericValue() >= 0) {
						obtener_gasto();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('num_cia').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('concepto').focus();
					}
				}
			});
			
			document.id('arrendatario').addEvents({
				change: function() {
					document.id('recibo_renta').selectedIndex = 0;
					
					var ok = false;
					
					Array.each(document.id('recibo_renta').options, function(op, i) {
						var data = JSON.decode(op.get('value'));
						
						if (!ok && data != null && data.idarrendatario == document.id('arrendatario').get('value').getNumericValue()) {
							document.id('recibo_renta').selectedIndex = i;
							
							document.id('concepto').set('value', data.mes + ' ' + data.anio + ' ' + data.nombre_arrendatario);
							
							ok = true;
						}
					});
				}
			});
			
			document.id('recibo_renta').addEvents({
				change: function() {
					document.id('arrendatario').selectedIndex = 0;
					
					var data = JSON.decode(this.get('value'));
					
					document.id('concepto').set('value', data.mes + ' ' + data.anio + ' ' + data.nombre_arrendatario);
					
					Array.each(document.id('arrendatario').options, function(op, i) {
						if (data != null && op.get('value').getNumericValue() == data.idarrendatario) {
							document.id('arrendatario').selectedIndex = i;
						}
					});
				}
			});
			
		},
		onOpenComplete: function() {
			document.id('num_cia').select();
		}
	});
	
	boxBaja = new mBox.Modal({
		id: 'box_baja',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Baja de movimiento',
		content: '&iquest;Desea borrar el movimiento seleccionado?',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					do_baja();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: true
	});
	
	inicio();
	
});

var inicio = function () {
	new Request({
		url: 'EstadoCuentaAdmin.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').empty().set('html', result);
			
			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('acreditados').select();
					}
				}
			});
			
			document.id('acreditados').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha1').select();
					}
				}
			});
			
			document.id('banco').addEvents({
				change: function() {
					switch (this.get('value').getNumericValue()) {
						
						case 1:
							this.removeClass('logo_banco_2').addClass('logo_banco_1');
							break;
						
						case 2:
							this.removeClass('logo_banco_1').addClass('logo_banco_2');
							break;
						
						default:
							this.removeClass('logo_banco_1').removeClass('logo_banco_2');
					}
					
					obtener_codigos();
				}
				
			});
			
			document.id('fecha1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha2').select();
					}
				}
			});
			
			document.id('fecha2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('conciliado1').focus();
					}
				}
			});
			
			document.id('conciliado1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('conciliado2').select();
					}
				}
			});
			
			document.id('conciliado2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('comprobantes').focus();
					}
				}
			});
			
			document.id('comprobantes').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('pros').focus();
					}
				}
			});
			
			document.id('pros').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('folios').focus();
					}
				}
			});
			
			document.id('folios').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('gastos').focus();
					}
				}
			});
			
			document.id('gastos').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('importes').focus();
					}
				}
			});
			
			document.id('importes').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('concepto').focus();
					}
				}
			});
			
			document.id('concepto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('cias').select();
					}
				}
			});
			
			obtener_codigos();
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
			
			document.id('cias').focus();
		}
	}).send();
}

var obtener_codigos = function() {
	new Request({
		'url': 'EstadoCuentaAdmin.php',
		'data': 'accion=obtener_codigos&' + $('inicio').toQueryString(),
		'onSuccess': function(codigos) {
			update_select($('codigos'), JSON.decode(codigos));
		}
	}).send();
}

var obtener_cod_mov = function() {
	new Request({
		'url': 'EstadoCuentaAdmin.php',
		'data': 'accion=obtener_codigos&' + $('modificar').toQueryString() + '&no_banco=1',
		'onSuccess': function(codigos) {
			update_select($('cod_mov'), JSON.decode(codigos));
			
			Array.each(document.id('cod_mov').options, function(op, i) {
				if (op.get('value').getNumericValue() == document.id('cod_mov').retrieve('tmp', 0)) {
					document.id('cod_mov').selectedIndex = i;
				}
			});
			
			obtener_rentas();
		}
	}).send();
}

var consultar = function() {
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}
	
	new Request({
		url: 'EstadoCuentaAdmin.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			if (result != '') {
				document.id('captura').empty().set('html', result);
				
				$$('img[id=mod]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', modificar.pass(id));
					
					img.removeProperty('alt');
				});
				
				$$('img[id=baja][src!=/lecaroz/iconos/cancel_gray.png]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', baja.pass(id));
					
					img.removeProperty('alt')
				});
				
				gastos_tooltip = new mBox.Tooltip({
					id: 'gastos_tooltip',
					setContent: 'info',
					attach: $$('span[id=deposito], a[id=cheque], a[id=tarjeta]'),
					position: {
						y: 'top'
					}
				});
				
				document.id('regresar').addEvent('click', inicio);
				
				boxProcessing.close();
			}
			else {
				inicio();
				
				boxProcessing.close();
				
				alert('No hay resultados');
			}
		}
	}).send();
}

var modificar = function(id) {
	current_id = id;
	
	new Request({
		url: 'EstadoCuentaAdmin.php',
		data: 'accion=datos_movimiento&id=' + current_id,
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(result) {
			var data = JSON.decode(result);
			
			document.id('id').set('value', data.id);
			document.id('num_cia').set('value', data.num_cia);
			document.id('nombre_cia').set('value', data.nombre_cia);
			document.id('cuenta_cia').set('value', data.cuenta_cia);
			document.id('num_cia_sec').set('value', data.num_cia_sec > 0 ? data.num_cia_sec : '');
			document.id('nombre_cia_sec').set('value', data.nombre_cia_sec);
			document.id('cuenta_cia_sec').set('value', data.cuenta_cia_sec);
			Array.each(document.id('banco').options, function(op, i) {
				if (op.get('value').getNumericValue() == data.banco) {
					document.id('banco').selectedIndex = i;
				}
			});
			document.id('banco').removeClass('logo_banco_1').removeClass('logo_banco_2').addClass('logo_banco_' + data.banco)
			document.id('fecha').set('value', data.fecha);
			document.id('conciliado').set('value', data.conciliado);
			document.id('tipo_mov').set('value', data.tipo_mov ? 'TRUE' : 'FALSE');
			document.id('importe').set('value', data.importe.numberFormat(2, '.', ',')).set('').removeClass(data.tipo_mov ? 'blue' : 'red').addClass(data.tipo_mov ? 'red' : 'blue');
			document.id('folio').set('value', data.folio > 0 ? data.folio : '').removeClass('purple').removeClass('orange').removeClass('green').addClass(data.cod_mov == 41 ? 'purple' : (data.cod_mov == 5 ? 'orange' : 'green'));
			document.id('beneficiario').set('text', data.beneficiario);
			document.id('gasto').set('value', data.gasto > 0 ? data.gasto : '');
			document.id('descripcion_gasto').set('value', data.descripcion_gasto);
			document.id('concepto').set('value', data.concepto);
			
			update_select(document.id('cod_mov'), data.codigos);
			
			update_select(document.id('arrendatario'), data.arrendatarios);
			
			update_select(document.id('recibo_renta'), data.recibos_renta);
			
			if (data.tipo_mov == false && [1, 16, 44, 99].contains(data.cod_mov)) {
				document.id('num_cia_sec').set('readonly', false);
			} else {
				document.id('num_cia_sec').set('readonly', true);
			}	
			
			if (data.conciliado != null || data.folio > 0) {
				$$('#num_cia, #importe').set('readonly', true);
			} else {
				$$('#num_cia, #importe').set('readonly', false);
			}
			
			if (data.folio > 0) {
				document.id('gasto').set('readonly', false);
			} else {
				document.id('gasto').set('readonly', true)
			}
			
			boxProcessing.close();
			
			boxModificar.open();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía a la que pertenece el movimiento.');
		
		document.id('num_cia').select();
	} else if (document.id('fecha').get('value') == '') {
		alert('Debe especificar la fecha del movimiento.');
		
		document.id('fecha').select();
	} else if (document.id('importe').get('value').getNumericValue() <= 0) {
		alert('El importe del movimiento debe ser mayor a cero.');
		
		document.id('importe').select();
	} else if (document.id('concepto').get('value') == '') {
		alert('El concepto del movimiento no puede estar vacío.');
		
		document.id('concepto').focus();
	} else if (document.id('folio').get('value').getNumericValue() > 0
		&& document.id('gasto').get('value').getNumericValue() == 0) {
		alert('Para el caso de cheques y/o transferencias debe especificar el gasto.');
		
		document.id('gasto').select();
	} else if (document.id('cod_mov').get('value').getNumericValue() == 2
		&& document.id('recibo_renta').get('value') == ''
		&& !confirm('El movimiento es un depósito de renta y no ha especificado un recibo de arrendamiento. ¿Desea continuar?')) {
		document.id('recibo_renta').focus();
	} else {
		new Request({
			url: 'EstadoCuentaAdmin.php',
			data: 'accion=do_modificar&' + document.id('modificar').toQueryString(),
			onRequest: function() {
				boxModificar.close();
				
				boxProcessing.open();
			},
			onSuccess: function() {
				consultar(param);
			}
		}).send();
	}
}

var baja = function(id) {
	current_id = id;
	
	boxBaja.open();
}

var do_baja = function(id) {
	new Request({
		url: 'EstadoCuentaAdmin.php',
		data: 'accion=do_baja&id=' + current_id,
		onRequest: function() {
			boxBaja.close();
			
			boxProcessing.open();
		},
		onSuccess: function() {
			boxProcessing.close();
			
			document.id('row' + current_id).destroy();
		}
	}).send();
}

var obtener_cia = function() {
	if (document.id('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			url: 'EstadoCuentaAdmin.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value') + '&banco=' + document.id('banco').get('value'),
			onSuccess: function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					document.id('nombre_cia').set('value', data.nombre);
					document.id('cuenta_cia').set('value', data.cuenta);
					
					obtener_rentas();
				} else {
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));
					
					alert('La compañía no esta en el catálogo.');
					
					document.id('num_cia').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia, #nombre_cia, #cuenta_cia, #num_cia_sec, #nombre_cia_sec, #cuenta_cia_sec').set('value', '');
	}
}

var obtener_cia_sec = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0 && document.id('num_cia_sec').get('value').getNumericValue() > 0) {
		alert('Debe especificar la compañía donde esta hecho el movimiento');
		
		document.id('num_cia').select();
	} else if (document.id('num_cia').get('value').getNumericValue() > 0 && document.id('num_cia_sec').get('value').getNumericValue() > 0) {
		new Request({
			url: 'EstadoCuentaAdmin.php',
			data: 'accion=obtener_cia_sec&num_cia=' + document.id('num_cia').get('value') + '&num_cia_sec=' + document.id('num_cia_sec').get('value') + '&banco=' + document.id('banco').get('value'),
			onSuccess: function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					document.id('nombre_cia_sec').set('value', data.nombre);
					document.id('cuenta_cia_sec').set('value', data.cuenta);
				} else {
					document.id('num_cia_sec').set('value', document.id('num_cia_sec').retrieve('tmp', ''));
					
					alert('La compañía no esta en el catálogo o no pertenece a la misma razón social.');
					
					document.id('num_cia_sec').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia_sec, #nombre_cia_sec, #cuenta_cia_sec').set('value', '');
	}
}

var obtener_gasto = function() {
	if (document.id('gasto').get('value').getNumericValue() > 0) {
		new Request({
			url: 'EstadoCuentaAdmin.php',
			data: 'accion=obtener_gasto&gasto=' + document.id('gasto').get('value'),
			onSuccess: function(result) {
				if (result != '') {
					document.id('descripcion_gasto').set('value', result);
				} else {
					document.id('gasto').set('value', document.id('gasto').retrieve('tmp', ''));
					
					alert('El código de gasto no esta en el catálogo.');
					
					document.id('gasto').select();
				}
			}
		}).send();
	} else {
		$$('#gasto, #descripcion_gasto').set('value', '');
	}
}

var obtener_rentas = function() {
	if (document.id('cod_mov').get('value').getNumericValue() == 2) {
		new Request({
			url: 'EstadoCuentaAdmin.php',
			data: 'accion=obtener_rentas&num_cia=' + document.id('num_cia').get('value') + '&id=' + document.id('id').get('value'),
			onSuccess: function(result) {
				var data = JSON.decode(result);
				
				update_select(document.id('arrendatario'), data.arrendatarios);
				
				update_select(document.id('recibo_renta'), data.recibos_renta);
			}
		}).send();
	} else {
		update_select(document.id('arrendatario'), []);
		
		update_select(document.id('recibo_renta'), []);
	}
}

var update_select = function() {
	var select = arguments[0],
		options = arguments[1];
	
	select.length = 0;
	
	if (options.length > 0) {
		select.length = options.length;
		
		Array.each(select.options, function(el, i) {
			el.set(options[i]);
		});
	} else {
		select.length = 1;
		Array.each(select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});
		
		select.selectedIndex = 0;
	}
}
