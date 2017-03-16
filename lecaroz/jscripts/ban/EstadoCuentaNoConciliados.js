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
		url: 'EstadoCuentaNoConciliados.php',
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
						
						document.id('omitir_cias').select();
					}
				}
			});
			
			document.id('omitir_cias').addEvents({
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
						
						document.id('codigos_depositos').focus();
					}
				}
			});
			
			document.id('codigos_depositos').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('codigos_cargos').focus();
					}
				}
			});
			
			document.id('codigos_cargos').addEvents({
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
	
	if (!document.id('depositos').get('checked') && !document.id('cargos').get('checked')) {
		alert('Debe seleccionar al menos un tipo de movimiento');
		
		return false;
	}
	
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}
	
	new Request({
		url: 'EstadoCuentaNoConciliados.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			if (result != '') {
				document.id('captura').empty().set('html', result);
				
				document.id('regresar').addEvent('click', inicio);
				
				document.id('listado').addEvent('click', listado);
				
				document.id('exportar').addEvent('click', exportar);
				
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

var listado = function() {
	var url = 'EstadoCuentaNoConciliados.php',
		url_param = '?accion=listado&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + url_param, 'listado', opt);
	
	win.focus();
}

var exportar = function() {
	var url = 'EstadoCuentaNoConciliados.php',
		url_param = '?accion=exportar&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
		win;
	
	win = window.open(url + url_param, 'exportar', opt);
	
	win.focus();
}
