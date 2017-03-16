// JavaScript Document

window.addEvent('domready', function() {
	$$('a').each(function(el) {
		var data = JSON.decode(el.get('alt'));
		
		el.addEvent('click', ExpendioDetalleRezago.pass(data));
		
		el.removeProperty('alt');
	});
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});
});

var ExpendioDetalleRezago = function() {
	var data = arguments[0],
		url = 'ExpendioDetalleRezago.php',
		param = '?accion=reporte&num_cia=' + data.num_cia + '&num_exp=' + data.num_exp + '&nombre_exp=' + data.nombre_exp + '&fecha1=' + data.fecha1 + '&fecha2=' + data.fecha2 + '&dias=' + data.dias,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, 'reporte_detalle_rezago', opt);
	
	win.focus();
}
