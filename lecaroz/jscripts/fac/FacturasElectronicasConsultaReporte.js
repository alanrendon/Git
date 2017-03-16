// JavaScript Document

var tips;

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
	}
});
