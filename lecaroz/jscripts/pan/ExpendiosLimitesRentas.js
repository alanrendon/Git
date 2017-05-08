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
	
	box = new mBox.Modal({
		id: 'box',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					
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
		},
		onOpenComplete: function() {
		}
	});
	
	boxFailure = new mBox.Modal({
		id: 'box_failure',
		title: 'Error',
		content: '',
		buttons: [
			{ title: 'Aceptar' }
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: false,
	});
	
	inicio();
	
});

var inicio = function () {
	new Request({
		url: 'ExpendiosLimitesRentas.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('cias').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('descripcion').focus();
					}
				}
			}).focus();
			
			document.id('descripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('productos').focus();
					}
				}
			});
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
		}
	}).send();
}

var consultar = function () {
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}
	
	new Request({
		url: 'ExpendiosLimitesRentas.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			document.id('alta').addEvent('click', alta);
			
			$$('img[id=mod]').each(function(el) {
				var id = el.get('alt');
				
				el.addEvent('click', modificar.pass(id));
				
				el.removeProperty('alt');
			});
			
			$$('img[id=baja]').each(function(el) {
				var id = el.get('alt');
				
				el.addEvent('click', do_baja.pass(id));
				
				el.removeProperty('alt');
			});
			
			document.id('regresar').addEvent('click', inicio);
			
			boxProcessing.close();
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'ExpendiosLimitesRentas.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_limite'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('num_cia').addEvents({
				change: obtener_cia,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').select();
					}
				}
			}).focus();

			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('num_exp').select();
					}
				}
			});

			document.id('num_exp').addEvents({
				change: obtener_exp,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha_inicio').select();
					}
				}
			});

			document.id('fecha_inicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha_termino').select();
					}
				}
			});

			document.id('fecha_termino').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('importe').select();
					}
				}
			});

			document.id('aplicar_iva').addEvent('click', calcular_renta);
			document.id('aplicar_ret').addEvent('click', calcular_renta);

			document.id('importe').addEvents({
				change: calcular_renta,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('num_cia').select();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', consultar.pass(param));
			
			document.id('alta').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		document.id('num_cia').focus();
	} else if (document.id('nombre').get('value') == '' && document.id('num_exp').get('value').getNumericValue() == 0) {
		alert('Debe especificar el nombre o seleccionar un expendio');
		
		document.id('nombre').focus();
	} else if (document.id('importe').get('value').getNumericValue() == 0) {
		alert('Debe especificar el total de renta');
		
		document.id('importe').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ExpendiosLimitesRentas.php',
			data: 'accion=do_alta&' + document.id('alta_limite').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
			}
		}).send();
	}
}

var modificar = function(id) {
	new Request({
		url: 'ExpendiosLimitesRentas.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar_limite'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('num_cia').addEvents({
				change: obtener_cia,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').select();
					}
				}
			}).focus();

			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('num_exp').select();
					}
				}
			});

			document.id('num_exp').addEvents({
				change: obtener_exp,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha_inicio').select();
					}
				}
			});

			document.id('fecha_inicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha_termino').select();
					}
				}
			});

			document.id('fecha_termino').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('importe').select();
					}
				}
			});

			document.id('aplicar_iva').addEvent('click', calcular_renta);
			document.id('aplicar_ret').addEvent('click', calcular_renta);

			document.id('importe').addEvents({
				change: calcular_renta,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('num_cia').select();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', consultar.pass(param));
			
			document.id('modificar').addEvent('click', do_modificar);
			
			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		document.id('num_cia').focus();
	} else if (document.id('nombre').get('value') == '' && document.id('num_exp').get('value').getNumericValue() == 0) {
		alert('Debe especificar el nombre o seleccionar un expendio');
		
		document.id('nombre').focus();
	} else if (document.id('importe').get('value').getNumericValue() == 0) {
		alert('Debe especificar el total de renta');
		
		document.id('importe').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ExpendiosLimitesRentas.php',
			data: 'accion=do_modificar&' + document.id('modificar_limite').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
			}
		}).send();
	}
}

var do_baja = function(id) {
	if (confirm('¿Desea borrar el producto seleccionado?')) {
		new Request({
			url: 'ExpendiosLimitesRentas.php',
			data: 'accion=do_baja&id=' + id,
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
				
				if (result.getNumericValue() < 0) {
					alert('El producto no puede ser borrado porque existen movimientos codificados con el mismo.');
				}
			}
		}).send();
	}
}

var calcular_renta = function() {
	if (document.id('importe').get('value').getNumericValue() > 0) {
		var total = document.id('importe').get('value').getNumericValue();
		var importe = total / 0.95333;
		var iva = 0;
		var ret_iva = 0;
		var ret_isr = 0;

		if ( ! document.id('aplicar_iva').get('checked') && ! document.id('aplicar_ret').get('checked')) {
			importe = total;
		} else if (document.id('aplicar_iva').get('checked') && ! document.id('aplicar_ret').get('checked')) {
			iva = (total - total / 1.16).round(2);
			importe = total - iva;
		} else if (document.id('aplicar_ret').get('checked')) {
			importe = total / 0.95333;
			iva = (importe * 0.16).round(2);
			ret_iva = (iva * 2 / 3).round(2);
			ret_isr = (importe * 0.10).round(2);

			importe = importe + iva - ret_iva - ret_isr != total ? total - iva + ret_iva + ret_isr : importe;
		}

		document.id('importe_bruto').set('value', importe.numberFormat(2, '.', ','));
		document.id('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
		document.id('ret_iva').set('value', ret_iva > 0 ? ret_iva.numberFormat(2, '.', ',') : '');
		document.id('ret_isr').set('value', ret_isr > 0 ? ret_isr.numberFormat(2, '.', ',') : '');
	} else {
		document.id('importe_bruto').set('value', '');
		document.id('iva').set('value', '');
		document.id('ret_iva').set('value', '');
		document.id('ret_isr').set('value', '');
		document.id('importe').set('value', '');
	}
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ExpendiosLimitesRentas.php',
			data: 'accion=cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result);
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp'));

					alert('La compañía no esta en el catálogo');

					document.id('num_cia').select();
				}
			}
		}).send();
	}
	else
	{
		document.id('num_cia').set('value', '');
		document.id('nombre_cia').set('value', '');

		document.id('num_exp').set('value', '');
		document.id('nombre_exp').set('value', '');
	}
}

var obtener_exp = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		if (document.id('num_exp').get('value').getNumericValue() > 0)
		{
			new Request({
				url: 'ExpendiosLimitesRentas.php',
				data: 'accion=exp&num_cia=' + document.id('num_cia').get('value') + '&num_exp=' + document.id('num_exp').get('value'),
				onRequest: function() {},
				onSuccess: function(result)
				{
					if (result != '')
					{
						document.id('nombre_exp').set('value', result);

						if (document.id('nombre_exp').get('value') == '') {
							document.id('nombre_exp').set('value', result);
						}
					}
					else
					{
						document.id('num_exp').set('value', document.id('num_exp').retrieve('tmp'));

						alert('El expendio no esta en el catálogo o no pertenece a la compañía seleccionada');

						document.id('num_exp').select();
					}
				}
			}).send();
		}
	}
	else
	{
		document.id('num_exp').set('value', '');
		document.id('nombre_exp').set('value', '');

		alert('Debe especificar primero el número de compañía');

		document.id('num_cia').select();
	}
}
