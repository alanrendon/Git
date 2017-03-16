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
		url: 'CompaniasCatalogo.php',
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
		url: 'CompaniasCatalogo.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			document.id('alta').addEvent('click', alta);

			$$('img[id=mod]').each(function(el) {
				var num_cia = el.get('alt');

				el.addEvent('click', modificar.pass(num_cia));

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

var obtener_cia = function(num_cia, nombre_cia)
{
	if (num_cia.get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'CompaniasCatalogo.php',
			data: 'accion=obtener_cia&num_cia=' + num_cia.get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
				if (result != '')
				{
					nombre_cia.set('value', result);
				}
				else
				{
					num_cia.set('value', num_cia.retrieve('tmp', ''));

					alert('La compañía no está en el catálogo');

					num_cia.focus();
				}
			}
		}).send();
	}
	else
	{
		num_cia.set('value', '');
		nombre_cia.set('value', '');
	}
}

var obtener_pro = function()
{
	if (document.id('num_proveedor').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'CompaniasCatalogo.php',
			data: 'accion=obtener_pro&num_pro=' + document.id('num_proveedor').get('value'),
			onRequest: function() {},
			onSuccess: function(result) {
				if (result != '')
				{
					document.id('nombre_proveedor').set('value', result);
				}
				else
				{
					document.id('num_proveedor').set('value', document.id('num_proveedor').retrieve('tmp', ''));

					alert('El proveedor no está en el catálogo');

					document.id('num_proveedor').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_proveedor, #nombre_proveedor').set('value', '');
	}
}

var alta = function() {
	new Request({
		url: 'CompaniasCatalogo.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('alta_cia'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('nombre').select();
					}
				}
			}).focus();

			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('nombre_corto').select();
					}
				}
			});

			document.id('nombre_corto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_proveedor').select();
					}
				}
			});

			document.id('num_proveedor').addEvents({
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

						document.id('num_cia_primaria').select();
					}
				}
			});

			document.id('num_cia_primaria').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_primaria'), document.id('nombre_cia_primaria'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia_saldos').select();
					}
				}
			});

			document.id('num_cia_saldos').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_saldos'), document.id('nombre_cia_saldos'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cia_aguinaldos').select();
					}
				}
			});

			document.id('cia_aguinaldos').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('cia_aguinaldos'), document.id('nombre_cia_aguinaldos'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia_ros').select();
					}
				}
			});

			document.id('num_cia_ros').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_ros'), document.id('nombre_cia_ros'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cia_fiscal_matriz').select();
					}
				}
			});

			document.id('cia_fiscal_matriz').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('cia_fiscal_matriz'), document.id('nombre_cia_fiscal_matriz'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('razon_social').select();
					}
				}
			});

			document.id('razon_social').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('rfc').select();
					}
				}
			});

			document.id('rfc').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('regimen_fiscal').select();
					}
				}
			});

			document.id('regimen_fiscal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('calle').select();
					}
				}
			});

			document.id('logo_cfd').addEvents({
				change: function() {
					document.id('logo_cfd').set('style', document.id('logo_cfd').options[document.id('logo_cfd').selectedIndex].get('style'))
				}

			}).fireEvent('change');

			document.id('calle').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_exterior').select();
					}
				}
			});

			document.id('no_exterior').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_interior').select();
					}
				}
			});

			document.id('no_interior').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('colonia').select();
					}
				}
			});

			document.id('colonia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('localidad').select();
					}
				}
			});

			document.id('localidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('referencia').select();
					}
				}
			});

			document.id('referencia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('municipio').select();
					}
				}
			});

			document.id('municipio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('codigo_postal').select();
					}
				}
			});

			document.id('codigo_postal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('telefono').select();
					}
				}
			});

			document.id('telefono').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('email').select();
					}
				}
			});

			document.id('email').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_imss').select();
					}
				}
			});

			document.id('no_imss').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_infonavit').select();
					}
				}
			});

			document.id('no_infonavit').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cod_gasolina').select();
					}
				}
			});

			document.id('cod_gasolina').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_cta_cia_luz').select();
					}
				}
			});

			document.id('no_cta_cia_luz').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('sub_cuenta_deudores').select();
					}
				}
			});

			document.id('sub_cuenta_deudores').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_banco').select();
					}
				}
			});

			document.id('clabe_banco').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_plaza').select();
					}
				}
			});

			document.id('clabe_plaza').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_cuenta').select();
					}
				}
			});

			document.id('clabe_cuenta').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_identificador').select();
					}
				}
			});

			document.id('clabe_identificador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_banco2').select();
					}
				}
			});

			document.id('clabe_banco2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_plaza2').select();
					}
				}
			});

			document.id('clabe_plaza2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_cuenta2').select();
					}
				}
			});

			document.id('clabe_cuenta2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_identificador2').select();
					}
				}
			});

			document.id('clabe_identificador2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cliente_cometra').select();
					}
				}
			});

			document.id('cliente_cometra').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('ref').select();
					}
				}
			});

			document.id('ref').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg').select();
					}
				}
			});

			document.id('por_bg').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo').select();
					}
				}
			});

			document.id('por_efectivo').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_1').select();
					}
				}
			});

			document.id('por_bg_1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_1').select();
					}
				}
			});

			document.id('por_efectivo_1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_2').select();
					}
				}
			});

			document.id('por_bg_2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_2').select();
					}
				}
			});

			document.id('por_efectivo_2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_3').select();
					}
				}
			});

			document.id('por_bg_3').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_3').select();
					}
				}
			});

			document.id('por_efectivo_3').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_4').select();
					}
				}
			});

			document.id('por_bg_4').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_4').select();
					}
				}
			});

			document.id('por_efectivo_4').addEvents({
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
		}
	}).send();
}

