var codigos = [];
var iduser = null;
var ts = null;
var current = null;

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

	boxFactura = new mBox.Modal(
	{
		id: 'box_factura',
		title: '<img src="/lecaroz/iconos/plus.png" width="16" height="16" /> Agregar factura',
		content: 'agregar_factura_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Agregar',
				event: function()
				{
					buscar_factura(document.id('num_cia_fac').get('value'), document.id('num_fact').get('value'));
				}
			}
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
		onOpen: function()
		{
			$$('#num_cia_fac, #nombre_cia_fac, #num_fact').set('value', '');
		},
		onOpenComplete: function()
		{
			document.id('num_cia_fac').focus();
		},
		onCloseComplete: function()
		{
		}
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

	$$('#banco, #tipo').addEvent('change', obtener_codigos);

	new_row(0);

	obtener_codigos();

	document.id('registrar').addEvent('click', registrar);

	new FormValidator(document.id('buscar_factura_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});

	document.id('num_cia_fac').addEvents(
	{
		change: obtener_cia_fac,
		keydown: function(e)
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
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				document.id('num_cia_fac').select();
			}
		}
	});

});

var new_row = function(i)
{
	var tr = new Element('tr');
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);
	var td5 = new Element('td').inject(tr);
	var td6 = new Element('td').inject(tr);

	var num_cia = new Element('input',
	{
		id: 'num_cia_' + i,
		name: 'num_cia[]',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3,
		value: i > 0 ? document.id('num_cia_' + (i - 1)).get('value') : ''
	}).addEvents(
	{
		change: obtener_cia.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				document.id('fecha_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('num_cia_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=num_cia]').length - 1)
				{
					document.id('num_cia_' + (i + 1)).select();
				}
			}
		}
	}).inject(td1);

	var nombre_cia = new Element('input',
	{
		id: 'nombre_cia_' + i,
		name: 'nombre_cia[]',
		type: 'text',
		size: 20,
		value: i > 0 ? document.id('nombre_cia_' + (i - 1)).get('value') : '',
		disabled: true,
	}).inject(td1);

	var cuenta_cia = new Element('input',
	{
		id: 'cuenta_cia_' + i,
		name: 'cuenta_cia[]',
		type: 'text',
		class: 'center',
		size: 10,
		value: i > 0 ? document.id('cuenta_cia_' + (i - 1)).get('value') : '',
		disabled: true,
	}).inject(td1);

	var fecha = new Element('input',
	{
		id: 'fecha_' + i,
		name: 'fecha[]',
		type: 'text',
		class: 'validate focus toDate center',
		size: 10,
		value: i > 0 ? document.id('fecha_' + (i - 1)).get('value') : current_date
	}).addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				document.id('importe_' + i).select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				document.id('num_cia_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('fecha_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=fecha]').length - 1)
				{
					document.id('fecha_' + (i + 1)).select();
				}
			}
		}
	}).inject(td2);

	var cod_mov = new Element('select',
	{
		id: 'cod_mov_' + i,
		name: 'cod_mov[]'
	}).inject(td3);

	var importe = new Element('input',
	{
		id: 'importe_' + i,
		name: 'importe[]',
		type: 'text',
		class: 'validate focus numberPosFormat right',
		precision: 2,
		size: 10,
		value: ''
	}).addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				document.id('concepto_' + i).select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				document.id('fecha_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('importe_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=importe]').length - 1)
				{
					document.id('importe_' + (i + 1)).select();
				}
			}
		}
	}).inject(td4);

	var concepto = new Element('input',
	{
		id: 'concepto_' + i,
		name: 'concepto[]',
		type: 'text',
		class: 'validate cleanText toUpper',
		size: 30,
		value: i > 0 ? document.id('concepto_' + (i - 1)).get('value').toUpperCase() : ''
	}).addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				if ( ! document.id('num_cia_' + (i + 1)))
				{
					new_row(i + 1);
				}

				document.id('num_cia_' + (i + 1)).select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				document.id('importe_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('concepto_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=concepto]').length - 1)
				{
					document.id('concepto_' + (i + 1)).select();
				}
			}
		}
	}).inject(td5);

	var add_facs = new Element('img',
	{
		id: 'add_fac_' + i,
		src: 'iconos/plus.png',
		width: 16,
		height: 16,
		'data-index': i,
		styles: {
			'cursor': 'pointer'
		}
	}).addEvents(
	{
		click: function(e)
		{
			current = this.get('data-index');

			boxFactura.open();
		}
	}).inject(td6);

	var span_facs = new Element('span',
	{
		id: 'span_facs_' + i,
		'data-index': i
	}).inject(td6);

	var validator = new FormValidator();

	validator.addElementEvents(num_cia);
	validator.addElementEvents(fecha);
	validator.addElementEvents(importe);
	validator.addElementEvents(concepto);

	update_select(cod_mov, codigos, i > 0 ? document.id('cod_mov_' + (i - 1)).selectedIndex : 0);

	tr.inject($$('#captura_table > tbody')[0]);

	return true;
}

