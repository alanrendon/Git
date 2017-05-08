// JavaScript Document

window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$$('input[id=nombre]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=ap_paterno]')[i].select();
			}
		});
	});
	
	$$('input[id=ap_paterno]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('input[id=ap_materno]')[i].select();
			}
		});
	});
	
	$$('input[id=ap_materno]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$$('textarea[id=observaciones]')[i].select();
			}
		});
	});
	
	$$('textarea[id=observaciones]').each(function(el, i) {
		el.addEvent('keydown', function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				if (!$chk($$('input[id=nombre]')[i + 1])) {
					newRow(i + 1);
				}
				
				$$('input[id=nombre]')[i + 1].select();
			}
		});
	});
	
	$('alta').addEvent('click', Alta);
	
	$('nombre').select();

	tipo_baja = $('tipo_baja').clone().set({
		id: 'tipo_baja',
		name: 'tipo_baja[]'
	});
});

var newRow = function(i) {
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	var td1 = new Element('td', {
		'valign': 'top'
	});
	var td2 = new Element('td', {
		'valign': 'top'
	});
	var td3 = new Element('td', {
		'valign': 'top'
	});
	var td4 = new Element('td', {
		'valign': 'top'
	});
	var td5 = new Element('td', {
		'valign': 'top'
	});
	
	var nombre = new Element('input', {
		'id': 'nombre',
		'name': 'nombre[]',
		'type': 'text',
		'class': 'valid onlyText cleanText toUpper',
		'size': 30,
		'maxlength': 200
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('input[id=ap_paterno]')[i].select();
		}
	}).inject(td1);
	
	var ap_paterno = new Element('input', {
		'id': 'ap_paterno',
		'name': 'ap_paterno[]',
		'type': 'text',
		'class': 'valid onlyText cleanText toUpper',
		'size': 30,
		'maxlength': 200
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('input[id=ap_materno]')[i].select();
		}
	}).inject(td2);
	
	var ap_materno = new Element('input', {
		'id': 'ap_materno',
		'name': 'ap_materno[]',
		'type': 'text',
		'class': 'valid onlyText cleanText toUpper',
		'size': 30,
		'maxlength': 200
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			$$('textarea[id=observaciones]')[i].select();
		}
	}).inject(td3);
	
	tipo_baja.inject(td4);
	
	var observaciones = new Element('textarea', {
		'id': 'observaciones',
		'name': 'observaciones[]',
		'class': 'valid toText cleanText toUpper',
		'cols': 50,
		'rows': 3
	}).addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			
			if (!$chk($$('input[id=nombre]')[i + 1])) {
				newRow(i + 1);
			}
			
			$$('input[id=nombre]')[i + 1].select();
		}
	}).inject(td5);
	
	validator.addElementEvents(nombre);
	validator.addElementEvents(ap_paterno);
	validator.addElementEvents(ap_materno);
	validator.addElementEvents(observaciones);
	
	styles.addElementEvents(nombre);
	styles.addElementEvents(ap_paterno);
	styles.addElementEvents(ap_materno);
	styles.addElementEvents(tipo_baja);
	styles.addElementEvents(observaciones);
	
	td1.inject(tr);
	td2.inject(tr);
	td3.inject(tr);
	td4.inject(tr);
	td5.inject(tr);
	
	tr.inject($('TablaCaptura'));
}

var Alta = function() {
	if (confirm('Los registros aqui capturados son permanentes y no pueden ser borrados o modificados, ¿Desea proseguir con el registro?')) {
		var queryString = [];
		
		$('Datos').getElements('input, textarea, select').each(function(el) {
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
			'url': 'ListaNegraAlta.php',
			'data': 'accion=alta&' + queryString.join('&'),
			'onRequest': function() {
			},
			'onSuccess': function() {
				alert('Se han ingresado los trabajores a la lista negra');
				
				$('TablaCaptura').empty();
				
				newRow.run(0);
				
				$('nombre').select();
			}
		}).send();
	}
}