var do_alta = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el nuevo número de compañía');

		document.id('num_cia').focus();
	}
	else if (document.id('nombre').get('value').clean().trim() == '')
	{
		alert('Debe especificar el nombre de la compañía');

		document.id('nombre').focus();
	}
	else if (document.id('nombre_corto').get('value').clean().trim() == '')
	{
		alert('Debe especificar el alias (nombre corto) de la compañía');

		document.id('nombre_corto').focus();
	}
	else if (document.id('razon_social').get('value').clean().trim() == '')
	{
		alert('Debe especificar la razón social de la compañía tal y como aparece en el alta de Hacienda');

		document.id('razon_social').focus();
	}
	else if (document.id('rfc').get('value').clean().trim() == '')
	{
		alert('Debe especificar R.F.C. de la compañía tal y como aparece en el alta de Hacienda');

		document.id('rfc').focus();
	}
	else if (document.id('regimen_fiscal').get('value').clean().trim() == '')
	{
		alert('Debe especificar régimen fiscal de la compañía');

		document.id('regimen_fiscal').focus();
	}
	else if (document.id('regimen_fiscal').get('value').clean().trim() == '')
	{
		alert('Debe especificar régimen fiscal de la compañía');

		document.id('regimen_fiscal').focus();
	}
	else if (document.id('calle').get('value').clean().trim() == ''
		|| document.id('colonia').get('value').clean().trim() == ''
		|| document.id('municipio').get('value').clean().trim() == ''
		|| document.id('codigo_postal').get('value').clean().trim() == '')
	{
		alert('Debe especificar el domicilio fiscal de la compañía tal y como aparece en el alta de Hacienda');

		if (document.id('calle').get('value').clean().trim() == '')
		{
			document.id('calle').focus();
		}
		else if (document.id('colonia').get('value').clean().trim() == '')
		{
			document.id('colonia').focus();
		}
		else if (document.id('municipio').get('value').clean().trim() == '')
		{
			document.id('municipio').focus();
		}
		else if (document.id('codigo_postal').get('value').clean().trim() == '')
		{
			document.id('codigo_postal').focus();
		}
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'CompaniasCatalogo.php',
			data: 'accion=do_alta&' + document.id('alta_cia').toQueryString(),
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
					alert('El número de compañía especificado no esta disponible');

					document.id('num_cia').select();

					boxProcessing.close();
				}
			}
		}).send();
	}
}

