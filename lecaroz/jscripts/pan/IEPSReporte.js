window.addEvent('domready', function() {
	
	boxProcessing = new mBox({
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		closeOnEsc: false,
		closeOnBodyClick: false
	});
	
	inicio();
	
});

var inicio = function () {
	new FormValidator(document.id('inicio'), {
		showErrors: true,
		selectOnFocus: true
	});

	document.id('cias').addEvents({
		'keydown': function(e) {
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
				
				document.id('cias').select();
			}
		}
	});
	
	document.id('consultar').addEvent('click', consultar);
	
	document.id('cias').focus();
}

var consultar = function() {
	if (document.id('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el año de consulta');
		document.id('anio').select();
	} else if (document.id('anio').get('value').getNumericValue() < 2014) {
		alert('El año de consulta debe ser igual o mayor al 2014');
		document.id('anio').select();
	} else {
		var url = 'IEPSReporte.php',
			arg = '?accion=reporte&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte_ieps', opt);
		win.focus();
	}
}
