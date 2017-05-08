window.addEvent('domready', function()
{

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
		closeInTitle: true,
		onBoxReady: function()
		{
		},
		onOpenComplete: function()
		{
		}
	});

	boxDepositoModificar = new mBox.Modal({
		id: 'box_deposito_modificar',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Modificar dep&oacute;sito',
		content: 'modificar_deposito_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_modificar_deposito();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('modificar_deposito'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('fecha_deposito').addEvents({
				change: function()
				{
					validar_periodo_deposito();
				},
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
		onOpenComplete: function()
		{
			document.id('fecha_deposito').select();
		}
	});

	boxDepositoDividir = new mBox.Modal({
		id: 'box_deposito_dividir',
		title: '<img src="/lecaroz/iconos/money_cut.png" width="16" height="16" /> Dividir dep&oacute;sito',
		content: 'dividir_deposito_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_dividir_deposito();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('dividir_deposito'), {
				showErrors: true,
				selectOnFocus: true
			});

			$$('input[id=importe_deposito_dividir]').each(function(el, i, array)
			{
				el.addEvents({
					change: function()
					{
						if (el.get('value').getNumericValue() > 0)
						{
							calcular_resto_deposito_dividido();
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'down')
						{
							e.stop();

							if (i < array.length - 1)
							{
								array[i + 1].select();
							} else {
								array[0].select();
							}
						} else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								array[i - 1].select();
							} else {
								array[array.length - 1].select();
							}
						}
					}
				});
			});
		},
		onOpenComplete: function()
		{
			document.id('importe_deposito_dividir').focus();
		}
	});

	boxDepositoCambiar = new mBox.Modal({
		id: 'box_deposito_cambiar',
		title: '<img src="/lecaroz/iconos/refresh.png" width="16" height="16" /> Acreditar dep&oacute;sito a otra compa&ntilde;&iacute;a',
		content: 'cambiar_deposito_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_cambiar_deposito();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('cambiar_deposito'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia_sec').addEvents({
				change: obtener_cia_sec,
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
		onOpenComplete: function()
		{
			document.id('num_cia_sec').focus();
		}
	});

	boxDepositoCarta = new mBox.Modal({
		id: 'box_deposito_carta',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Carta de bonificaci&oacute;n de dep&oacute;sito',
		content: 'carta_deposito_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_carta_deposito();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('carta_deposito'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia_destino').addEvents({
				change: obtener_cia_destino,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('contacto').focus();
					}
				}
			});

			document.id('contacto').addEvents({
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('num_cia_destino').select();
					}
				}
			});
		},
		onOpenComplete: function()
		{
			document.id('num_cia_destino').focus();
		}
	});

	boxDepositoCometra = new mBox.Modal({
		id: 'box_deposito_cometra',
		title: '<img src="/lecaroz/iconos/article.png" width="16" height="16" /> Ficha de Cometra de cambio de dep&oacute;sito',
		content: 'cometra_deposito_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_cometra_deposito();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('cometra_deposito'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia_destino_cometra').addEvents({
				change: obtener_cia_destino_cometra,
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
		onOpenComplete: function()
		{
			document.id('num_cia_destino_cometra').focus();
		}
	});

	boxOficinaModificar = new mBox.Modal({
		id: 'box_oficina_modificar',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Modificar dep&oacute;sito de oficina',
		content: 'modificar_oficina_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_modificar_oficina();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('modificar_oficina'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia_oficina').addEvents({
				change: obtener_cia_oficina,
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha_oficina').select();
					}
				}
			});

			document.id('fecha_oficina').addEvents({
				change: function()
				{
					validar_periodo_oficina();
				},
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('num_cia_oficina').select();
					}
				}
			});
		},
		onOpenComplete: function()
		{
			document.id('fecha_oficina').select();
		}
	});

	boxOficinaDividir = new mBox.Modal({
		id: 'box_oficina_dividir',
		title: '<img src="/lecaroz/iconos/money_cut.png" width="16" height="16" /> Dividir dep&oacute;sito de oficina',
		content: 'dividir_oficina_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_dividir_oficina();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('dividir_oficina'), {
				showErrors: true,
				selectOnFocus: true
			});

			$$('input[id=fecha_oficina_dividir]').each(function(el, i, array)
			{
				el.addEvents({
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=importe_oficina_dividir]')[i].select();
						} else if (e.key == 'down')
						{
							e.stop();

							if (i < array.length - 1)
							{
								array[i + 1].select();
							} else {
								array[0].select();
							}
						} else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								array[i - 1].select();
							} else {
								array[array.length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=importe_oficina_dividir]').each(function(el, i, array)
			{
				el.addEvents({
					change: function()
					{
						if (el.get('value').getNumericValue() != 0)
						{
							calcular_resto_oficina_dividido();
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							if (i < array.length - 1)
							{
								$$('input[id=fecha_oficina_dividir]')[i + 1].select();
							} else {
								$$('input[id=fecha_oficina_dividir]')[0].select();
							}
						} else if (e.key == 'left')  {
							e.stop();

							$$('input[id=fecha_oficina_dividir]')[i].select();
						} else if (e.key == 'down')
						{
							e.stop();

							if (i < array.length - 1)
							{
								array[i + 1].select();
							} else {
								array[0].select();
							}
						} else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								array[i - 1].select();
							} else {
								array[array.length - 1].select();
							}
						}
					}
				});
			});
		},
		onOpenComplete: function()
		{
			document.id('fecha_oficina_dividir').focus();
		}
	});

	boxFacturasElectronicas = new mBox.Modal({
		id: 'box_facturas_electronicas',
		title: '<img src="/lecaroz/iconos/article_text.png" width="16" height="16" /> Generar facturas electr&oacute;nicas de venta diaria',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_facturas_electronicas();
				}
			}
		],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true,
		onOpen: function()
		{
			$$('input[id=checkblock]').each(function(el)
			{
				el.addEvent('click', check_block.pass(el));
			});

			$$('input[id=datos]').each(function(el, index)
			{
				el.addEvent('click', calcular_factura_dia.pass([el, index]));
			});

			$$('a[id^=efectivo]').each(function(el, index)
			{
				var data = el.get('title').split('|'),
					cia = data[0].toInt(),
					dia = data[1].toInt();

				el.addEvents({
					//'click': imponer_efectivo.pass([cia, dia])
				});

				el.removeProperty('title');
			});

			$$('a[id^=clientes]').each(function(el, index)
			{
				var param = el.get('title');

				if (!$$('input[id=datos]')[index].get('disabled'))
				{
					el.addEvents({
						//'click': modificar_facturas_clientes.pass(param)
					});
				}

				el.removeProperty('title');
			});

		}
	});

	boxModificarFacturasClientes = new mBox.Modal({
		id: 'box_modificar_facturas_clientes',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Modificar facturas de clientes',
		content: '',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_modificar_facturas_clientes();
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
		onOpen: function()
		{
		}
	});

	boxFacturasElectronicasReporte = new mBox.Modal({
		id: 'box_facturas_electronicas_reporte',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Reporte de facturas electr&oacute;nicas generadas',
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
		closeOnEsc: true,
		closeOnBodyClick: false,
		closeInTitle: true
	});

	inicio();

});

