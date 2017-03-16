// JavaScript Document

window.addEvent('domready', function() {
	$('generar').addEvent('click', function() {
		var url = 'CartaMovimientosIMSS.php',
			param = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		
		var win = window.open(url + param, '', opt);
		
		win.focus();
	});
	
	$('limpiar_altas').addEvent('click', function() {
		if (confirm('¿Desea limpiar el listado de altas?')) {
			new Request({
				'url': 'CartaMovimientosIMSS.php',
				'data': 'accion=limpiar_altas',
				'onRequest': function() {
				},
				'onSuccess': function(result) {
					alert('Lista de altas ha sido vaciada');
				}
			}).send();
		}
	});
	
	$('limpiar_bajas').addEvent('click', function() {
		if (confirm('¿Desea limpiar el listado de bajas?')) {
			new Request({
				'url': 'CartaMovimientosIMSS.php',
				'data': 'accion=limpiar_bajas',
				'onRequest': function() {
				},
				'onSuccess': function(result) {
					alert('Lista de bajas ha sido vaciada');
				}
			}).send();
		}
	});
});

var TraspasarSaldo = function() {
	new Request({
		'url': 'CompaniasCondependenciaTraspasoSaldo.php',
		'data': 'accion=traspasar&tipo=' + $$('input[name=tipo]:checked')[0].get('value') + '&' + $('Datos').toQueryString(),
		'onRequest': function() {
			popup.Close();
			
			popup = new Popup('<img src="imagenes/_loading.gif" /> Realizando traspasos...', '<img src="iconos/envelope.png" /> Procesando...', 250, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
			
			Inicio.run();
			
			var url = 'CompaniasCondependenciaTraspasoSaldo.php',
				param = '?accion=listado&ts=' + result,
				opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
			
			var win = window.open(url + param, '', opt);
			
			win.focus();
		}
	}).send();
}