var modificar = function(num_cia) {
	new Request({
		url: 'CompaniasCatalogo.php',
		data: 'accion=modificar&num_cia=' + num_cia,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('modificar_cia'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nombre').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('nombre_corto').select();
					}
				}
			}).focus();

			document.id('nombre_corto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_proveedor').select();
					}
				}
			});

			document.id('num_proveedor').addEvents({
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

						document.id('num_cia_primaria').select();
					}
				}
			});

			document.id('num_cia_primaria').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_primaria'), document.id('nombre_cia_primaria'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia_saldos').select();
					}
				}
			});

			document.id('num_cia_saldos').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_saldos'), document.id('nombre_cia_saldos'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cia_aguinaldos').select();
					}
				}
			});

			document.id('cia_aguinaldos').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('cia_aguinaldos'), document.id('nombre_cia_aguinaldos'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia_ros').select();
					}
				}
			});

			document.id('num_cia_ros').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('num_cia_ros'), document.id('nombre_cia_ros'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cia_fiscal_matriz').select();
					}
				}
			});

			document.id('cia_fiscal_matriz').addEvents({
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_cia(document.id('cia_fiscal_matriz'), document.id('nombre_cia_fiscal_matriz'));
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('razon_social').select();
					}
				}
			});

			document.id('razon_social').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('rfc').select();
					}
				}
			});

			document.id('rfc').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('regimen_fiscal').select();
					}
				}
			});

			document.id('regimen_fiscal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('calle').select();
					}
				}
			});

			document.id('logo_cfd').addEvents({
				change: function() {
					document.id('logo_cfd').set('style', document.id('logo_cfd').options[document.id('logo_cfd').selectedIndex].get('style'))
				}

			}).fireEvent('change');

			document.id('calle').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_exterior').select();
					}
				}
			});

			document.id('no_exterior').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_interior').select();
					}
				}
			});

			document.id('no_interior').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('colonia').select();
					}
				}
			});

			document.id('colonia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('localidad').select();
					}
				}
			});

			document.id('localidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('referencia').select();
					}
				}
			});

			document.id('referencia').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('municipio').select();
					}
				}
			});

			document.id('municipio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('codigo_postal').select();
					}
				}
			});

			document.id('codigo_postal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('telefono').select();
					}
				}
			});

			document.id('telefono').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('email').select();
					}
				}
			});

			document.id('email').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_imss').select();
					}
				}
			});

			document.id('no_imss').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_infonavit').select();
					}
				}
			});

			document.id('no_infonavit').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cod_gasolina').select();
					}
				}
			});

			document.id('cod_gasolina').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('no_cta_cia_luz').select();
					}
				}
			});

			document.id('no_cta_cia_luz').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('sub_cuenta_deudores').select();
					}
				}
			});

			document.id('sub_cuenta_deudores').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_banco').select();
					}
				}
			});

			document.id('clabe_banco').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_plaza').select();
					}
				}
			});

			document.id('clabe_plaza').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_cuenta').select();
					}
				}
			});

			document.id('clabe_cuenta').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_identificador').select();
					}
				}
			});

			document.id('clabe_identificador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_banco2').select();
					}
				}
			});

			document.id('clabe_banco2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_plaza2').select();
					}
				}
			});

			document.id('clabe_plaza2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_cuenta2').select();
					}
				}
			});

			document.id('clabe_cuenta2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('clabe_identificador2').select();
					}
				}
			});

			document.id('clabe_identificador2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cliente_cometra').select();
					}
				}
			});

			document.id('cliente_cometra').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('ref').select();
					}
				}
			});

			document.id('ref').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg').select();
					}
				}
			});

			document.id('por_bg').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo').select();
					}
				}
			});

			document.id('por_efectivo').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_1').select();
					}
				}
			});

			document.id('por_bg_1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_1').select();
					}
				}
			});

			document.id('por_efectivo_1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_2').select();
					}
				}
			});

			document.id('por_bg_2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_2').select();
					}
				}
			});

			document.id('por_efectivo_2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_3').select();
					}
				}
			});

			document.id('por_bg_3').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_3').select();
					}
				}
			});

			document.id('por_efectivo_3').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_bg_4').select();
					}
				}
			});

			document.id('por_bg_4').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('por_efectivo_4').select();
					}
				}
			});

			document.id('por_efectivo_4').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('nombre').select();
					}
				}
			});

			$$('#num_proveedor, #num_cia_primaria, #num_cia_saldos, #cia_aguinaldos, #num_cia_ros, #cia_fiscal_matriz').fireEvent('change');

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el nuevo número de compañía');

		document.id('num_cia').focus();
	}
	else if (document.id('nombre').get('value').clean().trim() == '')
	{
		alert('Debe especificar el nombre de la compañía');

		document.id('nombre').focus();
	}
	else if (document.id('nombre_corto').get('value').clean().trim() == '')
	{
		alert('Debe especificar el alias (nombre corto) de la compañía');

		document.id('nombre_corto').focus();
	}
	else if (document.id('razon_social').get('value').clean().trim() == '')
	{
		alert('Debe especificar la razón social de la compañía tal y como aparece en el alta de Hacienda');

		document.id('razon_social').focus();
	}
	else if (document.id('rfc').get('value').clean().trim() == '')
	{
		alert('Debe especificar R.F.C. de la compañía tal y como aparece en el alta de Hacienda');

		document.id('rfc').focus();
	}
	else if (document.id('regimen_fiscal').get('value').clean().trim() == '')
	{
		alert('Debe especificar régimen fiscal de la compañía');

		document.id('regimen_fiscal').focus();
	}
	else if (document.id('regimen_fiscal').get('value').clean().trim() == '')
	{
		alert('Debe especificar régimen fiscal de la compañía');

		document.id('regimen_fiscal').focus();
	}
	else if (document.id('calle').get('value').clean().trim() == ''
		|| document.id('colonia').get('value').clean().trim() == ''
		|| document.id('municipio').get('value').clean().trim() == ''
		|| document.id('codigo_postal').get('value').clean().trim() == '')
	{
		alert('Debe especificar el domicilio fiscal de la compañía tal y como aparece en el alta de Hacienda');

		if (document.id('calle').get('value').clean().trim() == '')
		{
			document.id('calle').focus();
		}
		else if (document.id('colonia').get('value').clean().trim() == '')
		{
			document.id('colonia').focus();
		}
		else if (document.id('municipio').get('value').clean().trim() == '')
		{
			document.id('municipio').focus();
		}
		else if (document.id('codigo_postal').get('value').clean().trim() == '')
		{
			document.id('codigo_postal').focus();
		}
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'CompaniasCatalogo.php',
			data: 'accion=do_modificar&' + document.id('modificar_cia').toQueryString(),
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
			url: 'CompaniasCatalogo.php',
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
