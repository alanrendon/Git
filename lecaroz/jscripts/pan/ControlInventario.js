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
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" />',
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
	
	inicio();
	
});

var inicio = function ()
{
	new Request(
	{
		url: 'ControlInventario.php',
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
						// this.focus();
					}
				}
			}).focus();
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
		}
	}).send();
}

var obtener_cia = function()
{
	if (document.id('num_cia').get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ControlInventario.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				if (result != '')
				{
					document.id('nombre_cia').set('value', result);

					document.id('num_cia').select();
				}
				else
				{
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp', ''));

					boxFailure.setContent('La compa&ntilde;&iacute;a no est&aacute; en el cat&aacute;logo').open();
				}
			}
		}).send();
	}
	else
	{
		$$('#num_cia, #nombre_cia').set('value', '');
	}
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
		url: 'ControlInventario.php',
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
				
				document.id('alta').addEvent('click', alta.pass([document.id('num_cia').get('value'), document.id('fecha').get('value')]));
				
				document.id('regresar').addEvent('click', inicio);
				
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
		url: 'ControlInventario.php',
		data: 'accion=alta&num_cia=' + arguments[0] + '&fecha=' + arguments[1],
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

			$$('input[id=codmp]')[0].select();
			
			document.id('cancelar').addEvent('click', consultar.pass(param));

			document.id('registrar').addEvent('click', do_alta);
			
			boxProcessing.close();
		}
	}).send();
}

var do_alta = function() {
	if (confirm('¿Son correctos todos los datos?')) {
		new Request({
			url: 'ControlInventario.php',
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

var obtener_mp = function(i)
{
	if ($$('input[id=codmp]').get('value').getNumericValue().filter(function(value, j) {
		return j != i && value > 0;
	}).contains($$('input[id=codmp]')[i].get('value').getNumericValue()))
	{
		$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', ''));

		boxFailure.setContent('El producto ya esta en la lista').open();
	}
	else if ($$('input[id=codmp]')[i].get('value').getNumericValue() > 0)
	{
		new Request(
		{
			url: 'ControlInventario.php',
			data: 'accion=obtener_mp&codmp=' + $$('input[id=codmp]')[i].get('value') + '&num_cia=' + document.id('num_cia').get('value') + '&fecha=' + document.id('fecha').get('value'),
			onRequest: function() {},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);
				if (data.status == 1)
				{
					$$('input[id=nombre_mp]')[i].set('value', data.nombre);
					$$('input[id=data]')[i].set('value', result);
					document.id('status_' + i).set(
					{
						html: 'OK',
						class: 'green'
					});
				}
				else if (data.status == -1)
				{
					$$('input[id=nombre_mp]')[i].set('value', data.nombre);
					$$('input[id=data]')[i].set('value', '');
					document.id('status_' + i).set(
					{
						html: 'El producto ya est&aacute; en el inventario de la panader&iacute;a',
						class: 'orange'
					});
				}
				else if (data.status == -2)
				{
					$$('input[id=codmp]')[i].set('value', $$('input[id=codmp]')[i].retrieve('tmp', ''));

					boxFailure.setContent('El producto no est&aacute; en el cat&aacute;logo').open();
				}
			}
		}).send();
	}
	else
	{
		$$('input[id=codmp]')[i].set('value', '');
		$$('input[id=nombre_mp]')[i].set('value', '');
		$$('input[id=data]')[i].set('value', '');
		document.id('status_' + i).set(
		{
			html: '&nbsp;',
			class: ''
		});
	}
}

var new_row = function(i)
{
	var table = document.id('datos-table');
	var tr = new Element('tr').inject(table);
	var td1 = new Element('td').inject(tr);
	var td2 = new Element('td').inject(tr);

	var codmp = new Element('input',
	{
		name: 'codmp[]',
		id: 'codmp',
		type: 'text',
		class: 'validate focus toPosInt right',
		size: 3
	}).inject(td1);
	var nombre_mp = new Element('input',
	{
		name: 'nombre_mp[]',
		id: 'nombre_mp',
		type: 'text',
		size: 30,
		disabled: 'disabled'
	}).inject(td1);

	var data = new Element('input', {
		name: 'data[]',
		id: 'data',
		type: 'hidden',
		value: ''
	}).inject(td2);
	var status = new Element('span', {
		id: 'status_' + i,
		html: '&nbsp;'
	}).inject(td2);

	validator.addElementEvents(codmp).addEvents(
	{
		change: obtener_mp.pass(i),
		keydown: function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();

				if (i + 1 > $$('input[id=codmp]').length - 1)
				{
					new_row(i + 1);
				}

				$$('input[id=codmp]')[i + 1].select();
			}
			else if (e.key == 'up')
			{
				e.stop();

				if (i > 0)
				{
					$$('input[id=codmp]')[i - 1].select();
				}
				else
				{
					$$('input[id=codmp]')[$$('input[id=codmp]').length - 1].select();
				}
			}
			else if (e.key == 'down')
			{
				e.stop();

				if (i < $$('input[id=codmp]').length - 1)
				{
					$$('input[id=codmp]')[i + 1].select();
				}
				else
				{
					$$('input[id=codmp]')[0].select();
				}
			}
		}
	});
}
