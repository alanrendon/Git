// JavaScript Document

var tips;

window.addEvent('domready', function() {
	new Formulario('Datos');
	
	$$('select[id=nivel]').each(function(el, i) {
		el.addEvent('change', Actualizar.pass(el));
		
		Color(el);
	});
});

var Actualizar = function() {
	var el = arguments[0];
	
	new Request({
		'url': 'AutorizacionBalances.php',
		'data': 'accion=actualizar&data=' + el.get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			Color(el);
		}
	}).send();
}

function Color(el) {
	switch (el.get('value').split('|')[1]) {
		case '2':
			el.setStyle('color', '#00C');
		break;
		
		case '1':
			el.setStyle('color', '#060');
		break;
		
		case '0':
			el.setStyle('color', '#C00');
		break;
	}
}
