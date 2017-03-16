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
});
