// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				this.blur();
				e.stop();
			}
		}
	});
	
	$('consultar').addEvent('click', Consultar);
	
	$('cias').focus();
});

var Consultar = function() {
	var url = 'UltimoDiaCapturadoConsumos.php',
		arg = '?accion=consultar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	var win = window.open(url + arg, 'consulta', opt);
	win.focus();
}
