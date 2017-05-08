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

	if (document.id('cias').get('tag') == 'select') {
		document.id('fecha1').addEvents({
			keydown: function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
		
		document.id('fecha2').addEvents({
			keydown: function(e) {
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
	} else {
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
					
					document.id('productos').focus();
				}
			}
		});
		
		document.id('productos').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					document.id('cias').select();
				}
			}
		});
	}
	
	document.id('consultar').addEvent('click', consultar);
	
	document.id('exportar').addEvent('click', exportar);
	
	document.id('cias').focus();
}

var consultar = function() {
	if ( ! document.id('reporte_anual').get('checked') && document.id('fecha1').get('value') == '' && document.id('fecha2').get('value') == '') {
		alert('Debe especificar el dia o el periodo de consulta');
		document.id('fecha1').select();
	} else if (document.id('reporte_anual').get('checked') && document.id('fecha1').get('value') == '') {
		alert('Debe especificar la primera fecha para generar el reporte anual');
		document.id('fecha1').select();
	}
	else {
		var url = 'ProduccionReporte.php',
			arg = '?accion=reporte' + (document.id('reporte_anual').get('checked') ? '_totales' : '') + '&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'reporte_produccion', opt);
		win.focus();
	}
}

var exportar = function() {
	if (document.id('fecha1').get('value') == '' && document.id('fecha2').get('value') == '') {
		alert('Debe especificar el dia o el periodo de consulta');
		document.id('fecha1').select();
	}
	else {
		var url = 'ProduccionReporte.php',
			arg = '?accion=exportar' + (document.id('reporte_anual').get('checked') ? '_totales' : '') + '&' + document.id('inicio').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
			win;
		
		win = window.open(url + arg, 'reporte_produccion_exportar', opt);
		win.focus();
	}
}
