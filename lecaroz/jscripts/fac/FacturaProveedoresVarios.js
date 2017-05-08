window.addEvent('domready', function() {

	boxProcessing = new mBox({
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		closeOnEsc: false,
		closeOnBodyClick: false
	});

	boxSuccess = new mBox.Modal(
	{
		id: 'box_success',
		title: 'Terminado',
		content: 'Factura registrada con éxito.',
		buttons: [
			{ title: 'Aceptar' }
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: false,
	});

	boxFailure = new mBox.Modal(
	{
		id: 'box_failure',
		title: 'Error',
		content: '',
		buttons: [
			{ title: 'Aceptar' }
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: false,
	});

	inicio();

});

var inicio = function ()
{
	new FormValidator(document.id('factura_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});

	document.id('num_cia').addEvents(
	{
		change: obtener_cia,
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('num_pro').select();
			}
		}
	});

	document.id('num_pro').addEvents(
	{
		change: obtener_pro,
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('num_fact').select();
			}
		}
	});

	document.id('num_fact').addEvents(
	{
		change: validar_factura,
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('fecha').select();
			}
		}
	});

	document.id('fecha').addEvents(
	{
		change: validar_fecha,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('codgastos').select();
			}
		}
	});

	document.id('codgastos').addEvents(
	{
		change: obtener_gasto,
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto').select();
			}
		}
	});

	document.id('concepto').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				if (document.id('codgastos').get('value') == '79')
				{
					document.id('anio').select();
				}
				else
				{
					document.id('subtotal').focus();
				}

			}
		}
	});

	document.id('anio').addEvents(
	{
		change: validar_agua,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('bimestre').focus();
			}
		}
	});

	document.id('bimestre').addEvents(
	{
		change: validar_agua,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('subtotal').focus();
			}
		}
	});

	document.id('subtotal').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	document.id('concepto_otros').addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('importe_otros').focus();
			}
		}
	});

	document.id('importe_otros').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('num_cia').focus();
			}
		}
	});

	document.id('por_ieps').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	document.id('ieps_libre').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	document.id('por_iva').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	document.id('por_ret_iva').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	document.id('por_ret_isr').addEvents(
	{
		change: calcular_total,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('concepto_otros').focus();
			}
		}
	});

	$$('input[id^=aplicar_]').addEvent('change', calcular_total);


	document.id('borrar').addEvent('click', borrar);
	document.id('guardar').addEvent('click', guardar);

	document.id('num_cia').focus();
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response != '')
				{
					document.id('nombre_cia').set('value', response);
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo');

					document.id('num_cia').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia, #nombre_cia').set('value', '');
	}
}

var obtener_pro = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0)
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=obtener_pro&num_pro=' + document.id('num_pro').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response != '')
				{
					document.id('nombre_pro').set('value', response);

					validar_factura();
				}
				else
				{
					document.id('num_pro').set('value', document.id('num_pro').retrieve('tmp', ''));

					alert('El proveedor no esta en el catálogo');

					document.id('num_pro').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_pro, #nombre_pro').set('value', '');
	}
}

var obtener_gasto = function()
{
	if (document.id('codgastos').get('value').getNumericValue() > 0)
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=obtener_gasto&gasto=' + document.id('codgastos').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response != '')
				{
					document.id('desc').set('value', response);

					if (document.id('codgastos').get('value').getNumericValue() == 79)
					{
						$$('#row_anio, #row_bimestre').setStyle('display', 'table-row');
					}
				}
				else
				{
					document.id('codgastos').set('value', document.id('codgastos').retrieve('tmp', ''));

					alert('El gasto no esta en el catálogo');

					document.id('codgastos').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#codgastos, #desc').set('value', '');

		$$('#row_anio, #row_bimestre').setStyle('display', 'none');
	}
}

var validar_factura = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0 && document.id('num_fact').get('value') != '')
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=validar_factura&num_pro=' + document.id('num_pro').get('value') + '&num_fact=' + document.id('num_fact').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response.getNumericValue() < 0)
				{
					document.id('num_fact').set('value', document.id('num_fact').retrieve('tmp', ''));

					alert('La factura ya ha sido capturada con anterioridad');

					document.id('num_fact').focus();
				}
			}
		}).send();
	}
}

var validar_fecha = function()
{
	if (document.id('fecha').get('value') != '')
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=validar_fecha&fecha=' + document.id('fecha').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response.getNumericValue() < 0)
				{
					document.id('fecha').set('value', document.id('fecha').retrieve('tmp', ''));

					alert('La fecha no puede ser de meses posteriores');

					document.id('fecha').focus();
				}
			}
		}).send();
	}
}

