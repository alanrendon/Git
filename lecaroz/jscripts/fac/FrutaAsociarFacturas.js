// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$$('input[id=num_rem]').each(function(el, i, num_rem) {
		el.addEvents({
			'change': ObtenerRemision.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();
					
					$$('input[id=num_fact]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=num_rem]')[i - 1].select();
					}
					else {
						$$('input[id=num_rem]')[$$('input[id=num_rem]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=num_rem]').length - 1) {
						$$('input[id=num_rem]')[i + 1].select();
					}
					else {
						$$('input[id=num_rem]')[0].select();
					}
				}
			}
		});
	});
	
	$$('input[id=num_fact]').each(function(el, i, num_fact) {
		el.addEvents({
			'change': ValidarFactura.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if (i + 1 > $$('input[id=num_fact]').length - 1) {
						AgregarFila(i + 1);
					}
					
					$$('input[id=num_rem]')[i + 1].select();
				}
				else if (e.key == 'left') {
					e.stop();
					
					$$('input[id=num_rem]')[i].select();
				}
				else if (e.key == 'up') {
					e.stop();
					
					if (i > 0) {
						$$('input[id=num_fact]')[i - 1].select();
					}
					else {
						$$('input[id=num_fact]')[$$('input[id=num_fact]').length - 1].select();
					}
				}
				else if (e.key == 'down') {
					e.stop();
					
					if (i < $$('input[id=num_fact]').length -1) {
						$$('input[id=num_fact]')[i + 1].select();
					}
					else {
						$$('input[id=num_fact]')[0].select();
					}
				}
			}
		});
	});
	
	$$('select[id=num_pro]').each(function(el, i) {
		el.addEvent('change', DesglosarDatos.pass(i));
	});
	
	$('asociar').addEvent('click', Asociar);
	
	$('cancelar').addEvent('click', Cancelar);
	
	$('num_rem').select();
});

var AgregarFila = function() {
	var i = arguments[0],
		tbody = $('tbody'),
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject(tbody),
		td1 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td2 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td3 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td4 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td5 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		td6 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		num_rem = new Element('input', {
			'id': 'num_rem',
			'name': 'num_rem[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus onlyNumbersAndLetters toUpper'
		}).inject(td1),
		num_pro = new Element('select', {
			'id': 'num_pro',
			'name': 'num_pro[]'
		}).inject(td2),
		cia = new Element('input', {
			'id': 'cia',
			'name': 'cia[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td3),
		fecha = new Element('input', {
			'id': 'fecha',
			'name': 'fecha[]',
			'type': 'text',
			'size': 10,
			'maxlength': 10,
			'class': 'center',
			'disabled': true
		}).inject(td4),
		total = new Element('input', {
			'id': 'total',
			'name': 'total[]',
			'type': 'text',
			'size': 10,
			'class': 'right',
			'disabled': true
		}).inject(td5),
		num_fact = new Element('input', {
			'id': 'num_fact',
			'name': 'num_fact[]',
			'type': 'text',
			'size': 10,
			'class': 'valid Focus onlyNumbersAndLetters toUpper'
		}).inject(td6);
	
	validator.addElementEvents(num_rem);
	validator.addElementEvents(num_fact);
	
	styles.addElementEvents(num_rem);
	styles.addElementEvents(num_fact);
	
	num_rem.addEvents({
		'change': ObtenerRemision.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter' || e.key == 'right') {
				e.stop();
				
				$$('input[id=num_fact]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=num_rem]')[i - 1].select();
				}
				else {
					$$('input[id=num_rem]')[$$('input[id=num_rem]').length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=num_rem]').length - 1) {
					$$('input[id=num_rem]')[i + 1].select();
				}
				else {
					$$('input[id=num_rem]')[0].select();
				}
			}
		}
	});
	
	num_pro.addEvent('change', DesglosarDatos.pass(i));
	
	num_fact.addEvents({
		'change': ValidarFactura.pass(i),
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (i + 1 > $$('input[id=num_fact]').length - 1) {
					AgregarFila(i + 1);
				}
				
				$$('input[id=num_rem]')[i + 1].select();
			}
			else if (e.key == 'left') {
				e.stop();
				
				$$('input[id=num_rem]')[i].select();
			}
			else if (e.key == 'up') {
				e.stop();
				
				if (i > 0) {
					$$('input[id=num_fact]')[i - 1].select();
				}
				else {
					$$('input[id=num_fact]')[num_fact.length - 1].select();
				}
			}
			else if (e.key == 'down') {
				e.stop();
				
				if (i < $$('input[id=num_fact]').length -1) {
					$$('input[id=num_fact]')[i + 1].select();
				}
				else {
					$$('input[id=num_fact]')[0].select();
				}
			}
		}
	});
}

