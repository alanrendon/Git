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

	boxImportar = new mBox.Modal({
		id: 'box_importar',
		title: '<img src="/lecaroz/iconos/download.png" width="16" height="16" /> Importar archivo CSV',
		content: 'importar_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: do_importar
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
		onOpen: function() {
			document.id('file').set('value', '');
		}
	});

	boxAlert = new mBox.Modal({
		id: 'box_alert',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" />',
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
		closeInTitle: true
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

var obtener_pro = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ReferenciasBancariasCatalogo.php',
			data: {
				accion: 'obtener_pro',
				num_pro: document.id('num_pro').get('value')
			},
			onRequest: function() {},
			onSuccess: function(request)
			{
				if (request != '')
				{
					document.id('nombre_pro').set('value', request);
				}
				else
				{
					document.id('num_pro').set('value', document.id('num_pro').retrieve('tmp', ''));

					alert('El proveedor no esta en el catálogo');

					document.id('num_pro').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_pro, #nombre_pro').set('value', '');
	}
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ReferenciasBancariasCatalogo.php',
			data: {
				accion: 'obtener_cia',
				num_cia: document.id('num_cia').get('value'),
				num_pro: document.id('num_pro').get('value')
			},
			onRequest: function() {},
			onSuccess: function(request)
			{
				if (request != '')
				{
					document.id('nombre_cia').set('value', request);
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo o ya tiene una referencia asignada');

					document.id('num_cia').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia, #nombre_cia').set('value', '');
	}
}

var inicio = function () {
	new Request({
		url: 'ReferenciasBancariasCatalogo.php',
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

			document.id('num_pro').addEvents({
				change: obtener_pro,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
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
		if (document.id('num_pro').get('value').getNumericValue() == 0)
		{
			alert('Debe especificar el proveedor');

			document.id('num_pro').focus();

			return false;
		}

		param = document.id('inicio').toQueryString();
	}

	new Request({
		url: 'ReferenciasBancariasCatalogo.php',
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

			document.id('importar').addEvent('click', importar);

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'ReferenciasBancariasCatalogo.php',
		data: {
			accion: 'alta',
			num_pro: document.id('num_pro').get('value')
		},
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
				change: obtener_cia,
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('referencia').select();
					}
				}
			}).focus();

			document.id('referencia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia').focus();
					}
				}
			});

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('alta').addEvent('click', do_alta);

			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');

		document.id('num_cia').focus();
	} else if (document.id('referencia').get('value').clean() == '') {
		alert('Debe especificar la referencia');

		document.id('referencia').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ReferenciasBancariasCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_form').toQueryString(),
			onRequest: function() {
				boxProcessing.open();

				document.id('captura').empty();
			},
			onSuccess: function() {
				consultar(param);
			}
		}).send();
	}
}

var modificar = function(id) {
	new Request({
		url: 'ReferenciasBancariasCatalogo.php',
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

			document.id('referencia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
						this.select();
					}
				}
			}).select();

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('referencia').get('value').clean() == '') {
		alert('Debe especificar la referencia');

		document.id('referencia').focus();
	} else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ReferenciasBancariasCatalogo.php',
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
	if (confirm('¿Desea borrar la referencia seleccionada?')) {
		new Request({
			url: 'ReferenciasBancariasCatalogo.php',
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

var importar = function() {
	boxImportar.open();
}

var do_importar = function() {
	if (document.id('file').get('value') == '')
	{
		boxImportar.close();

		boxFailure.setContent('Debe seleccionar un archivo CSV para importaci&oacute;n').open();
	}
	else
	{
		var request = new Request.File({
			url: 'ReferenciasBancariasCatalogo.php',
			onRequest: function()
			{
				boxImportar.close();

				boxProcessing.open();
			},
			onSuccess: function(request)
			{
				var result = JSON.decode(request);

				consultar(param);

				boxProcessing.close();

				if ( !! result.importados)
				{
					boxAlert.setContent('Registros importados: ' + result.importados).open();
				}

			}
		});

		request.append('accion', 'importar');
		request.append('num_pro', document.id('num_pro').get('value'));
		request.append('file', document.id('file').files[0]);

		request.send();
	}
}
