// JavaScript Document

window.addEvent('domready', function() {
	new Formulario('Datos');
			
	$('cias').addEvents({
		'change': validarCias,
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
				$('cias').select();
				e.stop();
			}
		}
	});
	
	$('generar').addEvent('click', Generar);
	
	$('cias').focus();
});

var obtenerDatos = function() {
	
}

var validarCias = function() {
	if ($('fecha1').get('value') == '' && $('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		$('fecha1').focus();
	}
	else {
		new Request({
			'url': 'ConsultaAsistencias.php',
			'data': {
				'accion': 'getCia',
				'cias': $('cias').get('value')
			},
			'onSuccess': function(result) {
				if (result == '') {
					alert('La(s) compañía(s) no se encuentra(n) en el catálogo');
					$('cias').set('value', '');
					
					updSelect($('idemp'), []);
					
					$('cias').focus();
				}
				else {
					var data = JSON.decode(result);
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

var Generar = function() {
	var url = 'ConsultaAsistencias.php',
		data = '?accion=generar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + data, 'reporte', opt);
	win.focus();
}
