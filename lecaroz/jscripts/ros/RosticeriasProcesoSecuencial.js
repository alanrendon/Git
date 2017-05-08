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

	boxAlert = new mBox.Modal(
	{
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n',
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
		closeInTitle: true,
		zIndex: 99999,
		onBoxReady: function() {},
		onOpenComplete: function() {}
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

	// Ventanas modales

	boxPreciosCompra = new mBox.Modal(
	{
		id: 'box_precios_compra',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Precios de compra',
		content: '',
		buttons: [
			{ title: 'Cerrar' },
			{
				title: 'Actualizar',
				event: function() {
					var ok = true;

					$$('input[id=precio_compra]').each(function(el, i)
					{
						if ( ! ok)
						{
							return false;
						}

						if (ok && el.get('value').getNumericValue() == 0)
						{
							boxAlert.setContent('Debe de definir el precio de compra').open();

							el.select();

							ok = false;
						}
					});

					if (ok)
					{
						actualizar_precios_compra();
					}
				}
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
		closeInTitle: false,
		onBoxReady: function() {},
		onOpenComplete: function() {
			$$('input[id=precio_compra]')[0].select();
		}
	});

	boxAgregarPrecioCompra = new mBox.Modal(
	{
		id: 'box_agregar_precio_compra',
		title: '<img src="/lecaroz/iconos/plus_round.png" width="16" height="16" /> Agregar precio de compra',
		content: 'agregar_precio_compra_wrapper',
		buttons: [
			{
				title: 'Cerrar',
				event: function() {
					boxAgregarPrecioCompra.close();

					boxPreciosCompra.open();
				}
			},
			{
				title: 'Actualizar',
				event: function() {
					agregar_precio_compra();
				}
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
		closeInTitle: false,
		onBoxReady: function() {
			new FormValidator(document.id('agregar_precio_compra_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nuevo_codmp_compra').addEvents(
			{
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_mp_catalogo(this, document.id('nuevo_nombre_mp_compra'));
					}
				},
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nuevo_num_pro_compra').select();
					}
				}
			});

			document.id('nuevo_num_pro_compra').addEvents(
			{
				change: function()
				{
					if (this.get('value').getNumericValue() >= 0)
					{
						obtener_pro_catalogo(this, document.id('nuevo_nombre_pro_compra'));
					}
				},
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nuevo_precio_compra').select();
					}
				}
			});

			document.id('nuevo_precio_compra').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('nuevo_codmp_compra').select();
					}
				}
			});
		},
		onOpenComplete: function() {
			$$('#nuevo_codmp_compra, #nuevo_nombre_mp_compra, #nuevo_nombre_pro_compra, #nuevo_num_pro_compra, #nuevo_precio_compra').set('value', '');

			document.id('nuevo_codmp_compra').select();
		}
	});

	boxModificarPrecioVenta = new mBox.Modal(
	{
		id: 'box_modificar_precio_venta',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Modificar precio de venta',
		content: 'modificar_precio_venta_wrapper',
		buttons: [
			{ title: 'Cerrar' },
			{
				title: 'Actualizar',
				event: function() {
					if (document.id('precio_venta').get('value').getNumericValue() == 0)
					{
						boxAlert.setContent('Debe de definir el precio de venta').open();

						return false;
					}

					actualizar_precio_venta();
				}
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
		closeInTitle: false,
		onBoxReady: function() {
			new FormValidator(document.id('modificar_precio_venta_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('precio_venta').addEvents(
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
		onOpenComplete: function() {
			document.id('precio_venta').select();
		}
	});

	boxCantidadGas = new mBox.Modal(
	{
		id: 'box_cantidad_gas',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Gas comprado',
		content: 'cantidad_gas_wrapper',
		buttons: [
			{
				title: 'Aceptar',
				event: function() {
					if (document.id('cantidad_gas_input').get('value').getNumericValue() > 0)
					{
						$$('input[id=g_cantidad]')[document.id('row_gasto').get('value').getNumericValue()].set('value', document.id('cantidad_gas_input').get('value'));

						this.close();
					}
				}
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
		closeInTitle: false,
		onBoxReady: function() {
			new FormValidator(document.id('cantidad_gas_form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('cantidad_gas_input').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
						this.focus();
					}
				}
			});
		},
		onOpenComplete: function() {
			document.id('cantidad_gas_input').select();
		}
	});

	boxAgregarPrestamoEmpleado = new mBox.Modal({
		id: 'box_nuevo_prestamo',
		title: 'Agregar empleado para prestamo',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Agregar',
				event: function() {
					if ($$('input[name=empleado]:checked').length > 0) {
						agregar_prestamo_empleado($$('input[id=p_id_emp]').length, JSON.decode($$('input[name=empleado]:checked')[0].get('value')));
					}

					this.close();
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
	});

	boxListaEmpleados = new mBox.Modal({
		id: 'box_lista_empleados',
		title: 'Lista de empleados',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Agregar',
				event: function() {
					if ($$('input[name=empleado]:checked').length > 0) {
						asociar_movimiento_prestamo(document.id('pres_row_index').get('value'), JSON.decode($$('input[name=empleado]:checked')[0].get('value')));
					}

					this.close();
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
	});

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
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
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
					}
				}
			}).focus();

			document.id('siguiente').addEvent('click', validar_fecha);

			boxProcessing.close();
		}
	}).send();
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_cia',
				num_cia: document.id('num_cia').get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result);

					document.id('num_cia').focus();

					validar_fecha();
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

var obtener_mp_catalogo = function(cod, nombre)
{
	if (cod.get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_mp_catalogo',
				codmp: cod.get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					nombre.set('value', result);
				}
				else
				{
					cod.set('value', cod.retrieve('tmp', ''));

					alert('El producto no está en el catálogo');

					cod.focus();
				}
			}
		}).send();
	}
	else
	{
		cod.set('value', '');
		nombre.set('value', '');
	}
}

var obtener_pro_catalogo = function(cod, nombre)
{
	if (cod.get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_pro',
				num_pro: cod.get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					nombre.set('value', result);
				}
				else
				{
					cod.set('value', cod.retrieve('tmp', ''));

					alert('El proveedor no está en el catálogo');

					cod.focus();
				}
			}
		}).send();
	}
	else
	{
		cod.set('value', '');
		nombre.set('value', '');
	}
}

