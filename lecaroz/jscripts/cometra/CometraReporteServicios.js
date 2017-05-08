window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('Datos'));

	$('anio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('omitir_cias').focus();
			}
		}
	});

	$('omitir_cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('costo_servicio').focus();
			}
		}
	});

	$('costo_servicio').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('costo_millar').focus();
			}
		}
	});

	$('costo_millar').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('costo_llave').focus();
			}
		}
	});

	$('costo_llave').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('costo_servicio_fijo').focus();
			}
		}
	});

	$('costo_servicio_fijo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('cias_servicio_fijo').focus();
			}
		}
	});

	$('cias_servicio_fijo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				$('anio').focus();
			}
		}
	});

	$('anio').select();

	$('guardar').addEvent('click', Guardar);

	$('exportar').addEvent('click', Exportar);

	$('reporte').addEvent('click', Reporte);
});

var Reporte = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el años de consulta');
		$('anio').select();
	}
	else {
		var url = 'CometraReporteServicios.php',
			arg = '?accion=reporte&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;

		win = window.open(url + arg, 'reporte', opt);
		win.focus();
	}
}

var Exportar = function() {
	if ($('anio').get('value') == '') {
		alert('Debe especificar el año de consulta');
		$('anio').select();
	}
	else {
		var url = 'CometraReporteServicios.php',
			arg = '?accion=exportar&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';

		win = window.open(url + arg, 'exportar', opt);

		win.focus();
	}
}

var Guardar = function()
{
	var request = new Request({
		url: 'CometraReporteServicios.php',
		method: 'post',
		data: {
			accion: 'guardar',
			costo_servicio: $('costo_servicio').get('value'),
			costo_millar: $('costo_millar').get('value'),
			costo_llave: $('costo_llave').get('value'),
			costo_servicio_fijo: $('costo_servicio_fijo').get('value'),
			cias_servicio_fijo: $('cias_servicio_fijo').get('value')
		},
		onRequest: function() {},
		onSuccess: function(response)
		{

		}
	}).send();
}
