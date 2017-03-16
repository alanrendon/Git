window.addEvent('domready', function() {
	
	document.id('cerrar').addEvent('click', function() {
		self.close();
	});
	
	$$('a[id=info]').each(function(el) {
		el.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n detallada de gastos');
		el.store('tip:text', el.get('name'));
		
		el.removeProperty('name');
	});
	
	tips_info = new Tips($$('[id=info]'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
	
});
