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
				
				$('cantidad').select();
			}
		}
	}).focus();
	
	$('cantidad').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('imprimir').addEvent('click', Imprimir);
});

var Imprimir = function() {
	new Request({
		'url': 'CometraImpresionFichas.php',
		'data': 'accion=imprimir&' + $('Datos').toQueryString(),
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Imprimiendo fichas de Cometra...', '<img src="iconos/printer.png" /> Imprimiendo fichas...', 300, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
			
			popup = new Popup('<p>Las fichas fueron enviadas a la impresora</p><p><input type="button" name="cerrar" id="cerrar" value="Cerrar" /></p>', '<img src="iconos/printer.png" /> Imprimiendo fichas...', 300, 100, function() {
				$('cerrar').addEvent('click', function() {
					popup.Close();
				});
			}, null);
		}
	}).send();
}