var inicio = function ()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').empty().set('html', result);

			window.removeEvent('resize', acomodar_tablas);

			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('fecha').addEvents({
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
					}
				}
			});

			document.id('siguiente').addEvent('click', consultar);

			boxProcessing.close();

			document.id('fecha').focus();
		}
	}).send();
}

var consultar = function()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();

		direction = '';
	}

	if (arguments.length > 1 && typeOf(arguments[1]) == 'string')
	{
		if (arguments[1] == 'next')
		{
			direction = '&next=' + (document.id('next').get('value').getNumericValue() > 0 ? document.id('next').get('value') : document.id('num_cia').get('value')) + '&num_cia=' + document.id('num_cia').get('value');
		} else if (arguments[1] == 'right')
		{
			direction = '&right=1&num_cia=' + document.id('num_cia').get('value');
		} else if (arguments[1] == 'left')
		{
			direction = '&left=1&num_cia=' + document.id('num_cia').get('value');
		} else {
			direction = '';
		}
	}

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=consultar&' + param + direction,
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

				acomodar_tablas();

				document.id('facturas_electronicas_tool').addEvent('click', facturas_electronicas);
				document.id('email_tool').addEvent('click', enviar_email);

				$$('#info_cia_tooltip').destroy();

				infoCiaTips = new mBox.Tooltip({
					id: 'info_cia_tooltip',
					setContent: 'title',
					attach: document.id('info_cia'),
					position: {
						x: 'right',
						y: 'center'
					}
				});

				$$('#depositos_tooltip, #oficinas_tooltip').destroy();

				depositosTips = new mBox.Tooltip({
					id: 'depositos_tooltip',
					setContent: 'info',
					event: 'click',
					attach: $$('a[id=deposito], a[id=cheque], a[id=tarjeta]'),
					position: {
						y: 'bottom'
					},
					onOpenComplete: function()
					{
						var id = document.id('mod').get('alt');

						document.id('mod').removeProperty('alt');

						document.id('mod').addEvent('click', modificar_deposito.pass(id));

						document.id('div').addEvent('click', dividir_deposito.pass(id));

						document.id('mov').addEvent('click', cambiar_deposito.pass(id));

						document.id('carta').addEvent('click', carta_deposito.pass(id));

						document.id('ficha').addEvent('click', cometra_deposito.pass(id));
					}
				});

				depositosTips.reInit();

				oficinasTips = new mBox.Tooltip({
					id: 'oficinas_tooltip',
					setContent: 'info',
					event: 'click',
					attach: $$('a[id=oficina]'),
					position: {
						y: 'bottom'
					},
					onOpenComplete: function()
					{
						$$('img[id=mod_oficina]').each(function(img, i)
						{
							var id = img.get('alt');

							img.removeProperty('alt');

							img.addEvent('click', modificar_oficina.pass(id));
						});

						$$('img[id=div_oficina]').each(function(img, i)
						{
							var id = img.get('alt');

							img.removeProperty('alt');

							img.addEvent('click', dividir_oficina.pass(id));
						});
					}
				});

				oficinasTips.reInit();

				$$('img[id=subtract]').addEvent('click', recorrer_depositos.pass('subtract'));

				$$('img[id=add]').addEvent('click', recorrer_depositos.pass('add'));

				document.id('anterior').addEvent('click', consultar.pass([param, 'left']));

				document.id('siguiente').addEvent('click', consultar.pass([param, 'right']));

				document.id('ir').addEvent('click', consultar.pass([param, 'next']));

				document.id('next').addEvents({

					change: obtener_cia,

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

				document.id('terminar').addEvent('click', inicio);

				window.addEvent('resize', acomodar_tablas).fireEvent('resize');

				boxProcessing.close();
			}
			else {
				boxProcessing.close();

				alert('No hay resultados');

				inicio();
			}
		}
	}).send();
}

