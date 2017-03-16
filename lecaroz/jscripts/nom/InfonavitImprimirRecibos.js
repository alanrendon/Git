// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('folios').focus();
			}
		}
	});
	
	$('folios').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha2').select();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('anio').select();
			}
		}
	});
	
	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').focus();
			}
		}
	});
	
	$('email').addEvent('click', Email);
	
	$('imprimir').addEvent('click', Imprimir);
	
	$('cias').focus();
});

var Imprimir = function() {
	new Request({
		'url': 'InfonavitImprimirRecibos.php',
		'data': 'accion=generar&tipo=print&' + $('Datos').toQueryString(),
		'onRequest': function() {},
		'onSuccess': function(filename) {
			if (filename != '') {
				var url = 'InfonavitImprimirRecibos.php?accion=imprimir&filename=' + filename,
					opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
					win = window.open(url, '', opt);
				
				win.focus();
			}
			else {
				alert('No hay resultados');
			}
		}
	}).send();
}

var Email = function() {
	new Request({
		'url': 'InfonavitImprimirRecibos.php',
		'data': 'accion=generar&tipo=email&' + $('Datos').toQueryString(),
		'onRequest': function() {},
		'onSuccess': function(data) {
			if (data != '') {
				new Request({
					'url': 'InfonavitImprimirRecibos.php',
					'data': 'accion=email&data=' + data,
					'onRequest': function() {},
					'onSuccess': function(result) {
						if (result != '') {
							alert('Han ocurrido los siguientes errores al enviar los correos:\n\n' + result);
						}
						else {
							alert('Correos enviados');
						}
					}
				}).send();
			}
			else {
				alert('No hay resultados');
			}
		}
	}).send();
}
