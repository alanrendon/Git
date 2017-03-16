// JavaScript Document

window.addEvent('domready', function() {
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	if ($defined($('row'))) {console.log('aki');
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
		
		if ($defined($('info'))) {
			$$('a[id=info]').each(function(a) {
				a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Desglose de pasteles');
				a.store('tip:text', a.get('title'));
				
				a.removeProperty('title');
			});
			
			tips = new Tips($$('a[id=info]'), {
				'fixed': true,
				'className': 'Tip',
				'showDelay': 50,
				'hideDelay': 50
			});
		}
	}
});
