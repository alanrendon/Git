window.addEvent('domready', function() {
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	styles = new FormStyles($('Datos'));

	crearFila.run(0);

	$('cancelar').addEvent('click', Cancelar);

	$('registrar').addEvent('click', Registrar);

	$('arrendador').select();
});

var crearFila = function() {
	var i = arguments[0],
		tr = new Element('tr', {
			'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
		}).inject($('filas')),
		td1 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		idarrendador = new Element('input', {
			'id': 'idarrendador',
			'name': 'idarrendador[]',
			'type': 'hidden'
		}).inject(td1),
		arrendador = new Element('input', {
			'id': 'arrendador',
			'name': 'arrendador[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).addEvents({
			'change': obtenerArrendador.pass(i),
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					$$('input[id=anio]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=arrendador]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=arrendador]').length - 1) {
					$$('input[id=arrendador]')[i + 1].select();
				}
			}
		}).inject(td1),
		nombre_arrendador = new Element('input', {
			'id': 'nombre_arrendador',
			'name': 'nombre_arrendador[]',
			'type': 'text',
			'size': 30,
			'disabled': true
		}).inject(td1),
		td2 = new Element('td').inject(tr),
		idarrendatario = new Element('select', {
			'id': 'idarrendatario',
			'name': 'idarrendatario[]'
		}).addEvent('change', function(e) {
			e.stop();

			if ($$('select[id=idarrendatario]')[i].get('value').getNumericValue() > 0 && i + 1 > $$('input[id=anio]').length - 1) {
				crearFila.run(i + 1);
			}

			if (i + 1 <= $$('input[id=anio]').length - 1) {
				$$('input[id=arrendador]')[i + 1].select();
			}
		}).inject(td2),
		td3 = new Element('td').inject(tr),
		anio = new Element('input', {
			'id': 'anio',
			'name': 'anio[]',
			'type': 'text',
			'size': 3,
			'class': 'valid Focus toPosInt center'
		}).addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					$$('input[id=observaciones]')[i].select();
				}
				else if (e.key == 'left') {
					$$('input[id=arrendador]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=anio]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=anio]').length - 1) {
					$$('input[id=anio]')[i + 1].select();
				}
			}
		}).inject(td3),
		td4 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		mes = new Element('select', {
			'id': 'mes',
			'name': 'mes[]'
		}).addEvent('change', function(e) {
			e.stop();

			if ($$('select[id=mes]')[i].get('value').getNumericValue() > 0 && i + 1 > $$('input[id=anio]').length - 1) {
				crearFila.run(i + 1);
			}

			if (i + 1 <= $$('input[id=anio]').length - 1) {
				$$('input[id=arrendador]')[i + 1].select();
			}
		}).inject(td4),
		td5 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		status = new Element('select', {
			'id': 'status',
			'name': 'status[]'
		}).addEvent('change', function(e) {
			e.stop();

			if ($$('select[id=status]')[i].get('value').getNumericValue() > 0 && i + 1 > $$('input[id=anio]').length - 1) {
				crearFila.run(i + 1);
			}

			if (i + 1 <= $$('input[id=anio]').length - 1) {
				$$('input[id=arrendador]')[i + 1].select();
			}
		}).inject(td5);
		td6 = new Element('td', {
			'align': 'center'
		}).inject(tr),
		observaciones = new Element('input', {
			'id': 'observaciones',
			'name': 'observaciones[]',
			'type': 'text',
			'size': 20,
			'class': 'valid toText cleanText toUpper'
		}).addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					if (i + 1 > $$('input[id=observaciones]').length - 1) {
						crearFila.run(i + 1);
					}

					$$('input[id=arrendador]')[i + 1].select();
				}
				else if (e.key == 'left') {
					$$('input[id=anio]')[i].select();
				}
				else if (e.key == 'up' && i > 0) {
					$$('input[id=observaciones]')[i - 1].select();
				}
				else if (e.key == 'down' && i < $$('input[id=observaciones]').length - 1) {
					$$('input[id=observaciones]')[i + 1].select();
				}
			}
		}).inject(td6);

	validator.addElementEvents(arrendador);
	validator.addElementEvents(anio);
	validator.addElementEvents(observaciones);

	styles.addElementEvents(arrendador);
	styles.addElementEvents(anio);
	styles.addElementEvents(observaciones);

	updSelect(idarrendatario, []);

	updSelect(mes, [
		{'value': 1, 'text': 'ENERO'},
		{'value': 2, 'text': 'FEBRERO'},
		{'value': 3, 'text': 'MARZO'},
		{'value': 4, 'text': 'ABRIL'},
		{'value': 5, 'text': 'MAYO'},
		{'value': 6, 'text': 'JUNIO'},
		{'value': 7, 'text': 'JULIO'},
		{'value': 8, 'text': 'AGOSTO'},
		{'value': 9, 'text': 'SEPTIEMBRE'},
		{'value': 10, 'text': 'OCTUBRE'},
		{'value': 11, 'text': 'NOVIEMBRE'},
		{'value': 12, 'text': 'DICIEMBRE'}
	]);

	updSelect(status, [
		{'value': 3, 'text': 'PAGADO'},
		{'value': -2, 'text': 'DIAS DE GRACIA'}
	]);
}

var obtenerArrendador = function() {
	var i = arguments[0],
		idarrendador = $$('input[id=idarrendador]')[i],
		arrendador = $$('input[id=arrendador]')[i],
		nombre_arrendador = $$('input[id=nombre_arrendador]')[i],
		idarrendatario = $$('select[id=idarrendatario]')[i],
		anio = $$('input[id=anio]')[i],
		mes = $$('select[id=mes]')[i],
		status = $$('select[id=status]')[i];

	if (arrendador.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'RentasEstados.php',
			'data': 'accion=obtenerArrendador&arrendador=' + arrendador.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);

				if (data.arrendador > 0) {
					idarrendador.set('value', data.idarrendador);
					arrendador.set('value', data.arrendador);
					nombre_arrendador.set('value', data.nombre_arrendador);
					anio.set('value', data.anio);
					mes.selectedIndex = data.mes - 1;

					updSelect(idarrendatario, data.arrendatarios);

					anio.select();
				}
				else {
					alert('El arrendador no se encuentra en el catálogo');

					arrendador.set('value', arrendador.retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		idarrendador.set('value', '');
		arrendador.set('value', '');
		nombre_arrendador.set('value', '');
		anio.set('value', '');
		mes.selectedIndex = 0;

		updSelect(idarrendatario, []);
	}
}

var Registrar = function() {
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
		'url': 'RentasEstados.php',
		'data': 'accion=registrar&' + queryString.join('&'),
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Registrando estados...', '<img src="iconos/info.png" /> Informaci&oacute;n', 120, 80, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();

			$('filas').empty();

			crearFila.run(0);

			$('arrendador').select();
		}
	}).send();
}

var Cancelar = function() {
	$('filas').empty();

	crearFila.run(0);

	$('arrendador').select();
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
		Select.length = 1;
		$each(Select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});

		Select.selectedIndex = 0;
	}
}
