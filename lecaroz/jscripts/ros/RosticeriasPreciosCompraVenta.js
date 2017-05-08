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
		url: 'RosticeriasPreciosCompraVenta.php',
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

			document.id('num_cia').addEvents(
			{
				change: obtener_cia,
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
		if (document.id('num_cia').get('value').getNumericValue() == 0)
		{
			alert('Debe especificar la compañía');

			document.id('num_cia').focus();

			return false;
		}

		param = document.id('inicio').toQueryString();
	}

	new Request(
	{
		url: 'RosticeriasPreciosCompraVenta.php',
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

			sortable = new Sortables('#controles tbody',
			{
				//'clone': true,
				'revert': true,
				'opacity': 0.5,
				'handle': 'td.dragme',
				'onComplete': function(el)
				{
					actualizar_orden(this.serialize(1, function(el, i)
					{
						return 'orden[]=' + JSON.encode(
						{
							id: el.getProperty('id').replace('row_', '').getNumericValue(),
							orden: i + 1
						});
					}).join('&'));
				}
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var actualizar_orden = function(orden) {
	new Request(
	{
		url: 'RosticeriasPreciosCompraVenta.php',
		data: 'accion=actualizar_orden&num_cia=' + document.id('num_cia').get('value') + '&' + orden,
		onRequest: function() {},
		onSuccess: function(result) {}
	}).send();
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'RosticeriasPreciosCompraVenta.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result);
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));

					alert('La compañía no está en el catálogo');

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

var obtener_mp = function()
{
	if (document.id('codmp').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'RosticeriasPreciosCompraVenta.php',
			data: 'accion=obtener_mp&codmp=' + document.id('codmp').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_mp').set('value', result);
				}
				else
				{
					document.id('codmp').set('value', document.id('codmp').retrieve('tmp', ''));

					alert('El producto no está en el catálogo');

					document.id('codmp').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#codmp, #nombre_mp').set('value', '');
	}
}

var obtener_pro = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'RosticeriasPreciosCompraVenta.php',
			data: 'accion=obtener_pro&num_pro=' + document.id('num_pro').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_pro').set('value', result);
				}
				else
				{
					document.id('num_pro').set('value', document.id('num_pro').retrieve('tmp', ''));

					alert('El proveedor no está en el catálogo');

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

var alta = function()
{
	new Request({
		url: 'RosticeriasPreciosCompraVenta.php',
		data: 'accion=alta&num_cia=' + document.id('num_cia').get('value'),
		onRequest: function() {
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

			document.id('codmp').addEvents(
			{
				change: obtener_mp,
				keydown: function(e) {
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nombre_alt').select();
					}
				}
			}).focus();

			document.id('nombre_alt').addEvents(
			{
				keydown: function(e)
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
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('precio_compra').select();
					}
				}
			})

			document.id('precio_compra').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('precio_venta').select();
					}
				}
			});

			document.id('precio_venta').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('codmp').select();
					}
				}
			});

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('alta').addEvent('click', do_alta);

			boxProcessing.close();
		}
	}).send();
}

var do_alta = function()
{
	if (document.id('codmp').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el producto');

		document.id('codmp').focus();
	}
	// else if (document.id('num_pro').get('value').getNumericValue() == 0)
	// {
	// 	alert('Debe especificar el proveedor');

	// 	document.id('num_pro').focus();
	// }
	else if (document.id('precio_compra').get('value').getNumericValue() == 0 && document.id('precio_venta').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el precio de compra o venta');

		document.id('precio_compra').focus();
	}
	else if (confirm('¿Son correctos todos los datos?'))
	{
		new Request(
		{
			url: 'RosticeriasPreciosCompraVenta.php',
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
		url: 'RosticeriasPreciosCompraVenta.php',
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

			document.id('codmp').addEvents(
			{
				change: obtener_mp,
				keydown: function(e) {
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nombre_alt').select();
					}
				}
			}).focus();

			document.id('nombre_alt').addEvents(
			{
				keydown: function(e)
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
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('precio_compra').select();
					}
				}
			})

			document.id('precio_compra').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('precio_venta').select();
					}
				}
			});

			document.id('precio_venta').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('codmp').select();
					}
				}
			});

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('codmp').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el producto');

		document.id('codmp').focus();
	}
	// else if (document.id('num_pro').get('value').getNumericValue() == 0)
	// {
	// 	alert('Debe especificar el proveedor');

	// 	document.id('num_pro').focus();
	// }
	else if (document.id('precio_compra').get('value').getNumericValue() == 0 && document.id('precio_venta').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el precio de compra o venta');

		document.id('precio_compra').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'RosticeriasPreciosCompraVenta.php',
			data: 'accion=do_modificar&' + document.id('modificar_producto').toQueryString(),
			onRequest: function() {
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
			url: 'RosticeriasPreciosCompraVenta.php',
			data: 'accion=do_baja&id=' + id + '&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar(param);
			}
		}).send();
	}
}
