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
		url: 'ListaNegraTrabajadoresAdmin.php',
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
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_paterno').focus();
					}
				}
			}).focus();
			
			document.id('ap_paterno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_materno').focus();
					}
				}
			});
			
			document.id('ap_materno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').focus();
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
		url: 'GastosCatalogo.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			document.id('alta').addEvent('click', alta);
			
			$$('img[id=mod]').each(function(el) {
				var gasto = el.get('alt');
				
				el.addEvent('click', modificar.pass(gasto));
				
				el.removeProperty('alt');
			});
			
			$$('img[id=baja]').each(function(el) {
				var gasto = el.get('alt');
				
				el.addEvent('click', do_baja.pass(gasto));
				
				el.removeProperty('alt');
			});
			
			document.id('regresar').addEvent('click', inicio);
			
			boxProcessing.close();
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'GastosCatalogo.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_gasto'), {
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
			
			document.id('alta').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('descripcion').get('value') == '') {
		alert('Debe especificar la descripción del gasto');
		
		document.id('descripcion').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'GastosCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_gasto').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
				
				var data = JSON.decode(result);
				
				alert('El gasto "' + data.descripcion + '" ha sido dado de alta con el código "' + data.gasto + '"');
			}
		}).send();
	}
}

var modificar = function(gasto) {
	new Request({
		url: 'GastosCatalogo.php',
		data: 'accion=modificar&gasto=' + gasto,
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
