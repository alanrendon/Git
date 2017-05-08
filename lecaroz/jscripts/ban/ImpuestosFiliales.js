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
		title: 'Alta de compa&ntilde;&iacute;a matriz',
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

	boxAltaFilial = new mBox.Modal(
	{
		id: 'box_alta_filial',
		title: 'Alta de compa&ntilde;&iacute;a filial',
		content: 'alta_filial_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('filial').get('value').getNumericValue() > 0)
					{
						do_alta_filial();
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
			new FormValidator(document.id('alta_filial_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('filial').addEvents(
			{
				change: obtener_filial,
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
			document.id('filial').set('value', '').fireEvent('change');
		},
		onOpenComplete: function()
		{
			document.id('filial').focus();
		}
	});

	boxBajaMatriz = new mBox.Modal(
	{
		id: 'box_baja_matriz',
		title: 'Baja de compa&ntilde;&iacute;a matriz',
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

	boxBajaFilial = new mBox.Modal(
	{
		id: 'box_baja_filial',
		title: 'Baja de compa&ntilde;&iacute;a filial',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_baja_filial();
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
		url: 'ImpuestosFiliales.php',
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

						document.id('filiales').focus();
					}
				}
			}).focus();

			document.id('filiales').addEvents(
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
		url: 'ImpuestosFiliales.php',
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

			$$('img[id=alta_filial]').each(function(el)
			{
				el.addEvent('click', function()
				{
					matriz = JSON.decode(el.get('data-matriz'));

					boxAltaFilial.open();
				});
			});

			$$('img[id=baja_matriz]').each(function(el)
			{
				el.addEvent('click', function()
				{
					matriz = JSON.decode(el.get('data-matriz'));

					boxBajaMatriz.setContent('¿Desea dar de baja la compañía matriz ' + matriz.matriz + ' ' + matriz.nombre_matriz + '?').open();
				});
			});

			$$('img[id=baja_filial]').each(function(el)
			{
				el.addEvent('click', function()
				{
					filial = JSON.decode(el.get('data-filial'));

					boxBajaFilial.setContent('¿Desea dar de baja la compañía filial ' + filial.filial + ' ' + filial.nombre_filial + '?').open();
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
		url: 'ImpuestosFiliales.php',
		data: {
			accion: 'do_alta_matriz',
			num_cia: document.id('matriz').get('value')
		},
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

var do_alta_filial = function()
{
	new Request(
	{
		url: 'ImpuestosFiliales.php',
		data: {
			accion: 'do_alta_filial',
			id_matriz: matriz.id,
			matriz: matriz.matriz,
			filial: document.id('filial').get('value')
		},
		onRequest: function()
		{
			boxAltaFilial.close();

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
			url: 'ImpuestosFiliales.php',
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

					alert('La compañía no esta en el catálogo o ya esta dada de alta como matriz o filial');

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

var obtener_filial = function()
{
	if (document.id('filial').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ImpuestosFiliales.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('filial').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_filial').set('value', result);
				}
				else
				{
					document.id('filial').set('value', document.id('filial').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya esta dada de alta como cuenta filial');

					document.id('filial').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#filial, #nombre_filial').set('value', '');

		document.id('filial').select();
	}
}

var do_baja_matriz = function()
{
	new Request(
	{
		url: 'ImpuestosFiliales.php',
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

var do_baja_filial = function()
{
	new Request(
	{
		url: 'ImpuestosFiliales.php',
		data: {
			accion: 'do_baja_filial',
			id_matriz: filial.id_matriz,
			id_filial: filial.id_filial
		},
		onRequest: function()
		{
			boxBajaFilial.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}
