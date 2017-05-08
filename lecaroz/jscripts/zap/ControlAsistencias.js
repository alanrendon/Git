// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		
		updSelect($('idemp'), []);
		
		$('num_cia').focus();
	}
	else {
		new Request({
			'url': 'ControlAsistencias.php',
			'data': {
				'accion': 'getCia',
				'num_cia': $('num_cia').get('value')
			},
			'onSuccess': function(result) {
				if (result == '') {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', '');
					$('nombre_cia').set('value', '');
					
					updSelect($('idemp'), []);
					
					$('num_cia').focus();
				}
				else {
					var data = JSON.decode(result);
					$('nombre_cia').set('value', data.nombre_cia);
					
					updSelect($('idemp'), data.empleados);
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
			el.set({
				'value': Options[i].value,
				'text': Options[i].text
			});
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

var Inicio = function() {
	new Request({
		'url': 'ControlAsistencias.php',
		'data': 'accion=inicio',
		'onRquest': function() {
			
		},
		'onSuccess': function(result) {
			$('captura').set('html', result);
			
			new Formulario('Datos');
			
			$('num_cia').addEvents({
				'change': cambiaCia,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('fecha1').select();
						e.stop();
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
			
			$('consultar').addEvent('click', Consultar);
			
			$('num_cia').focus();
		}
	}).send();
}

var Consultar = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if ($('fecha1').get('value') == '' && $('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		$('fecha1').focus();
	}
	else {
		new Request({
			'url': 'ControlAsistencias.php',
			'data': 'accion=consultar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				
			},
			'onSuccess': function(result) {
				if (result == '') {
					Inicio();
					
					alert('No hay resultados');
				}
				else {
					$('captura').set('html', result);
					
					new Formulario('Datos');
					
					$$('select[id^=status]').each(function(el) {
						el.addEvent('change', Actualizar.pass(el));
					});
					
					$('regresar').addEvent('click', function() {
						Inicio();
					});
				}
			}
		}).send();
	}
}

var Actualizar = function() {
	var arg = JSON.decode("{" + arguments[0].get('alt') + ",'status':'" + arguments[0].get('value') + "'}"),
		accion = {'accion': 'actualizar'},
		data = $merge(accion, arg);
	
	new Request({
		'url': 'ControlAsistencias.php',
		'data': data,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			var A = 0,
				F = 0,
				I = 0,
				D = 0,
				V = 0,
				T = 0;
			
			$$('select[id=status' + data.row + ']').each(function(el) {
				switch (el.get('value')) {
					case '1':
						el.setStyle('color', '#00C');
						A++;
						T++;
					break;
					
					case '2':
						el.setStyle('color', '#C00');
						F++;
						//T--;
					break;
					
					case '3':
						el.setStyle('color', '#0C0');
						I++;
						T++;
					break;
					
					case '4':
						el.setStyle('color', '#F90');
						D++;
						T++;
					break;
					
					case '5':
						el.setStyle('color', '#60C');
						V++;
						T++;
					break;
				}
			});
			
			$$('input[id=A]')[data.row].set('value', A > 0 ? A : '');
			$$('input[id=F]')[data.row].set('value', F > 0 ? F : '');
			$$('input[id=I]')[data.row].set('value', I > 0 ? I : '');
			$$('input[id=D]')[data.row].set('value', D > 0 ? D : '');
			$$('input[id=V]')[data.row].set('value', V > 0 ? V : '');
			$$('input[id=T]')[data.row].set('value', T > 0 ? T : '');
			
			Totales();
		}
	}).send();
}

var Totales = function() {
	var A = 0,
		F = 0,
		I = 0,
		D = 0,
		V = 0,
		T = 0;
	
	$$('input[id=A]').each(function(el) {
		A += el.get('value').getVal();
	});
	
	$$('input[id=F]').each(function(el) {
		F += el.get('value').getVal();
	});
	
	$$('input[id=I]').each(function(el) {
		I += el.get('value').getVal();
	});
	
	$$('input[id=D]').each(function(el) {
		D += el.get('value').getVal();
	});
	
	$$('input[id=V]').each(function(el) {
		V += el.get('value').getVal();
	});
	
	$$('input[id=T]').each(function(el) {
		T += el.get('value').getVal();
	});
	
	$('TA').set('value', A > 0 ? A : '');
	$('TF').set('value', F > 0 ? F : '');
	$('TI').set('value', I > 0 ? I : '');
	$('TD').set('value', D > 0 ? D : '');
	$('TV').set('value', V > 0 ? V : '');
	$('TT').set('value', T > 0 ? T : '');
}