var ObtenerRemision = function() {
	var i = arguments[0];
	
	if ($$('input[id=num_rem]')[i].get('value') != '') {
		new Request({
			'url': 'FrutaAsociarFacturas.php',
			'data': 'accion=obtenerRemision&num_rem=' + $$('input[id=num_rem]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					updSelect($$('select[id=num_pro]')[i], data);
					
					DesglosarDatos.run(i);
				}
				else {
					alert('La remisión no esta registrada en el sistema o ya ha sido asociada a una factura');
					
					$$('input[id=num_rem]')[i].set('value', $$('input[id=num_rem]')[i].retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('input[id=cia]')[i].set('value', '');
		$$('input[id=fecha]')[i].set('value', '');
		$$('input[id=total]')[i].set('value', '');
		$$('input[id=num_fact]')[i].set('value', '');
		
		updSelect($$('select[id=num_pro]')[i], []);
	}
}

var DesglosarDatos = function() {
	var i = arguments[0],
		data = JSON.decode($$('select[id=num_pro]')[i].get('value'));
	
	$$('input[id=cia]')[i].set('value', data.num_cia + ' ' + data.nombre_cia);
	$$('input[id=fecha]')[i].set('value', data.fecha);
	$$('input[id=total]')[i].set('value', data.total.numberFormat(2, '.', ','));
}

var ValidarFactura = function() {
	var i = arguments[0];
	
	if ($$('select[id=num_pro]')[i].get('value') != '' && $$('input[id=num_fact]')[i].get('value') != '') {
		var data = JSON.decode($$('select[id=num_pro]')[i].get('value'));
		
		new Request({
			'url': 'FrutaAsociarFacturas.php',
			'data': 'accion=validarFactura&num_pro=' + data.num_pro + '&num_fact=' + $$('input[id=num_fact]')[i].get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var info = JSON.decode(result);
					
					alert('La factura ya esta registrada para la compañía "' + info.num_cia + ' ' + info.nombre_cia + '" el día "' + info.fecha + '" por un total de "' + info.total.numberFormat(2, '.', ',') + '"');
					
					$$('input[id=num_fact]')[i].set('value', $$('input[id=num_fact]')[i].retrieve('tmp', ''))
				}
			}
		}).send();
	}
}

var Asociar = function() {
	if (confirm('¿Son correctos todos los datos?')) {
		var queryString = [];
		
		$('Datos').getElements('input, select').each(function(el) {
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') {
				return;
			}
			
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt) {
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			
			$splat(value).each(function(val) {
				if (typeof val != 'undefined') {
					queryString.push(el.name + '=' + encodeURIComponent(val));
				}
			});
		});
		
		new Request({
			'url': 'FrutaAsociarFacturas.php',
			'data': 'accion=asociar&' + queryString.join('&'),
			'onRequest': function() {
				$$('#asociar, #cancelar').set('disabled', true);
			},
			'onSuccess': function(result) {
				if (result != '') {
					var string = 'Las siguientes remisiones no pueden ser borradas porque son de meses anteriores:\n'
						data = JSON.decode(result);
					
					data.each(function(rec, i) {
						string += '\nProveedor "' + rec.num_pro + ' ' + rec.nombre_pro + '" remisión "' + rec.num_rem + '"';
					});
					
					alert(string);
				}
				
				$('tbody').empty();
				
				AgregarFila.run(0);
				
				$('num_rem').select();
				
				$$('#asociar, #cancelar').set('disabled', false);
			}
		}).send();
	}
}

var Cancelar = function() {
	$('tbody').empty();
	
	AgregarFila.run(0);
	
	$('num_rem').select();
}

var updSelect = function() {
	var Select = arguments[0],
		Options = arguments[1];
	
	if (Options.length > 0) {
		Select.length = Options.length;
		
		$each(Select.options, function(el, i) {
			el.set(Options[i]);
		});
		
		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;
		
		Select.selectedIndex = -1;
	}
}
