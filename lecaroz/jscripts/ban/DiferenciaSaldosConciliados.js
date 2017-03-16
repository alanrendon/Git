// JavaScript Document

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
				
				this.blur();
				this.focus();
			}
		}
	}).focus();
	
	$('consultar').addEvent('click', Consultar);
});

var Consultar = function() {
	if ($$('[id=banco]:checked').length == 0) {
		alert('Debe seleccionar al menos un banco');
		
		return false;
	}
	
	var url = 'DiferenciaSaldosConciliados.php',
		param = '?accion=consultar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}