var obtener_cia = function()
{
	if (document.id('next').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('next').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('next_nombre').set('value', result);
				} else {
					alert('La compañía no se encuentra en el catálogo.');

					document.id('next').set('value', document.id('next').retrieve('tmp', '')).select();
				}
			}
		}).send();
	} else {
		$$('#next, #next_nombre').set('value', '');

		document.id('next').focus();
	}
}

var obtener_cia_sec = function()
{
	if (document.id('num_cia_sec').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=obtener_cia_sec&num_cia=' + document.id('num_cia').get('value') + '&num_cia_sec=' + document.id('num_cia_sec').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia_sec').set('value', result);

					document.id('num_cia_sec').select();
				} else {
					document.id('num_cia_sec').set('value', document.id('num_cia_sec').retrieve('tmp', ''));

					alert('La compañía no se encuentra en el catálogo o no pertenece a la misma razon social.');

					document.id('num_cia_sec').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia_sec, #nombre_cia_sec').set('value', '');

		document.id('num_cia_sec').focus();
	}
}

var obtener_cia_destino = function()
{
	if (document.id('num_cia_destino').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=obtener_cia_destino&num_cia=' + document.id('num_cia').get('value') + '&num_cia_destino=' + document.id('num_cia_destino').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia_destino').set('value', result);
				} else {
					document.id('num_cia_destino').set('value', document.id('num_cia_destino').retrieve('tmp', ''));

					alert('La compañía no se encuentra en el catálogo o no pertenece a la misma razon social.');

					document.id('num_cia_destino').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia_destino, #nombre_cia_destino').set('value', '');

		document.id('num_cia_destino').focus();
	}
}

