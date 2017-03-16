window.addEvent('domready', function() {
	
	document.id('cerrar').addEvent('click', function() {
		self.close();
	});
	
	$$('a.info').each(function(el) {
		// el.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n');
		el.store('tip:text', el.get('data-info'));
		
		el.removeProperty('data-info');
	});
	
	tips_info = new Tips($$('a.info'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
	
});