var obtener_cia_fac = function(i)
{
	if (document.id('num_cia_fac').get('value').getNumericValue() <= 0)
	{
		$$('#num_cia_fac, #nombre_cia_fac').set('value', '');

		return false;
	}

	new Request(
	{
		url: 'MovimientosBancariosCaptura.php',
		type: 'post',
		data: {
			accion: 'obtener_cia_fac',
			num_cia: document.id('num_cia_fac').get('value'),
		},
		onSuccess: function(response)
		{
			if ( ! response)
			{
				alert('La compañía ' + document.id('num_cia_fac').get('value') + ' no esta en el catálogo');

				document.id('num_cia_fac').set('value', document.id('num_cia_fac').retrieve('tmp', '')).select();

				return false;
			}

			var data = JSON.decode(response);

			document.id('nombre_cia_fac').set('value', data.nombre_cia);
		}
	}).send();
}

var obtener_cia = function(i)
{
	if (document.id('num_cia_' + i).get('value').getNumericValue() <= 0)
	{
		$$('#num_cia_' + i + ', #nombre_cia_' + i + ', #cuenta_cia_' + i).set('value', '');

		return false;
	}

	new Request({
		url: 'MovimientosBancariosCaptura.php',
		type: 'post',
		data: {
			accion: 'obtener_cia',
			num_cia: document.id('num_cia_' + i).get('value'),
			banco: document.id('banco').get('value')
		},
		onSuccess: function(response)
		{
			if ( ! response)
			{
				alert('La compañía ' + document.id('num_cia_' + i).get('value') + ' no esta en el catálogo');

				document.id('num_cia_' + i).set('value', document.id('num_cia_' + i).retrieve('tmp', '')).select();

				return false;
			}

			var data = JSON.decode(response);

			document.id('nombre_cia_' + i).set('value', data.nombre_cia);
			document.id('cuenta_cia_' + i).set('value', data.cuenta_cia);
		}
	}).send();
}

var obtener_codigos = function()
{
	new Request({
		url: 'MovimientosBancariosCaptura.php',
		type: 'post',
		data: {
			accion: 'obtener_codigos',
			banco: document.id('banco').get('value'),
			tipo: document.id('tipo').get('value')
		},
		onSuccess: function(response)
		{
			codigos = JSON.decode(response);

			$$('select[id^=cod_mov_]').each(function(el)
			{
				update_select(el, codigos, 0);
			});
		}
	}).send();
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
	if ( ! confirm('¿Son correctos todos los datos?'))
	{
		return false;
	}

	new Request({
		url: 'MovimientosBancariosCaptura.php',
		type: 'post',
		data: 'accion=registrar&' + document.id('captura_form').toQueryString(),
		onSuccess: function(response)
		{
			data = JSON.decode(response);

			iduser = data.idins;
			ts = data.tsins;

			$$('#captura_table > tbody')[0].empty();

			new_row(0);

			reporte();
		}
	}).send();
}

var reporte = function()
{
	boxReporte.open();
}

var buscar_factura = function(num_cia, num_fact)
{
	if ( ! num_cia || ! num_fact)
	{
		return false;
	}

	new Request({
		url: 'MovimientosBancariosCaptura.php',
		type: 'post',
		data: {
			accion: 'buscar_factura',
			num_cia: num_cia,
			num_fact: num_fact
		},
		onSuccess: function(response)
		{
			if ( ! response)
			{
				alert('La factura no existe.');

				return false;
			}

			var data = JSON.decode(response);

			agregar_factura(current, data);

			boxFactura.close();

			return true;
		}
	}).send();
}

var agregar_factura = function(index, factura)
{
	var fac_span = new Element('span', {
		id: 'fac_span_' + index,
		html: factura.num_cia + '-' + factura.serie + factura.folio,
		styles: {
			'margin-left': '2px',
			'border': 'solid 1px #000',
			'background-color': '#fff',
			'padding': '2px',
			'cursor': 'pointer'
		}
	}).addEvent('click', function(e)
	{
		this.destroy();
	}).inject(document.id('span_facs_' + current));

	factura.index = index;

	var fac_input = new Element('input', {
		id: 'fac_' + index,
		name: 'fac_' + index + '[]',
		type: 'hidden',
		value: JSON.encode(factura)
	}).inject(fac_span);

	return true;
}