var obtener_pro = function(i)
{
	if ($$('input[id=cd_num_pro]')[i].get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_pro',
				num_pro: $$('input[id=cd_num_pro]')[i].get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					$$('input[id=cd_nombre_pro]')[i].set('value', result);

					obtener_mp(i, false);
				}
				else
				{
					$$('input[id=cd_num_pro]')[i].set('value', $$('input[id=cd_num_pro]')[i].retrieve('tmp', ''));

					alert('El proveedor no está en el catálogo');

					$$('input[id=cd_num_pro]')[i].focus();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=cd_num_pro]')[i].set('value', '');
		$$('input[id=cd_nombre_pro]')[i].set('value', '');
	}
}

var obtener_mp = function(i, borrar)
{
	if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_mp',
				num_cia: document.id('num_cia').get('value'),
				codmp: $$('input[id=cd_codmp]')[i].get('value'),
				num_pro: $$('input[id=cd_num_pro]')[i].get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					var data = JSON.decode(result);

					if (data.no_exi == true)
					{
						$$('input[id=cd_codmp]')[i].set('value', $$('input[id=cd_codmp]')[i].retrieve('tmp', ''));

						alert('El producto "' + data.codmp + ' ' + data.nombre + '" es un producto solo para venta, no tiene existencia');

						$$('input[id=cd_codmp]')[i].focus();

						return false;
					}

					if ($$('input[id=cd_num_pro]')[i].get('value') != '' && ! borrar && data.precio_compra == 0)
					{
						alert('No hay precio de compra del producto con el proveedor especificado');

						$$('input[id=cd_num_pro]')[i].set('value', '').focus();
						$$('input[id=cd_nombre_pro]')[i].set('value', '');
						$$('input[id=cd_num_fact]')[i].set('value', '');
					}
					else if ($$('input[id=cd_num_pro]')[i].get('value') == '' && data.precio_compra == 0)
					{
						alert('No hay precio de compra para el producto especificado');
					}

					$$('input[id=cd_nombremp]')[i].set('value', data.nombre);
					$$('input[id=cd_cantidad]')[i].removeClass('red');
					$$('input[id=cd_precio_compra]')[i].set('value', data.precio_compra.round(4));
					$$('input[id=cd_precio_inv]')[i].set('value', data.precio_inv.round(4));
					$$('input[id=cd_precio]')[i].set('value', (data.precio_compra > 0 ? data.precio_compra : 0).numberFormat(4, '.', ','));
					$$('input[id=cd_min]')[i].set('value', (data.precio_inv * 0.80).round(4));
					$$('input[id=cd_max]')[i].set('value', (data.precio_inv * 1.20).round(4));

					$$('input[id=cd_importe]')[i].set('value', '');

					if (borrar == true)
					{
						$$('input[id=cd_kilos]')[i].set('value', '');
						$$('input[id=cd_aplica_gasto]')[i].set('checked', true);
						$$('input[id=cd_num_pro]')[i].set('value', '').set('readonly', true);
						$$('input[id=cd_nombre_pro]')[i].set('value', 'COMPRAS DIRECTAS');
						$$('input[id=cd_num_fact]')[i].set('value', '').set('readonly', true);
					}

					calcular_total_compra(i);

					calcular_total_compras();

					return true;
				}
				else
				{
					$$('input[id=cd_codmp]')[i].set('value', $$('input[id=cd_codmp]')[i].retrieve('tmp', ''));

					alert('El producto no está en el catálogo');

					$$('input[id=cd_codmp]')[i].focus();

					return false;
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=cd_codmp]')[i].set('value', '');
		$$('input[id=cd_nombremp]')[i].set('value', '');
		$$('input[id=cd_cantidad]')[i].removeClass('red');
		$$('input[id=cd_precio]')[i].set('value', '');
		$$('input[id=cd_precio_inv]')[i].set('value', '');
		$$('input[id=cd_min]')[i].set('value', '');
		$$('input[id=cd_max]')[i].set('value', '');
		$$('input[id=cd_cantidad]')[i].set('value', '');
		$$('input[id=cd_kilos]')[i].set('value', '');
		$$('input[id=cd_importe]')[i].set('value', '').removeClass('red');
		$$('input[id=cd_aplica_gasto]')[i].set('checked', true);
		$$('input[id=cd_num_pro]')[i].set('value', '').set('readonly', true);
		$$('input[id=cd_nombre_pro]')[i].set('value', '');
		$$('input[id=cd_num_fact]')[i].set('value', '').set('readonly', true);

		calcular_total_compras();

		return true;
	}
}

var obtener_gasto = function(i)
{
	if ($$('input[id=g_codgastos]')[i].get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'RosticeriasProcesoSecuencial.php',
			data:
			{
				accion: 'obtener_gasto',
				codgastos: $$('input[id=g_codgastos]')[i].get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					$$('input[id=g_descripcion]')[i].set('value', result);

					if ($$('input[id=g_codgastos]')[i].get('value').getNumericValue() == 90)
					{
						document.id('row_gasto').set('value', i);
						document.id('cantidad_gas_input').set('value', $$('input[id=g_cantidad]')[i].get('value').getNumericValue() > 0 ? $$('input[id=g_cantidad]')[i].get('value').getNumericValue().numberFormat(2, '.', ',') : '');

						boxCantidadGas.open();
					}
					else
					{
						$$('input[id=g_cantidad]')[i].set('value', '');
					}
				}
				else
				{
					$$('input[id=g_codgastos]')[i].set('value', $$('input[id=g_codgastos]')[i].retrieve('tmp', ''));

					alert('El gasto no está en el catálogo');

					$$('input[id=g_codgastos]')[i].focus();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=g_codgastos]')[i].set('value', '');
		$$('input[id=g_descripcion]')[i].set('value', '');
		$$('input[id=g_cantidad]')[i].set('value', '');
	}
}

var validar_fecha = function ()
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'validar_fecha',
			num_cia: document.id('num_cia').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			if (data.status == 1)
			{
				validar_productos_venta(data.num_cia, data.fecha);
			}
			else
			{
				definir_fecha(data.num_cia);
			}
		}
	}).send();

	return true;
}

var definir_fecha = function (num_cia)
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'definir_fecha',
			num_cia: num_cia
		},
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('definir_fecha'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('fecha').addEvents(
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
			}).focus();

			document.id('cancelar').addEvent('click', inicio);

			document.id('siguiente').addEvent('click', function()
			{
				if (document.id('fecha').get('value') == '')
				{
					alert('Debe especificar la fecha de inicio de captura');

					document.id('fecha').focus();

					return false;
				}
				else if (confirm('¿Es correcta la fecha de inicio?'))
				{
					validar_productos_venta(num_cia, document.id('fecha').get('value'));
				}
			});

			boxProcessing.close();
		}
	}).send();

	return true;
}