var obtener_cia_destino_cometra = function()
{
	if (document.id('num_cia_destino_cometra').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=obtener_cia_destino&num_cia=' + document.id('num_cia').get('value') + '&num_cia_destino=' + document.id('num_cia_destino_cometra').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia_destino_cometra').set('value', result);
				} else {
					document.id('num_cia_destino_cometra').set('value', document.id('num_cia_destino_cometra').retrieve('tmp', ''));

					alert('La compañía no se encuentra en el catálogo o no pertenece a la misma razon social.');

					document.id('num_cia_destino_cometra').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia_destino_cometra, #nombre_cia_destino_cometra').set('value', '');

		document.id('num_cia_destino_cometra').focus();
	}
}

var obtener_cia_oficina = function()
{
	if (document.id('num_cia_oficina').get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=obtener_cia_oficina&num_cia=' + document.id('num_cia').get('value') + '&num_cia_oficina=' + document.id('num_cia_oficina').get('value'),
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia_oficina').set('value', result);

					document.id('fecha_oficina').select();
				} else {
					document.id('num_cia_oficina').set('value', document.id('num_cia_oficina').retrieve('tmp', ''));

					alert('La compañía no se encuentra en el catálogo o no pertenece a la misma razon social.');

					document.id('num_cia_oficina').select();
				}
			}
		}).send();
	} else {
		$$('#num_cia_oficina, #nombre_cia_oficina').set('value', '');

		document.id('num_cia_oficina').focus();
	}
}

var validar_periodo_deposito = function()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=validar_periodo&fecha_corte=' + document.id('fecha').get('value') + '&fecha=' + document.id('fecha_deposito').get('value'),
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			if (data.status != 0)
			{
				if (data.status < 0)
				{
					alert('La fecha no puede ser menor a ' + data.fecha_inicio);

					document.id('fecha_deposito').set('value', document.id('fecha_deposito').retrieve('tmp', '')).select();
				} else if (data.status > 0)
				{
					alert('La fecha no puede ser mayor a ' + data.fecha_fin);

					document.id('fecha_deposito').set('value', document.id('fecha_deposito').retrieve('tmp', '')).select();
				}
			}
		}
	}).send();
}

var validar_periodo_oficina = function()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=validar_periodo&fecha_corte=' + document.id('fecha').get('value') + '&fecha=' + document.id('fecha_oficina').get('value'),
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			if (data.status != 0)
			{
				if (data.status < 0)
				{
					alert('La fecha no puede ser menor a ' + data.fecha_inicio);

					document.id('fecha_oficina').set('value', document.id('fecha_oficina').retrieve('tmp', '')).select();
				} else if (data.status > 0)
				{
					alert('La fecha no puede ser mayor a ' + data.fecha_fin);

					document.id('fecha_oficina').set('value', document.id('fecha_oficina').retrieve('tmp', '')).select();
				}
			}
		}
	}).send();
}

var modificar_deposito = function(id)
{
	depositosTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_deposito&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			document.id('id_deposito').set('value', data.id);
			document.id('fecha_deposito').set('value', data.fecha);
			document.id('conciliado_deposito').set('html', data.conciliado);
			document.id('concepto_deposito').set('html', data.concepto);
			document.id('importe_deposito').set('html', data.importe.numberFormat(2, '.', ','));
			document.id('banco').set('src', data.banco == 1 ? '/lecaroz/imagenes/Banorte16x16.png' : '/lecaroz/imagenes/Santander16x16.png');

			update_select(document.id('codigo_deposito'), data.codigos, data.codigo);

			boxProcessing.close();

			boxDepositoModificar.open();
		}
	}).send();
}

var dividir_deposito = function(id)
{
	depositosTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_deposito&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			$$('input[id=importe_deposito_dividir]').set('value', '');
			document.id('total_deposito_dividido').set('text', '0.00');

			document.id('id_deposito_dividir').set('value', data.id);
			document.id('deposito_dividir').set('html', data.importe.numberFormat(2, '.', ','));
			document.id('resto_deposito_dividir').set('html', data.importe.numberFormat(2, '.', ','));

			boxProcessing.close();

			boxDepositoDividir.open();
		}
	}).send();
}

var cambiar_deposito = function(id)
{
	depositosTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_deposito&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			document.id('id_deposito_cambiar').set('value', data.id);
			document.id('num_cia_sec').set('value', data.num_cia_sec);
			document.id('nombre_cia_sec').set('value', data.nombre_cia_sec);

			boxProcessing.close();

			boxDepositoCambiar.open();
		}
	}).send();
}

