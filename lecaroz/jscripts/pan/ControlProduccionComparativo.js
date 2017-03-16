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
	});
	
	$('reporte').addEvent('click', Reporte);
	
	$('cias').select();
});

var Reporte = function() {
	var url = 'ControlProduccionComparativo.php',
		param = '?accion=reporte&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}
