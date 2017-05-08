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
		url: 'ProductosCatalogo.php',
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
			
			document.id('productos').addEvents({
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
		url: 'ProductosCatalogo.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			document.id('alta').addEvent('click', alta);
			
			$$('img[id=mod]').each(function(el) {
				var producto = el.get('alt');
				
				el.addEvent('click', modificar.pass(producto));
				
				el.removeProperty('alt');
			});
			
			$$('img[id=baja]').each(function(el) {
				var producto = el.get('alt');
				
				el.addEvent('click', do_baja.pass(producto));
				
				el.removeProperty('alt');
			});
			
			document.id('regresar').addEvent('click', inicio);
			
			boxProcessing.close();
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'ProductosCatalogo.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_producto'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('descripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			}).focus();
			
			document.id('cancelar').addEvent('click', consultar.pass(param));
			
			document.id('alta').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('descripcion').get('value') == '') {
		alert('Debe especificar el nombre del producto');
		
		document.id('descripcion').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ProductosCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_producto').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
				
				var data = JSON.decode(result);
				
				alert('El producto "' + data.descripcion + '" ha sido dado de alta con el código "' + data.producto + '"');
			}
		}).send();
	}
}

var modificar = function(producto) {
	new Request({
		url: 'ProductosCatalogo.php',
		data: 'accion=modificar&producto=' + producto,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar_producto'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('descripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			}).focus();
			
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
			url: 'ProductosCatalogo.php',
			data: 'accion=do_modificar&' + document.id('modificar_producto').toQueryString(),
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

var do_baja = function(producto) {
	if (confirm('¿Desea borrar el producto seleccionado?')) {
		new Request({
			url: 'ProductosCatalogo.php',
			data: 'accion=do_baja&producto=' + producto,
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
