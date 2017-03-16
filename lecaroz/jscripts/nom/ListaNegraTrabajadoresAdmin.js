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
		url: 'ListaNegraTrabajadoresAdmin.php',
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
		url: 'ListaNegraTrabajadoresAdmin.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('alta_registro'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_paterno').select();
					}
				}
			}).focus();
			
			document.id('ap_paterno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_materno').select();
					}
				}
			});
			
			document.id('ap_materno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('observaciones').select();
					}
				}
			});
			
			document.id('observaciones').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').select();
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
	if (document.id('nombre').get('value') == '') {
		alert('Debe especificar el nombre del empleado');
		
		document.id('nombre').focus();
	} else if (document.id('ap_paterno').get('value') == '') {
		alert('Debe especificar el ap_paterno del empleado');
		
		document.id('ap_paterno').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ListaNegraTrabajadoresAdmin.php',
			data: 'accion=do_alta&' + document.id('alta_registro').toQueryString(),
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
		url: 'ListaNegraTrabajadoresAdmin.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('modificar_registro'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_paterno').select();
					}
				}
			}).focus();
			
			document.id('ap_paterno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_materno').select();
					}
				}
			});
			
			document.id('ap_materno').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('observaciones').select();
					}
				}
			});
			
			document.id('observaciones').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').select();
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
	if (document.id('nombre').get('value') == '') {
		alert('Debe especificar el nombre del empleado');
		
		document.id('nombre').focus();
	} else if (document.id('ap_paterno').get('value') == '') {
		alert('Debe especificar el ap_paterno del empleado');
		
		document.id('ap_paterno').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ListaNegraTrabajadoresAdmin.php',
			data: 'accion=do_modificar&' + document.id('modificar_registro').toQueryString(),
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
	if (confirm('¿Desea borrar el registro seleccionado?')) {
		new Request({
			url: 'ListaNegraTrabajadoresAdmin.php',
			data: 'accion=do_baja&id=' + id,
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
