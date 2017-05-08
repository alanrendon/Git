window.addEvent('domready', function() {

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

	boxMessage = new mBox.Modal(
	{
		id: 'box_message',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" />',
		content: '',
		buttons:
		[
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{

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
		onBoxReady: function() {},
		onOpenComplete: function() {}
	});

	boxAlert = new mBox.Modal(
	{
		id: 'box_message',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Alerta',
		content: '',
		buttons:
		[
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
		onBoxReady: function() {},
		onOpenComplete: function() {}
	});

	boxFailure = new mBox.Modal(
	{
		id: 'box_failure',
		title: 'Error',
		content: '',
		buttons:
		[
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

	boxGenerar = new mBox.Modal(
	{
		id: 'box_generar',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Seleccionar banco y fecha',
		content: 'generar-wrapper',
		buttons:
		[
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function()
				{
					if (document.id('fecha').get('value') == '')
					{
						return false;
					}

					generar_pagos();
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
			new FormValidator(document.id('generar-form'),
			{
				showErrors: true,
				selectOnFocus: true
			});

			document.id('banco').addEvents({
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

					}
				}
			}).fireEvent('change');

			document.id('fecha').addEvents(
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
			document.id('generar-form').reset();
			document.id('banco').fireEvent('change');
		},
		onOpenComplete: function()
		{
			document.id('fecha').select();
		}
	});

	inicio();

});

var inicio = function ()
{
	new Request(
	{
		url: 'PagosFijos.php',
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

			document.id('cias').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('pros').focus();
					}
				}
			}).focus();

			document.id('pros').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('gastos').focus();
					}
				}
			});

			document.id('gastos').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('cias').focus();
					}
				}
			});

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
		param = document.id('inicio').toQueryString();
	}

	new Request(
	{
		url: 'PagosFijos.php',
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
				document.id('captura').set('html', result);

				document.id('alta').addEvent('click', alta);

				$$('img[id=mod]').each(function(el) {
					var id = el.get('alt');

					el.addEvent('click', modificar.pass(id));

					el.removeProperty('alt');
				});

				$$('img[id=del]').each(function(el) {
					var id = el.get('alt');

					el.addEvent('click', do_baja.pass(id));

					el.removeProperty('alt');
				});

				document.id('regresar').addEvent('click', inicio);

				document.id('generar').addEvent('click', function()
				{
					boxGenerar.open();
				});

				boxProcessing.close();
			}
			else
			{
				boxMessage.setTitle('<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n').setContent('No hay resultados').open();

				inicio();
			}
		}
	}).send();
}

var alta = function() {
	new Request({
		url: 'PagosFijos.php',
		data: 'accion=alta',
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			validator = new FormValidator(document.id('datos-form'), {
				showErrors: true,
				selectOnFocus: true
			});

			new_row(0);

			$$('input[id=num_cia]')[0].select();

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('registrar').addEvent('click', do_alta);

			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'PagosFijos.php',
			data: 'accion=do_alta&' + document.id('datos-form').toQueryString(),
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

var obtener_cia = function(i)
{
	var num_cia = i < 0 ? document.id('num_cia') : $$('input[id=num_cia]')[i];
	var nombre_cia = i < 0 ? document.id('nombre_cia') : $$('input[id=nombre_cia]')[i];

	if (num_cia.get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'PagosFijos.php',
			data: 'accion=obtener_cia&num_cia=' + num_cia.get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					nombre_cia.set('value', result);
				}
				else
				{
					num_cia.set('value', num_cia.retrieve('tmp', ''));

					boxFailure.setContent('La compa&ntilde;&iacute;a no est&aacute; en el cat&aacute;logo').open();
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

var obtener_pro = function(i)
{
	var num_pro = i < 0 ? document.id('num_pro') : $$('input[id=num_pro]')[i];
	var nombre_pro = i < 0 ? document.id('nombre_pro') : $$('input[id=nombre_pro]')[i];

	if (num_pro.get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'PagosFijos.php',
			data: 'accion=obtener_pro&num_pro=' + num_pro.get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					nombre_pro.set('value', result);
				}
				else
				{
					num_pro.set('value', num_pro.retrieve('tmp', ''));

					boxFailure.setContent('El proveedor no est&aacute; en el cat&aacute;logo').open();
				}
			}
		}).send();
	}
	else
	{
		num_pro.set('value', '');
		nombre_pro.set('value', '');
	}
}

var obtener_gasto = function(i)
{
	var cod = i < 0 ? document.id('cod') : $$('input[id=cod]')[i];
	var gasto = i < 0 ? document.id('gasto') : $$('input[id=gasto]')[i];

	if (cod.get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'PagosFijos.php',
			data: 'accion=obtener_gasto&cod=' + cod.get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					gasto.set('value', result);
				}
				else
				{
					cod.set('value', cod.retrieve('tmp', ''));

					boxFailure.setContent('El gasto no est&aacute; en el cat&aacute;logo').open();
				}
			}
		}).send();
	}
	else
	{
		cod.set('value', '');
		gasto.set('value', '');
	}
}

