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
		url: 'CatalogoContadores.php',
		data: 'accion=inicio',
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
			
			boxProcessing.close();
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'CatalogoContadores.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('email').select();
					}
				}
			}).focus();
			
			document.id('email').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', inicio);
			
			document.id('do_alta').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('nombre').get('value') == '') {
		alert('Debe especificar el nombre');
		
		document.id('nombre').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'CatalogoContadores.php',
			data: 'accion=do_alta&' + document.id('alta').toQueryString(),
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

var modificar = function(id) {
	new Request({
		url: 'CatalogoContadores.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('email').select();
					}
				}
			}).focus();
			
			document.id('email').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', inicio);
			
			document.id('do_modificar').addEvent('click', do_modificar);
			
			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('nombre').get('value') == '') {
		alert('Debe especificar el nombre');
		
		document.id('nombre').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'CatalogoContadores.php',
			data: 'accion=do_modificar&' + document.id('modificar').toQueryString(),
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

var do_baja = function(id) {
	if (confirm('¿Desea borrar el registro seleccionado?')) {
		new Request({
			url: 'CatalogoContadores.php',
			data: 'accion=do_baja&id=' + id,
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