var validar_productos_venta = function(num_cia, fecha)
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'validar_productos_venta',
			num_cia: num_cia,
			fecha: fecha
		},
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			if (result)
			{
				document.id('captura').set('html', result);

				document.id('cancelar').addEvent('click', inicio);

				boxProcessing.close();
			}
			else
			{
				proceso_secuencial(num_cia, fecha);
			}

		}
	}).send();
}

var proceso_secuencial = function(num_cia, fecha) {
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'proceso_secuencial',
			num_cia: num_cia,
			fecha: fecha
		},
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			validator = new FormValidator(document.id('proceso_secuencial'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			// Botones de cancelación

			document.id('cd_cancelar').addEvent('click', inicio);
			document.id('v_cancelar').addEvent('click', inicio);
			document.id('g_cancelar').addEvent('click', inicio);
			document.id('p_cancelar').addEvent('click', inicio);
			document.id('tan_cancelar').addEvent('click', inicio);
			document.id('t_cancelar').addEvent('click', inicio);

			// Botones de navegación entre páginas y validaciones

			document.id('cd_siguiente').addEvent('click', function()
			{
				for (var i = 0; i < $$('input[id=cd_codmp]').length; i++)
				{
					if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() > 0)
					{
						/*if ($$('input[id=cd_importe]')[i].get('value').getNumericValue() == 0)
						{
							alert('El importe debe ser mayor a cero.');

							$$('input[id=cd_cantidad]')[i].select();

							return false;
						}
						else */if ( ! $$('input[id=cd_aplica_gasto]')[i].checked && $$('input[id=cd_num_pro]')[i].get('value').getNumericValue() == 0)
						{
							alert('Debe especificar el proveedor.');

							$$('input[id=cd_num_pro]')[i].select();

							return false;
						}
						else if ( ! $$('input[id=cd_aplica_gasto]')[i].checked && $$('input[id=cd_num_fact]')[i].get('value') == '')
						{
							alert('Debe especificar el número de factura.');

							$$('input[id=cd_num_fact]')[i].select();

							return false;
						}
					}
				}

				recalcular_existencias();

				calcular_total_compras();

				calcular_totales();

				document.id('compra_directa').removeClass('show').addClass('hide');
				document.id('ventas').removeClass('hide').addClass('show');

				$$('input[id=v_cantidad]')[0].select();
			});

			document.id('v_anterior').addEvent('click', function()
			{
				document.id('ventas').removeClass('show').addClass('hide');
				document.id('compra_directa').removeClass('hide').addClass('show');

				$$('input[id=cd_codmp]')[0].select();
			});

			document.id('v_siguiente').addEvent('click', function()
			{
				for (var i = 0; i < $$('input[id=v_codmp]').length; i++)
				{
					if ($$('input[id=v_existencia]')[i].get('value').getNumericValue() < 0)
					{
						alert('La existencia del producto no puede quedar negativa.');

						$$('input[id=v_cantidad]')[i].select();

						return false;
					}
				}

				calcular_totales();

				document.id('ventas').removeClass('show').addClass('hide');
				document.id('gastos').removeClass('hide').addClass('show');

				$$('input[id=g_codgastos]')[0].select();
			});

			document.id('g_anterior').addEvent('click', function()
			{
				document.id('gastos').removeClass('show').addClass('hide');
				document.id('ventas').removeClass('hide').addClass('show');

				$$('input[id=v_cantidad]')[0].select();
			});

			document.id('g_siguiente').addEvent('click', function()
			{
				for (var i = 0; i < $$('input[id=g_codgastos]').length; i++)
				{
					if ($$('input[id=g_codgastos]')[i].get('value').getNumericValue() > 0)
					{
						if ($$('input[id=g_concepto]')[i].get('value') == '')
						{
							alert('Debe especificar el concepto del gasto.');

							$$('input[id=g_concepto]')[i].select();

							return false;
						}
						else if ($$('input[id=g_importe]')[i].get('value').getNumericValue() == 0)
						{
							alert('El importe del gasto debe ser mayor a cero.');

							$$('input[id=g_importe]')[i].select();

							return false;
						}
					}
					else if ($$('input[id=g_concepto]')[i].get('value') != '')
					{
						if ($$('input[id=g_codgastos]')[i].get('value').getNumericValue() == 0)
						{
							alert('Debe codificar el gasto.');

							$$('input[id=g_codgastos]')[i].select();

							return false;
						}
					}
				}

				calcular_totales();

				document.id('gastos').removeClass('show').addClass('hide');
				document.id('prestamos').removeClass('hide').addClass('show');

				if ($$('input[id=p_id_emp]').length > 0)
				{
					$$('input[id=p_prestamo]')[0].select();
				}
			});

			document.id('p_anterior').addEvent('click', function()
			{
				document.id('prestamos').removeClass('show').addClass('hide');
				document.id('gastos').removeClass('hide').addClass('show');

				$$('input[id=g_codgastos]')[0].select();
			});

			document.id('p_siguiente').addEvent('click', function()
			{
				if ($$('input[id=p_id_emp]').length > 0)
				{
					for (var i = 0; i < $$('input[id=p_id_emp]').length; i++)
					{
						if ($$('input[id=p_saldo_emp_final]')[i].get('value').getNumericValue() < 0)
						{
							alert('El saldo final del prestamo no puede ser menor a cero');

							$$('input[id=p_abono]')[i].select();

							return false;
						}
					}
				}

				if ($$('input[id=p_id_tmp]').length > 0)
				{
					for (var i = 0; i < $$('input[id=p_id_tmp]').length; i++)
					{
						if ($$('input[id=p_num_emp]')[i].get('value').getNumericValue() == 0)
						{
							alert('Debe codificar los movimientos provenientes de la rosticería');

							return false;
						}
					}
				}

				calcular_totales();

				document.id('prestamos').removeClass('show').addClass('hide');
				document.id('tanques_gas').removeClass('hide').addClass('show');
			});

			document.id('tan_anterior').addEvent('click', function()
			{
				document.id('tanques_gas').removeClass('show').addClass('hide');
				document.id('prestamos').removeClass('hide').addClass('show');
			});

			document.id('tan_siguiente').addEvent('click', function()
			{
				document.id('tanques_gas').removeClass('show').addClass('hide');
				document.id('totales').removeClass('hide').addClass('show');
			});

			document.id('t_anterior').addEvent('click', function()
			{
				document.id('totales').removeClass('show').addClass('hide');
				document.id('prestamos').removeClass('hide').addClass('show');

				if ($$('input[id=p_id_emp]').length > 0)
				{
					$$('input[id=p_prestamo]')[0].select();
				}
			});

			document.id('t_siguiente').addEvent('click', function()
			{
				if (confirm('¿Son correctos todos los datos?'))
				{
					new Request(
					{
						url: 'RosticeriasProcesoSecuencial.php',
						data: 'accion=registrar&' + document.id('proceso_secuencial').toQueryString(),
						onRequest: function()
						{
							boxProcessing.open();
						},
						onSuccess: function(lista)
						{
							inicio();
						}
					}).send();
				}
			});

			// Funciones de la página de compras directas

			$$('input[id=cd_codmp]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_mp(i, true);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=cd_cantidad]')[i].select();
						}
					}
				});
			});

			$$('input[id=cd_cantidad]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de producto');

							this.set('value', '');

							$$('input[id=cd_codmp]')[i].select();
						}
						else if (this.get('value').getNumericValue() >= 0)
						{
							calcular_total_compra(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=cd_kilos]')[i].select();
						}
					}
				});
			});

			$$('input[id=cd_kilos]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de producto');

							this.set('value', '');

							$$('input[id=cd_codmp]')[i].select();
						}
						if ($$('input[id=cd_cantidad]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar la cantidad');

							this.set('value', '');

							$$('input[id=cd_cantidad]')[i].select();
						}
						else if (this.get('value').getNumericValue() >= 0)
						{
							calcular_total_compra(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							if ($$('input[id=cd_aplica_gasto]')[i].get('checked'))
							{
								$$('input[id=cd_codmp]')[i < $$('input[id=cd_codmp]').length - 1 ? i + 1 : 0].select();
							}
							else
							{
								$$('input[id=cd_num_pro]')[i].select();
							}
						}
					}
				});
			});

			$$('input[id=cd_aplica_gasto]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de producto');

							this.set('checked', false);

							$$('input[id=cd_codmp]')[i].select();
						}

						/*if ($$('input[id=cd_importe]')[i].get('value').getNumericValue() <= 0)
						{
							alert('El importe debe ser mayor a cero');

							this.set('checked', false);

							$$('input[id=cd_cantidad]')[i].select();
						}
						else */if (this.checked)
						{
							$$('input[id=cd_num_pro]')[i].set({
								value: null,
								readonly: true
							});

							// var precio = $$('input[id=cd_precio_compra]')[i].get('value').getNumericValue() > 0 ? $$('input[id=cd_precio_compra]')[i].get('value').getNumericValue() : 0;

							// $$('input[id=cd_precio]')[i].set('value', precio.numberFormat(4, '.', ','));

							$$('input[id=cd_nombre_pro]')[i].set('value', 'COMPRAS DIRECTAS');

							$$('input[id=cd_num_fact]')[i].set({
								value: null,
								readonly: true
							});

							obtener_mp(i);
						}
						else
						{
							$$('input[id=cd_num_pro]')[i].set('readonly', false).set('value', '').select();
							$$('input[id=cd_nombre_pro]')[i].set('value', '');
							$$('input[id=cd_num_fact]')[i].set('readonly', false).set('value', '');

							calcular_total_compras();
						}
					}
				});
			});

			$$('input[id=cd_num_pro]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de producto');

							this.set('value', '');

							$$('input[id=cd_codmp]')[i].select();
						}

						/*if ($$('input[id=cd_importe]')[i].get('value').getNumericValue() <= 0)
						{
							alert('El importe debe ser mayor a cero');

							this.set('value', '');

							$$('input[id=cd_cantidad]')[i].select();
						}
						else */if (this.get('value').getNumericValue() >= 0)
						{
							obtener_pro(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=cd_num_fact]')[i].select();
						}
					}
				});
			});

			$$('input[id=cd_num_fact]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if ($$('input[id=cd_codmp]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de producto');

							this.set('value', '');

							$$('input[id=cd_codmp]')[i].select();
						}
						/*if ($$('input[id=cd_importe]')[i].get('value').getNumericValue() <= 0)
						{
							alert('El importe debe ser mayor a cero');

							this.set('value', '');

							$$('input[id=cd_cantidad]')[i].select();
						}
						else */if ($$('input[id=cd_num_pro]')[i].get('value').getNumericValue() <= 0)
						{
							alert('Debe ingresar el código de proveedor');

							this.set('value', '');

							$$('input[id=cd_num_pro]')[i].select();
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=cd_codmp]')[i < $$('input[id=cd_codmp]').length - 1 ? i + 1 : 0].select();
						}
					}
				});
			});

			document.id('modificar_precios_compra').addEvent('click', function()
			{
				obtener_precios_compra();
			});

			$$('input[id=cd_codmp]')[0].select();

			// Funciones de la página de ventas

			$$('input[id=v_cantidad]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							calcular_total_venta(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							if (i < $$('input[id=v_cantidad]').length - 1)
							{
								$$('input[id=v_cantidad]')[i + 1].select();
							}
							else
							{
								document.id('v_otros').select();
							}
						}
					}
				});
			});

			$$('input[id=v_precio]').each(function(el, i)
			{
				el.addEvents(
				{
					click: function()
					{
						document.id('id_precio_venta').set('value', $$('input[id=v_id]')[i].get('value'));
						document.id('index_precio_venta').set('value', i);
						document.id('nombre_producto_venta').set('text', $$('input[id=v_codmp]')[i].getParent('td').get('text'));
						document.id('precio_venta').set('value', $$('input[id=v_precio]')[i].get('value'));

						boxModificarPrecioVenta.open();
					}
				});
			});

			document.id('v_otros').addEvents({
				change: calcular_total_ventas,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						$$('input[id=v_cantidad]')[0].select();
					}
				}
			});

			// Funciones de la página de gastos

			$$('input[id=g_codgastos]').each(function(el, i)
			{
				el.addEvents(
				{
					change: obtener_gasto.pass(i),
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_gasto(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=g_concepto]')[i].select();
						}
					}
				});
			});

			$$('input[id=g_concepto]').each(function(el, i)
			{
				el.addEvents(
				{
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=g_importe]')[i].select();
						}
					}
				});
			});

			$$('input[id=g_importe]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							calcular_total_gastos(i);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=g_codgastos]')[i < $$('input[id=g_codgastos]').length - 1 ? i + 1 : 0].select();
						}
					}
				});
			});

			// Funciones de la página de prestamos a empleados

			document.id('alta_prestamo').addEvent('click', listar_empleados_rosticeria_alta);

			$$('input[id=p_prestamo]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=p_abono]')[i].get('value').getNumericValue() > 0)
							{
								alert('Sólo puede realizar prestamo o abono pero no ambos.');

								this.set('value', '').focus();
							}
							else
							{
								var index = $$('input[id=p_id_emp_tmp]').get('value').getNumericValue().indexOf($$('input[id=p_id_emp]')[i].get('value').getNumericValue());

								if (index >= 0)
								{
									if ($$('input[id=p_tipo]')[index].get('value') != 'PRESTAMO' || $$('input[id=p_importe]')[index].get('value').getNumericValue() != this.get('value').getNumericValue())
									{
										$$('input[id=p_num_emp]')[index].set('value', '');
										$$('input[id=p_id_emp_tmp]')[index].set('value', '');
									}
								}

								calcular_saldo_prestamo(i);
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=p_abono]')[i].select();
						}
					}
				});
			});

			$$('input[id=p_abono]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=p_prestamo]')[i].get('value').getNumericValue() > 0)
							{
								alert('Sólo puede realizar prestamo o abono pero no ambos.');

								this.set('value', '').focus();
							}
							else
							{
								var index = $$('input[id=p_id_emp_tmp]').get('value').getNumericValue().indexOf($$('input[id=p_id_emp]')[i].get('value').getNumericValue());

								if (index >= 0)
								{
									if ($$('input[id=p_tipo]')[index].get('value') != 'ABONO' || $$('input[id=p_importe]')[index].get('value').getNumericValue() != this.get('value').getNumericValue())
									{
										$$('input[id=p_num_emp]')[index].set('value', '');
										$$('input[id=p_id_emp_tmp]')[index].set('value', '');
									}
								}

								calcular_saldo_prestamo(i);
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							$$('input[id=p_prestamo]')[i < $$('input[id=p_abono]').length - 1 ? i + 1 : 0].select();
						}
					}
				});
			});

			$$('input[id=p_num_emp]').each(function(el, i)
			{
				el.addEvents(
				{
					focus: function()
					{
						if (this.get('value').getNumericValue() == 0)
						{
							listar_empleados_rosticeria(i);
						}
					}
				});
			});

			boxProcessing.close();
		}
	}).send();

	return true;
}

