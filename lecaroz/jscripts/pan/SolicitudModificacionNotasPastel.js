// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Registro');
	
	nuevaFila(0);
	
	$('registrar').addEvent('click', function() {
		if (confirm('¿Son correctos todos los datos?'))
			f.form.submit();
	});
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	var i = arguments[0];
	var num_cia = f.form.num_cia.length == undefined ? f.form.num_cia : f.form.num_cia[i];
	var nombre = f.form.nombre.length == undefined ? f.form.nombre : f.form.nombre[i];
	
	if (num_cia.get('value') == 0) {
		num_cia.set('value', '');
		nombre.set('value', '');
	}
	else {
		new Request({
			url: 'SolicitudModificacionNotasPastel.php',
			method: 'post',
			data: {
				accion: 'cia',
				num_cia: num_cia.get('value')
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('La compañía no se encuentra en el catálogo');
					num_cia.set('value', Formulario.tmp);
					num_cia.focus();
				}
				else
					nombre.set('value', data);
			}
		}).send();
	}
}

var validarFac = function() {
	var i = arguments[0];
	var num_cia = f.form.num_cia.length == undefined ? f.form.num_cia : f.form.num_cia[i];
	var num_remi = f.form.num_remi.length == undefined ? f.form.num_remi : f.form.num_remi[i];
	var letra_folio = f.form.letra_folio.length == undefined ? f.form.letra_folio : f.form.letra_folio[i];
	
	if (num_cia.get('value').getVal() > 0 && num_remi.get('value').getVal() > 0) {
		new Request({
			url: 'SolicitudModificacionNotasPastel.php',
			method: 'post',
			data: {
				accion: 'validar',
				num_cia:  num_cia.get('value'),
				num_remi: num_remi.get('value'),
				letra_folio: letra_folio.get('value')
			},
			onSuccess: function(result) {
				if (result == -1) {
					alert('No existe la nota');
					num_remi.set('value', '');
					letra_folio.set('value', '');
					letra_folio.focus();
				}
			}
		}).send();
	}
}

function movCursor(key, enter, lt, rt, up, dn) {
	if (key == 'enter' && enter != null)
		enter.focus();
	else if (key == 'left' && lt != null)
		lt.focus();
	else if (key == 'right' && rt != null)
		rt.focus();
	else if (key == 'up' && up != null)
		up.focus();
	else if (key == 'down' && dn != null)
		dn.focus();
}

