
window.addEvent('domready', function()
{

	boxProcessing = new mBox(
	{
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

	boxReporte = new mBox.Modal(
	{
		id: 'box_reporte',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Reporte para imprimir',
		content: 'reporte_wrapper',
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
		closeInTitle: true,
		onOpenComplete: function()
		{
			document.id('reporte_frame').set('src', 'MovimientosBancariosCaptura.php?accion=reporte&iduser=' + iduser + '&ts=' + ts);
		},
		onCloseComplete: function()
		{
			document.id('reporte_frame').set('src', '');
		}
	});

	new FormValidator(document.id('captura_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});

	document.id('num_pro').addEvents({
		change: obtener_pro,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('num_fact').select();
			}
		}
	}).select();

	document.id('num_fact').addEvents({
		change: validar_fac,
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('fecha').select();
			}
		}
	});

	document.id('fecha').addEvents({
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				$$('input[id^=num_rem]')[0].select();
			}
		}
	});

	new_row(0);

	document.id('registrar').addEvent('click', registrar);

});

var new_row = function(i)
{
	var tr = new Element('tr');
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);

	var num_rem = new Element('input',
	{
		id: 'num_rem_' + i,
		name: 'num_rem[]',
		type: 'text',
		class: 'validate onlyNumbersAndLetters cleanText toUpper',
		size: 10,
		// value: i > 0 ? document.id('num_rem_' + (i - 1)).get('value') : ''
	}).addEvents(
	{
		change: obtener_remision.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				if ( ! document.id('num_rem_' + (i + 1)))
				{
					new_row(i + 1);
				}

				document.id('num_rem_' + (i + 1)).select();


			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('num_rem_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=num_rem]').length - 1)
				{
					document.id('num_rem_' + (i + 1)).select();
				}
			}
		}
	}).inject(td1);

	var num_cia = new Element('select',
	{
		id: 'num_cia_' + i,
		name: 'num_cia[]'
	}).addEvent('change', actualizar_remision.pass(i)).inject(td2);

	var fecha_rem = new Element('input',
	{
		id: 'fecha_rem_' + i,
		name: 'fecha_rem[]',
		type: 'text',
		class: 'center',
		size: 10,
		disabled: true
	}).inject(td3);

	var importe = new Element('input',
	{
		id: 'importe_' + i,
		name: 'importe[]',
		type: 'text',
		class: 'right',
		size: 10,
		disabled: true
	}).inject(td4);

	var validator = new FormValidator();

	validator.addElementEvents(num_rem);

	tr.inject($$('#captura_table > tbody')[0]);

	update_select(document.id('num_cia_' + i), [], 0);

	return true;
}

var obtener_pro = function(i)
{
	if (document.id('num_pro').get('value').getNumericValue() <= 0)
	{
		$$('#num_pro, #nombre_pro, #fecha').set('value', '');

		$$('#captura_table > tbody')[0].empty();

		new_row(0);

		return false;
	}

	new Request({
		url: 'RosticeriaAsociarFacturas.php',
		type: 'post',
		data: {
			accion: 'obtener_pro',
			num_pro: document.id('num_pro').get('value')
		},
		onSuccess: function(response)
		{
			if ( ! response)
			{
				alert('El proveedor ' + document.id('num_pro').get('value') + ' no esta en el catálogo');

				document.id('num_pro').set('value', document.id('num_pro').retrieve('tmp', '')).select();

				return false;
			}

			document.id('nombre_pro').set('value', response);

			validar_fac();
		}
	}).send();
}

var validar_fac = function()
{
	if (document.id('num_pro').get('value').getNumericValue() == 0 || document.id('num_fact').get('value').clean() == '')
	{
		$$('#captura_table > tbody')[0].empty();

		new_row(0);

		return false;
	}

	new Request({
		url: 'RosticeriaAsociarFacturas.php',
		type: 'post',
		data: {
			accion: 'validar_fac',
			num_pro: document.id('num_pro').get('value'),
			num_fact: document.id('num_fact').get('value')
		},
		onSuccess: function(response)
		{
			var data = JSON.decode(response);

			if (data.status == -1)
			{
				return true;
			}

			alert('La factura del proveedor '
				+ document.id('num_pro').get('value')
				+ ' '
				+ document.id('nombre_pro').get('value')
				+ ' con número ' + document.id('num_fact').get('value')
				+ ' ya existe en el sistema.');

			document.id('num_pro').set('value', '').select();
			document.id('nombre_pro').set('value', '');
			document.id('num_fact').set('value', '');
			document.id('fecha').set('value', '');
		}
	}).send();
}

