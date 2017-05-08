// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('anio').addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			this.blur();
		}
	});
	
	$('consultar').addEvent('click', function() {
		var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		var win = window.open('', 'accionistas', opt);
		
		$('Datos').submit();
		
		win.focus();
	});
	
	$('anio').focus();
});