var validar_agua = function()
{
	if (document.id('num_cia').get('value').getNumericValue() <= 0)
	{
		$$('#anio, #bimestre').set('value', '');

		alert('Debe capturar primero la compañía');

		document.id('num_cia').focus();

		return false;
	}

	if (document.id('bimestre').get('value').getNumericValue() > 6)
	{
		document.id('bimestre').set('value', '');

		alert('El valor del bimestre debe estar entre 1 y 6');

		document.id('bimestre').focus();

		return false;
	}

	if (document.id('num_cia').get('value').getNumericValue() > 0 && document.id('anio').get('value').getNumericValue() > 0 && document.id('bimestre').get('value').getNumericValue() > 0)
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=validar_agua&num_cia=' + document.id('num_cia').get('value') + '&anio=' + document.id('anio').get('value') + '&bimestre=' + document.id('bimestre').get('value'),
			'onRequest': function() {},
			'onSuccess': function(response) {
				if (response.getNumericValue() < 0)
				{
					$$('#anio, #bimestre').set('value', '');

					alert('Una factura de agua del mismo bimestre ya esta capturada en el sistema');

					document.id('anio').focus();
				}
			}
		}).send();
	}
}

var calcular_total = function()
{
	var subtotal = document.id('subtotal').get('value').getNumericValue();
	var por_iva = document.id('por_iva').get('value').getNumericValue();
	var iva = 0;
	var por_ieps = document.id('por_ieps').get('value').getNumericValue();
	var ieps = 0;
	var ieps_libre = document.id('ieps_libre').get('value').getNumericValue();
	var por_ret_iva = document.id('por_ret_iva').get('value').getNumericValue();
	var ret_iva = 0;
	var por_ret_isr = document.id('por_ret_isr').get('value').getNumericValue();
	var ret_isr = 0;
	var importe_otros = document.id('importe_otros').get('value').getNumericValue();
	var total = 0;

	if ( !! document.id('aplicar_iva').get('checked'))
	{
		iva = subtotal * por_iva / 100;
	}

	if ( !! document.id('aplicar_ieps').get('checked'))
	{
		ieps = subtotal * por_ieps / 100;
	}

	if ( !! document.id('aplicar_ret_iva').get('checked'))
	{
		ret_iva = subtotal * por_ret_iva / 100;
	}

	if ( !! document.id('aplicar_ret_isr').get('checked'))
	{
		ret_isr = subtotal * por_ret_isr / 100;
	}

	total = subtotal + iva + ieps + ieps_libre - ret_iva - ret_isr + importe_otros;

	document.id('iva').set('value', iva.numberFormat(2, '.', ','));
	document.id('ieps').set('value', ieps.numberFormat(2, '.', ','));
	document.id('ret_iva').set('value', ret_iva.numberFormat(2, '.', ','));
	document.id('ret_isr').set('value', ret_isr.numberFormat(2, '.', ','));
	document.id('total').set('value', total.numberFormat(2, '.', ','));
}

var guardar = function()
{
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar la compañía');

		document.id('num_cia').focus();

		return false;
	}
	else if (document.id('num_pro').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el proveedor');

		document.id('num_pro').focus();

		return false;
	}
	else if (document.id('num_fact').get('value').clean() == '')
	{
		alert('Debe especificar el número de factura');

		document.id('num_fact').focus();

		return false;
	}
	else if (document.id('fecha').get('value') == '')
	{
		alert('Debe especificar la fecha');

		document.id('fecha').focus();

		return false;
	}
	else if (document.id('codgastos').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el gasto');

		document.id('codgastos').focus();

		return false;
	}
	else if (document.id('codgastos').get('value') == '79' && document.id('anio').get('value').getNumericValue() == 0)
	{
		alert('Para facturas con código 79 AGUA debe especificar el año');

		document.id('anio').focus();

		return false;
	}
	else if (document.id('codgastos').get('value') == '79' && document.id('bimestre').get('value').getNumericValue() == 0)
	{
		alert('Para facturas con código 79 AGUA debe especificar el bimestre');

		document.id('bimestre').focus();

		return false;
	}
	else if (document.id('concepto').get('value').clean() == '')
	{
		alert('Debe especificar el concepto');

		document.id('concepto').focus();

		return false;
	}
	else if (document.id('concepto_otros').get('value').clean() == '' && document.id('importe_otros').get('value').getNumericValue() > 0)
	{
		alert('Debe especificar otros conceptos');

		document.id('concepto_otros').focus();

		return false;
	}
	else if (document.id('concepto_otros').get('value').clean() != '' && document.id('importe_otros').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el importe de otros conceptos');

		document.id('importe_otros').focus();

		return false;
	}
	else if (document.id('total').get('value').getNumericValue() == 0)
	{
		alert('El importe de la factura no puede ser cero');

		document.id('subtotal').focus();

		return false;
	}
	else
	{
		new Request({
			'url': 'FacturaProveedoresVarios.php',
			'data': 'accion=guardar&' + document.id('factura_form').toQueryString(),
			'onRequest': function() {
				boxProcessing.open();
			},
			'onSuccess': function(response) {
				boxProcessing.close();

				boxSuccess.open();

				borrar();
			}
		}).send();
	}
}

var borrar = function()
{
	$$('input[type=text]').set('value', '');
	$$('input[id^=aplicar_]').set('checked', false);

	document.id('por_iva').set('value', '16.00');
	document.id('por_ieps').set('value', '8.00');
	document.id('por_ret_iva').set('value', '10.6666667');
	document.id('por_ret_isr').set('value', '10.00');
}
