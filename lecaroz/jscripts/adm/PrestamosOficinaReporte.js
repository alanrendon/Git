// JavaScript Document

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
		
		$$('.detalle').each(function(el, i) {
			el.store('tip:title', '<img src="imagenes/info.png" /> Detalle de movimientos');
			el.store('tip:text', el.get('title'));
			
			el.removeProperty('title');
		});
		
		tips = new Tips($$('.detalle'), {
			'fixed': true,
			'className': 'Tip',
			'showDelay': 50,
			'hideDelay': 50
		});
	}
});
