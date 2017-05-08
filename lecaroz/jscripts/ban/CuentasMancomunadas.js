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

	boxAltaPrincipal = new mBox.Modal(
	{
		id: 'box_alta_principal',
		title: 'Alta de cuenta principal',
		content: 'alta_principal_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('principal').get('value').getNumericValue() > 0)
					{
						do_alta_principal();
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
			new FormValidator(document.id('alta_principal_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('principal').addEvents(
			{
				change: obtener_principal,
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
			document.id('principal').set('value', '').fireEvent('change');
		},
		onOpenComplete: function()
		{
			document.id('principal').focus();
		}
	});

	boxAltaSecundaria = new mBox.Modal(
	{
		id: 'box_alta_secundaria',
		title: 'Alta de cuenta secundaria',
		content: 'alta_secundaria_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('secundaria').get('value').getNumericValue() > 0)
					{
						do_alta_secundaria();
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
			new FormValidator(document.id('alta_secundaria_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('secundaria').addEvents(
			{
				change: obtener_secundaria,
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
			document.id('secundaria').set('value', '').fireEvent('change');
		},
		onOpenComplete: function()
		{
			document.id('secundaria').focus();
		}
	});

	boxBajaPrincipal = new mBox.Modal(
	{
		id: 'box_baja_principal',
		title: 'Baja de cuenta principal',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_baja_principal();
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

	boxBajaSecundaria = new mBox.Modal(
	{
		id: 'box_baja_secundaria',
		title: 'Baja de cuenta secundaria',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_baja_secundaria();
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
		url: 'CuentasMancomunadas.php',
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

			document.id('principales').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('secundarias').focus();
					}
				}
			}).focus();

			document.id('secundarias').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('principales').focus();
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
		url: 'CuentasMancomunadas.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			document.id('alta_principal').addEvent('click', function()
			{
				boxAltaPrincipal.open();
			});

			$$('img[id=alta_secundaria]').each(function(el)
			{
				el.addEvent('click', function()
				{
					principal = JSON.decode(el.get('data-principal'));

					boxAltaSecundaria.open();
				});
			});

			$$('img[id=baja_principal]').each(function(el)
			{
				el.addEvent('click', function()
				{
					principal = JSON.decode(el.get('data-principal'));

					boxBajaPrincipal.setContent('¿Desea dar de baja la cuenta principal ' + principal.principal + ' ' + principal.nombre_principal + '?').open();
				});
			});

			$$('img[id=baja_secundaria]').each(function(el)
			{
				el.addEvent('click', function()
				{
					secundaria = JSON.decode(el.get('data-secundaria'));

					boxBajaSecundaria.setContent('¿Desea dar de baja la cuenta secundaria ' + secundaria.secundaria + ' ' + secundaria.nombre_secundaria + '?').open();
				});
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var do_alta_principal = function()
{
	new Request(
	{
		url: 'CuentasMancomunadas.php',
		data: {
			accion: 'do_alta_principal',
			num_cia: document.id('principal').get('value')
		},
		onRequest: function()
		{
			boxAltaPrincipal.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var do_alta_secundaria = function()
{
	new Request(
	{
		url: 'CuentasMancomunadas.php',
		data: {
			accion: 'do_alta_secundaria',
			id_matriz: principal.id,
			principal: principal.principal,
			secundaria: document.id('secundaria').get('value')
		},
		onRequest: function()
		{
			boxAltaSecundaria.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var obtener_principal = function()
{
	if (document.id('principal').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'CuentasMancomunadas.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('principal').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_principal').set('value', result);

					document.id('principal').select();
				}
				else
				{
					document.id('principal').set('value', document.id('principal').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya esta dada de alta como cuenta principal o secundaria');

					document.id('principal').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#principal, #nombre_principal').set('value', '');

		document.id('principal').select();
	}
}

var obtener_secundaria = function()
{
	if (document.id('secundaria').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'CuentasMancomunadas.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('secundaria').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_secundaria').set('value', result);
				}
				else
				{
					document.id('secundaria').set('value', document.id('secundaria').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya esta dada de alta como cuenta secundaria');

					document.id('secundaria').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#secundaria, #nombre_secundaria').set('value', '');

		document.id('secundaria').select();
	}
}

var do_baja_principal = function()
{
	new Request(
	{
		url: 'CuentasMancomunadas.php',
		data: {
			accion: 'do_baja_principal',
			principal: principal.principal
		},
		onRequest: function()
		{
			boxBajaPrincipal.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}

var do_baja_secundaria = function()
{
	new Request(
	{
		url: 'CuentasMancomunadas.php',
		data: {
			accion: 'do_baja_secundaria',
			id_principal: secundaria.id_principal,
			id_secundaria: secundaria.id_secundaria
		},
		onRequest: function()
		{
			boxBajaSecundaria.close();

			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			consultar(param);
		}
	}).send();
}