var carta_deposito = function(id)
{
	depositosTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_deposito&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			document.id('id_deposito_carta').set('value', data.id);
			$$('#num_cia_destino, #nombre_cia_destino, #contacto').set('value', '');

			boxProcessing.close();

			boxDepositoCarta.open();
		}
	}).send();
}

var cometra_deposito = function(id)
{
	depositosTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_deposito&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			document.id('id_deposito_cometra').set('value', data.id);
			$$('#num_cia_destino_cometra, #nombre_cia_destino_cometra').set('value', '');

			boxProcessing.close();

			boxDepositoCometra.open();
		}
	}).send();
}

var modificar_oficina = function(id)
{
	oficinasTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_oficina&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			document.id('id_oficina').set('value', data.id);
			document.id('num_cia_oficina').set('value', data.num_cia);
			document.id('nombre_cia_oficina').set('value', data.nombre_cia);
			document.id('fecha_oficina').set('value', data.fecha);
			document.id('importe_oficina').set('html', data.importe.numberFormat(2, '.', ','));

			boxProcessing.close();

			boxOficinaModificar.open();
		}
	}).send();
}

var dividir_oficina = function(id)
{
	oficinasTips.close({
		instant: true
	});

	current_id = id;

	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=datos_oficina&id=' + current_id,
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			var data = JSON.decode(result);

			$$('input[id=importe_oficina_dividir]').set('value', '');
			document.id('total_oficina_dividido').set('text', '0.00');

			document.id('id_oficina_dividir').set('value', data.id);
			document.id('fecha_dividir').set('html', data.fecha);
			document.id('oficina_dividir').set('html', data.importe.numberFormat(2, '.', ','));
			document.id('resto_oficina_dividir').set('html', data.importe.numberFormat(2, '.', ','));

			$$('input[id=fecha_oficina_dividir]').each(function(el, i)
			{
				el.set('value', data.fecha);
			});

			boxProcessing.close();

			boxOficinaDividir.open();
		}
	}).send();
}

var calcular_resto_deposito_dividido = function()
{
	var importes = $$('input[id=importe_deposito_dividir]').get('value').getNumericValue().sum(),
		deposito = document.id('deposito_dividir').get('text').getNumericValue();

	document.id('total_deposito_dividido').set('text', importes.numberFormat(2, '.', ','));
	document.id('resto_deposito_dividir').set({
		'text': (deposito - importes).numberFormat(2, '.', ','),
		'class': deposito - importes >= 0 ? 'bold' : 'bold red'
	});
}

var calcular_resto_oficina_dividido = function()
{
	var importes = $$('input[id=importe_oficina_dividir]').get('value').getNumericValue().sum(),
		deposito = document.id('oficina_dividir').get('text').getNumericValue();

	document.id('total_oficina_dividido').set('text', importes.numberFormat(2, '.', ','));
	document.id('resto_oficina_dividir').set({
		'text': (deposito - importes).numberFormat(2, '.', ','),
		'class': deposito - importes >= 0 ? 'bold' : 'bold red'
	});
}

var do_modificar_deposito = function()
{
	if (document.id('fecha_deposito').get('value') == '')
	{
		document.id('fecha_deposito').select();
	} else {
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=modificar_deposito&' + document.id('modificar_deposito').toQueryString(),
			onRequest: function()
			{
				boxDepositoModificar.close();

				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');
			}
		}).send();
	}
}

var do_dividir_deposito = function()
{
	var importes = $$('input[id=importe_deposito_dividir]').get('value').getNumericValue().sum().round(2),
		deposito = document.id('deposito_dividir').get('text').getNumericValue();

	if (importes - deposito == 0)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=dividir_deposito&' + document.id('dividir_deposito').toQueryString(),
			onRequest: function()
			{
				boxDepositoDividir.close();

				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');
			}
		}).send();
	} else {console.log(importes, deposito, importes - deposito);
		alert('Los importes no coinciden con el depósito original');
	}
}

var do_cambiar_deposito = function()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: 'accion=cambiar_deposito&num_cia=' + document.id('num_cia').get('value') + '&' + document.id('cambiar_deposito').toQueryString(),
		onRequest: function()
		{
			boxDepositoCambiar.close();

			boxProcessing.open();
		},
		onSuccess: function()
		{
			consultar(param, 'next');
		}
	}).send();
}

