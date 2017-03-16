window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('anios').addEvents({
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
					
					$('anios').select();
				}
			}
		});
		
		$('anios').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('cias').focus();
				}
			}
		});
		
		$('cias').select();
	}
	
	$('exportar').addEvent('click', Exportar);
	
	$('reporte').addEvent('click', Reporte);
});

var Reporte = function() {
	if ($('anios').get('value') == '') {
		alert('Debe especificar el años o años de consulta');
		$('anios').select();
	}
	else {
		var url = 'ClientesComparativo.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}

var Exportar = function() {
	if ($('anios').get('value') == '') {
		alert('Debe especificar el años o años de consulta');
		$('anios').select();
	}
	else {
		var url = 'ClientesComparativo.php',
			arg = '?accion=exportar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
		
		win = window.open(url + arg, 'exportar', opt);
		
		win.focus();
	}
}
