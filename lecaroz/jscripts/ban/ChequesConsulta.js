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

	boxError = new mBox.Modal(
	{
		id: 'box_error',
		title: '<img src="/lecaroz/iconos/stop.png" width="16" height="16" /> Error',
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
	});

	boxCancelar = new mBox.Modal(
	{
		id: 'box_cancelar',
		title: '<img src="/lecaroz/iconos/cancel_round.png" width="16" height="16" /> Cancelar cheque',
		content: 'cancelar_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('fecha_cancelacion').get('value') == '')
					{
						return false;
					}

					do_cancelar();

					this.close();
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
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('cancelar_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('fecha_cancelacion').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.select();
					}
				}
			});
		},
		onOpen: function()
		{
			document.id('fecha_cancelacion').set('value', fecha_cancelacion);
			$$('#devolver_facturas, #inversa').set('checked', true);
		},
		onOpenComplete: function()
		{
			document.id('fecha_cancelacion').select();
		}
	});

	boxCancelarSeleccion = new mBox.Modal(
	{
		id: 'box_cancelar_seleccion',
		title: '<img src="/lecaroz/iconos/cancel_round.png" width="16" height="16" /> Cancelar cheques seleccionados',
		content: 'cancelar_seleccion_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('fecha_cancelacion_seleccion').get('value') == '')
					{
						return false;
					}

					do_cancelar_seleccion();

					this.close();
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
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('cancelar_seleccion_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('fecha_cancelacion_seleccion').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.select();
					}
				}
			});
		},
		onOpen: function()
		{
			document.id('fecha_cancelacion_seleccion').set('value', fecha_cancelacion);
			$$('#devolver_facturas_seleccion, #inversa_seleccion').set('checked', true);
		},
		onOpenComplete: function()
		{
			document.id('fecha_cancelacion_seleccion').select();
		}
	});

	boxCambiarFechaSeleccion = new mBox.Modal(
	{
		id: 'box_cambiar_fecha_seleccion',
		title: '<img src="/lecaroz/iconos/calendar.png" width="16" height="16" /> Cambiar fecha de cheques seleccionados',
		content: 'cambiar_fecha_seleccion_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('nueva_fecha_seleccion').get('value') == '')
					{
						return false;
					}

					do_cambiar_fecha_seleccion();

					this.close();
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
		closeInTitle: true,
		onBoxReady: function()
		{
			new FormValidator(document.id('cambiar_fecha_seleccion_wrapper'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('nueva_fecha_seleccion').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						this.blur();
						this.select();
					}
				}
			});
		},
		onOpen: function()
		{
			document.id('nueva_fecha_seleccion').set('value', fecha_cancelacion);
		},
		onOpenComplete: function()
		{
			document.id('nueva_fecha_seleccion').select();
		}
	});

	boxImprimir = new mBox.Modal(
	{
		id: 'box_imprimir',
		title: '<img src="/lecaroz/iconos/printer.png" width="16" height="16" /> Imprimir cheque',
		content: '¿Desea poner el cheque seleccionado como pendiente para impresi&oacute;n?',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_imprimir();

					this.close();
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
		closeInTitle: true,
	});

	boxImprimirSeleccion = new mBox.Modal(
	{
		id: 'box_imprimir_seleccion',
		title: '<img src="/lecaroz/iconos/printer.png" width="16" height="16" /> Imprimir cheques seleccionados',
		content: '¿Desea poner los cheques seleccionados como pendientes para impresi&oacute;n?',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_imprimir_seleccion();

					this.close();
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
		closeInTitle: true,
	});

	boxRegresarPasivo = new mBox.Modal(
	{
		id: 'box_pasivo',
		title: '<img src="/lecaroz/iconos/cancel_round.png" width="16" height="16" /> Regresar pasivo',
		content: '¿Desea regresar a pasivo las facturas del pago seleccionado?',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					do_pasivo();

					this.close();
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
		closeInTitle: true,
		onBoxReady: function(){},
		onOpenComplete: function(){}
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

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
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

						document.id('fecha1').select();
					}
				}
			});

			document.id('banco').addEvents(
			{
				change: function()
				{
					switch (this.get('value').getNumericValue())
					{

						case 1:
							this.removeClass('logo_banco_2').addClass('logo_banco_1');
							break;

						case 2:
							this.removeClass('logo_banco_1').addClass('logo_banco_2');
							break;

						default:
							this.removeClass('logo_banco_1').removeClass('logo_banco_2');
					}
				}
			}).fireEvent('change');

			document.id('fecha1').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha2').select();
					}
				}
			});

			document.id('fecha2').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cobrado1').focus();
					}
				}
			});

			document.id('cobrado1').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cobrado2').select();
					}
				}
			});

			document.id('cobrado2').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('pros').focus();
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

						document.id('folios').select();
					}
				}
			});

			document.id('folios').addEvents(
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

						document.id('omitir_gastos').select();
					}
				}
			});

			document.id('omitir_gastos').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('importes').select();
					}
				}
			});

			document.id('importes').addEvents(
			{
				'keydown': function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('concepto').select();
					}
				}
			});

			document.id('concepto').addEvents(
			{
				'keydown': function(e)
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
		url: 'ChequesConsulta.php',
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

				document.id('seleccionar_todos').addEvent('change', function()
				{
					var checked = this.checked;

					$$('input[id=data]:enabled').set('checked', checked);
					$$('input[id=seleccionar_cia]').set('checked', checked);
					$$('input[id=seleccionar_banco]').set('checked', checked);
				});

				$$('input[id=seleccionar_cia]').addEvent('change', function()
				{
					var checked = this.checked;
					var cia = this.get('data-cia');

					$$('input[id=data][data-cia=' + cia + ']:enabled').set('checked', checked);

					document.id('seleccionar_todos').set('checked', false);
					$$('input[id=seleccionar_banco][data-cia=' + cia + ']').set('checked', checked);
				});

				$$('input[id=seleccionar_banco]').addEvent('change', function()
				{
					var checked = this.checked;
					var cia = this.get('data-cia');
					var banco = this.get('data-banco');

					$$('input[id=data][data-cia=' + cia + '][data-banco=' + banco + ']:enabled').set('checked', checked);

					document.id('seleccionar_todos').set('checked', false);
					$$('input[id=seleccionar_cia][data-cia=' + cia + ']').set('checked', false);
				});

				$$('input[id=data]:enabled').addEvent('change', function()
				{
					var cia = this.get('data-cia');
					var banco = this.get('data-banco');

					document.id('seleccionar_todos').set('checked', false);
					$$('input[id=seleccionar_cia][data-cia=' + cia + ']').set('checked', false);
					$$('input[id=seleccionar_banco][data-cia=' + cia + '][data-banco=' + banco + ']').set('checked', false);
				});

				$$('img[id=cancelar][src!=/lecaroz/iconos/cancel_round_gray.png]').each(function(el)
				{
					var data = el.get('data-row');

					el.addEvents(
					{
						'click': cancelar.pass(data)
					});
				});

				$$('img[id=imprimir][src!=/lecaroz/iconos/printer_gray.png]').each(function(el)
				{
					var id = el.get('data-id').getNumericValue();

					el.removeProperty('data-id').addEvents(
					{
						'click': imprimir.pass(id)
					});
				});

				$$('img[id=pasivo][src!=/lecaroz/iconos/article_download_gray.png]').each(function(el)
				{
					var id = el.get('data-id').getNumericValue();

					el.removeProperty('data-id').addEvents(
					{
						'click': pasivo.pass(id)
					});
				});

				document.id('regresar').addEvent('click', inicio);

				// document.id('listado').addEvent('click', listado);

				// document.id('exportar').addEvent('click', exportar);

				document.id('cancelar_seleccion').addEvent('click', cancelar_seleccion);

				document.id('imprimir_seleccion').addEvent('click', imprimir_seleccion);

				document.id('cambiar_fecha_seleccion').addEvent('click', cambiar_fecha_seleccion);

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

var cancelar = function(data)
{
	data_cheque = data;

	boxCancelar.open();
}

var do_cancelar = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'cancelar',
			fecha_cancelacion: document.id('fecha_cancelacion').get('value'),
			devolver_facturas: document.id('devolver_facturas').get('checked') ? 1 : 0,
			inversa: document.id('inversa').get('checked') ? 1 : 0,
			data: data_cheque
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			boxProcessing.close();

			consultar(param);
		}
	}).send();
}