var calcular_total_compra = function(i)
{
	var cantidad = $$('input[id=cd_cantidad]')[i].get('value').getNumericValue();
	var kilos = $$('input[id=cd_kilos]')[i].get('value').getNumericValue();
	var precio = $$('input[id=cd_precio]')[i].get('value').getNumericValue();
	var importe = 0;
	var precio_inv = $$('input[id=cd_precio_inv]')[i].get('value').getNumericValue();
	var min = $$('input[id=cd_min]')[i].get('value').getNumericValue();
	var max = $$('input[id=cd_max]')[i].get('value').getNumericValue();

	if (cantidad > 0/* && precio > 0*/ && kilos > 0)
	{
		importe = kilos * precio;

		var precio_unidad = importe / cantidad;

		if (precio_unidad != 0 && (precio_unidad < min || precio_unidad > max))
		{
			var confirm_msg = 'El costo en inventario es de $' + precio_inv.numberFormat(2, '.', ',') + ' por unidad,  por lo tanto la cantidad';
			confirm_msg += '\ncapturada debería estar entre ' + (importe / max).ceil().numberFormat(2, '.', ',') +  ' y ' + (importe / min).floor().numberFormat(2, '.', ',') + ' unidades.';
			confirm_msg += '\n\nSeleccione \'Aceptar\' para dejar la cantidad ingresada (el nuevo costo en inventario sera de $' + ((precio_unidad + importe / cantidad) / 2).numberFormat(2, '.', ',') + ')';
			confirm_msg += ' o \'Cancelar\' para ajustar (' + ((importe / min + importe / max) / 2).floor().numberFormat(2, '.', ',') + ' unidades).';

			if (confirm(confirm_msg))
			{
				$$('input[id=cd_cantidad]')[i].addClass('red');
			}
			else
			{
				cantidad = ((importe / min + importe / max) / 2).floor();

				$$('input[id=cd_cantidad]')[i].set('value', cantidad.numberFormat(2, '.', ',')).removeClass('red').select();
			}
		}

		$$('input[id=cd_importe]')[i].set('value', importe.numberFormat(2, '.', ','));
	}
	else if (cantidad > 0/* && precio > 0*/ && kilos == 0)
	{
		importe = cantidad * precio;

		$$('input[id=cd_cantidad]')[i].removeClass('red')
		$$('input[id=cd_importe]')[i].set('value', importe.numberFormat(2, '.', ',')).removeClass('red');
	}
	else
	{
		$$('input[id=cd_cantidad]')[i].removeClass('red')
		$$('input[id=cd_importe]')[i].set('value', '').removeClass('red');
	}

	calcular_total_compras();
}

