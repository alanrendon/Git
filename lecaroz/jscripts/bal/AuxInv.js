// JavaScript Document

window.addEvent('domready', function() {
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	$$('tr[id=row]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				this.addClass('over');
			},
			mouseout: function() {
				this.removeClass('over');
			}
		});
	});
	
	var cods = $$("a[title=cod]");
	
	if (cods.length > 0) {
		cods.each(function(el) {
			el.addEvents({
				click: codDetalle.pass([$('num_cia').get('value'), el.get('text'), $('anio').get('value'), $('mes').get('value'), $('inv').get('value')])
			});
		});
	}
});

var codDetalle = function() {
	var num_cia = arguments[0];
	var codmp = arguments[1];
	var anio = arguments[2];
	var mes = arguments[3];
	var inv = arguments[4];
	
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	var url = 'AuxiliarInventario.php?accion=reporte&num_cia=' + num_cia + '&codmp=' + codmp + '&anio=' + anio + '&mes=' + mes + '&inv=' + inv + '&gas=1';
	
	var win = window.open(url, 'auxinvdet', opt);
	win.focus();
}