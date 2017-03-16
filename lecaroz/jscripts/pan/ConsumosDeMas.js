// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('fecha').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
		
		new Calendar(
			{
				'fecha': 'd/m/Y',
			},
			{
				'days': ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
				'months': ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
			}
		);
	}
	else {
		$('cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('fecha').select();
				}
			}
		});
		
		$('fecha').addEvents({
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
	if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de corte');
		$('fecha').select();
	}
	else {
		var url = 'ConsumosDeMas.php',
			arg = '?accion=reporte' + ($('codmp').get('value').getNumericValue() > 0 ? '2' : '') + '&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, '', opt);
		win.focus();
	}
}

var Exportar = function() {
	if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha de corte');
		$('fecha').select();
	}
	else {
		var url = 'ConsumosDeMas.php',
			arg = '?accion=exportar' + ($('codmp').get('value').getNumericValue() > 0 ? '2' : '') + '&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1,height=1',
			win;
		
		document.location = url + arg;
	}
}