var calcular_total_compras = function()
{
	var total = 0;
	var compras = 0;

	total = $$('input[id=cd_importe]').get('value').getNumericValue().sum();

	document.id('cd_total').set('value', total.numberFormat(2, '.', ','));

	compras = $$('input[id=cd_importe]').map(function(el, i) {
		return $$('input[id=cd_aplica_gasto]')[i].get('checked') ? el.get('value').getNumericValue() : 0;
	}).sum();

	document.id('g_compras').set('value', compras.numberFormat(2, '.', ','));

	calcular_total_gastos();
}

var recalcular_existencias = function()
{
	var productos = [];
	var cantidades = [];

	$$('input[id=cd_codmp]').each(function(el, i)
	{
		if (el.get('value').getNumericValue() > 0 && $$('input[id=cd_cantidad]')[i].get('value').getNumericValue() > 0/* && $$('input[id=cd_importe]')[i].get('value').getNumericValue() > 0*//* && $$('input[id=cd_num_fact]')[i].get('value') != ''*/)
		{
			if (productos.indexOf(el.get('value').getNumericValue()) == -1)
			{
				productos.push(el.get('value').getNumericValue());
				cantidades.push(0);
			}

			cantidades[productos.indexOf(el.get('value').getNumericValue())] += $$('input[id=cd_cantidad]')[i].get('value').getNumericValue();
		}
	});

	$$('input[id=v_codmp]').each(function(el, i)
	{
		var index = productos.indexOf(el.get('value').getNumericValue());
		var no_existencia = $$('input[id=v_sin_existencia]')[i].get('value') == '1' ? true : false;

		$$('input[id=v_compras]')[i].set('value', index >= 0 && ! no_existencia ? cantidades[index] : 0);

		calcular_total_venta(i);
	});
}