var obtener_remision = function(i)
{
	if (document.id('num_pro').get('value').getNumericValue() == 0 || document.id('num_fact').get('value').clean() == '' || document.id('fecha').get('value').clean() == '')
	{
		alert('Debe especificar el proveedor, la factura y la fecha.');

		document.id('num_pro').select();

		return false;
	}

	if (document.id('num_rem_' + i).get('value').clean() == '')
	{
		update_select(document.id('num_cia_' + 1), [], 0);

		$$('#fecha_rem_' + i + ', #importe_' + i + ', #status_' + i).set('value', '');

		return false;
	}

	new Request({
		url: 'RosticeriaAsociarFacturas.php',
		type: 'post',
		data: {
			accion: 'obtener_remision',
			num_pro: document.id('num_pro').get('value'),
			num_rem: document.id('num_rem_' + i).get('value')
		},
		onSuccess: function(response)
		{
			if ( ! response)
			{
				alert('No existe datos de la remisión capturada.');

				update_select(document.id('num_cia_' + 1), [], 0);

				$$('#num_rem_' + i + ', #fecha_rem_' + i + ', #importe_' + i + ', #status_' + i).set('value', '');

				document.id('num_rem_' + i).select();

				return false;
			}

			var data = JSON.decode(response);

			update_select(document.id('num_cia_' + i), data, 0);

			document.id('num_cia_' + i).fireEvent('change');
		}
	}).send();
}

var actualizar_remision = function(i)
{
	if (document.id('num_cia_' + i).get('value') == '')
	{
		return false;
	}

	var data = JSON.decode(document.id('num_cia_' + i).get('value'));

	if ( ! data.status)
	{
		alert("La remisión ya esta asociada a la factura '" + data.num_fact + "', no puede ser asociada");

		document.id('fecha_rem_' + i).set('value', '');
		document.id('importe_' + i).set('value', '');

		calcular_total();

		return false;
	}

	document.id('fecha_rem_' + i).set('value', data.fecha);
	document.id('importe_' + i).set('value', data.total.numberFormat(2, '.', ','));

	calcular_total();
}

var calcular_total = function()
{
	var total = $$('input[id^=importe_]').get('value').getNumericValue().sum();

	$$('#captura_table > tfoot > tr > td')[1].set('html', total.numberFormat(2, '.', ','));
}

var update_select = function(select, options, selected_index)
{
	select.length = 0;

	if (options.length > 0)
	{
		select.length = options.length;

		Array.each(select.options, function(el, i)
		{
			el.set(options[i]);
		});

		select.selectedIndex = selected_index;
	}
	else
	{
		select.length = 1;
		Array.each(select.options, function(el, i)
		{
			el.set(
			{
				'value': '',
				'text': ''
			});
		});

		select.selectedIndex = 0;
	}
}

var registrar = function()
{
	if (document.id('num_pro').get('value').getNumericValue() <= 0)
	{
		alert('Debe especificar el proveedor.');

		document.id('num_pro').select();

		return false;
	}

	if (document.id('num_fact').get('value').clean() == '')
	{
		alert('Debe especificar el número de factura.');

		document.id('num_fact').select();

		return false;
	}

	if (document.id('fecha').get('value').clean() == '')
	{
		alert('Debe especificar la fecha de la factura.');

		document.id('fecha').select();

		return false;
	}

	if ($$('input[id^=importe_]').get('value').getNumericValue().sum() == 0)
	{
		alert('Debe ingresar al menos una remisión.');

		document.id('num_rem_0').select();

		return false;
	}

	if ( ! confirm('¿Son correctos todos los datos?'))
	{
		return false;
	}

	new Request({
		url: 'RosticeriaAsociarFacturas.php',
		type: 'post',
		data: 'accion=registrar&' + document.id('captura_form').toQueryString(),
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(response)
		{
			boxProcessing.close();

			$$('#num_pro, #nombre_pro, #num_fact, #fecha').set('value', '');

			$$('#captura_table > tbody')[0].empty();

			new_row(0);

			alert('Las remisiones fueron asociadas correctamente.');

			document.id('num_pro').select();
		}
	}).send();
}
