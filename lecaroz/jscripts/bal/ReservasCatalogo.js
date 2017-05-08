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
		url: 'ReservasCatalogo.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			document.id('alta').addEvent('click', alta);
			
			$$('img[id=mod]').each(function(el) {
				var reserva = el.get('alt');
				
				el.addEvent('click', modificar.pass(reserva));
				
				el.removeProperty('alt');
			});
			
			$$('img[id=baja]').each(function(el) {
				var reserva = el.get('alt');
				
				el.addEvent('click', do_baja.pass(reserva));
				
				el.removeProperty('alt');
			});
			
			boxProcessing.close();
		}
	}).send();
}

var obtener_gasto = function() {
	if (document.id('gasto').get('value').getNumericValue() > 0) {
		new Request({
			url: 'ReservasCatalogo.php',
			data: 'accion=obtener_gasto&gasto=' + document.id('gasto').get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
				if (result != '') {
					document.id('descripcion_gasto').set('value', result)
				} else {
					alert('El código no se encuentra en el catálogo.');
					
					document.id('gasto').set('value', document.id('gasto').retrieve('tmp', '')).select();
				}
			}
		}).send();
	} else {
		$$('#gasto, #descripcion_gasto').set('value', '');
	}
	
}

var alta = function() {
	new Request({
		url: 'ReservasCatalogo.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_reserva'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('descripcion_reserva').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('gasto').select();
					}
				}
			}).focus();
			
			document.id('gasto').addEvents({
				change: obtener_gasto,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('descripcion_reserva').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', inicio);
			
			document.id('alta').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('descripcion_reserva').get('value') == '') {
		alert('Debe especificar la descripción de la reserva');
		
		document.id('descripcion_reserva').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ReservasCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_reserva').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				inicio();
			}
		}).send();
	}
}

var modificar = function(reserva) {
	new Request({
		url: 'ReservasCatalogo.php',
		data: 'accion=modificar&reserva=' + reserva,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar_reserva'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('descripcion_reserva').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('gasto').select();
					}
				}
			}).focus();
			
			document.id('gasto').addEvents({
				change: obtener_gasto,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('descripcion_reserva').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', inicio);
			
			document.id('modificar').addEvent('click', do_modificar);
			
			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('descripcion_reserva').get('value') == '') {
		alert('Debe especificar la descripción de la reserva');
		
		document.id('descripcion_reserva').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ReservasCatalogo.php',
			data: 'accion=do_modificar&' + document.id('modificar_reserva').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				inicio();
			}
		}).send();
	}
}

var do_baja = function(reserva) {
	if (confirm('¿Desea borrar el registro seleccionado?')) {
		new Request({
			url: 'ReservasCatalogo.php',
			data: 'accion=do_baja&reserva=' + reserva,
			onRequest: function() {
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				inicio();
			}
		}).send();
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