var do_carta_deposito = function()
{
	if (document.id('num_cia_destino').get('value').getNumericValue() > 0 && document.id('contacto').get('value') != '')
	{
		var id = document.id('id_deposito_carta').get('value'),
			num_cia_destino = document.id('num_cia_destino').get('value'),
			contacto = document.id('contacto').get('value');

		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=carta_deposito&' + document.id('carta_deposito').toQueryString(),
			onRequest: function()
			{
				boxDepositoCarta.close();

				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');

				var url_carta = 'EfectivosConciliacion.php',
					param_carta = '?accion=carta_deposito_documento&id=' + id + '&num_cia_destino=' + num_cia_destino + '&contacto=' + contacto,
					opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
					win = window.open(url_carta + param_carta, 'carta_bonificacion', opt);

				win.focus();
			}
		}).send();
	}
}

var do_cometra_deposito = function()
{
	if (document.id('num_cia_destino_cometra').get('value').getNumericValue() > 0)
	{
		var id = document.id('id_deposito_cometra').get('value'),
			num_cia_destino = document.id('num_cia_destino_cometra').get('value');

		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=cometra_deposito&' + document.id('cometra_deposito').toQueryString(),
			onRequest: function()
			{
				boxDepositoCometra.close();

				boxProcessing.open();
			},
			onSuccess: function(result)
			{
				boxProcessing.close();

				boxAlert.setContent('Movimientos de cometra generados con el folio ' + result).open();
			}
		}).send();
	}
}

var do_modificar_oficina = function()
{
	if (document.id('num_cia_oficina').get('value').getNumericValue() == 0)
	{
		document.id('num_cia_oficina').select();
	} else if (document.id('fecha_oficina').get('value') == '')
	{
		document.id('fecha_oficina').select();
	} else {
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=modificar_oficina&' + document.id('modificar_oficina').toQueryString(),
			onRequest: function()
			{
				boxOficinaModificar.close();

				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');
			}
		}).send();
	}
}

var do_dividir_oficina = function()
{
	var importes = $$('input[id=importe_oficina_dividir]').get('value').getNumericValue(),
		fechas = $$('input[id=fecha_oficina_dividir]').get('value'),
		total = $$('input[id=importe_oficina_dividir]').get('value').getNumericValue().sum(),
		deposito = document.id('oficina_dividir').get('text').getNumericValue(),
		ok = true;

	for (var i = 0; i < importes.length; i++)
	{
		if (importes[i] > 0 && fechas[i] == '')
		{
			ok = false;
		}
	}

	if (total - deposito == 0 && ok)
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=dividir_oficina&' + document.id('dividir_oficina').toQueryString(),
			onRequest: function()
			{
				boxOficinaDividir.close();

				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');
			}
		}).send();
	}
}

var recorrer_depositos = function(op)
{
	if ($$('input[id=dia]:checked').length > 0)
	{
		var num_cia = document.id('num_cia').get('value'),
			anio = document.id('fecha').get('value').split('/')[2].getNumericValue(),
			mes = document.id('fecha').get('value').split('/')[1].getNumericValue()

		new Request({
			url: 'EfectivosConciliacion.php',
			data: 'accion=recorrer_depositos&num_cia=' + num_cia + '&anio=' + anio + '&mes=' + mes + '&op=' + op + '&' + $$('input[id=dia]:checked').get('value').map(function(dia, i)
			{
				return 'dia[]=' + dia;
			}).join('&'),
			onRequest: function()
			{
				boxProcessing.open();
			},
			onSuccess: function()
			{
				consultar(param, 'next');
			}
		}).send();
	} else {
		alert('Debe seleccionar al menos un día');
	}
}

