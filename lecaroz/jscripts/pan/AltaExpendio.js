// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		'change': cambiaCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('num_expendio').select();
				e.stop();
			}
		}
	});
	
	$('num_expendio').addEvents({
		'change': validarNumExp,
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('num_referencia').select();
				e.stop();
			}
		}
	});
	
	$('num_referencia').addEvents({
		'change': validarNumRef,
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('nombre').select();
				e.stop();
			}
		}
	});
	
	$('nombre').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('direccion').select();
				e.stop();
			}
		}
	});
	
	$('direccion').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('porciento_ganancia').select();
				e.stop();
			}
		}
	});
	
	$('porciento_ganancia').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('importe_fijo').select();
				e.stop();
			}
		}
	});
	
	$('importe_fijo').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('devolucion_maxima').select();
				e.stop();
			}
		}
	});

	$$('input[name=tipo_devolucion]').each(function(el)
	{
		el.addEvent('click', function()
		{
			var value = this.get('value');

			if (this.get('value') == '0')
			{
				$('tipo_devolucion_span').set('html', 'Porcentaje');
			}
			else if (this.get('value') == '1')
			{
				$('tipo_devolucion_span').set('html', 'Importe');
			}
		});
	});

	$('devolucion_maxima').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('num_cia_exp').select();
				e.stop();
			}
		}
	});
	
	$('num_cia_exp').addEvents({
		'change': cambiaCiaExp,
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('num_cia').select();
				e.stop();
			}
		}
	});
	
	$('alta').addEvent('click', Alta);
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
		$('num_expendio').set('value', '');
		$('num_referencia').set('value', '');
		return false;
	}
	
	new Request({
		'url': 'AltaExpendio.php',
		'data': {
			'accion': 'cia',
			'num_cia': $('num_cia').get('value')
		},
		'onSuccess': function(result) {
			if (result.clean() == '') {
				alert('La compañía no se encuentra en el catálogo');
				$('num_cia').set('value', '');
				$('nombre_cia').set('value', '');
			}
			else {
				$('nombre_cia').set('value', result);
			}
		}
	}).send();
}

var cambiaCiaExp = function() {
	if ($('num_cia_exp').get('value').getVal() == 0) {
		$('num_cia_exp').set('value', '');
		$('nombre_cia_exp').set('value', '');
		return false;
	}
	
	new Request({
		'url': 'AltaExpendio.php',
		'data': {
			'accion': 'cia',
			'num_cia': $('num_cia_exp').get('value')
		},
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result.clean() == '') {
				alert('La compañía no se encuentra en el catálogo');
				$('num_cia_exp').set('value', '');
				$('nombre_cia_exp').set('value', '');
			}
			else {
				$('nombre_cia_exp').set('value', result);
			}
		}
	}).send();
}

var validarNumExp = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_expendio').set('value', '');
		$('num_cia').select();
	}
	else {
		new Request({
			'url': 'AltaExpendio.php',
			'data': {
				'accion': 'validarExp',
				'num_cia': $('num_cia').get('value'),
				'num_expendio': $('num_expendio').get('value')
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert(result);
					$('num_expendio').set('value', '');
				}
			}
		}).send();
	}
}

var validarNumRef = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_referencia').set('value', '');
	}
	else {
		new Request({
			'url': 'AltaExpendio.php',
			'data': {
				'accion': 'validarRef',
				'num_cia': $('num_cia').get('value'),
				'num_referencia': $('num_referencia').get('value')
			},
			'onSuccess': function(result) {
				if (result != '') {
					alert(result);
					$('num_referencia').set('value', '');
				}
			}
		}).send();
	}
}

var Alta = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if ($('num_expendio').get('value').getVal() == 0) {
		alert('Debe especificar el número de expendio');
		$('num_expendio').focus();
	}
	else if ($('nombre').get('value').clean() == '') {
		alert('Debe expecificar el nombre del expendio');
		$('nombre').focus();
	}
	else if ($('aut_dev').get('checked') && $('tipo_devolucion_0').get('checked') && $('devolucion_maxima').get('value').replace(/[^\+\-\d\.]/g, '').toFloat() > 9.99) {
		alert('El porcentaje de devolución no puede ser mayor a 9.99%');
		$('devolucion_maxima').focus();
	}
	else if ($('aut_dev').get('checked') && $('devolucion_maxima').get('value').replace(/[^\+\-\d\.]/g, '').toFloat() > 9999.99) {
		alert('El importe de devolución no puede ser mayor a 9,999.99');
		$('devolucion_maxima').focus();
	}
	else {
		if (confirm('¿Son correctos todos los datos?')) {
			new Request({
				'url': 'AltaExpendio.php',
				'data': 'accion=alta&' + $('Datos').toQueryString(),
				'onSuccess': function(result) {
					alert(result);
					$('Datos').reset();
				}
			}).send();
		}
		else {
			$('num_cia').select();
		}
	}
}
