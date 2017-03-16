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
	
	box = new mBox.Modal(
	{
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
	
	iniciar();
	
});

var iniciar = function ()
{
	validator = new FormValidator(document.id('datos_form'), {
		showErrors: true,
		selectOnFocus: true
	});

	new_row(0);

	$$('input[id=num_cia]')[0].select();

	document.id('cancelar').addEvent('click', cancelar_validaciones);
}

var new_row = function(i)
{
	var table = document.id('datos_table');
	var tr = new Element('tr').inject(table);
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);
	var td3 = new Element('td').inject(tr);
	var td4 = new Element('td').inject(tr);

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
		size: 30
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
		size: 30
	}).inject(td2);

	var num_fact = new Element('input',
	{
		name: 'num_fact[]',
		id: 'num_fact',
		type: 'text',
		class: 'validate onlyNumbersAndLetters toUpper',
		size: 10
	}).inject(td3);

	var data = new Element('input', {
		name: 'data[]',
		id: 'data',
		type: 'hidden',
		value: ''
	}).inject(td4);
	var status = new Element('span', {
		id: 'status_' + i,
		html: '&nbsp;'
	}).inject(td4);

	validator.addElementEvents(num_cia).addEvents({
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

	validator.addElementEvents(num_pro).addEvents({
		change: obtener_pro.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter' || e.key == 'right')
			{
				e.stop();

				$$('input[id=num_fact]')[i].select();
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

	validator.addElementEvents(num_fact).addEvents({
		change: validar_fac.pass(i),
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

				$$('input[id=num_pro]')[i].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=num_fact]')[i - 1].select();
				}
				else
				{
					$$('input[id=num_fact]')[$$('input[id=num_fact]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=num_fact]').length - 1)
				{
					$$('input[id=num_fact]')[i + 1].select();
				}
				else
				{
					$$('input[id=num_fact]')[0].select();
				}
			}
		}
	});
}

var obtener_cia = function(i)
{
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ValidarFacturasCancelar.php',
			data: 'accion=obtener_cia&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					$$('input[id=nombre_cia]')[i].set('value', result);
				}
				else
				{
					$$('input[id=num_cia]')[i].set('value', $$('input[id=num_cia]')[i].retrieve('tmp', ''));

					boxFailure.setContent('La compa&ntilde;&iacute;a no est&aacute; en el cat&aacute;go').open();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=num_cia]')[i].set('value', '');
		$$('input[id=nombre_cia]')[i].set('value', '');
	}

	validar_fac(i);
}

var obtener_pro = function(i)
{
	if ($$('input[id=num_pro]')[i].get('value').getNumericValue() > 0)
	{
		new Request({
			url: 'ValidarFacturasCancelar.php',
			data: 'accion=obtener_pro&num_pro=' + $$('input[id=num_pro]')[i].get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					$$('input[id=nombre_pro]')[i].set('value', result);
				}
				else
				{
					$$('input[id=num_pro]')[i].set('value', $$('input[id=num_pro]')[i].retrieve('tmp', ''));

					boxFailure.setContent('El proveedor no est&aacute; en el cat&aacute;go').open();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=num_pro]')[i].set('value', '');
		$$('input[id=nombre_pro]')[i].set('value', '');
	}

	validar_fac(i);
}

var validar_fac = function(i)
{
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0
		&& $$('input[id=num_pro]')[i].get('value').getNumericValue() > 0
		&& $$('input[id=num_fact]')[i].get('value') != '')
	{
		new Request({
			url: 'ValidarFacturasCancelar.php',
			data: 'accion=validar_factura&num_cia=' + $$('input[id=num_cia]')[i].get('value') + '&num_pro=' + $$('input[id=num_pro]')[i].get('value') + '&num_fact=' + $$('input[id=num_fact]')[i].get('value'),
			onRequest: function()
			{

			},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status == 1)
				{
					$$('input[id=data]')[i].set('value', result);

					document.id('status_' + i).set({
						html: 'OK',
						class: 'green'
					});
				}
				else if (data.status == -1)
				{
					$$('input[id=data]')[i].set('value', '');

					document.id('status_' + i).set({
						html: 'No existe la factura o no se ha validado con anterioridad',
						class: 'orange'
					});
				}
				else if (data.status == -2)
				{
					$$('input[id=data]')[i].set('value', '');

					document.id('status_' + i).set({
						html: 'La factura ya esta pagada el d&iacute;a ' + data.fecha_pago,
						class: 'red'
					});
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=data]')[i].set('value', '');
		document.id('status_' + i).set('html', '&nbsp;');
	}
}

var cancelar_validaciones = function()
{
	if (confirm('¿Son correctos todos los datos?'))
	{
		new Request({
			url: 'ValidarFacturasCancelar.php',
			data: 'accion=cancelar_validaciones&' + document.id('datos_form').toQueryString(),
			onRequest: function()
			{
				boxProcessing.open();
			},
			onSuccess: function()
			{
				boxProcessing.close();

				document.id('datos_table').empty();

				new_row(0);

				$$('input[id=num_cia]')[0].select();
			}
		}).send();
	}
}