var calcular_impuestos = function(i)
{
	var importe = i < 0 ? document.id('importe').get('value').getNumericValue() : $$('input[id=importe]')[i].get('value').getNumericValue();
	var iva = i < 0 ? document.id('iva').get('value').getNumericValue() : $$('input[id=iva]')[i].get('value').getNumericValue();
	var ret_iva = i < 0 ? document.id('ret_iva').get('value').getNumericValue() : $$('input[id=ret_iva]')[i].get('value').getNumericValue();
	var isr = i < 0 ? document.id('isr').get('value').getNumericValue() : $$('input[id=isr]')[i].get('value').getNumericValue();
	var cedular = i < 0 ? document.id('cedular').get('value').getNumericValue() : $$('input[id=cedular]')[i].get('value').getNumericValue();
	var total = i < 0 ? document.id('total').get('value').getNumericValue() : $$('input[id=total]')[i].get('value').getNumericValue();

	if (importe > 0 && (iva != 0 || total == 0))
	{
		iva = (importe * 0.16).round(2);
	}

	if (importe > 0 && (ret_iva != 0 || total == 0))
	{
		ret_iva = (importe * 0.1066666667 * -1).round(2);
	}

	if (importe > 0 && (isr != 0 || total == 0))
	{
		isr = (importe * 0.10).round(2);
	}

	if (importe > 0 && (cedular != 0 || total == 0))
	{
		cedular = (importe * 0.1).round(2);
	}

	if (i < 0)
	{
		document.id('iva').set('value', iva != 0 ? iva.numberFormat(2, '.', ',') : '');
		document.id('ret_iva').set('value', ret_iva != 0 ? ret_iva.numberFormat(2, '.', ',') : '');
		document.id('isr').set('value', isr != 0 ? isr.numberFormat(2, '.', ',') : '');
		document.id('cedular').set('value', cedular != 0 ? cedular.numberFormat(2, '.', ',') : '');
	}
	else
	{
		$$('input[id=iva]')[i].set('value', iva != 0 ? iva.numberFormat(2, '.', ',') : '');
		$$('input[id=ret_iva]')[i].set('value', ret_iva != 0 ? ret_iva.numberFormat(2, '.', ',') : '');
		$$('input[id=isr]')[i].set('value', isr != 0 ? isr.numberFormat(2, '.', ',') : '');
		$$('input[id=cedular]')[i].set('value', cedular != 0 ? cedular.numberFormat(2, '.', ',') : '');
	}

	calcular_total(i);
}

var calcular_total = function(i)
{
	var importe = i < 0 ? document.id('importe').get('value').getNumericValue() : $$('input[id=importe]')[i].get('value').getNumericValue();
	var iva = i < 0 ? document.id('iva').get('value').getNumericValue() : $$('input[id=iva]')[i].get('value').getNumericValue();
	var ret_iva = i < 0 ? document.id('ret_iva').get('value').getNumericValue() : $$('input[id=ret_iva]')[i].get('value').getNumericValue();
	var isr = i < 0 ? document.id('isr').get('value').getNumericValue() : $$('input[id=isr]')[i].get('value').getNumericValue();
	var cedular = i < 0 ? document.id('cedular').get('value').getNumericValue() : $$('input[id=cedular]')[i].get('value').getNumericValue();
	var total = 0;

	total = importe + iva + ret_iva - isr - cedular;

	if (i < 0)
	{
		document.id('total').set('value', total != 0 ? total.numberFormat(2, '.', ',') : '');
	}
	else
	{
		$$('input[id=total]')[i].set('value', total != 0 ? total.numberFormat(2, '.', ',') : '');
	}
}

