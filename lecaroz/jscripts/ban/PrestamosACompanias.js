// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': cambiaCia.pass(0),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=fecha]')[0].select();
			}	
		}
	});
	
	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=importe]')[0].select();
			}
		}
	});
	
	$('importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (!$chk($$('input[id=num_cia]')[1])) {
					newRow.run(1);
				}
				
				$$('input[id=num_cia]')[1].select();
			}
		}
	});
	
	$('registrar').addEvent('click', Registrar);
});

var cambiaCia = function() {
}

var newRow = function() {
	var i = arguments[0],
		tr = new Element('tr', {
			
		})
}

var Registrar = function() {
}
