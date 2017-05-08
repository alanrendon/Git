// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function() {
	new Request({
		'url': 'CompaniasCondependenciaTraspasoSaldo.php',
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
						
						this.blur();
						this.focus();
					}
				}
			});
			
			$('siguiente').addEvent('click', Consultar);
			
			$('cias').focus();
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
		'url': 'CompaniasCondependenciaTraspasoSaldo.php',
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
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('input[id=checkblock]').each(function(el) {
					el.addEvent('change', CheckBlock.pass(el));
				});
				
				$('checkall').addEvent('change', CheckAll.pass($('checkall')));
				
				$('regresar').addEvent('click', Inicio);
				
				$('traspasar').addEvent('click', SeleccionarTipo);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var CheckBlock = function() {
	var num_cia = arguments[0].get('value'),
		checked = arguments[0].get('checked');
	
	$$('input[id=data][num_cia=' + num_cia + ']').filter(function(el) {
		return !el.get('disabled');
	}).set('checked', checked);
	
	CalcularTotal.run(num_cia);
}

var CalcularTotal = function() {
	var num_cia = arguments[0],
		total = 0;
	
	$$('input[id=data][num_cia=' + num_cia + ']:checked').filter(function(el) {
		return !el.get('disabled');
	}).each(function(el) {
		var data = JSON.decode(el.get('value'));
		
		total += data.saldo_tra;
	});
	
	$('total' + num_cia).set('html', total.numberFormat(2, '.', ','));
}

var CheckAll = function() {
	var checked = arguments[0].get('checked');
	
	$$('input[id=checkblock]').set('checked', checked);
	$$('input[id=checkblock]').fireEvent('change');
}

var SeleccionarTipo = function() {
	if ($$('input[id=data]:checked').filter(function(el) {
		return !el.get('disabled');
	}).length > 0) {
		new Request({
			'url': 'CompaniasCondependenciaTraspasoSaldo.php',
			'data': 'accion=seleccionarTipo',
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				popup = new Popup(result, '<img src="iconos/info.png" /> Seleccionar tipo de traspaso', 300, 200, popupTipo, null);
			}
		}).send();
	}
	else {
		alert('Debe seleccionar al menos un registro');
	}
}

var popupTipo = function() {
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aceptar').addEvent('click', function() {
		TraspasarSaldo.run();
	});
}

var TraspasarSaldo = function() {
	new Request({
		'url': 'CompaniasCondependenciaTraspasoSaldo.php',
		'data': 'accion=traspasar&tipo=' + $$('input[name=tipo]:checked')[0].get('value') + '&' + $('Datos').toQueryString(),
		'onRequest': function() {
			popup.Close();
			
			popup = new Popup('<img src="imagenes/_loading.gif" /> Realizando traspasos...', '<img src="iconos/envelope.png" /> Procesando...', 250, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
			
			Inicio.run();
			
			var url = 'CompaniasCondependenciaTraspasoSaldo.php',
				param = '?accion=listado&ts=' + result,
				opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
			
			var win = window.open(url + param, '', opt);
			
			win.focus();
		}
	}).send();
}
