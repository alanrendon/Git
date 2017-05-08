// JavaScript Document

window.addEvent('domready', function() {
	Reporte.run();
});

var Reporte = function() {
	new Request({
		'url': 'RentasPagadasAlerta.tpl',
		'data': 'accion=reporte',
		'onSuccess': function(result) {
			if (result != '') {
				$(document).set('html', result);
				
				$('cerrar').addEvent('click', function(e) {
					e.stop();
				});
			}
			else {
				self.close();
			}
		}
	}).send();
}