var acomodar_tablas = function()
{
	var info = document.id('info'),
		herramientas = document.id('herramientas'),
		leyendas = document.id('leyendas'),
		navegacion = document.id('navegacion'),
		reporte = document.id('reporte'),
		info_coordinates = info.getCoordinates(),
		herramientas_coordinates = herramientas.getCoordinates(),
		leyendas_coordinates = leyendas.getCoordinates(),
		navegacion_coordinates = navegacion.getCoordinates(),
		reporte_coordinates = reporte.getCoordinates(),
		window_size = window.getSize();

	if (/*reporte_coordinates.left - info_coordinates.width - 10 >= 0
		&& reporte_coordinates.left + reporte_coordinates.width + 10 + navegacion_coordinates.width <= window_size.x*/
		reporte_coordinates.left + reporte_coordinates.width + 10 + info_coordinates.width <= window_size.x
		&& reporte_coordinates.top + info_coordinates.height + herramientas_coordinates.height + leyendas_coordinates.height + 30 <= window_size.y)
	{
		info.setStyles({
			position: 'fixed',
			top: reporte_coordinates.top,
			// left: reporte_coordinates.left - info_coordinates.width - 10,
			left: reporte_coordinates.left + reporte_coordinates.width + 10,
			'margin-bottom': 0
		});

		herramientas.setStyles({
			position: 'fixed',
			top: reporte_coordinates.top + info_coordinates.height + 10,
			// left: reporte_coordinates.left - herramientas_coordinates.width - 10,
			left: reporte_coordinates.left + reporte_coordinates.width + 10,
			'margin-bottom': 0
		});

		leyendas.setStyles({
			position: 'fixed',
			// top: reporte_coordinates.top,
			top: reporte_coordinates.top + info_coordinates.height + herramientas_coordinates.height + 20,
			left: reporte_coordinates.left + reporte_coordinates.width + 10,
			'margin-top': 0,
			'margin-bottom': 0
		});

		navegacion.setStyles({
			position: 'fixed',
			// top: reporte_coordinates.top + leyendas_coordinates.height + 10,
			top: reporte_coordinates.top + info_coordinates.height + herramientas_coordinates.height + leyendas_coordinates.height + 30,
			left: reporte_coordinates.left + reporte_coordinates.width + 10
		});
	} else {
		info.setStyles({
			position: 'static',
			top: 'auto',
			left: 'auto',
			'margin-bottom': '20px'
		});

		herramientas.setStyles({
			position: 'static',
			top: 'auto',
			left: 'auto',
			'margin-top': '20px',
			'margin-bottom': '20px'
		});

		leyendas.setStyles({
			position: 'static',
			top: 'auto',
			left: 'auto',
			'margin-bottom': '20px'
		});

		navegacion.setStyles({
			position: 'static',
			top: 'auto',
			left: 'auto'
		});
	}

}

var update_select = function()
{
	var Select = arguments[0],
		Options = arguments[1],
		Value = !!arguments[2] && typeOf(arguments[2]) == 'number' ? arguments[2] : null;

	if (Options.length > 0)
	{
		Select.length = Options.length;

		Select.selectedIndex = 0;

		Array.each(Select.options, function(el, i)
		{
			el.set(Options[i]);

			if (Options[i].value == Value)
			{
				Select.selectedIndex = i;
			}
		});
	}
	else {
		Select.length = 1;
		Array.each(Select.options, function(el, i)
		{
			el.set({
				'value': '',
				'text': ''
			});
		});

		Select.selectedIndex = 0;
	}
}

var facturas_electronicas = function()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: {
			accion: 'facturas_electronicas',
			num_cia: document.id('num_cia').get('value'),
			fecha: document.id('fecha').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open()
		},
		onSuccess: function(result)
		{
			boxProcessing.close();

			boxFacturasElectronicas.setContent(result).open();
		}
	}).send();
}

var do_facturas_electronicas = function()
{
	if (confirm('¿Desea generar las facturas electrónicas de los días seleccionados?'))
	{
		new Request({
			'url': 'EfectivosConciliacion.php',
			'data': 'accion=generar_facturas_electronicas&' + $('facturacion_electronica').toQueryString(),
			'onRequest': function()
			{
				boxFacturasElectronicas.close();

				boxProcessing.open();
			},
			'onSuccess': function(result)
			{
				boxProcessing.close();

				if (result == '-1')
				{
					alert('Error al conectar al servidor de CFD');
				} else {
					boxFacturasElectronicasReporte.setContent(result).open();
				}
			}
		}).send();
	}
}

