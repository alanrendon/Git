// JavaScript Document

window.addEvent('domready', function() {
	$('terminar').addEvent('click', function() {
		document.location = 'ReporteAguinaldo.php';
	});
	
	$('reporte').addEvent('click', function() {
		var url = 'ReporteAguinaldo.php',
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			param = '?accion=pdf&folio=' + $('folio').get('value'),
			win;
			
			win = window.open(url + param, '', opt);
			
			win.focus();
	});
});
