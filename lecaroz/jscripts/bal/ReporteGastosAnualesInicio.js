window.addEvent('domready', function() {
	
	new FormValidator(document.id('inicio'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	document.id('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				document.id('gasto').select();
			}
		}
	}).select();
	
	document.id('gasto').addEvents({
		change: obtener_gasto,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				document.id('anio').select();
			}
		}
	});
	 
	document.id('anio').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				document.id('cias').focus();
			}
		}
	});
	
	document.id('reporte').addEvent('click', reporte);
	
});

var obtener_gasto = function() {
	if (document.id('gasto').get('value').getNumericValue() > 0) {
		new Request({
			url: 'ReporteGastosAnuales.php',
			data: 'accion=obtener_gasto&gasto=' + document.id('gasto').get('value'),
			onRequest: function() {
			},
			onSuccess: function(result) {
				if (result != '') {
					document.id('descripcion').set('value', result);
				} else {
					document.id('gasto').set('value', document.id('gasto').retrieve('tmp'));
					
					alert('El código de gasto no se encuentra en el catálogo.');
					
					document.id('gasto').select();
				}
			}
		}).send();
	} else {
		$$('#gasto, #descripcion').set('value', '');
	}
}


var reporte = function() {
	if ($('gasto').get('value').getNumericValue() == 0) {
		alert('Debe especificar código de gasto a consultar');
		$('gasto').select();
	} else if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el años de consulta');
		$('anio').select();
	}
	else {
		var url = 'ReporteGastosAnuales.php',
			arg = '?accion=reporte&' + $('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, '', opt);
		win.focus();
	}
}
