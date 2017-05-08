window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('fecha1').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
		
		$('fecha2').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
		
		new Calendar(
			{
				'fecha1': 'd/m/Y',
				'fecha2': 'd/m/Y'
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
					
					$('productos').select();
				}
			}
		});
		
		$('productos').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('fecha1').focus();
				}
			}
		});
		
		$('fecha1').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('fecha2').focus();
				}
			}
		});
		
		$('fecha2').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('cias').focus();
				}
			}
		});
		
		$('cias').focus();
	}
	
	$('reporte').addEvent('click', Reporte);
});

var Reporte = function() {
	if ($('productos').get('value') == '') {
		alert('Debe especificar los productos a consultar');
		$('producto').focus();
	}
	else if ($('fecha1').get('value') == '' && $('fecha2').get('value') == '') {
		alert('Debe especificar la fecha o el periodo de búsqueda');
		$('fecha1').select();
	}
	else {
		var url = 'ProduccionConsultaPiezasDia.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}
