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

	box = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{

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
		onBoxReady: function()
		{
		},
		onOpenComplete: function()
		{
		}
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

	boxAltaMatriz = new mBox.Modal(
	{
		id: 'box_alta_matriz',
		title: 'Alta de matriz',
		content: 'alta_matriz_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('matriz').get('value').getNumericValue() > 0)
					{
						do_alta_matriz();
					}
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
		closeInTitle: false,
		onBoxReady: function()
		{
			new FormValidator(document.id('alta_matriz_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('matriz').addEvents(
			{
				change: obtener_matriz,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
					}
				}
			});
		},
		onOpen: function()
		{
			document.id('matriz').set('value', '').fireEvent('change');
		},
		onOpenComplete: function()
		{
			document.id('matriz').focus();
		}
	});

	boxAltaSucursal = new mBox.Modal(
	{
		id: 'box_alta_sucursal',
		title: 'Alta de sucursal',
		content: 'alta_sucursal_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('sucursal').get('value').getNumericValue() == 0)
					{
						alert('Debe especificar la nueva sucursal');

						document.id('sucursal').focus();

						return false;
					}
					else if (document.id('porcentaje_alta').get('value').getNumericValue() == 0)
					{
						alert('Debe especificar el porcentaje para la sucursal');

						document.id('porcentaje_alta').focus();

						return false;
					}
					else if (document.id('porcentaje_alta').get('value').getNumericValue() >= matriz.porcentaje)
					{
						alert('El porcentaje de la sucursal no puede ser igual o mayor al ' + matriz.porcentaje + '%');

						document.id('porcentaje_alta').select();

						return false;
					}

					do_alta_sucursal();
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
		closeInTitle: false,
		onBoxReady: function()
		{
			new FormValidator(document.id('alta_sucursal_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('sucursal').addEvents(
			{
				change: obtener_sucursal,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('porcentaje_alta').select();
					}
				}
			});

			document.id('porcentaje_alta').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('sucursal').select();
					}
				}
			});
		},
		onOpen: function()
		{
			document.id('sucursal').set('value', '').fireEvent('change');
			document.id('porcentaje_alta').set('value', '');
		},
		onOpenComplete: function()
		{
			document.id('sucursal').focus();
		}
	});

	boxModificarSucursal = new mBox.Modal(
	{
		id: 'box_mod_sucursal',
		title: 'Modificar sucursal',
		content: 'mod_sucursal_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('porcentaje_mod').get('value').getNumericValue() == 0)
					{
						alert('Debe especificar el porcentaje para la sucursal');

						document.id('porcentaje_mod').focus();

						return false;
					}
					else if (document.id('porcentaje_mod').get('value').getNumericValue() >= sucursal.porcentaje_matriz + sucursal.porcentaje_sucursal)
					{
						alert('El porcentaje de la sucursal no puede ser igual o mayor al ' + (sucursal.porcentaje_matriz + sucursal.porcentaje_sucursal) + '%');

						document.id('porcentaje_mod').select();

						return false;
					}

					do_modificar_sucursal();
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
		closeInTitle: false,
		onBoxReady: function()
		{
			new FormValidator(document.id('mod_sucursal_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('porcentaje_mod').addEvents(
			{
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
		},
		onOpen: function() {},
		onOpenComplete: function()
		{
			document.id('porcentaje_mod').select();
		}
	});

	boxBajaMatriz = new mBox.Modal(
	{
		id: 'box_baja_matriz',
		title: 'Baja de matriz',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_baja_matriz();
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
		closeInTitle: false
	});

	boxBajaSucursal = new mBox.Modal(
	{
		id: 'box_baja_sucursal',
		title: 'Baja de sucursal',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_baja_sucursal();
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
		closeInTitle: false
	});

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('matrices').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('sucursales').focus();
					}
				}
			}).focus();

			document.id('sucursales').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('matrices').focus();
					}
				}
			});

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
	else {
		param = document.id('inicio').toQueryString();
	}

	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			document.id('alta_matriz').addEvent('click', function()
			{
				boxAltaMatriz.open();
			});

			$$('img[id=alta_sucursal]').each(function(el)
			{
				el.addEvent('click', function()
				{
					matriz = JSON.decode(el.get('data-matriz'));

					boxAltaSucursal.open();
				});
			});

			$$('img[id=mod_sucursal]').each(function(el)
			{

				el.addEvent('click', function()
				{
					sucursal = JSON.decode(el.get('data-sucursal'));

					document.id('nombre_sucursal_mod').set('html', sucursal.sucursal + ' ' + sucursal.nombre_sucursal);

					document.id('porcentaje_mod').set('value', sucursal.porcentaje_sucursal.numberFormat(2, '.', ','));

					boxModificarSucursal.open();
				});
			});

			$$('img[id=baja_matriz]').each(function(el)
			{
				el.addEvent('click', function()
				{
					matriz = JSON.decode(el.get('data-matriz'));

					boxBajaMatriz.setContent('¿Desea dar de baja la matriz ' + matriz.matriz + ' ' + matriz.nombre_matriz + '?').open();
				});
			});

			$$('img[id=baja_sucursal]').each(function(el)
			{
				el.addEvent('click', function()
				{
					sucursal = JSON.decode(el.get('data-sucursal'));

					boxBajaSucursal.setContent('¿Desea dar de baja la sucursal ' + sucursal.sucursal + ' ' + sucursal.nombre_sucursal + '?').open();
				});
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var do_alta_matriz = function()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: 'accion=do_alta_matriz&num_cia=' + document.id('matriz').get('value'),
		onRequest: function()
		{
			boxAltaMatriz.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var do_alta_sucursal = function()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: {
			accion: 'do_alta_sucursal',
			id_matriz: matriz.id,
			matriz: matriz.matriz,
			sucursal: document.id('sucursal').get('value'),
			porcentaje: document.id('porcentaje_alta').get('value').getNumericValue()
		},
		onRequest: function()
		{
			boxAltaSucursal.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var obtener_matriz = function()
{
	if (document.id('matriz').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'PorcentajesVentasSucursales.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('matriz').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_matriz').set('value', result);

					document.id('matriz').select();
				}
				else
				{
					document.id('matriz').set('value', document.id('matriz').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya esta dada de alta como matriz o sucursal');

					document.id('matriz').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#matriz, #nombre_matriz').set('value', '');

		document.id('matriz').select();
	}
}

var obtener_sucursal = function()
{
	if (document.id('sucursal').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'PorcentajesVentasSucursales.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('sucursal').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_sucursal').set('value', result);

					document.id('porcentaje_alta').select();
				}
				else
				{
					document.id('sucursal').set('value', document.id('sucursal').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya esta dada de alta como sucursal');

					document.id('sucursal').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#sucursal, #nombre_sucursal').set('value', '');

		document.id('sucursal').select();
	}
}

var do_modificar_sucursal = function()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: {
			accion: 'do_modificar_sucursal',
			id_matriz: sucursal.id_matriz,
			id_sucursal: sucursal.id_sucursal,
			porcentaje_matriz: sucursal.porcentaje_matriz + sucursal.porcentaje_sucursal - document.id('porcentaje_mod').get('value').getNumericValue(),
			porcentaje_sucursal: document.id('porcentaje_mod').get('value').getNumericValue()
		},
		onRequest: function()
		{
			boxModificarSucursal.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var do_baja_matriz = function()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: {
			accion: 'do_baja_matriz',
			matriz: matriz.matriz
		},
		onRequest: function()
		{
			boxBajaMatriz.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var do_baja_sucursal = function()
{
	new Request(
	{
		url: 'PorcentajesVentasSucursales.php',
		data: {
			accion: 'do_baja_sucursal',
			id_matriz: sucursal.id_matriz,
			id_sucursal: sucursal.id_sucursal,
			porcentaje: sucursal.porcentaje_sucursal
		},
		onRequest: function()
		{
			boxBajaSucursal.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}
