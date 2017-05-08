// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Altas');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha_caducidad_general').select();
			}
		}
	});
	
	$('fecha_caducidad_general').addEvents({
		change: function() {
			$$('input[id=fecha_caducidad]').set('value', this.get('value'));
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_ext').select();
			}
		}
	});
	
	$('num_ext').addEvents({
		change: function() {
			if ($('Captura'))
				$('Captura').destroy();
			
			var num = this.get('value').getVal();
			if (num > 0) {
				new Element('table', {
					'id': 'Captura',
					'class': 'tabla_captura'
				}).inject($('Datos'), 'after');
				
				var tr = new Element('tr');
				
				new Element('th', {
					html: 'No.'
				}).inject(tr);
				
				new Element('th', {
					html: 'Fecha Caducidad'
				}).inject(tr);
				
				tr.inject($('Captura'));
				
				for (var i = 0; i < num; i++)
					nuevaFila(i);
			}
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				if ($defined($('fecha_caducidad'))) {
					e.stop();
					$$('input[id=fecha_caducidad]')[0].select();
				}
				else {
					e.stop();
					$('num_cia').focus();
				}
			}
		}
	});
	
	$('alta').addEvent('click', function() {
		if ($('num_cia').get('value').getVal() == 0) {
			alert('Debe especificar la compañía');
			$('num_cia').focus();
		}
		else if ($('num_ext').get('value').getVal() == 0) {
			alert('Debe especificar el número de extintores');
			$('num_ext').focus();
		}
		else if (confirm('¿Son correctos todos los datos?'))
			f.form.submit();
	});
	
	$('num_cia').select();
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
			url: 'AltaExtintores.php',
			method: 'post',
			data: {
				accion: 'cia',
				num_cia: num_cia.get('value')
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('La compañía no se encuentra en el catálogo');
					num_cia.set('value', Formulario.tmp);
					num_cia.select();
				}
				else
					nombre.set('value', data);
			}
		}).send();
	}
}

function movCursor(key, enter, lt, rt, up, dn) {
	if (key == 'enter' && enter != null)
		enter.select();
	else if (key == 'left' && lt != null)
		lt.select();
	else if (key == 'right' && rt != null)
		rt.select();
	else if (key == 'up' && up != null)
		up.select();
	else if (key == 'down' && dn != null)
		dn.select();
}

var nuevaFila = function() {
	var i = arguments[0];
	
	var fecha_caducidad = $('fecha_caducidad_copy').clone();
	fecha_caducidad.cloneEvents($('fecha_caducidad_copy'));
	fecha_caducidad.set({
		id: 'fecha_caducidad',
		name: 'fecha_caducidad[]',
		value: $('fecha_caducidad_general').get('value')
	});
	
	var tr = new Element('tr', {
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	
	new Element('td', {
		'html': i + 1,
		'align': 'center'
	}).inject(tr);
	
	var td = new Element('td', {
		align: 'center'
	});
	fecha_caducidad.inject(td);
	td.inject(tr);
	
	tr.inject($('Captura'));
	
	fecha_caducidad.addEvent('keydown', function(e) {
		if (f.form.fecha_caducidad.length == undefined)
			movCursor(e.key, $defined(f.form.fecha_caducidad[i + 1]) ? f.form.fecha_caducidad[i + 1] : null, null, null, null, null);
		else
			movCursor(e.key, $defined(f.form.fecha_caducidad[i + 1]) ? f.form.fecha_caducidad[i + 1] : null, null, null, f.form.fecha_caducidad[i > 0 ? i - 1 : f.form.fecha_caducidad.length - 1], f.form.fecha_caducidad[i < f.form.fecha_caducidad.length - 1 ? i + 1 : 0]);
	}.bind(i));
}