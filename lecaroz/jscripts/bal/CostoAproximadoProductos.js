window.addEvent('domready', function()
{

	boxProcessing = new mBox(
	{
		id: 'box_processing',
		content: '<img src="/lecaroz/imagenes/mbox/mBox-Spinner.gif" width="32" height="32" /> Procesando, espere unos segundos por favor...',
		overlay: true,
		overlayStyles:
		{
			color: 'white',
			opacity: 0.8
		},
		closeOnEsc: false,
		closeOnBodyClick: false
	});

	box = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {}
			}
		],
		overlay: true,
		overlayStyles:
		{
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function() {},
		onOpenComplete: function() {}
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
		overlayStyles:
		{
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
	new Request(
	{
		url: 'CostoAproximadoProductos.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('inicio'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nombre_producto').addEvents(
			{
				keydown: function(e) {
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.focus();
					}
				}
			}).focus();

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();
		}
	}).send();
}

var consultar = function ()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		param = document.id('inicio').toQueryString();
	}

	new Request(
	{
		url: 'CostoAproximadoProductos.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			document.id('alta').addEvent('click', alta);

			$$('img[id=mod]').each(function(el)
			{
				var id = el.get('alt');

				el.addEvent('click', modificar.pass(id));

				el.removeProperty('alt');
			});

			$$('img[id=baja]').each(function(el)
			{
				var id = el.get('alt');

				el.addEvent('click', do_baja.pass(id));

				el.removeProperty('alt');
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var obtener_mp = function(i)
{
	if (document.id('codmp_' + i).get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'CostoAproximadoProductos.php',
			data: 'accion=obtener_mp&codmp=' + document.id('codmp_' + i).get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					var data = JSON.decode(result);

					document.id('nombre_mp_' + i).set('value', data.nombre);
					document.id('unidad_' + i).set('value', data.unidad);
					document.id('precio_unidad_' + i).set('value', data.precio_unidad.numberFormat(6, '.', ','));

					calcular_costo_producto(i);
				}
				else
				{
					document.id('codmp_' + i).set('value', document.id('codmp_' + i).retrieve('tmp', ''));

					alert('El producto no está en el catálogo');

					document.id('codmp_' + i).focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#cantidad_' + i +', #codmp_' + i + ', #nombre_mp_' + i + ', #unidad_' + i + ', #precio_unidad_' + i + ', #costo_producto_' + i).set('value', '');

		calcular_totales();
	}
}

var alta = function()
{
	new Request({
		url: 'CostoAproximadoProductos.php',
		data: 'accion=alta',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('alta_producto'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nombre_producto').addEvent('keydown', function(e)
			{
				if (e.key == 'enter')
				{
					e.stop();

					document.id('cantidad_0').select();
				}
			});

			document.id('porc_raya').addEvents({
				change: calcular_totales,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.focus();
					}
				}
			});

			new_row(0);

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('alta').addEvent('click', do_alta);

			document.id('nombre_producto').focus();

			boxProcessing.close();
		}
	}).send();
}

var do_alta = function()
{
	if (document.id('nombre_producto').get('value').trim().clean() == '')
	{
		alert('Debe especificar el nombre del producto');

		document.id('nombre_producto').focus();

		return false;
	}
	if (document.id('costo_total').get('value').getNumericValue() <= 0)
	{
		alert('Debe especificar el desglose de productos de avio');

		document.id('cantidad_0').focus();
	}
	else if (confirm('¿Son correctos todos los datos?'))
	{
		new Request(
		{
			url: 'CostoAproximadoProductos.php',
			data: 'accion=do_alta&' + document.id('alta_producto').toQueryString(),
			onRequest: function()
			{
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function(result)
			{
				consultar(param);
			}
		}).send();
	}
}

var modificar = function(id)
{
	new Request(
	{
		url: 'CostoAproximadoProductos.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('modificar_producto'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nombre_producto').addEvent('keydown', function(e)
			{
				if (e.key == 'enter')
				{
					e.stop();

					document.id('cantidad_0').select();
				}
			});

			document.id('porc_raya').addEvents({
				change: calcular_totales,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.focus();
					}
				}
			});

			$$('input[id^=cantidad_]').each(function(el, i)
			{
				el.addEvents(
				{
					change: calcular_costo_producto.pass(i),
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							document.id('codmp_' + i).select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								document.id('cantidad_' + (i - 1)).select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id^=cantidad]').length - 1)
							{
								document.id('cantidad_' + (i + 1)).select();
							}
						}
					}
				});
			});

			$$('input[id^=codmp_]').each(function(el, i)
			{
				el.addEvents(
				{
					change: obtener_mp.pass(i),
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							if ( ! document.id('cantidad_' + (i + 1)))
							{
								new_row(i + 1);
							}

							document.id('cantidad_' + (i + 1)).select();
						}
						else if (e.key == 'left')
						{
							e.stop();

							document.id('cantidad_' + i).select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								document.id('codmp_' + (i - 1)).select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id^=codmp]').length - 1)
							{
								document.id('codmp_' + (i + 1)).select();
							}
						}
					}
				});
			});

			var last_index = $$('input[id^=codmp_]').length;

			new_row(last_index);

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			document.id('cantidad_' + last_index).focus();

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function()
{
	if (document.id('nombre_producto').get('value').trim().clean() == '')
	{
		alert('Debe especificar el nombre del producto');

		document.id('nombre_producto').focus();

		return false;
	}
	if (document.id('costo_total').get('value').getNumericValue() <= 0)
	{
		alert('Debe especificar el desglose de productos de avio');

		document.id('cantidad_0').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'CostoAproximadoProductos.php',
			data: 'accion=do_modificar&' + document.id('modificar_producto').toQueryString(),
			onRequest: function()
			{
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
			}
		}).send();
	}
}

