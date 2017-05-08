// JavaScript Document

window.addEvent('domready', function()
{
	var width = $$('table[id=empleados]').getWidth().max();

	$$('a[id=documentos_faltantes]').each(function(a) {
		a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n');
		a.store('tip:text', a.get('data-tooltip'));
	});

	tips = new Tips($$('a[id=documentos_faltantes]'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});

	$$('table[id=empleados]').setStyle('width', width + 'px');

	$('cerrar').addEvent('click', function()
	{
		self.close();
	});

	$('email').addEvent('click', enviarAvisosEmail);
});

var enviarAvisosEmail = function()
{
	new Request({
		'url': 'AlertaTrabajadoresDocumentosFaltantes.php',
		'data': 'accion=email',
		'onRequest': function()
		{
			popup = new Popup('<img src="imagenes/_loading.gif" /> Enviando avisos por email...', 'Documentos faltantes', 400, 100, null, null);
		},
		'onSuccess': function(result)
		{
			popup.Close();
		}
	}).send();
}
