id_fac = null;
tipo_fac = null;

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

	boxDetalle = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Detalle',
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
		closeInTitle: true,
	});

	boxCancelarFactura = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/cancel_round.png" width="16" height="16" /> Cancelar factura',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_cancelar_factura();
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
	});

	boxViewCFD = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Comprobante Fiscal Digital',
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
		closeInTitle: true,
	});

	boxCargaConta = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Carga de contabilidad',
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
		closeInTitle: true,
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

	inicio();

});

var inicio = function ()
{
	new Request({
		url: 'FacturasPagadasOtrosEjercicios.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').empty().set('html', result);

			new FormValidator(document.id('inicio'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('cias').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('pros').select();
					}
				}
			});

			document.id('pros').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('gastos').select();
					}
				}
			});

			document.id('gastos').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('anio').select();
					}
				}
			});

			document.id('anio').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').select();
					}
				}
			});

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();

			document.id('cias').focus();
		}
	}).send();
}

var consultar = function()
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
		url: 'FacturasPagadasOtrosEjercicios.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			if (result != '')
			{
				document.id('captura').empty().set('html', result);

				$$('a[id=detalle]').each(function(el, i)
				{
					var data = JSON.decode(el.get('alt'));

					el.addEvent('click', function()
					{
						detalle(data.id, data.tipo);
					});

					el.removeProperty('alt');

				});

				document.id('regresar').addEvent('click', inicio);

				// document.id('listado').addEvent('click', listado);

				// document.id('exportar').addEvent('click', exportar);

				boxProcessing.close();
			}
			else
			{
				inicio();

				boxProcessing.close();

				alert('No hay resultados');
			}
		}
	}).send();
}

var detalle = function(id, tipo)
{
	new Request({
		url: 'FacturasPagadasOtrosEjercicios.php',
		data: 'accion=detalle&id=' + id + '&tipo=' + tipo,
		onRequest: function() {},
		onSuccess: function(result)
		{
			boxDetalle.setContent(result).open();
		}
	}).send();
}

var cancelar_factura = function(id, tipo)
{
	new Request({
		'url': 'FacturasPagadasOtrosEjercicios.php',
		'data': 'accion=obtener_datos_fac&id=' + id + '&tipo=' + tipo,
		'onRequest': function() {},
		'onSuccess': function(response) {
			if (response != '')
			{
				id_fac = id;
				tipo_fac = tipo;

				var data = JSON.decode(response);

				boxCancelarFactura.setContent('<p>¿Desea cancelar la factura <strong>' + data.num_fact + '</strong> del proveedor <strong>' + data.num_pro + ' ' + data.nombre_pro + '</strong> por un importe de <strong>' + data.importe.toFloat().numberFormat(2, '.', ',') + '</strong>?</p>').open();
			}
		}
	}).send();
}

var do_cancelar_factura = function(id)
{
	new Request({
		'url': 'FacturasPagadasOtrosEjercicios.php',
		'data': 'accion=cancelar_factura&id=' + id_fac + '&tipo=' + tipo_fac,
		'onRequest': function() {
			boxCancelarFactura.close();

			boxProcessing.open();
		},
		'onSuccess': function() {
			consultar(param);
		}
	}).send();
}

var visualizar_cfd = function(id)
{
	boxViewCFD.setContent('<iframe frameborder="0" width="800" height="400" src="FacturasPagadasOtrosEjercicios.php?accion=visualizar_cfd&id=' + id + '"></iframe>').open();
}

var imprimir_cfd = function(id) {
	new Request({
		'url': 'FacturasPagadasOtrosEjercicios.php',
		'data': 'accion=imprimir_cfd&id=' + id,
		'onRequest': function() {
			boxProcessing.open();
		},
		'onSuccess': function() {
			boxProcessing.close();
		}
	}).send();
}

var descargar_cfd = function(id) {
	var url = 'obtenerCFDProveedor.php';
	var data = '?id=' + id;
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
	var win = window.open(url + data, 'CFDdownload', opt);
}

var cargar_conta = function() {
	if ( ! ($$('input[id^=id_]:checked').length > 0))
	{
		alert('Debe seleccionar al menos un registro para carga de contabilidad');

		return false;
	}

	new Request({
		'url': 'FacturasPagadasOtrosEjercicios.php',
		'type': 'POST',
		'data': {
			accion: 'cargar_conta',
			ids: $$('input[id^=id_]:checked').get('value')
		},
		'onRequest': function() {
			boxProcessing.open();
		},
		'onSuccess': function(result) {
			boxProcessing.close();

			boxCargaConta.setContent('<pre style="width:800px; height:400px; overflow:auto;">' + result + '</pre>').open();
		}
	}).send();
}

// var listado = function() {
// 	var url = 'FacturasPagadasOtrosEjercicios.php',
// 		url_param = '?accion=listado&' + param,
// 		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
// 		win;

// 	win = window.open(url + url_param, 'listado', opt);

// 	win.focus();
// }

// var exportar = function() {
// 	var url = 'FacturasPagadasOtrosEjercicios.php',
// 		url_param = '?accion=exportar&' + param,
// 		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=10,height=10',
// 		win;

// 	win = window.open(url + url_param, 'exportar', opt);

// 	win.focus();
// }