var do_baja = function(id) {
	if (confirm('¿Desea borrar el producto seleccionado?')) {
		new Request({
			url: 'CostoAproximadoProductos.php',
			data: 'accion=do_baja&id=' + id,
			onRequest: function()
			{
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
			}
		}).send();
	}
}

var new_row = function(i)
{
	var tr = new Element('tr');
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);
	var td5 = new Element('td').inject(tr);

	var row_id = new Element('input',
	{
		id: 'row_id_' + i,
		name: 'row_id[]',
		type: 'hidden',
		value: ''
	}).inject(td1);

	var cantidad = new Element('input',
	{
		id: 'cantidad_' + i,
		name: 'cantidad[]',
		type: 'text',
		class: 'validate focus toFloat right',
		size: 10,
		value: ''
	}).addEvents(
	{
		change: calcular_costo_producto.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				document.id('codmp_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('cantidad_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=cantidad]').length - 1)
				{
					document.id('cantidad_' + (i + 1)).select();
				}
			}
		}
	}).inject(td1);

	var codmp = new Element('input',
	{
		id: 'codmp_' + i,
		name: 'codmp[]',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3,
		value: ''
	}).addEvents(
	{
		change: obtener_mp.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				if ( ! document.id('cantidad_' + (i + 1)))
				{
					new_row(i + 1);
				}

				document.id('cantidad_' + (i + 1)).select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				document.id('cantidad_' + i).select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					document.id('codmp_' + (i - 1)).select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id^=codmp]').length - 1)
				{
					document.id('codmp_' + (i + 1)).select();
				}
			}
		}
	}).inject(td2);

	var nombre_mp = new Element('input',
	{
		id: 'nombre_mp_' + i,
		name: 'nombre_mp[]',
		type: 'text',
		size: 20,
		value: '',
		disabled: true,
	}).inject(td2);

	var unidad = new Element('input',
	{
		id: 'unidad_' + i,
		name: 'unidad[]',
		type: 'text',
		size: 10,
		value: '',
		disabled: true,
	}).inject(td3);

	var precio_unidad = new Element('input',
	{
		id: 'precio_unidad_' + i,
		name: 'precio_unidad[]',
		type: 'text',
		class: 'right green',
		size: 10,
		value: '',
		readonly: true,
	}).inject(td4);

	var costo_consumo = new Element('input',
	{
		id: 'costo_consumo_' + i,
		name: 'costo_consumo[]',
		type: 'text',
		class: 'right red',
		size: 10,
		value: '',
		readonly: true,
	}).inject(td5);

	var validator = new FormValidator();

	validator.addElementEvents(cantidad);
	validator.addElementEvents(codmp);

	tr.inject($$('#captura_table > tbody')[0]);

	return true;
}

var calcular_costo_producto = function(i)
{
	var cantidad = document.id('cantidad_' + i).get('value').getNumericValue();
	var precio_unidad = document.id('precio_unidad_' + i).get('value').getNumericValue();
	var costo_consumo = cantidad * precio_unidad;

	document.id('costo_consumo_' + i).set('value', costo_consumo > 0 ? costo_consumo.numberFormat(6, '.', ',') : '');

	calcular_totales();
}

var calcular_totales = function()
{
	var total_consumo = $$('input[id^=costo_consumo_]').get('value').getNumericValue().sum().round(6);
	var porc_raya = document.id('porc_raya').get('value').getNumericValue();
	var raya = (total_consumo * porc_raya / 100).round(6);
	var costo_total = (total_consumo + raya).round(6);

	document.id('total_consumo').set('value', total_consumo.numberFormat(6, '.', ','));
	document.id('raya').set('value', raya.numberFormat(6, '.', ','));
	document.id('costo_total').set('value', costo_total.numberFormat(6, '.', ','));
}