var calcular_total_venta = function(i)
{
	var codmp = $$('input[id=v_codmp]')[i].get('value').getNumericValue();
	var no_existencia = $$('input[id=v_sin_existencia]')[i].get('value') == '1' ? true : false;
	var existencia_inicial = $$('input[id=v_existencia_inicial]')[i].get('value').getNumericValue();
	var compras = $$('input[id=v_compras]')[i].get('value').getNumericValue();
	var cantidad = $$('input[id=v_cantidad]')[i].get('value').getNumericValue();
	var precio = $$('input[id=v_precio]')[i].get('value').getNumericValue();
	var existencia = 0;
	var importe = 0;

	importe = cantidad * precio;

	$$('input[id=v_importe]')[i].set('value', importe > 0 ? importe.numberFormat(2, '.', ',') : '');

	cantidad = 0;

	$$('input[id=v_codmp]').each(function(el, j)
	{
		if (el.get('value').getNumericValue() == codmp)
		{
			cantidad += $$('input[id=v_cantidad]')[j].get('value').getNumericValue();
		}
	});

	$$('input[id=v_codmp]').each(function(el, j)
	{
		if (el.get('value').getNumericValue() == codmp)
		{
			existencia = ! no_existencia ? existencia_inicial + compras - cantidad : 0;

			$$('input[id=v_existencia]')[j].set('value', existencia != 0 ? existencia.numberFormat(0, '.', ',') : '');

			$$('input[id=v_existencia]')[j].removeClass(existencia < 0 ? 'green' : 'red').addClass(existencia < 0 ? 'red' : 'green');
		}
	});

	calcular_total_ventas();
}

var calcular_total_ventas = function()
{
	var total = $$('input[id=v_importe]').get('value').getNumericValue().sum() + document.id('v_otros').get('value').getNumericValue();

	document.id('v_total').set('value', total.numberFormat(2, '.', ','));
}

var calcular_total_gastos = function()
{
	var total = $$('input[id=g_importe]').get('value').getNumericValue().sum();
	var compras = document.id('g_compras').get('value').getNumericValue();
	var gastos = total + compras;

	document.id('g_total').set('value', total.numberFormat(2, '.', ','));
	document.id('g_gastos').set('value', gastos.numberFormat(2, '.', ','));
}

var calcular_saldo_prestamo = function(i)
{
	var saldo_inicio = $$('input[id=p_saldo_emp_inicio]')[i].get('value').getNumericValue();
	var prestamo = $$('input[id=p_prestamo]')[i].get('value').getNumericValue();
	var abono = $$('input[id=p_abono]')[i].get('value').getNumericValue();
	var saldo_final = saldo_inicio + prestamo - abono;

	$$('input[id=p_saldo_emp_final]')[i].set('value', saldo_final.numberFormat(2, '.', ','));

	calcular_total_prestamos();
}

var calcular_total_prestamos = function()
{
	var saldo_inicio = $$('input[id=p_saldo_emp_inicio]').get('value').getNumericValue().sum();
	var prestamos = $$('input[id=p_prestamo]').get('value').getNumericValue().sum();
	var abonos = $$('input[id=p_abono]').get('value').getNumericValue().sum();
	var saldo_final = $$('input[id=p_saldo_emp_final]').get('value').getNumericValue().sum();

	$$('input[id=p_total_saldo_inicio]').set('value', saldo_inicio.numberFormat(2, '.', ','));
	$$('input[id=p_total_prestamos]').set('value', prestamos.numberFormat(2, '.', ','));
	$$('input[id=p_total_abonos]').set('value', abonos.numberFormat(2, '.', ','));
	$$('input[id=p_total_saldo_final]').set('value', saldo_final.numberFormat(2, '.', ','));
}

var listar_empleados_rosticeria_alta = function()
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'obtener_lista_empleados',
			num_cia: document.id('num_cia').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(lista)
		{
			boxProcessing.close();

			if (lista != '')
			{
				boxAgregarPrestamoEmpleado.setContent(lista).open();
			}
			else
			{
				boxFailure.setContent('La rosticer&iacute;a no tiene empleados para prestamo.').open();
			}
		}
	}).send();
}

var listar_empleados_rosticeria = function(i)
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'obtener_lista_empleados',
			num_cia: document.id('num_cia').get('value'),
			no_omitir: 1,
			index: i
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(lista)
		{
			boxProcessing.close();

			if (lista != '')
			{
				boxListaEmpleados.setContent(lista).open();
			}
			else
			{
				boxFailure.setContent('La rosticer&iacute;a no tiene empleados para prestamo.').open();
			}
		}
	}).send();
}

