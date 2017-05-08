// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		'change': cambiaCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('folio').select();
				e.stop();
			}
		}
	});
	
	$('folio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha2').select();
				e.stop();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('num_cia').select();
				e.stop();
			}
		}
	});
	
	$('buscar').addEvent('click', Buscar);
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
	}
	else {
		new Request({
			'url': 'ban_con_mem.php',
			'data': {
				'accion': 'cia',
				'num_cia': $('num_cia').get('value')
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', '');
					$('nombre_cia').set('value', '');
					$('num_cia').focus();
				}
			}
		}).send();
	}
}

var Buscar = function() {
	new Request({
		'url': 'ban_con_mem.php',
		'data': 'accion=buscar&' + $('Datos').toQueryString(),
		'onRequest': function() {
			$('result').set('html', '');
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('result'));
			
			new Element('span', {
				'text': ' Buscando...'
			}).inject($('result'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('result').set('html', result);
				
				new Formulario('Result');
				
				$$('tr[id=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$('checkall').addEvent('change', CheckAll);
				
				$$('img[id=memo]').each(function(el, i) {
					el.addEvents({
						'click': Memo.pass(el.get('alt')),
						'mouseover': function() {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							this.setStyle('cursor', 'default');
						}
					});
				});
				
				$('cancelar').addEvent('click', Cancelar);
				$('aclarar').addEvent('click', Aclarar);
			}
			else {
				alert('No hay resultados');
				$('result').set('html', '');
				$('num_cia').select();
			}
		}
	}).send();
}

var CheckAll = function() {
	$$('input[id=id]').set('checked', $('checkall').get('checked'));
}

var Memo = function() {
	var url = 'ban_con_mem.php',
		arg = '?accion=memo&id=' + arguments[0],
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + arg, 'memo', opt);
	win.focus();
}

var Cancelar = function() {
	$('result').set('html', '');
	$('num_cia').select();
}

var Aclarar = function() {
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm('¿Desea aclarar todos los memos seleccionados?')) {
		new Request({
			'url': 'ban_con_mem.php',
			'data': 'accion=aclarar&' + $('result').toQueryString(),
			'onRequest': function() {
				$('result').set('html', '');
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('result'));
				
				new Element('span', {
					'text': ' Aclarando...'
				}).inject($('result'));
			},
			'onSuccess': function() {
				$('result').set('html', '');
				
				alert('Todos los registros seleccionados han sido aclarados');
				
				$('num_cia').select();
			}
		}).send();
	}
}
