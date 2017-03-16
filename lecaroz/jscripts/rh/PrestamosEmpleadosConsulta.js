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
	
	inicio();
	
});

var inicio = function () {
	new Request({
		url: 'PrestamosEmpleadosConsulta.php',
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
						
						document.id('empleados').select();
					}
				}
			});
			
			document.id('empleados').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('nombre').select();
					}
				}
			});
			
			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('ap_paterno').focus();
					}
				}
			});
			
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
						
						document.id('rfc').focus();
					}
				}
			});
			
			document.id('rfc').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('cias').focus();
					}
				}
			});
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
			
			document.id('cias').focus();
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
		url: 'PrestamosEmpleadosConsulta.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			if (result != '') {
				document.id('captura').empty().set('html', result);
				
				$$('img[id=detalle]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', detalle.pass(id));
					
					img.removeProperty('alt');
				});
				
				new mBox.Tooltip({
					setContent: 'data-tooltip',
					attach: $$('img[id=info]')
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

var detalle = function(id) {
	new Request({
		url: 'PrestamosEmpleadosConsulta.php',
		data: 'accion=detalle&id=' + id,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			if (result != '') {
				document.id('captura').empty().set('html', result);
				
				document.id('regresar').addEvent('click', consultar.pass(param));
				
				boxProcessing.close();
			}
			else {
				consultar(param);
			}
		}
	}).send();
}
