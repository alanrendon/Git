// JavaScript Document

var f;
var canModify = true;
var fecha_tmp = null;
var id_tmp = null;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				this.blur();
			}
		}
	});
	
	$('buscar').addEvent('click', function() {
		$('Resultado').empty();
		
		new Request({
			url: 'ConsultaExtintores.php',
			method: 'post',
			data: {
				accion: 'search',
				c: $('num_cia').get('value')
			},
			onRequest: function() {
				new Element('img', {
					src: 'imagenes/ajax-loader.gif'
				}).inject($('Resultado'));
				
				new Element('p', {
					text: 'Buscando...'
				}).inject($('Resultado'));
			},
			onSuccess: function(result) {
				if (result == '') {
					alert('No hay resultados');
					
					$('Resultado').empty();
					
					$('num_cia').select();
					
					return false;
				}
				
				$('Resultado').set('html', result);
				
				asignarFunciones();
			}
		}).send();
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
			url: 'ConsultaExtintores.php',
			method: 'post',
			data: {
				accion: 'cia',
				c: $('num_cia').get('value')
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

var asignarFunciones = function() {
	$$('img[id=mod]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				this.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				this.setStyle('cursor', 'default');
			},
			click: modRec.pass(el)
		});
	});
	
	$$('img[id=del]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				this.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				this.setStyle('cursor', 'default');
			},
			click: delRec.pass(el)
		});
	});
}

var modRec = function() {
	var el = arguments[0];
	
	if (!canModify) {
		alert('Otro registro ya esta siendo modificado');
		$('fecha_caducidad').select();
		return false;
	}
	
	var row = el.getParent('tr');
	var tdmod = row.getChildren('td')[1];
	var tdtool = row.getChildren('td')[2];
	fecha_tmp = tdmod.get('text');
	id_tmp = el.get('alt');
	
	tdmod.empty();
	tdtool.empty();
	
	var input = $('fecha_copy').clone();
	input.cloneEvents($('fecha_copy'));
	input.set({
		id: 'fecha_caducidad',
		name: 'fecha_caducidad',
		value: fecha_tmp
	});
	
	input.addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			this.blur();
		}
	});
	
	input.inject(tdmod);
	
	new Element('img', {
		src: 'menus/insert.gif',
		id: 'ok',
		name: 'ok',
		alt: id_tmp
	}).inject(tdtool);
	
	new Element('img', {
		src: 'imagenes/delete16x16.png',
		id: 'cancel',
		name: 'cancel',
		alt: id_tmp
	}).inject(tdtool);
	
	$('ok').addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: updateRec.pass([tdmod, tdtool])
	});
	
	$('cancel').addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: restoreRec.pass([tdmod, tdtool])
	});
	
	canModify = false;
	
	input.focus();
}

var updateRec = function() {
	fecha_tmp = $('fecha_caducidad').get('value');
	
	new Request({
		url: 'ConsultaExtintores.php',
		method: 'post',
		data: {
			accion: 'update',
			id: id_tmp,
			fecha: fecha_tmp
		}
	}).send();
	
	restoreRec.attempt(arguments);
}

var restoreRec = function() {
	var tdmod = arguments[0];
	var tdtool = arguments[1];
	
	tdmod.empty().set('html', fecha_tmp);
	tdtool.empty();
	
	var mod = new Element('img', {
		src: 'imagenes/pencil16x16.png',
		id: 'mod',
		name: 'mod',
		width: 16,
		height: 16,
		alt: id_tmp
	}).inject(tdtool).addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: modRec.pass(tdtool.getChildren('img[id=mod]'))
	});
	
	var del = new Element('img', {
		src: 'imagenes/delete16x16.png',
		id: 'del',
		name: 'del',
		width: 16,
		height: 16,
		alt: id_tmp
	}).inject(tdtool).addEvents({
		mouseover: function() {
			this.setStyle('cursor', 'pointer');
		},
		mouseout: function() {
			this.setStyle('cursor', 'default');
		},
		click: delRec.pass(tdtool.getChildren('img[id=del]'))
	});
	
	canModify = true;
}

var delRec = function() {
	var el = arguments[0];
	
	if (confirm('¿Desea eliminar el registro?')) {
		new Request({
			url: 'ConsultaExtintores.php',
			method: 'post',
			data: {
				accion: 'delete',
				id: el.get('alt')
			},
			onSuccess: function() {
				el.getParent('tr').getAllNext('tr[id=row]').each(function(row) {
					row.toggleClass('linea_off');
					row.toggleClass('linea_on');
				});
				
				el.getParent('tr').destroy();
			}
		}).send();
	}
}