// JavaScript Document

window.addEvent('domready', function() {
	var width = $$('table[id=empleados]').getWidth().max();

	$$('table[id=empleados]').setStyle('width', width + 'px');

	$('cerrar').addEvent('click', function() {
		self.close();
	});

	$('cerrar').addEvent('click', function() {
		self.close();
	});

	$('email').addEvent('click', enviarAvisosEmail);
});

var enviarAvisosEmail = function() {
	new Request({
		'url': 'AlertaTrabajadoresLicenciasProximasVencer.php',
		'data': 'accion=email',
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Enviando avisos por email...', 'Licencias vencidas', 400, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
		}
	}).send();
}
