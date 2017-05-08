// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'ConciliarComisiones.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').set('html', '');
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').set('html', result);
			
			new Formulario('Datos');
			
			$('cias').addEvents({
				'change': Codigos,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('fecha1').select();
						e.stop();
					}
				}
			});
			
			$('admin').addEvents({
				'change': Codigos
			});
			
			$('banco').addEvents({
				'change': Codigos
			});
			
			$('fecha1').addEvents({
				'change': Codigos,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('fecha2').select();
						e.stop();
					}
				}
			});
			
			$('fecha2').addEvents({
				'change': Codigos,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('cias').select();
						e.stop();
					}
				}
			});
			
			$('buscar').addEvent('click', Buscar);
			
			$('cias').select();
		}
	}).send();
}

var Codigos = function() {
	if ($('banco').get('value').getVal() > 0) {
		new Request({
			'url': 'ConciliarComisiones.php',
			'data': 'accion=codigos&' + $('Datos').toQueryString(),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					updSelect($('codigos'), data.codigos);
					$('codigos').set('size', data.codigos.length > 5 ? data.codigos.length : 5);
				}
				else {
					updSelect($('codigos'), []);
					$('codigos').set('size', 5);
				}
			}
		}).send();
	}
}

var updSelect = function() {
	var Select = arguments[0],
		Options = arguments[1];
	
	if (Options.length > 0) {
		Select.length = Options.length;
		
		$each(Select.options, function(el, i) {
			el.set(Options[i]);
		});
	}
	else {
		Select.length = 1;
		$each(Select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});
	}
}

var Buscar = function() {
	if ($('banco').get('value'). getVal() == 0) {
		alert('Debe seleccionar el banco');
		$('banco').focus();
	}
	else {
		new Request({
			'url': 'ConciliarComisiones.php',
			'data': 'accion=buscar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').set('html', '');
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Buscando...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('captura').set('html', result);
					
					new Formulario('Datos');
					
					$$('tr[id=row]').addEvents({
						'mouseover': function() {
							this.addClass('highlight');
						},
						'mouseout': function() {
							this.removeClass('highlight');
						}
					});
					
					$('checkall').addEvent('click', function() {
						$$('input[id=id]').set('checked', this.get('checked'));
					});
					
					$('cancelar').addEvent('click', Inicio);
					$('conciliar').addEvent('click', Conciliar);
				}
				else {
					alert('No hay resultados');
					
					Inicio();
				}
			}
		}).send();
	}
}

var Conciliar = function() {
	var msj = $('bonificaciones').get('checked') ? '¿Desea conciliar y generar bonificaciones de los registros seleccionados?' : '¿Desea conciliar los registros seleccionados?';
	
	if ($$('input[id=id]:checked').length == 0) {
		alert('Debe seleccionar al menos un registro');
	}
	else if (confirm(msj)) {
		new Request({
			'url': 'ConciliarComisiones.php',
			'data': 'accion=conciliar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').set('html', '');
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Conciliando comisiones...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result),
					url = 'ConciliarComisiones.php',
					arg = '?accion=reporte&banco=' + data.banco + '&' + data.ids.map(function(el) { return 'id[]=' + el; }).join('&'),
					name = 'reporte',
					opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
					win;
				
				Inicio();
				
				win = window.open(url + arg, name, opt);
				win.focus();
			}
		}).send();
	}
}