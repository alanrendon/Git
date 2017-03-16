// JavaScript Document

window.addEvent('domready', function() {
	
	$$('a[id=idarrendador]').each(function(a) {
		a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n de contacto');
		a.store('tip:text', a.get('title'));
		
		a.removeProperty('title');
	});
	
	tips_contacto = new Tips($$('a[id=idarrendador]'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
	
	$$('[id=info]').each(function(el) {
		el.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n');
		el.store('tip:text', el.get('alt'));
		
		el.removeProperty('alt');
	});
	
	tips_info = new Tips($$('[id=info]'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
});
