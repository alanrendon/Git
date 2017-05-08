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
				
				if ($('anio').tagName == 'INPUT') {
					$('anio').select();
				}
				else {
					this.blur();
				}
			}
		}
	});
	
	if ($('anio').tagName == 'INPUT') {
		$('anio').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('cias').select();
				}
			}
		});
	}
	
	$('cias').select();
	
	$('generar').addEvent('click', Generar);
	
	$('descargar').addEvent('click', Descargar);
});

var Generar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		$('anio').select();
	}
	else {
		var url = 'VariacionAnualPreciosCompra.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}

var Descargar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		$('anio').select();
	}
	else {
		var url = 'VariacionAnualPreciosCompra.php',
			arg = '?accion=descargar&' + $('Datos').toQueryString();
		
		document.location = url + arg;
	}
}