var cancelar_seleccion = function()
{
	if ($$('input[id=data]:checked').length == 0)
	{
		boxError.setContent('Debe seleccionar al menos un registro para ejecutar esta tarea.').open();
	}
	else
	{
		boxCancelarSeleccion.open();
	}
}

var do_cancelar_seleccion = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'cancelar_seleccion',
			fecha_cancelacion: document.id('fecha_cancelacion_seleccion').get('value'),
			devolver_facturas: document.id('devolver_facturas_seleccion').get('checked') ? 1 : 0,
			inversa: document.id('inversa_seleccion').get('checked') ? 1 : 0,
			data: $$('input[id=data]:checked').get('value')
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			boxProcessing.close();

			consultar(param);
		}
	}).send();
}

var imprimir = function(id)
{
	id_cheque = id;

	boxImprimir.open();
}

var imprimir_seleccion = function()
{
	if ($$('input[id=data]:checked').length == 0)
	{
		boxError.setContent('Debe seleccionar al menos un registro para ejecutar esta tarea.').open();
	}
	else
	{
		boxImprimirSeleccion.open();
	}
}

var do_imprimir = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'imprimir',
			id: id_cheque
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			boxProcessing.close();
		}
	}).send();
}

var do_imprimir_seleccion = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'imprimir_seleccion',
			data: $$('input[id=data]:checked').get('value')
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			boxProcessing.close();
		}
	}).send();
}

var pasivo = function(id)
{
	id_cheque = id;

	boxRegresarPasivo.open();
}

var do_pasivo = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'regresar_pasivo',
			id: id_cheque
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			consultar(param);

			boxProcessing.close();
		}
	}).send();
}

var cambiar_fecha_seleccion = function()
{
	if ($$('input[id=data]:checked').length == 0)
	{
		boxError.setContent('Debe seleccionar al menos un registro para ejecutar esta tarea.').open();
	}
	else
	{
		boxCambiarFechaSeleccion.open();
	}
}

var do_cambiar_fecha_seleccion = function()
{
	new Request(
	{
		url: 'ChequesConsulta.php',
		data: {
			accion: 'cambiar_fecha_seleccion',
			nueva_fecha: document.id('nueva_fecha_seleccion').get('value'),
			data: $$('input[id=data]:checked').get('value')
		},
		'onRequest': function()
		{
			boxProcessing.open();
		},
		'onSuccess': function()
		{
			boxProcessing.close();

			consultar(param);
		}
	}).send();
}