var nuevaFila = function() {
	var i = arguments[0];
	
	
	var num_cia = $('num_cia_copy').clone();
	num_cia.cloneEvents($('num_cia_copy'));
	num_cia.set({
		id: 'num_cia',
		name: 'num_cia[]'
	});
	num_cia.addEvent('change', cambiaCia.pass(i));
	num_cia.addEvent('change', validarFac.pass(i));
	
	var nombre = $('nombre_copy').clone();
	nombre.set({
		id: 'nombre',
		name: 'nombre[]',
		disabled: true
	});
	
	var letra_folio = $('letra_folio_copy').clone();
	letra_folio.cloneEvents($('letra_folio_copy'));
	letra_folio.set({
		id: 'letra_folio',
		name: 'letra_folio[]'
	});
	letra_folio.addEvent('change', validarFac.pass(i));
	
	var num_remi = $('num_remi_copy').clone();
	num_remi.cloneEvents($('num_remi_copy'));
	num_remi.set({
		id: 'num_remi',
		name: 'num_remi[]'
	});
	num_remi.addEvent('change', validarFac.pass(i));
	
	var descripcion = $('descripcion_copy').clone();
	descripcion.cloneEvents($('descripcion_copy'));
	descripcion.set({
		id: 'descripcion',
		name: 'descripcion[]'
	});
	descripcion.addEvent('keydown', function(e) {
		if (e.key == 'enter' && !$defined(f.form.num_cia[i + 1]))
			nuevaFila(i + 1);
	}.bind(i));
	
	var kilos_mas = new Element('input', {
		type: 'radio',
		id: 'kilos_' + i,
		name: 'kilos_' + i,
		value: 1
	}).addEvent('click', function() {
		$$('input[id=mod_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
	}.bind(i));
	
	var kilos_menos = new Element('input', {
		type: 'radio',
		id: 'kilos_' + i,
		name: 'kilos_' + i,
		value: -1
	}).addEvent('click', function() {
		$$('input[id=mod_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
	}.bind(i));
	
	var precio = new Element('input', {
		type: 'checkbox',
		id: 'precio_' + i,
		name: 'precio_' + i,
		value: 1
	}).addEvent('click', function() {
		$$('input[id=mod_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
	}.bind(i));
	
	var base = new Element('input', {
		type: 'checkbox',
		id: 'base_' + i,
		name: 'base_' + i,
		value: 1
	}).addEvent('click', function() {
		$$('input[id=mod_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
	}.bind(i));
	
	var pan = new Element('input', {
		type: 'radio',
		id: 'mod_' + i,
		name: 'mod_' + i,
		value: 2
	}).addEvent('click', function() {
		$$('input[id=kilos_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
		$('precio_' + i).set('checked', false);
		$('base_' + i).set('checked', false);
	}.bind(i));
	
	var fecha = new Element('input', {
		type: 'radio',
		id: 'mod_' + i,
		name: 'mod_' + i,
		value: 3
	}).addEvent('click', function() {
		$$('input[id=kilos_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
		$('precio_' + i).set('checked', false);
		$('base_' + i).set('checked', false);
	}.bind(i));
	
	var entrega = new Element('input', {
		type: 'radio',
		id: 'mod_' + i,
		name: 'mod_' + i,
		value: 4
	}).addEvent('click', function() {
		$$('input[id=kilos_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
		$('precio_' + i).set('checked', false);
		$('base_' + i).set('checked', false);
	}.bind(i));
	
	var cancelar = new Element('input', {
		type: 'radio',
		id: 'mod_' + i,
		name: 'mod_' + i,
		value: 5
	}).addEvent('click', function() {
		$$('input[id=kilos_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
		$('precio_' + i).set('checked', false);
		$('base_' + i).set('checked', false);
	}.bind(i));
	
	var extraviada = new Element('input', {
		type: 'radio',
		id: 'mod_' + i,
		name: 'mod_' + i,
		value: 6
	}).addEvent('click', function() {
		$$('input[id=kilos_' + i + ']').each(function(el) {
			el.set('checked', false);
		});
		$('precio_' + i).set('checked', false);
		$('base_' + i).set('checked', false);
	}.bind(i));
	
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	
	var td = new Element('td', {
		align: 'center'
	});
	num_cia.inject(td);
	nombre.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	letra_folio.inject(td);
	num_remi.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	descripcion.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	kilos_mas.inject(td);
	new Element('span', {
		styles: {
			'color': '#00C',
			'font-weight': 'bold'
		},
		text: 'Más'
	}).inject(td);
	kilos_menos.inject(td);
	new Element('span', {
		styles: {
			'color': '#C00',
			'font-weight': 'bold'
		},
		text: 'Menos'
	}).inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	precio.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	base.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	pan.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	fecha.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	entrega.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	cancelar.inject(td);
	td.inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	extraviada.inject(td);
	td.inject(tr);
	
	tr.inject($('Captura'));
	
	num_cia.addEvent('keydown', function(e) {
		if (f.form.num_cia.length == undefined)
			movCursor(e.key, f.form.letra_folio, null, f.form.letra_folio, null, null);
		else
			movCursor(e.key, f.form.letra_folio[i], null, f.form.letra_folio[i], f.form.num_cia[i > 0 ? i - 1 : f.form.num_cia.length - 1], f.form.num_cia[i < f.form.num_cia.length - 1 ? i + 1 : 0]);
	}.bind(i));
	
	letra_folio.addEvent('keydown', function(e) {
		if (f.form.letra_folio.length == undefined)
			movCursor(e.key, f.form.num_remi, f.form.num_cia, f.form.num_remi, null, null);
		else
			movCursor(e.key, f.form.num_remi[i], f.form.num_cia[i], f.form.num_remi[i], f.form.letra_folio[i > 0 ? i - 1 : f.form.letra_folio.length - 1], f.form.letra_folio[i < f.form.letra_folio.length - 1 ? i + 1 : 0]);
	}.bind(i));
	
	num_remi.addEvent('keydown', function(e) {
		if (f.form.num_remi.length == undefined)
			movCursor(e.key, f.form.descripcion, f.form.letra_folio, f.form.descripcion, null, null);
		else
			movCursor(e.key, f.form.descripcion[i], f.form.letra_folio[i], f.form.descripcion[i], f.form.num_remi[i > 0 ? i - 1 : f.form.num_remi.length - 1], f.form.num_remi[i < f.form.num_remi.length - 1 ? i + 1 : 0]);
	}.bind(i));
	
	descripcion.addEvent('keydown', function(e) {
		if (f.form.descripcion.length == undefined)
			movCursor(e.key, $defined(f.form.num_cia[i + 1]) ? f.form.num_cia[i + 1] : null, f.form.num_remi, null, null, null);
		else
			movCursor(e.key, $defined(f.form.num_cia[i + 1]) ? f.form.num_cia[i + 1] : null, f.form.num_remi[i], null, f.form.descripcion[i > 0 ? i - 1 : f.form.descripcion.length - 1], f.form.descripcion[i < f.form.descripcion.length - 1 ? i + 1 : 0]);
	}.bind(i));
}