var new_row = function(i)
{
	var table = document.id('datos-table');
	var tr = new Element('tr').inject(table);
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);
	var td5 = new Element('td').inject(tr);
	var td6 = new Element('td').inject(tr);
	var td7 = new Element('td').inject(tr);
	var td8 = new Element('td').inject(tr);
	var td9 = new Element('td').inject(tr);
	var td10 = new Element('td').inject(tr);
	var td11 = new Element('td').inject(tr);

	var options = [
		{ value: 1, text: 'INTERNA' },
		{ value: 2, text: 'EXTERNA' }
	];

	var num_cia = new Element('input',
	{
		name: 'num_cia[]',
		id: 'num_cia',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3
	}).inject(td1);
	var nombre_cia = new Element('input',
	{
		name: 'nombre_cia[]',
		id: 'nombre_cia',
		type: 'text',
		size: 30,
		disabled: 'disabled'
	}).inject(td1);

	var num_pro = new Element('input',
	{
		name: 'num_pro[]',
		id: 'num_pro',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3
	}).inject(td2);
	var nombre_pro = new Element('input',
	{
		name: 'nombre_pro[]',
		id: 'nombre_pro',
		type: 'text',
		size: 30,
		disabled: 'disabled'
	}).inject(td2);

	var cod = new Element('input',
	{
		name: 'cod[]',
		id: 'cod',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3
	}).inject(td3);
	var gasto = new Element('input',
	{
		name: 'gasto[]',
		id: 'gasto',
		type: 'text',
		size: 30,
		disabled: 'disabled'
	}).inject(td3);

	var concepto = new Element('input',
	{
		name: 'concepto[]',
		id: 'concepto',
		type: 'text',
		class: 'validate toText cleanText toUpper',
		size: 30
	}).inject(td4);

	var importe = new Element('input',
	{
		name: 'importe[]',
		id: 'importe',
		type: 'text',
		class: 'validate focus numberPosFormat right green',
		precision: 2,
		size: 10
	}).inject(td5);

	var iva = new Element('input',
	{
		name: 'iva[]',
		id: 'iva',
		type: 'text',
		class: 'validate focus numberPosFormat right red',
		precision: 2,
		size: 10
	}).inject(td6);

	var ret_iva = new Element('input',
	{
		name: 'ret_iva[]',
		id: 'ret_iva',
		type: 'text',
		class: 'validate focus numberFormat right blue',
		precision: 2,
		size: 10
	}).inject(td7);

	var isr = new Element('input',
	{
		name: 'isr[]',
		id: 'isr',
		type: 'text',
		class: 'validate focus numberPosFormat right blue',
		precision: 2,
		size: 10
	}).inject(td8);

	var cedular = new Element('input',
	{
		name: 'cedular[]',
		id: 'cedular',
		type: 'text',
		class: 'validate focus numberPosFormat right blue',
		precision: 2,
		size: 10
	}).inject(td9);

	var total = new Element('input',
	{
		name: 'total[]',
		id: 'total',
		type: 'text',
		class: 'bold right',
		precision: 2,
		size: 10,
		readonly: 'readonly'
	}).inject(td10);

	var tipo_renta = new Element('select',
	{
		name: 'tipo_renta[]',
		id: 'tipo_renta'
	}).inject(td11);

	validator.addElementEvents(num_cia).addEvents(
	{
		change: obtener_cia.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=num_pro]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=num_cia]')[i - 1].select();
				}
				else
				{
					$$('input[id=num_cia]')[$$('input[id=num_cia]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=num_cia]').length - 1)
				{
					$$('input[id=num_cia]')[i + 1].select();
				}
				else
				{
					$$('input[id=num_cia]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(num_pro).addEvents(
	{
		change: obtener_pro.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=cod]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=num_cia]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=num_pro]')[i - 1].select();
				}
				else
				{
					$$('input[id=num_pro]')[$$('input[id=num_pro]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=num_pro]').length - 1)
				{
					$$('input[id=num_pro]')[i + 1].select();
				}
				else
				{
					$$('input[id=num_pro]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(cod).addEvents(
	{
		change: obtener_gasto.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=concepto]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=num_pro]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=cod]')[i - 1].select();
				}
				else
				{
					$$('input[id=cod]')[$$('input[id=cod]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=cod]').length - 1)
				{
					$$('input[id=cod]')[i + 1].select();
				}
				else
				{
					$$('input[id=cod]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(concepto).addEvents(
	{
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=importe]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=cod]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=cod]')[i - 1].select();
				}
				else
				{
					$$('input[id=cod]')[$$('input[id=cod]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=cod]').length - 1)
				{
					$$('input[id=cod]')[i + 1].select();
				}
				else
				{
					$$('input[id=cod]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(importe).addEvents(
	{
		change: calcular_impuestos.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=iva]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=concepto]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=importe]')[i - 1].select();
				}
				else
				{
					$$('input[id=importe]')[$$('input[id=importe]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=importe]').length - 1)
				{
					$$('input[id=importe]')[i + 1].select();
				}
				else
				{
					$$('input[id=importe]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(iva).addEvents(
	{
		change: calcular_total.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=ret_iva]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=importe]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=iva]')[i - 1].select();
				}
				else
				{
					$$('input[id=iva]')[$$('input[id=iva]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=iva]').length - 1)
				{
					$$('input[id=iva]')[i + 1].select();
				}
				else
				{
					$$('input[id=iva]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(ret_iva).addEvents(
	{
		change: calcular_total.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=isr]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=iva]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=ret_iva]')[i - 1].select();
				}
				else
				{
					$$('input[id=ret_iva]')[$$('input[id=ret_iva]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=ret_iva]').length - 1)
				{
					$$('input[id=ret_iva]')[i + 1].select();
				}
				else
				{
					$$('input[id=ret_iva]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(isr).addEvents(
	{
		change: calcular_total.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=cedular]')[i].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=ret_iva]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=isr]')[i - 1].select();
				}
				else
				{
					$$('input[id=isr]')[$$('input[id=isr]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=isr]').length - 1)
				{
					$$('input[id=isr]')[i + 1].select();
				}
				else
				{
					$$('input[id=isr]')[0].select();
				}
			}
		}
	});

	validator.addElementEvents(cedular).addEvents(
	{
		change: calcular_total.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				if (i + 1 > $$('input[id=num_cia]').length - 1)
				{
					new_row(i + 1);
				}

				$$('input[id=num_cia]')[i + 1].select();
			}
			else if (e.key == 'left')
			{
				e.stop();

				$$('input[id=isr]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=cedular]')[i - 1].select();
				}
				else
				{
					$$('input[id=cedular]')[$$('input[id=cedular]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=cedular]').length - 1)
				{
					$$('input[id=cedular]')[i + 1].select();
				}
				else
				{
					$$('input[id=cedular]')[0].select();
				}
			}
		}
	});

	tipo_renta.length = options.length;

	Array.each(tipo_renta.options, function(el, i) {
		el.set(options[i]);
	});
}

