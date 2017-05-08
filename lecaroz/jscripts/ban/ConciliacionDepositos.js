var current_index = null;

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

	boxTarjeta = new mBox.Modal(
	{
		id: 'box_tarjeta',
		title: '<img src="/lecaroz/iconos/credit_card.png" width="16" height="16" /> Tarjeta de cr&eacute;dito',
		content: 'tarjeta_wrapper',
		buttons: [
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('tarjeta_importe').get('value').getNumericValue() > 0 && document.id('tarjeta_fecha').get('value') != '')
					{
						calcular_tarjeta();

						this.close();

						return true;
					}

					return false;
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
		onOpenComplete: function()
		{
			document.id('tarjeta_importe').select();
		},
		onCloseComplete: function()
		{
		}
	});

	new FormValidator(document.id('tarjeta_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});

	document.id('tarjeta_importe').addEvent('keydown', function(e)
	{
		if (e.key == 'enter')
		{
			e.stop();

			document.id('tarjeta_fecha').select();
		}
	});

	document.id('tarjeta_fecha').addEvent('keydown', function(e)
	{
		if (e.key == 'enter')
		{
			e.stop();

			document.id('tarjeta_importe').select();
		}
	});

	moment.locale('es');

	inicio();

});

var inicio = function()
{
	new Request(
	{
		url: 'ConciliacionDepositos.php',
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

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();
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
		param = document.id('inicio_form').toQueryString();
	}

	new Request(
	{
		url: 'ConciliacionDepositos.php',
		data: 'accion=consulta&' + param,
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

				new FormValidator(document.id('consulta_form'),
				{
					showErrors: true,
					selectOnFocus: true
				});

				document.id('check_all').addEvent('change', function()
				{
					var checked = this.get('checked');

					$$('input[id^=check_cia_]').set('checked', checked).fireEvent('change');
				});

				$$('input[id^=check_cia_]').addEvent('change', function()
				{
					var num_cia = this.get('value');
					var checked = this.get('checked');

					$$('input[id^=deposito][data-cia=' + num_cia + ']:enabled').set('checked', checked);

					$$('input[id^=deposito][data-cia=' + num_cia + ']').each(function(el)
					{
						var index = el.get('data-index');

						$$('#fecha_' + index + ', #cod_mov_' + index + ', #concepto_' + index + ', #tarjeta_' + index).set('disabled', checked ? false : true);
					});

					calcular_total(num_cia);
				});

				$$('input[id^=deposito]').addEvent('change', function()
				{
					var index = this.get('data-index');
					var num_cia = this.get('data-cia');
					var checked = this.get('checked');

					$$('#fecha_' + index + ', #cod_mov_' + index + ', #concepto_' + index + ', #tarjeta_' + index).set('disabled', checked ? false : true);

					calcular_total(num_cia);
				});

				$$('select[id^=cod_mov]').addEvent('change', function()
				{
					var index = this.get('data-index');

					cambio_codigo(index);
				});

				fechas = $$('input[id^=fecha]');
				conceptos = $$('input[id^=concepto]');

				fechas.each(function(el, index)
				{
					el.addEvents({
						keydown: function(e)
						{
							if (e.key == 'enter' || e.key == 'right')
							{
								e.stop();

								conceptos[index].select();
							}
							else if (e.key == 'up' && index > 0)
							{
								fechas[index - 1].select();
							}
							else if (e.key == 'down' && index < fechas.length - 1)
							{
								fechas[index + 1].select();
							}
						}
					});
				});

				conceptos.each(function(el, index)
				{
					el.addEvents({
						keydown: function(e)
						{
							if (e.key == 'enter')
							{
								e.stop();

								if (index < fechas.length - 1)
								{
									fechas[index + 1].select();
								}
							}
							else if (e.key == 'left')
							{
								e.stop();

								fechas[index].select();
							}
							else if (e.key == 'up' && index > 0)
							{
								conceptos[index - 1].select();
							}
							else if (e.key == 'down' && index < conceptos.length - 1)
							{
								conceptos[index + 1].select();
							}
						}
					});
				});


				document.id('regresar').addEvent('click', inicio);

				document.id('conciliar').addEvent('click', validar);

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

var calcular_total = function(num_cia)
{
	var total = $$('input[id^=deposito][data-cia=' + num_cia + ']:checked').get('data-importe').getNumericValue().sum();

	document.id('total_seleccion_' + num_cia).set('html', total.numberFormat(2, '.', ','));
}

var cambio_codigo = function(index)
{
	current_index = index;

	if (document.id('cod_mov_' + index).get('value') == '44')
	{
		tarjeta();
	}
	else
	{
		var importe = document.id('deposito_' + current_index).get('data-importe').getNumericValue();

		document.id('tarjeta_' + current_index).set('value', '');
		document.id('fecha_' + current_index).set('readonly', false);
		document.id('importe_' + current_index).set('html', importe.numberFormat(2, '.', ','));
	}
}

var tarjeta = function()
{
	var tarjeta = JSON.decode(document.id('tarjeta_' + current_index).get('value'));

	var importe = !! tarjeta ? tarjeta.importe : document.id('deposito_' + current_index).get('data-importe').getNumericValue();
	var fecha = !! tarjeta ? tarjeta.fecha : document.id('fecha_' + current_index).get('value');

	document.id('tarjeta_importe').set('value', (importe * 1.0237).numberFormat(2, '.', ','));

	var dia = moment(fecha, 'DD-MM-YYYY').day();

	if ( ! tarjeta && [ 6, 0, 1, 2 ].contains(dia))
	{
		var dif = 0;

		switch (dia)
		{
			case 6:
				dif = 1;
				break;

			case 0:
				dif = 2;
				break;

			case 1:
				dif = 3;
				break;

			case 2:
				dif = 1;
		}

		document.id('tarjeta_fecha').set('value',  moment(fecha, 'DD-MM-YYYY').subtract(dif, 'days').format('DD/MM/YYYY'));
	}
	else if ( ! tarjeta)
	{
		document.id('tarjeta_fecha').set('value', moment(fecha, 'DD-MM-YYYY').subtract(2, 'days').format('DD/MM/YYYY'));
	}

	boxTarjeta.open();
}

var calcular_tarjeta = function()
{
	var importe_capturado = document.id('tarjeta_importe').get('value').getNumericValue();
	var importe_deposito = document.id('deposito_' + current_index).get('data-importe').getNumericValue();
	var comision_tarjeta = (importe_capturado * 0.02).round(2);
	var iva_comision = (comision_tarjeta * 0.16).round(2);
	var fecha = document.id('tarjeta_fecha').get('value');
	var importe_tarjeta = importe_capturado - importe_deposito;

	var data = {
		deposito: importe_deposito,
		tarjeta: importe_tarjeta,
		comision: comision_tarjeta,
		iva: iva_comision,
		importe: importe_capturado,
		fecha: fecha
	};

	document.id('tarjeta_' + current_index).set('value', JSON.encode(data));
	document.id('fecha_' + current_index).set('value', fecha).set('readonly', true);
	document.id('importe_' + current_index).set('html', '<img id="info_' + current_index + '" src="iconos/info.png" width="16" height="16" style="float:left; cursor:pointer;" data-index="' + current_index + '"><span class="green">' + (importe_deposito + importe_tarjeta - comision_tarjeta - iva_comision).numberFormat(2, '.', ',') + '</span>');

	document.id('info_' + current_index).store('tip:title', '<img src="/lecaroz/iconos/info.png"> Informaci&oacute;n de dep&oacute;sito');
	document.id('info_' + current_index).store('tip:text', '<strong>Fecha: '+ fecha + '</strong><br>'
		+ '-----------------------------------<br>'
		+ '<strong>+ Dep&oacute;sito: ' + importe_deposito.numberFormat(2, '.', ',') + '</strong><br>'
		+ '<strong>+ Tarjeta: ' + importe_tarjeta.numberFormat(2, '.', ',') + '</strong><br>'
		+ '<strong>- Comisi&oacute;n: ' + comision_tarjeta.numberFormat(2, '.', ',') + '</strong><br>'
		+ '<strong>- I.V.A.: ' + iva_comision.numberFormat(2, '.', ',') + '</strong><br>'
		+ '-----------------------------------<br>'
		+ '<strong>= Total: ' + (importe_deposito + importe_tarjeta - comision_tarjeta - iva_comision).numberFormat(2, '.', ',') + '</strong>');

	document.id('info_' + current_index).addEvent('click', function()
	{
		current_index = this.get('data-index');

		tarjeta();
	});

	new Tips(document.id('info_' + current_index),
	{
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
}

var validar = function()
{
	if ($$('input[id^=deposito]:enabled').length == 0)
	{
		alert('Debes seleccionar al menos un depósito');

		return false;
	}

	var ok = true;

	$$('input[id^=deposito]:enabled').each(function(el, index)
	{
		if (document.id('fecha_' + index).get('value') == '')
		{
			ok = false;

			alert('Debes especificar la fecha del movimiento');

			document.id('fecha_' + index).focus();

			return false;
		}

		if (document.id('concepto_' + index).get('value').clean() == '')
		{
			ok = false;

			alert('Debes especificar el concepto del movimiento');

			document.id('concepto_' + index).focus();

			return false;
		}
	});

	if ( ! ok)
	{
		return false;
	}

	if (confirm('¿Deseas conciliar los depósitos seleccionados?'))
	{
		conciliar();
	}
}

var conciliar = function()
{
	new Request(
	{
		url: 'ConciliacionDepositos.php',
		data: 'accion=conciliar&' + document.id('consulta_form').toQueryString(),
		onRequest: function()
		{
			boxProcessing.open();
		},
		onSuccess: function(response)
		{
			boxProcessing.close();

			inicio();
		}
	}).send();
}
