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

	box = new mBox.Modal({
		id: 'box',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {

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
		onBoxReady: function() {
		},
		onOpenComplete: function() {
		}
	});

	boxFailure = new mBox.Modal({
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

var inicio = function () {
	new Request({
		url: 'AguaPredialCatalogo.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('cias').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
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

var consultar = function () {
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}

	new Request({
		url: 'AguaPredialCatalogo.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			document.id('alta').addEvent('click', alta);

			$$('img[id=mod]').each(function(el) {
				var id = el.get('alt');

				el.addEvent('click', modificar.pass(id));

				el.removeProperty('alt');
			});

			$$('img[id=baja]').each(function(el) {
				var id = el.get('alt');

				el.addEvent('click', do_baja.pass(id));

				el.removeProperty('alt');
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
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

var obtener_pro = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=obtener_pro&num_pro=' + document.id('num_pro').get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
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

var obtener_arrendador = function()
{
	if (document.id('arrendador').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=obtener_arrendador&arrendador=' + document.id('arrendador').get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
				if (result != '')
				{
					var data = JSON.decode(result);

					document.id('idarrendador').set('value', data.id);
					document.id('nombre_arrendador').set('value', data.nombre);
				}
				else
				{
					document.id('arrendador').set('value', document.id('arrendador').retrieve('tmp', ''));

					alert('La inmobiliaria no está en el catálogo');

					document.id('arrendador').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#idarrendador, #num_pro, #nombre_pro').set('value', '');
	}
}

var alta = function(num_cia) {
	new Request({
		url: 'AguaPredialCatalogo.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('alta_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_pro').select();
					}
				}
			});

			document.id('num_pro').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_pro();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cuenta').select();
					}
				}
			});

			document.id('cuenta').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('arrendador').select();
					}
				}
			});

			document.id('arrendador').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_arrendador();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia').select();
					}
				}
			});

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('alta').addEvent('click', do_alta);

			boxProcessing.close();

			document.id('num_cia').fireEvent('change').select();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar la compañía');

		document.id('num_cia').focus();
	}
	else if (document.id('num_pro').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el proveedor');

		document.id('num_pro').focus();
	}
	else if (document.id('cuenta').get('value').clean().trim() == '')
	{
		alert('Debe especificar la cuenta');

		document.id('cuenta').focus();
	}
	else if (document.id('arrendador').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar al propietario (inmobiliaria)');

		document.id('arrendador').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_form').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
			},
			onSuccess: function(result) {
				var data = JSON.decode(result);

				if (data.status == 1)
				{
					consultar(param);
				}
				else if (data.status == -1)
				{
					alert('Ha ocurrido un error');

					document.id('num_cia').select();

					boxProcessing.close();
				}
			}
		}).send();
	}
}

var modificar = function(id) {
	new Request({
		url: 'AguaPredialCatalogo.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('modificar_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_pro').select();
					}
				}
			});

			document.id('num_pro').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_pro();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cuenta').select();
					}
				}
			});

			document.id('cuenta').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('arrendador').select();
					}
				}
			});

			document.id('arrendador').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_arrendador();
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia').select();
					}
				}
			});

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();

			document.id('num_cia').select();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar la compañía');

		document.id('num_cia').focus();
	}
	else if (document.id('num_pro').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el proveedor');

		document.id('num_pro').focus();
	}
	else if (document.id('cuenta').get('value').clean().trim() == '')
	{
		alert('Debe especificar la cuenta');

		document.id('cuenta').focus();
	}
	else if (document.id('arrendador').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar al propietario (inmobiliaria)');

		document.id('arrendador').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=do_modificar&' + document.id('modificar_form').toQueryString(),
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
	if (confirm('¿Desea borrar el pago seleccionado?')) {
		new Request({
			url: 'AguaPredialCatalogo.php',
			data: 'accion=do_baja&id=' + id,
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
