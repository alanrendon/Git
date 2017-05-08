var tipo_graficas = null;

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

	// boxReporte = new mBox.Modal(
	// {
	// 	id: 'box_reporte',
	// 	title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Reporte para imprimir',
	// 	content: 'reporte_wrapper',
	// 	buttons: [
	// 		{ title: 'Aceptar' }
	// 	],
	// 	overlay: true,
	// 	overlayStyles: {
	// 		color: 'white',
	// 		opacity: 0.8
	// 	},
	// 	draggable: true,
	// 	closeOnEsc: false,
	// 	closeOnBodyClick: false,
	// 	closeInTitle: true,
	// 	onOpenComplete: function()
	// 	{
	// 		document.id('reporte_frame').set('src', 'ProcesoPagosManual.php?accion=reporte&' + param);
	// 	},
	// 	onCloseComplete: function()
	// 	{
	// 		document.id('reporte_frame').set('src', '');
	// 	}
	// });

	inicio();

});

var inicio = function()
{
	new Request(
	{
		url: 'ProcesoPagosManual.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').empty().set('html', result);

			new FormValidator(document.id('inicio_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_pro').addEvents(
			{
				change: obtener_pro,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').select();
					}
				}
			});

			document.id('cias').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('omitir_cias').select();
					}
				}
			});

			document.id('omitir_cias').addEvents(
			{
				keydown: function(e)
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
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('omitir_pros').select();
					}
				}
			});

			document.id('omitir_pros').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha_corte').select();
					}
				}
			});

			document.id('fecha_corte').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha_pago').select();
					}
				}
			});

			document.id('fecha_pago').addEvents(
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

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();

			document.id('num_pro').focus();
		}
	}).send();
}

