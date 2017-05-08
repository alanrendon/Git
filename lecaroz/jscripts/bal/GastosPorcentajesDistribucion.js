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

	inicio();
});

var inicio = function ()
{
	new Request(
	{
		url: 'GastosPorcentajesDistribucion.php',
		data: 'accion=inicio',
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('inicio_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('codgastos').addEvents(
			{
				change: obtener_gasto,
				keydown: function(e) {
					if (e.key == 'enter')
					{
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

var consultar = function ()
{
	if (typeOf(arguments[0]) == 'string')
	{
		param = arguments[0];
	}
	else
	{
		if (document.id('codgastos').get('value').getNumericValue() == 0)
		{
			alert('Debe especificar la compañía');

			document.id('codgastos').focus();

			return false;
		}

		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'GastosPorcentajesDistribucion.php',
		data: 'accion=consultar&' + param,
		onRequest: function()
		{
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result)
		{
			document.id('captura').set('html', result);

			new FormValidator(document.id('porcentajes_form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			$$('input[id=num_ros_0]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_cia(i, 0);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=porc_0]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=num_ros_0]')[i - 1].select();
							}
							else
							{
								$$('input[id=num_ros_0]')[$$('input[id=num_ros_0]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=num_ros_0]').length - 1)
							{
								$$('input[id=num_ros_0]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_0]')[$$('input[id=num_ros_0]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=porc_0]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function(e)
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=num_ros_0]')[i].get('value').getNumericValue() > 0)
							{
								validar_porcentajes(i, 0);
							}
							else
							{
								alert('Debe especificar primero la compañía');

								this.set('value', '');

								$$('input[id=num_ros_0]')[i].select();
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=num_ros_1]')[i].select();
						}
						else if (e.key == 'left')
						{
							e.stop();

							$$('input[id=num_ros_0]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=porc_0]')[i - 1].select();
							}
							else
							{
								$$('input[id=porc_0]')[$$('input[id=porc_0]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=porc_0]').length - 1)
							{
								$$('input[id=porc_0]')[i + 1].select();
							}
							else
							{
								$$('input[id=porc_0]')[$$('input[id=porc_0]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=num_ros_1]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_cia(i, 1);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=porc_1]')[i].select();
						}
						if (e.key == 'left')
						{
							e.stop();

							$$('input[id=porc_0]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=num_ros_1]')[i - 1].select();
							}
							else
							{
								$$('input[id=num_ros_1]')[$$('input[id=num_ros_1]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=num_ros_1]').length - 1)
							{
								$$('input[id=num_ros_1]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_1]')[$$('input[id=num_ros_1]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=porc_1]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function(e)
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=num_ros_1]')[i].get('value').getNumericValue() > 0)
							{
								validar_porcentajes(i, 1);
							}
							else
							{
								alert('Debe especificar primero la compañía');

								this.set('value', '');

								$$('input[id=num_ros_1]')[i].select();
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=num_ros_2]')[i].select();
						}
						else if (e.key == 'left')
						{
							e.stop();

							$$('input[id=num_ros_1]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=porc_1]')[i - 1].select();
							}
							else
							{
								$$('input[id=porc_1]')[$$('input[id=porc_1]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=porc_1]').length - 1)
							{
								$$('input[id=porc_1]')[i + 1].select();
							}
							else
							{
								$$('input[id=porc_1]')[$$('input[id=porc_1]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=num_ros_2]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_cia(i, 2);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=porc_2]')[i].select();
						}
						if (e.key == 'left')
						{
							e.stop();

							$$('input[id=porc_1]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=num_ros_2]')[i - 1].select();
							}
							else
							{
								$$('input[id=num_ros_2]')[$$('input[id=num_ros_2]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=num_ros_2]').length - 1)
							{
								$$('input[id=num_ros_2]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_2]')[$$('input[id=num_ros_2]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=porc_2]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function(e)
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=num_ros_2]')[i].get('value').getNumericValue() > 0)
							{
								validar_porcentajes(i, 2);
							}
							else
							{
								alert('Debe especificar primero la compañía');

								this.set('value', '');

								$$('input[id=num_ros_2]')[i].select();
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=num_ros_3]')[i].select();
						}
						else if (e.key == 'left')
						{
							e.stop();

							$$('input[id=num_ros_2]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=porc_2]')[i - 1].select();
							}
							else
							{
								$$('input[id=porc_2]')[$$('input[id=porc_2]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=porc_2]').length - 1)
							{
								$$('input[id=porc_2]')[i + 1].select();
							}
							else
							{
								$$('input[id=porc_2]')[$$('input[id=porc_2]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=num_ros_3]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_cia(i, 3);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=porc_3]')[i].select();
						}
						if (e.key == 'left')
						{
							e.stop();

							$$('input[id=porc_2]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=num_ros_3]')[i - 1].select();
							}
							else
							{
								$$('input[id=num_ros_3]')[$$('input[id=num_ros_3]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=num_ros_3]').length - 1)
							{
								$$('input[id=num_ros_3]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_3]')[$$('input[id=num_ros_3]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=porc_3]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function(e)
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=num_ros_3]')[i].get('value').getNumericValue() > 0)
							{
								validar_porcentajes(i, 3);
							}
							else
							{
								alert('Debe especificar primero la compañía');

								this.set('value', '');

								$$('input[id=num_ros_3]')[i].select();
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=num_ros_4]')[i].select();
						}
						else if (e.key == 'left')
						{
							e.stop();

							$$('input[id=num_ros_3]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=porc_3]')[i - 1].select();
							}
							else
							{
								$$('input[id=porc_3]')[$$('input[id=porc_3]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=porc_3]').length - 1)
							{
								$$('input[id=porc_3]')[i + 1].select();
							}
							else
							{
								$$('input[id=porc_3]')[$$('input[id=porc_3]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=num_ros_4]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function()
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							obtener_cia(i, 4);
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter' || e.key == 'right')
						{
							e.stop();

							$$('input[id=porc_4]')[i].select();
						}
						if (e.key == 'left')
						{
							e.stop();

							$$('input[id=porc_3]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=num_ros_4]')[i - 1].select();
							}
							else
							{
								$$('input[id=num_ros_4]')[$$('input[id=num_ros_4]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=num_ros_4]').length - 1)
							{
								$$('input[id=num_ros_4]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_4]')[$$('input[id=num_ros_4]').length - 1].select();
							}
						}
					}
				});
			});

			$$('input[id=porc_4]').each(function(el, i)
			{
				el.addEvents(
				{
					change: function(e)
					{
						if (this.get('value').getNumericValue() >= 0)
						{
							if ($$('input[id=num_ros_4]')[i].get('value').getNumericValue() > 0)
							{
								validar_porcentajes(i, 4);
							}
							else
							{
								alert('Debe especificar primero la compañía');

								this.set('value', '');

								$$('input[id=num_ros_4]')[i].select();
							}
						}
					},
					keydown: function(e)
					{
						if (e.key == 'enter')
						{
							e.stop();

							if (i < $$('input[id=num_ros_0]').length - 1)
							{
								$$('input[id=num_ros_0]')[i + 1].select();
							}
							else
							{
								$$('input[id=num_ros_0]')[$$('input[id=num_ros_0]').length - 1].select();
							}
						}
						else if (e.key == 'left')
						{
							e.stop();

							$$('input[id=num_ros_4]')[i].select();
						}
						else if (e.key == 'up')
						{
							e.stop();

							if (i > 0)
							{
								$$('input[id=porc_4]')[i - 1].select();
							}
							else
							{
								$$('input[id=porc_4]')[$$('input[id=porc_4]').length - 1].select();
							}
						}
						else if (e.key == 'down')
						{
							e.stop();

							if (i < $$('input[id=porc_4]').length - 1)
							{
								$$('input[id=porc_4]')[i + 1].select();
							}
							else
							{
								$$('input[id=porc_4]')[$$('input[id=porc_4]').length - 1].select();
							}
						}
					}
				});
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}

var obtener_cia = function(i, j)
{
	if ($$('input[id=num_ros_' + j + ']')[i].get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'GastosPorcentajesDistribucion.php',
			data: 'accion=obtener_cia&num_cia=' + $$('input[id=num_ros_' + j + ']')[i].get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					$$('input[id=nombre_ros_' + j + ']')[i].set('value', result);

					actualizar_porcentaje(i, j);
				}
				else
				{
					$$('input[id=num_ros_' + j + ']')[i].set('value', $$('input[id=num_ros_' + j + ']')[i].retrieve('tmp', ''));

					alert('La compañía no está en el catálogo');

					$$('input[id=num_ros_' + j + ']')[i].focus();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=num_ros_' + j + ']')[i].set('value', '');
		$$('input[id=nombre_ros_' + j + ']')[i].set('value', '');
		$$('input[id=porc_' + j + ']')[i].set('value', '');

		actualizar_porcentaje(i, j);
	}
}

var obtener_gasto = function()
{
	if (document.id('codgastos').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'GastosPorcentajesDistribucion.php',
			data: 'accion=obtener_gasto&codgastos=' + document.id('codgastos').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('descripcion').set('value', result);
				}
				else
				{
					document.id('codgastos').set('value', document.id('codgastos').retrieve('tmp', ''));

					alert('El código de gasto no está en el catálogo');

					document.id('codgastos').focus();
				}
			}
		}).send();
	}
	else
	{
		$$('#codgastos, #descripcion').set('value', '');
	}
}

// var validar_repetidos = function(num_cia)
// {
// 	if ($$('input[id^=num_ros_]').get('value').getNumericValue().filter(function(value) { return value > 0; }).filter(function(value) { return value == num_cia; }).length)
// 	{
// 		alert('La compañía ya tiene un porcentaje asignado en otro espacio');
// 	}
// }

var validar_porcentajes = function(i, j)
{
	var porc_0 = $$('input[id=porc_0]')[i].get('value').getNumericValue();
	var porc_1 = $$('input[id=porc_1]')[i].get('value').getNumericValue();
	var porc_2 = $$('input[id=porc_2]')[i].get('value').getNumericValue();
	var porc_3 = $$('input[id=porc_3]')[i].get('value').getNumericValue();
	var porc_4 = $$('input[id=porc_4]')[i].get('value').getNumericValue();

	var total = porc_0 + porc_1 + porc_2 + porc_3 + porc_4;

	if (total > 100)
	{
		$$('input[id=porc_' + j + ']')[i].set('value', $$('input[id=porc_' + j + ']')[i].retrieve('tmp', ''));

		alert('La suma de porcentajes para la distribución de gastos de la compañía no puede ser mayor al 100%');

		$$('input[id=porc_' + j + ']')[i].select();

		return false;
	}

	actualizar_porcentaje(i, j);

	return true;
}

var actualizar_porcentaje = function(i, j) {
	if ($$('input[id=id_' + j + ']')[i].get('value').getNumericValue() > 0 || ($$('input[id=num_ros_' + j + ']')[i].get('value').getNumericValue() > 0 && $$('input[id=porc_' + j + ']')[i].get('value').getNumericValue() > 0))
	{
		new Request(
		{
			url: 'GastosPorcentajesDistribucion.php',
			data:
			{
				accion: 'actualizar_porcentaje',
				id: $$('input[id=id_' + j + ']')[i].get('value'),
				num_cia: $$('input[id=num_cia]')[i].get('value'),
				ros: $$('input[id=num_ros_' + j + ']')[i].get('value'),
				codgastos: document.id('codgastos').get('value'),
				porc: $$('input[id=porc_' + j + ']')[i].get('value')
			},
			onRequest: function() {},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status == 1)
				{
					$$('input[id=id_' + j + ']')[i].set('value', data.id);
				}
				else if (data.status == 2)
				{
					$$('input[id=id_' + j + ']')[i].set('value', '');
				}
			}
		}).send();
	}
}
