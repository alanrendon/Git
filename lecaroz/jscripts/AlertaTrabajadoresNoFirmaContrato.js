// JavaScript Document

window.addEvent('domready', function() {
	var width = $$('table[id=empleados]').getWidth().max();
	
	$$('table[id=empleados]').setStyle('width', width + 'px');
	
	$('email').addEvent('click', enviarAvisosEmail);
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});
});

var enviarAvisosEmail = function() {
	new Request({
		'url': 'AlertaTrabajadoresNoFirmaContrato.php',
		'data': 'accion=email',
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Enviando avisos por email...', 'Contratos Vencidos', 400, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
		}
	}).send();
}