var obtener_pro = function()
{
	if (document.id('num_pro').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ProcesoPagosManual.php',
			data: 'accion=obtener_pro&num_pro=' + document.id('num_pro').get('value'),
			onRequest: function() {},
			onSuccess: function(response)
			{
				if (response != '')
				{
					document.id('nombre_pro').set('value', response);
				}
				else
				{
					document.id('num_pro').set('value', document.id('num_pro').retrieve('tmp'));

					alert('El código de proveedor no se encuentra en el catálogo.');

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

var obtener_next = function()
{
	if (document.id('next').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ProcesoPagosManual.php',
			data: 'accion=obtener_pro&num_pro=' + document.id('next').get('value'),
			onRequest: function() {},
			onSuccess: function(response)
			{
				if (response != '')
				{
					document.id('nombre_next').set('value', response);
				}
				else
				{
					document.id('num_next').set('value', document.id('num_next').retrieve('tmp'));

					alert('El código de proveedor no se encuentra en el catálogo.');

					document.id('num_next').select();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_next, #nombre_next').set('value', '');
	}
}

var consultar = function()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		if (document.id('fecha_corte').get('value') == '')
		{
			alert('Debes especificar la fecha de corte');

			document.id('fecha_corte').focus();

			return false;
		}
		if (document.id('fecha_pago').get('value') == '')
		{
			alert('Debes especificar la fecha de pago');

			document.id('fecha_pago').focus();

			return false;
		}

		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'ProcesoPagosManual.php',
		data: 'accion=consulta&' + param + ( !! document.id('current') ? '&current=' + document.id('current').get('value') : '') +  ( !! document.id('next') ? '&next=' + document.id('next').get('value') : ''),
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

				document.id('check_all').addEvent('change', function()
				{
					var checked = this.get('checked');

					$$('input[id^=check_cia_]').set('checked', checked)
						.fireEvent('change');
				});

				$$('input[id^=check_cia_]').addEvent('change', function()
				{
					var num_cia = this.get('value');
					var checked = this.get('checked');

					$$('input[id^=factura][data-cia=' + num_cia + ']:enabled').set('checked', checked)
						.getParent('tr')
						.removeClass(checked ? 'unchecked' : 'checked')
						.addClass(checked ? 'checked' : 'unchecked');

					calcular_totales(num_cia);
				});

				$$('input[id^=factura]').addEvent('change', function()
				{
					var num_cia = this.get('data-cia');
					var checked = this.get('checked');

					this.getParent('tr')
						.removeClass(checked ? 'unchecked' : 'checked')
						.addClass(checked ? 'checked' : 'unchecked');

					calcular_totales(num_cia);
				});

				$$('select[id^=num_cia_pago]').addEvent('change', function()
				{
					var num_cia = this.get('data-cia');

					cambiar_cia_pago(num_cia);
				});

				document.id('next').addEvents(
				{
					change: obtener_next,
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							this.blur();
						}
					}
				});

				document.id('salir').addEvent('click', salir);

				document.id('pagar_salir').addEvent('click', function()
				{
					validar('salir');
				});

				document.id('siguiente').addEvent('click', function()
				{
					validar('siguiente');
				});

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

var cambiar_cia_pago = function(sucursal)
{
	var matriz = $$('input[id^=factura][data-cia=' + sucursal + ']')[0].get('data-cia-pago');

	$$('input[id^=factura][data-cia=' + sucursal + ']').set('data-cia-pago', document.id('num_cia_pago_' + sucursal).get('value')).each(function(el)
	{
		var data = JSON.decode(el.get('value'));

		data.num_cia_pago = document.id('num_cia_pago_' + sucursal).get('value').getNumericValue();

		el.set('value', JSON.encode(data));
	});

	calcular_totales(sucursal);
	calcular_totales(matriz);
}

var calcular_totales = function(sucursal)
{
	var matriz = sucursal;

	if ( !! document.id('num_cia_pago_' + sucursal))
	{
		matriz = document.id('num_cia_pago_' + sucursal).get('value');
	}

	var saldo_inicio = !! document.id('saldo_inicio_' + matriz) ? document.id('saldo_inicio_' + matriz).get('value').getNumericValue() : 0;
	var total_pago = $$('input[id^=factura][data-cia-pago=' + matriz + ']:checked').get('data-importe').getNumericValue().sum();
	var total_pago_matriz = $$('input[id^=factura][data-cia=' + matriz + ']:checked').get('data-importe').getNumericValue().sum();
	var total_pago_sucursal = $$('input[id^=factura][data-cia=' + sucursal + ']:checked').get('data-importe').getNumericValue().sum();
	var total_pago_sucursales = total_pago - total_pago_matriz;
	var saldo_final = saldo_inicio - total_pago;

	if ( !! document.id('total_pago_' + sucursal))
	{
		document.id('total_pago_' + sucursal).set('html', total_pago_sucursal.numberFormat(2, '.', ','));
	}

	if ( !! document.id('total_pago_otras_cias_' + matriz))
	{
		document.id('total_pago_otras_cias_' + matriz).set('html', total_pago_sucursales.numberFormat(2, '.', ','));
	}

	$$('#saldo_final_' + matriz + ' > span').set('html', saldo_final.numberFormat(2, '.', ',')).removeClass(saldo_final > 0 ? 'red' : 'blue').addClass(saldo_final > 0 ? 'blue' : 'red');
}

var validar = function(accion)
{
	if ($$('input[id^=factura]:enabled:checked').length > 0)
	{
		if (accion == 'salir')
		{
			if (confirm('¿Deseas pagar las facturas seleccionadas y terminar el programa?'))
			{
				pagar(salir);

				return true;
			}
			else if (confirm('¿Deseas terminar el programa?'))
			{
				salir();
			}
		}
		else if (accion == 'siguiente')
		{
			if (confirm('¿Deseas pagar las facturas seleccionadas y pasar al siguiente proveedor?'))
			{
				pagar(siguiente);

				return true;
			}
			else if (confirm('¿Deseas pasar al siguiente proveedor?'))
			{
				siguiente();
			}
		}
	}
	else
	{
		if (accion == 'salir')
		{
			if (confirm('¿Deseas terminar el programa?'))
			{
				salir();
			}
		}
		else if (accion == 'siguiente')
		{
			if (confirm('¿Deseas pasar al siguiente proveedor?'))
			{
				siguiente();
			}
		}
	}
}

var pagar = function(accion)
{
	new Request(
	{
		url: 'ProcesoPagosManual.php',
		data: 'accion=pagar&' + document.id('consulta_form').toQueryString(),
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(response)
		{
			siguiente();

			boxProcessing.close();
		}
	}).send();
}

var salir = function()
{
	inicio();
}

var siguiente = function()
{
	consultar(param);
}
