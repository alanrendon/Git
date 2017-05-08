window.addEvent('domready', function() {
	inicio.run();
});

var inicio = function() {
	new Request({
		'url': 'PagosImdisco.php',
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
						
						$('fecha1').focus();
					}
				}
			});
			
			$('fecha1').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha2').focus();
					}
				}
			});
			
			$('fecha2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha_pago1').focus();
					}
				}
			});
			
			$('fecha_pago1').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('fecha_pago2').focus();
					}
				}
			});
			
			$('fecha_pago2').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('folios').focus();
					}
				}
			});
			
			$('folios').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', consultar);
			
			$('cias').focus();
		}
	}).send();
}

var consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'PagosImdisco.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$$('tr[class^=linea_]').each(function(row, i) {
					row.addEvents({
						'mouseover': function(e) {
							e.stop();
							
							row.addClass('highlight');
						},
						'mouseout': function(e) {
							e.stop();
							
							row.removeClass('highlight');
						}
					});
				});
				
				$('checkall').addEvent('change', function() {
					$$('input[id=id]').set('checked', $('checkall').get('checked'));
				});
				
				$$('img[id=pagar]').each(function(img) {
					var id = img.get('alt');
					
					img.addEvents({
						mouseover: function() {
							this.setStyle('cursor', 'pointer');
						},
						mouseout: function() {
							this.setStyle('cursor', 'default');
						},
						click: popup_fecha.pass(id)
					});
					
					img.removeProperty('alt');
				});
				
				$$('img[id=baja]').each(function(img) {
					var id = img.get('alt');
					
					img.addEvents({
						mouseover: function() {
							this.setStyle('cursor', 'pointer');
						},
						mouseout: function() {
							this.setStyle('cursor', 'default');
						},
						click: function() {
							if (confirm('¿Desea dar de baja el registro seleccionado?')) {
								baja.run(id);
							}
						}
					});
					
					img.removeProperty('alt');
				});
				
				$('baja_seleccion').addEvent('click', function() {
					if ($$('input[id=id]:checked').length == 0) {
						alert('Debe seleccionar al menos un registro');
					} else if (confirm('Desea dar de baja los registros seleccionados')) {
						baja.run(null)
					}
				});
				
				$('pagar_seleccion').addEvent('click', function() {
					if ($$('input[id=id]:checked').length == 0) {
						alert('Debe seleccionar al menos un registro');
					} else {
						popup_fecha.run(null)
					}
				});
				
				$('regresar').addEvent('click', inicio);
			}
			else {
				alert('No hay resultados');
				
				inicio.run();
			}
		}
	}).send();
}

var popup_fecha = function(id) {
	new Request({
		url: 'PagosImdisco.php',
		data: 'accion=fecha_pago' + (!!id ? '&id=' + id : ''),
		onRequest: function() {
		},
		onSuccess: function(content) {
			popup = new Popup(content, '<img src="/lecaroz/iconos/calendar.png" width="16" height="16" /> Fecha de pago', 300, 200, fecha_open, null);
		}
	}).send();
}

var fecha_open = function() {
	new FormValidator($('Pagar'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Pagar'));
	
	$('fecha_pago').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
				this.focus();
			}
		}
	}).select();
	
	$('poner_fecha_pago').addEvent('click', function() {
		if ($('fecha_pago').get('value') == '') {
			alert('Debe especificar la fecha de pago');
		} else {
			pagar($('fecha_pago').get('value'), $('idventa').get('value').getNumericValue() > 0 ? [$('idventa').get('value')] : $$('input[id=id]:checked').get('value'));
		}
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
}

var pagar = function(fecha, ids) {
	new Request({
		url: 'PagosImdisco.php',
		data: 'accion=pagar&fecha=' + fecha + '&' + ids.map(function(value) { return 'id[]=' + value }).join('&'),
		onRequest: function() {
			popup.Close();
			
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Actualizando...'
			}).inject($('captura'));
		},
		onSuccess: function(result) {
			consultar.run(param);
		}
	}).send();
}

var baja = function(id) {
	var ids = !!id ? [id] : $$('input[id=id]:checked').get('value')
	
	new Request({
		url: 'PagosImdisco.php',
		data: 'accion=baja&' + ids.map(function(value) { return 'id[]=' + value }).join('&'),
		onRequest: function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Actualizando...'
			}).inject($('captura'));
		},
		onSuccess: function(result) {
			consultar.run(param);
		}
	}).send();
}
