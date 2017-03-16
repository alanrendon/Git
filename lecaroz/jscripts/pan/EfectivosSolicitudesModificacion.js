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
		'url': 'EfectivosSolicitudesModificacion.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		'onSuccess': function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			}).focus();
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
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
		'url': 'EfectivosSolicitudesModificacion.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		'onSuccess': function(result) {
			if (result != '') {
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
			else {
				alert('No hay resultados');
				
				inicio();
			}
		}
	}).send();
}

var obtener_cia = function() {
	if (document.id('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			url: 'EfectivosSolicitudesModificacion.php',
			data: 'accion=alta',
			onRequest: function() {
			},
			onSuccess: function(result) {
				if (result != '') {
					document.id('nombre_cia').set('value', result);
				} else {
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));
					
					alert('La compañía no esta en el catálogo.');
					
					document.id('num_cia').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia, #nombre_cia, #fecha').set('value', '');
		
		document.id('observaciones').set('text', '');
		
		$$('input[type=checkbox]').set('checked', false);
	}
}

var alta = function() {
	new Request({
		url: 'EfectivosSolicitudesModificacion.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_solicitud'), {
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
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha').select();
					}
				}
			}).focus();
			
			document.id('fecha').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('observaciones').focus();
					}
				}
			});
			
			document.id('observaciones').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('num_cia').focus();
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
	} else if (document.id('observaciones').get('value') == '') {
		alert('Debe especificar el porque esta solicitando la modificación');
		
		document.id('observaciones').focus();
	} else if ($$('input[type=checkbox]:checked').length == 0) {
		alert('Debe seleccionar al menos un campo para moficar');
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'EfectivosSolicitudesModificacion.php',
			data: 'accion=do_alta&' + document.id('alta_solicitud').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
				
				var data = JSON.decode(result);
			}
		}).send();
	}
}

var modificar = function(id) {
	new Request({
		url: 'EfectivosSolicitudesModificacion.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar_gasto'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('descripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('orden').select();
					}
				}
			}).focus();
			
			document.id('orden').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('descuento').focus();
					}
				}
			});
			
			document.id('descuento').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('descripcion').focus();
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
	if (document.id('descripcion').get('value') == '') {
		alert('Debe especificar la descripción del gasto');
		
		document.id('descripcion').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'GastosCatalogo.php',
			data: 'accion=do_modificar&' + document.id('modificar_gasto').toQueryString(),
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

var do_baja = function(gasto) {
	if (confirm('¿Desea borrar el gasto seleccionado?')) {
		new Request({
			url: 'GastosCatalogo.php',
			data: 'accion=do_baja&gasto=' + gasto,
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
				
				if (result.getNumericValue() < 0) {
					alert('El gasto no puede ser borrado porque existen movimientos codificados con el mismo.');
				}
			}
		}).send();
	}
}

