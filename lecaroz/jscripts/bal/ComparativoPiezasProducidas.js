// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('anio').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
	}
	else {
		$('cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('anio').select();
				}
			}
		});
		
		$('anio').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('cias').select();
				}
			}
		});
		
		$('cias').select();
	}
	
	$('generar').addEvent('click', Generar);
	
	if ($chk($('exportar'))) {
		$('exportar').addEvent('click', Exportar);
	}
});

var Generar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		$('fecha').select();
	}
	else {
		var url = 'ComparativoPiezasProducidas.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, '', opt);
		win.focus();
	}
}
