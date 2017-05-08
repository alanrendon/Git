// JavaScript Document

window.addEvent('domready', function() {
	$('generar').addEvent('click', Generar);
});

var Generar = function() {
	new Request({
		'url': 'ChequeraSeguridadBanorte.php',
		'data': 'accion=consultar',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result.getNumericValue() > 0) {
				document.location = 'ChequeraSeguridadBanorte.php?accion=generar';
			}
			else {
				alert('No hay cheques pendientes por liberar');
			}
		}
	}).send();
}
