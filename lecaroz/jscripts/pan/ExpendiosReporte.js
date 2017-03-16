// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('cias').addEvents({
		'change': ObtenerExpendios,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha2').select();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('cias').select();
			}
		}
	});
	
	$('agente_ventas').addEvent('change', ObtenerExpendios);
	
	$('reporte').addEvent('click', Reporte);
	
	$('cias').select();
});

var Reporte = function() {
	if ($('fecha1').get('value') == '' && $('fecha2').get('value') == '') {
		alert('Debe especificar el periodo de consulta');
		
		$('fecha1').select();
	}
	else {
		var url = 'ExpendiosReporte.php',
		param = '?' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
		
		win = window.open(url + param, $('accion').get('value'), opt);
		
		win.focus();
	}
}

var ObtenerExpendios = function() {
	if ($('cias').get('value') != '' || $('agente_ventas').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'ExpendiosReporte.php',
			'data': 'accion=obtenerExpendios&cias=' + $('cias').get('value') + '&agente_ventas=' + $('agente_ventas').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					updSelect($('expendio'), data);
				}
				else {
					updSelect($('expendio'), []);
				}
			}
		}).send();
	}
	else {
		updSelect($('expendio'), []);
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
		
		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;
		
		Select.selectedIndex = -1;
	}
}