var generar_pagos = function()
{
	boxGenerar.close();

	new Request(
	{
		url: 'PagosFijos.php',
		data: 'accion=generar_pagos&banco=' + document.id('banco').get('value') + '&fecha=' + document.id('fecha').get('value') + '&' + param,
		onRequest: function()
		{
			boxGenerar.close();

			boxProcessing.open();
		},
		onSuccess: function(result)
		{
			boxProcessing.close();

			boxAlert.setContent('Pagos generados con exito.').open();
		}
	}).send();
}

var modificar = function(id) {
	new Request({
		url: 'PagosFijos.php',
		data: 'accion=modificar&id=' + id,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			new FormValidator(document.id('datos-form'), {
				showErrors: true,
				selectOnFocus: true
			});

			document.id('num_cia').addEvents({
				change: obtener_cia.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_pro').select();
					}
				}
			}).focus();

			document.id('num_pro').addEvents({
				change: obtener_pro.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cod').select();
					}
				}
			});

			document.id('cod').addEvents({
				change: obtener_gasto.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('concepto').select();
					}
				}
			});

			document.id('concepto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('importe').select();
					}
				}
			});

			document.id('importe').addEvents({
				change: calcular_impuestos.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('iva').select();
					}
				}
			});

			document.id('iva').addEvents({
				change: calcular_total.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('ret_iva').select();
					}
				}
			});

			document.id('ret_iva').addEvents({
				change: calcular_total.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('isr').select();
					}
				}
			});

			document.id('isr').addEvents({
				change: calcular_total.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('cedular').select();
					}
				}
			});

			document.id('cedular').addEvents({
				change: calcular_total.pass(-1),
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						document.id('num_cia').select();
					}
				}
			});

			$$('#num_cia, #num_pro, #cod').fireEvent('change');

			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('modificar').addEvent('click', do_modificar);

			boxProcessing.close();
		}
	}).send();
}

var do_modificar = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar número de compañía');

		document.id('num_cia').focus();
	}
	if (document.id('num_pro').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar número de proveedor');

		document.id('num_pro').focus();
	}
	if (document.id('cod').get('value').getNumericValue() == 0)
	{
		alert('Debe especificar el gasto');

		document.id('cod').focus();
	}
	if (document.id('importe').get('value').getNumericValue() <= 0)
	{
		alert('El importe debe ser mayor a cero');

		document.id('importe').focus();
	}
	if (document.id('total').get('value').getNumericValue() <= 0)
	{
		alert('El total debe ser mayor a cero');

		document.id('importe').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'PagosFijos.php',
			data: 'accion=do_modificar&' + document.id('datos-form').toQueryString(),
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
			url: 'PagosFijos.php',
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
