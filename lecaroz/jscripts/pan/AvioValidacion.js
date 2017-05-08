var codmp = null;

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

	boxAuxiliar = new mBox.Modal(
	{
		id: 'box_auxiliar',
		title: '<img src="/lecaroz/iconos/article_text.png" width="16" height="16" /> Auxiliar de inventario',
		content: 'auxiliar_wrapper',
		buttons: [
			{ title: 'Cerrar' }
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
			document.id('auxiliar_frame').set('src', 'AuxiliarInventario.php?accion=reporte&num_cia=' + document.id('num_cia').get('value')
				+ '&anio=' + document.id('anio').get('value')
				+ '&mes=' + document.id('mes').get('value')
				+ '&codmp=' + codmp
				+ '&inv=virtual');
		},
		onCloseComplete: function()
		{
			document.id('auxiliar_frame').set('src', '');
		}
	});

	inicio();

});

var inicio = function()
{
	new Request(
	{
		url: 'AvioValidacion.php',
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

			$$('button.revisar').addEvent('click', function()
			{
				document.id('num_cia').set('value', this.get('data-cia'));
				document.id('fecha').set('value', this.get('data-fecha'));

				consultar();
			});

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
		url: 'AvioValidacion.php',
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

				$$('a.aux').addEvent('click', function()
				{
					codmp = this.get('data-mp');

					boxAuxiliar.open();
				});

				document.id('regresar').addEvent('click', inicio);

				document.id('validar').addEvent('click', validar);

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

var validar = function()
{
	document.id('validar').set('disabled', true);

	new Request(
	{
		url: 'AvioValidacion.php',
		data: 'accion=validar&' + document.id('consulta_form').toQueryString(),
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