var calcular_factura_dia = function()
{
	var el = arguments[0],
		index = arguments[1],
		data = JSON.decode(el.get('value')),
		arrastre_diferencia = $$('input[id=arrastre_diferencia][cia=' + data.num_cia + ']')[0];

	if (el.get('checked'))
	{
		if (index > 0
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index - 1].get('disabled')
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index - 1].get('checked'))
		{
			alert('No puede seleccionar este d\xeda si no ha seleccionado los d\xedas anteriores');

			el.set('checked', false);
		}
		else {
			data.facturas_venta = data.depositos - data.facturas_clientes + arrastre_diferencia.get('value').getNumericValue();

			if (data.facturas_venta < 0)
			{
				data.diferencia = data.facturas_venta;

				data.facturas_venta = 0;
			}
			else {
				data.diferencia = data.depositos - data.facturas_clientes - data.facturas_venta;
			}

			data.arrastre = arrastre_diferencia.get('value').getNumericValue();

			arrastre_diferencia.set('value', data.facturas_venta > 0 ? 0 : data.diferencia);

			document.id('venta-' + data.num_cia + '-' + data.dia).set('html', data.facturas_venta != 0 ? '<span style="float:left;" class="font6">(1)</span>&nbsp;' + data.facturas_venta.numberFormat(2, '.', ',') : '');
			document.id('diferencia-' + data.num_cia + '-' + data.dia).set('html', data.diferencia != 0 ? data.diferencia.numberFormat(2, '.', ',') : '').addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}
	else {
		if (index < $$('input[id=datos][cia=' + data.num_cia + ']').length - 1
			&& !$$('input[id=datos][cia=' + data.num_cia + ']')[index + 1].get('disabled')
			&& $$('input[id=datos][cia=' + data.num_cia + ']')[index + 1].get('checked'))
		{
				alert('No puede deseleccionar este d\xeda si no ha deseleccionado los d\xedas posteriores');

				el.set('checked', true);
		}
		else {
			data.facturas_venta = 0;

			data.diferencia = data.depositos - data.facturas_clientes;

			arrastre_diferencia.set('value', data.arrastre);

			data.arrastre = 0;

			$('venta-' + data.num_cia + '-' + data.dia).set('html', '&nbsp;');
			$('diferencia-' + data.num_cia + '-' + data.dia).set('html', data.diferencia.numberFormat(2, '.', ',')).addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}

	calcular_total_cia(data.num_cia);
}

var calcular_total_cia = function()
{
	var cia = arguments[0],
		depositos = 0,
		facturas_clientes = 0,
		facturas_venta = 0,
		diferencia = 0;

	$$('input[id=datos][cia=' + cia + ']').each(function(el)
	{
		data = JSON.decode(el.get('value'));

		depositos += data.depositos;
		facturas_clientes += data.facturas_clientes;
		facturas_venta += data.facturas_venta;
	});

	diferencia = depositos - facturas_clientes - facturas_venta;

	$('total_venta-' + cia).set('text', facturas_venta.numberFormat(2, '.', ','));
	$('total_diferencia-' + cia).set('text', diferencia.numberFormat(2, '.', ',')).addClass(diferencia >= 0 ? 'blue' : 'red').removeClass(diferencia >= 0 ? 'red' : 'blue');
}

var modificar_facturas_clientes = function()
{
	new Request({
		url: 'EfectivosConciliacion.php',
		data: {
			accion: 'modificar_facturas_clientes',
			num_cia: document.id('num_cia').get('value'),
			fecha: document.id('fecha').get('value')
		},
		onRequest: function()
		{
			boxProcessing.open()
		},
		onSuccess: function(result)
		{
			boxProcessing.close();

			boxFacturasElectronicas.setContent(result).open();
		}
	}).send();
}

var check_block = function()
{
	var cia = arguments[0].get('cia'),
		checked = arguments[0].get('checked');

	if (!checked)
	{
		$$('input[id=datos][cia=' + cia + ']').filter(function(el)
		{
			return !el.get('disabled');
		}).reverse().each(function(el)
		{
			el.set('checked', false);
			el.fireEvent('click');
		});
	}
	else {
		$$('input[id=datos][cia=' + cia + ']').filter(function(el)
		{
			return !el.get('disabled');
		}).each(function(el)
		{
			el.set('checked', true);
			el.fireEvent('click');
		});
	}
}

var enviar_email = function()
{
	if (confirm('¿Desea enviar un email al administrador y encargado?'))
	{
		new Request({
			url: 'EfectivosConciliacion.php',
			data: {
				accion: 'enviar_email',
				num_cia: document.id('num_cia').get('value'),
				fecha: document.id('fecha').get('value')
			},
			onRequest: function()
			{
				boxProcessing.open()
			},
			onSuccess: function(result)
			{
				boxProcessing.close();
			}
		}).send();
	}
}