var agregar_prestamo_empleado = function(i, empleado)
{
	var table = document.id('prestamos_sistema_table');
	var tr = new Element('tr',
	{
		id: 'prestamo_row_' + i
	}).inject(table);
	var td1 = new Element('td',
	{
		html: empleado.num_emp + ' ' + empleado.nombre_emp
	}).inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);
	var td5 = new Element('td').inject(tr);
	var td6 = new Element('td').inject(tr);

	var id_emp = new Element('input',
	{
		id: 'p_id_emp',
		name: 'p_id_emp[]',
		type: 'hidden',
		value: empleado.idempleado
	}).inject(td1);

	var saldo_inicio = new Element('input',
	{
		id: 'p_saldo_emp_inicio',
		name: 'p_saldo_emp_inicio[]',
		type: 'text',
		value: '0.00',
		class: 'bold right green',
		size: 10,
		readonly: true
	}).inject(td2);

	var prestamo = new Element('input',
	{
		id: 'p_prestamo',
		name: 'p_prestamo[]',
		type: 'text',
		size: 10,
		class: 'validate focus numberPosFormat right red',
		precision: 2
	}).inject(td3);

	var abono = new Element('input',
	{
		id: 'p_abono',
		name: 'p_abono[]',
		type: 'text',
		size: 10,
		class: 'validate focus numberPosFormat right blue',
		precision: 2
	}).inject(td4);

	var saldo_final = new Element('input',
	{
		id: 'p_saldo_emp_final',
		name: 'p_saldo_emp_final[]',
		type: 'text',
		value: '0.00',
		class: 'bold right green',
		size: 10,
		readonly: true
	}).inject(td5);

	var img = new Element('img',
	{
		src: '/lecaroz/iconos/cancel_round.png',
		id: 'cancelar_prestamo',
		class: 'icono',
		width: 16,
		height: 16
	}).addEvent('click', function()
	{
		var index = $$('input[id=p_id_emp_tmp]').get('value').getNumericValue().indexOf(empleado.idempleado);

		if (index >= 0)
		{
			$$('input[id=p_num_emp]')[index].set('value', '');
			$$('input[id=p_id_emp_tmp]')[index].set('value', '');
		}

		document.id('prestamo_row_' + i).destroy();

		calcular_total_prestamos();
	}).inject(td6);

	validator.addElementEvents(prestamo);
	validator.addElementEvents(abono);

	prestamo.addEvents({
		change: function()
		{
			if (this.get('value').getNumericValue() >= 0)
			{
				if ($$('input[id=p_abono]')[i].get('value').getNumericValue() > 0)
				{
					alert('Sólo puede realizar prestamo o abono pero no ambos.');

					this.set('value', '').focus();
				}
				else
				{
					var index = $$('input[id=p_id_emp_tmp]').get('value').getNumericValue().indexOf($$('input[id=p_id_emp]')[i].get('value').getNumericValue());

					if (index >= 0)
					{
						if ($$('input[id=p_tipo]')[index].get('value') != 'PRESTAMO' || $$('input[id=p_importe]')[index].get('value').getNumericValue() != this.get('value').getNumericValue())
						{
							$$('input[id=p_num_emp]')[index].set('value', '');
							$$('input[id=p_id_emp_tmp]')[index].set('value', '');
						}
					}

					calcular_saldo_prestamo(i);
				}
			}
		},
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				$$('input[id=p_abono]')[i].select();
			}
		}
	});

	abono.addEvents({
		change: function()
		{
			if (this.get('value').getNumericValue() >= 0)
			{
				if ($$('input[id=p_prestamo]')[i].get('value').getNumericValue() > 0)
				{
					alert('Sólo puede realizar prestamo o abono pero no ambos.');

					this.set('value', '').focus();
				}
				else
				{
					var index = $$('input[id=p_id_emp_tmp]').get('value').getNumericValue().indexOf($$('input[id=p_id_emp]')[i].get('value').getNumericValue());

					if (index >= 0)
					{
						if ($$('input[id=p_tipo]')[index].get('value') != 'ABONO' || $$('input[id=p_importe]')[index].get('value').getNumericValue() != this.get('value').getNumericValue())
						{
							$$('input[id=p_num_emp]')[index].set('value', '');
							$$('input[id=p_id_emp_tmp]')[index].set('value', '');
						}
					}

					calcular_saldo_prestamo(i);
				}
			}
		},
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				$$('input[id=p_prestamo]')[i < $$('input[id=p_abono]').length - 1 ? i + 1 : 0].select();
			}
		}
	});

	prestamo.select();
}

var asociar_movimiento_prestamo = function(i, empleado)
{
	var index = $$('input[id=p_id_emp]').get('value').getNumericValue().indexOf(empleado.idempleado);

	if (index >= 0)
	{
		$$('input[id=p_prestamo]')[index].set('value', $$('input[id=p_tipo]')[i].get('value') == 'PRESTAMO' ? $$('input[id=p_importe]')[i].get('value') : '');
		$$('input[id=p_abono]')[index].set('value', $$('input[id=p_tipo]')[i].get('value') == 'ABONO' ? $$('input[id=p_importe]')[i].get('value') : '');

		$$('input[id=p_num_emp]')[i].set('value', empleado.num_emp);
		$$('input[id=p_id_emp_tmp]')[i].set('value', empleado.idempleado);

		calcular_saldo_prestamo(index);

		return true;
	}
	else if ($$('input[id=p_tipo]')[i].get('value') == 'ABONO')
	{
		alert('No puede abonar a un empleado que no tiene prestamo.');

		return false;
	}
	else
	{
		var index = $$('input[id=p_id_emp]').length;

		agregar_prestamo_empleado(index, empleado);

		$$('input[id=p_prestamo]')[index].set('value', $$('input[id=p_importe]')[i].get('value')).set('readonly', true);
		$$('input[id=p_abono]')[index].set('readonly', true);

		$$('input[id=p_num_emp]')[i].set('value', empleado.num_emp);
		$$('input[id=p_id_emp_tmp]')[i].set('value', empleado.idempleado);

		calcular_saldo_prestamo(index);

		return true;
	}
}

var calcular_totales = function()
{
	var compras = document.id('g_compras').get('value').getNumericValue();
	var ventas = document.id('v_total').get('value').getNumericValue();
	var gastos = document.id('g_total').get('value').getNumericValue();
	var prestamos = document.id('p_total_prestamos').get('value').getNumericValue();
	var abonos = document.id('p_total_abonos').get('value').getNumericValue();
	var efectivo = ventas + abonos - compras - gastos - prestamos;

	document.id('total_compras_td').set('html', compras != 0 ? compras.numberFormat(2, '.', ',') : '&nbsp;');
	document.id('total_ventas_td').set('html', ventas != 0 ? ventas.numberFormat(2, '.', ',') : '&nbsp;');
	document.id('total_gastos_td').set('html', gastos != 0 ? gastos.numberFormat(2, '.', ',') : '&nbsp;');
	document.id('total_prestamos_td').set('html', prestamos != 0 ? prestamos.numberFormat(2, '.', ',') : '&nbsp;');
	document.id('total_abonos_td').set('html', abonos != 0 ? abonos.numberFormat(2, '.', ',') : '&nbsp;');
	document.id('total_efectivo_td').set('html', efectivo != 0 ? efectivo.numberFormat(2, '.', ',') : '&nbsp;');
}

var obtener_precios_compra = function()
{
	new Request(
	{
		url: 'RosticeriasProcesoSecuencial.php',
		data:
		{
			accion: 'obtener_precios_compra',
			num_cia: document.id('num_cia').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(precios)
		{
			boxProcessing.close();

			if (precios != '')
			{
				boxPreciosCompra.setContent(precios);

				precios_compra_validator = new FormValidator(document.id('precios_compra_form'), {
					showErrors: true,
					selectOnFocus: true
				});

				document.id('agregar_precio_compra').addEvent('click', function()
				{
					boxPreciosCompra.close();

					boxAgregarPrecioCompra.open();
				});

				$$('input[id=precio_compra]').each(function(el, i)
				{
					el.addEvents(
					{
						change: function()
						{
							var data = JSON.decode($$('input[id=precio_compra_data]')[i].get('value'));

							data.precio_compra = el.get('value').getNumericValue();

							$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));
						},
						keydown: function(e)
						{
							if (e.key == 'enter' || e.key == 'down')
							{
								e.stop();

								$$('input[id=precio_compra]')[i < $$('input[id=precio_compra]').length - 1 ? i + 1 : 0].select();
							}
							else if (e.key == 'up')
							{
								e.stop();

								$$('input[id=precio_compra]')[i > 0 ? i - 1 : $$('input[id=precio_compra]').length - 1].select();
							}
						}
					});
				});

				$$('img[id=borrar_precio_compra]').each(function(el, i)
				{
					el.addEvents(
					{
						click: function()
						{
							var data = JSON.decode($$('input[id=precio_compra_data]')[i].get('value'));

							if (data.id == null)
							{
								$$('input[id=precio_compra_data]')[i].getParent('tr').destroy();
							}
							else
							{
								if (data.status == 1)
								{
									data.status = 0;

									$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));

									el.set('src', '/lecaroz/iconos/minus_round.png');
								}
								else
								{
									data.status = 1;

									$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));

									el.set('src', '/lecaroz/iconos/accept_green.png');
								}
							}

						}
					});
				});

				boxPreciosCompra.open();
			}
			else
			{
				boxFailure.setContent('La rosticer&iacute;a no precios de compra.').open();
			}
		}
	}).send();
}

