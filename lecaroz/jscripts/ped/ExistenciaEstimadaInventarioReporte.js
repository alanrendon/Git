// JavaScript Document// JavaScript Document

window.addEvent('domready', function() {
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	if ($defined($('row'))) {
		$$('tr[id=row]').each(function(el) {
			el.addEvents({
				'mouseover': function() {
					this.addClass('over');
				},
				'mouseout': function() {
					this.removeClass('over');
				}
			});
		});
		
		$$('a[id=auxiliar]').each(function(el) {
			var data = JSON.decode(el.get('title'));
			
			el.removeProperty('title');
			
			el.addEvent('click', function() {
				var url = 'AuxiliarInventario.php',
					param = '?accion=reporte&num_cia=' + data.num_cia + '&codmp=' + data.codmp + '&anio=' + data.anio + '&mes=' + data.mes + '&inv=real',
					opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
				
				win = window.open(url + param, '', opt);
				
				win.focus();
			});
		});
	}
});
