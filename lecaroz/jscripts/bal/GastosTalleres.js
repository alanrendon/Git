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
				
				document.id('fecha1').select();
			}
		}
	});
	
	document.id('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				document.id('fecha2').select();
			}
		}
	});
	
	document.id('fecha2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				document.id('cias').focus();
			}
		}
	});
	
	document.id('consultar').addEvent('click', consultar);
	
	document.id('exportar').addEvent('click', exportar);
	
	document.id('cias').focus();
}

var consultar = function() {
	if (document.id('fecha1').get('value') == '' || document.id('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		document.id('fecha1').select();
	}
	else {
		var url = 'GastosTalleres.php',
			arg = '?accion=reporte&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte_gastos_talleres', opt);
		win.focus();
	}
}

var exportar = function() {
	if (document.id('fecha1').get('value') == '' || document.id('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		document.id('fecha1').select();
	}
	else {
		var url = 'GastosTalleres.php',
			arg = '?accion=exportar&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
			win;
		
		win = window.open(url + arg, 'reporte_gastos_talleres_exportar', opt);
		win.focus();
	}
}
