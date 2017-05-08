window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha2').select();
			}
		}
	}).select();
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha1').select();
			}
		}
	});
	
	$('imprimir').addEvent('click', Imprimir);
	
	$('generar').addEvent('click', Generar);
});

var Imprimir = function() {
	new Request({
		'url': 'CometraImprimirComprobantes.php',
		'data': 'accion=imprimirComprobantes&' + $('Datos').toQueryString(),
		'onRequest': function() {
			popup = new Popup('Imprimiendo comprobantes...', 'Imprimir comprobantes', 500, 150, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
		},
		'onFailure': function(xhr) {
			popup.Close();
			
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var Generar = function() {
	var url = 'CometraImprimirComprobantes.php',
		arg = '?accion=generarComprobantes&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768';
	
	win = window.open(url + arg, 'comprobantes', opt);
	
	win.focus();
}
