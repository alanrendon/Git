// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'TrabajadoresSolicitudCompanias.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur;
						this.focus();
					}
				}
			});
			
			$('cias').select();
			
			$('consultar').addEvent('click', Consultar);
		}
	}).send();
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'TrabajadoresSolicitudCompanias.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result) {
				$('captura').empty().set('html', result);
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('img[id=validar]').each(function(el) {
					var data = el.get('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'default');
						},
						'click': Validar.pass(data)
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=cancelar]').each(function(el) {
					var id = el.get('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							e.stop();
							
							this.setStyle('cursor', 'default');
						},  
						'click': BorrarMovimiento.pass(id)
					});
					
					el.removeProperty('alt');
				});
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');
				
				Inicio.run();
			}
		}
	}).send();
}

var Validar = function() {
	var data = JSON.decode(arguments[0]);
	
	if (data.tipo == 0) {
		new Request({
			'url': 'TrabajadoresSolicitudCompanias.php',
			'data': 'accion=validarAlta&id=' + data.id,
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					if (data.status == -1) {
						alert('El empleado esta en la lista negra con el folio ' + data.folio + ' y no puede ser ingresado/modificado por las siguientes razones:\n\n' + data.observaciones);
					}
					else if (data.status == -2) {
						var msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías y no podrá ser ingresado/modificado:';
						
						data.each(function(rec) {
							msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
							msg += '\nFecha de alta:\t' + rec.fecha_alta;
							msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
							msg += '\nRFC:\t\t\t' + rec.rfc;
							msg += '\nUsuario:\t\t' + rec.usuario;
						});
						
						alert(msg);
					}
					else {
						doAlta.run(data.id);
					}
				}
			}
		}).send();
	}
	else if (data.tipo == 1) {
		new Request({
			'url': 'TrabajadoresSolicitudCompanias.php',
			'data': 'accion=validarBaja&id=' + data.id,
			'onRequest': function() {
				popup = new Popup('<img src="imagenes/_loading.gif" /> Buscando trabajadores...', 'Baja de trabajador', 200, 100, null, null);
			},
			'onSuccess': function(result) {
				if (result != '') {
					popup.Close();
					
					popup = new Popup(result, 'Baja de trabajador', 700, 300, popupOpen, null);
				}
			}
		}).send();
	}
}

var popupOpen = function() {
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('baja').addEvent('click', doBaja);
}

var doAlta = function() {
	var id = arguments[0];
	
	if (confirm('¿Desea realizar la alta de este trabajador?')) {
		new Request({
			'url': 'TrabajadoresSolicitudCompanias.php',
			'data': 'accion=doAlta&id=' + id,
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Consultar.run(param);
				
				var data = JSON.decode(result),
					msg = 'Datos de alta:\n\nCompañía:\t' + data.cia + '\nTrabajador:\t' + data.trabajador;
				
				alert(msg);
			}
		}).send();
	}
}

var doBaja = function() {
	if ($$('input[name=id]:checked').length == 0) {
		alert('Debe seleccionar un trabajador de la lista');
	}
	else if (confirm('¿Desea dar de baja el trabajador seleccionado?')) {
		new Request({
			'url': 'TrabajadoresSolicitudCompanias.php',
			'data': 'accion=doBaja&' + $('DatosBaja').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Consultar.run(param);
				
				popup.Close();
			}
		}).send();
	}
}

var BorrarMovimiento = function() {
	if (confirm('¿Desea borrar el movimiento?')) {
		new Request({
			'url': 'TrabajadoresSolicitudCompanias.php',
			'data': 'accion=borrarMovimiento&id=' + arguments[0],
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				Consultar.run(param);
			}
		}).send();
	}
}