var agregar_precio_compra = function()
{
	var productos = [];
	var proveedores = [];
	var i = $$('input[id=precio_compra_data]').length;

	$$('input[id=precio_compra_data]').each(function(el, i)
	{
		var data = JSON.decode(el.get('value'));

		productos[i] = data.codmp;
		proveedores[i] = data.num_pro;
	});

	if (document.id('nuevo_codmp_compra').get('value').getNumericValue() == 0)
	{
		boxAlert.setContent('Debe especificar el producto').open();

		return false;
	}
	// else if (document.id('nuevo_num_pro_compra').get('value').getNumericValue() == 0)
	// {
	// 	boxAlert.setContent('Debe especificar el proveedor').open();

	// 	return false;
	// }
	else if (document.id('nuevo_precio_compra').get('value').getNumericValue() == 0)
	{
		boxAlert.setContent('Debe especificar el precio de compra').open();

		return false;
	}
	else if (productos.indexOf(document.id('nuevo_codmp_compra').get('value').getNumericValue()) >= 0 && proveedores[productos.indexOf(document.id('nuevo_codmp_compra').get('value').getNumericValue())] == document.id('nuevo_num_pro_compra').get('value').getNumericValue())
	{
		boxAlert.setContent('El producto para el proveedor asociado ya tiene precio de compra').open();

		return false;
	}

	var data = {
		id: null,
		num_cia: document.id('num_cia').get('value').getNumericValue(),
		codmp: document.id('nuevo_codmp_compra').get('value').getNumericValue(),
		num_pro: document.id('nuevo_num_pro_compra').get('value').getNumericValue(),
		precio_compra: document.id('nuevo_precio_compra').get('value').getNumericValue(),
		status: 1
	};

	var tr = new Element('tr').inject($$('#precios_compra_form > table > tbody')[0]);

	var td1 = new Element('td',
	{
		html: data.codmp + ' ' + document.id('nuevo_nombre_mp_compra').get('value')
	}).inject(tr);

	var td2 = new Element('td',
	{
		html: data.num_pro + ' ' + document.id('nuevo_nombre_pro_compra').get('value')
	}).inject(tr);

	var td3 = new Element('td').inject(tr);

	var input_data = new Element('input',
	{
		name: 'precio_compra_data[]',
		id: 'precio_compra_data',
		type: 'hidden',
		value: JSON.encode(data)
	}).inject(td3);

	var input_precio = new Element('input',
	{
		name: 'precio_compra[]',
		id: 'precio_compra',
		type: 'text',
		class: 'validate focus numberPosFormat right',
		precision: 2,
		size: 10,
		value: data.precio_compra.numberFormat(2, '.', ',')
	}).inject(td3);

	var td4 = new Element('td').inject(tr);

	var img = new Element('img',
	{
		src: '/lecaroz/iconos/accept_blue.png',
		id: 'borrar_precio_compra',
		'data-index': i,
		class: 'icono',
		width: 16,
		height: 16
	}).addEvents(
	{
		click: function()
		{
			var data = JSON.decode($$('input[id=precio_compra_data]')[i].get('value'));

			if (data.id == null)
			{
				$$('input[id=precio_compra_data]')[i].getParent('tr').destroy();
			}
			else
			{
				if (data.status == 1)
				{
					data.status = 0;

					$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));

					el.set('src', '/lecaroz/iconos/minus_round.png');
				}
				else
				{
					data.status = 1;

					$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));

					el.set('src', '/lecaroz/iconos/accept_green.png');
				}
			}
		}
	}).inject(td4);

	precios_compra_validator.addElementEvents(input_precio);

	input_precio.addEvents(
	{
		change: function()
		{
			var data = JSON.decode($$('input[id=precio_compra_data]')[i].get('value'));

			data.precio_compra = el.get('value').getNumericValue();

			$$('input[id=precio_compra_data]')[i].set('value', JSON.encode(data));
		},
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'down')
			{
				e.stop();

				$$('input[id=precio_compra]')[i < $$('input[id=precio_compra]').length - 1 ? i + 1 : 0].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				$$('input[id=precio_compra]')[i > 0 ? i - 1 : $$('input[id=precio_compra]').length - 1].select();
			}
		}
	});

	boxAgregarPrecioCompra.close();

	boxPreciosCompra.open();
}

var actualizar_precios_compra = function()
{
	new Request({
		url: 'RosticeriasProcesoSecuencial.php',
		data: {
			accion: 'actualizar_precios_compra',
			data: $$('input[id=precio_compra_data]').get('value')
		},
		onRequest: function() {},
		onSuccess: function()
		{
			$$('input[id=cd_codmp]').each(function(el, i)
			{
				obtener_mp(i, false);
			});

			$$('input[id=cd_cantidad]').each(function(el, i)
			{
				if (el.get('value').getNumericValue() > 0 && $$('input[id=cd_codmp]')[i].get('value').getNumericValue() > 0)
				{
					calcular_total_compra(i);
				}
			});

			calcular_total_compras();

			boxPreciosCompra.close();
		}
	}).send();
}

var actualizar_precio_venta = function()
{
	new Request({
		url: 'RosticeriasProcesoSecuencial.php',
		data: {
			accion: 'actualizar_precio_venta',
			id: document.id('id_precio_venta').get('value'),
			precio_venta: document.id('precio_venta').get('value').getNumericValue()
		},
		onRequest: function() {},
		onSuccess: function()
		{
			$$('input[id=v_precio]')[document.id('index_precio_venta').get('value').getNumericValue()].set('value', document.id('precio_venta').get('value').getNumericValue().numberFormat(2, '.', ','));

			calcular_total_venta(document.id('index_precio_venta').get('value').getNumericValue());
			calcular_total_ventas();

			boxModificarPrecioVenta.close();
		}
	}).send();
}
