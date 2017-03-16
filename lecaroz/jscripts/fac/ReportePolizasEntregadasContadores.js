var barcode = '';
var ids = [];
var contador = null;
var validas = 0;
var invalidas = 0;

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
		id: 'box_alert',
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
	
	boxMessage = new mBox.Modal(
	{
		id: 'box_message',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Mensaje',
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

	boxConfirm = new mBox.Modal(
	{
		id: 'box_confirm',
		title: '<img src="/lecaroz/iconos/accept_green.png" width="16" height="16" /> Confirmar',
		content: '',
		buttons:
		[
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: do_nuevo
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
		title: '<img src="/lecaroz/iconos/stop_round.png" width="16" height="16" /> Error',
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

window.addEvent('unload', function()
{
	document.removeEvent('keydown', obtener_teclas_pulsadas);
});

var inicio = function ()
{
	new Request(
	{
		url: 'ReportePolizasEntregadasContadores.php',
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
			
			document.id('folios').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha1').focus();
					}
				}
			}).focus();

			document.id('fecha1').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('fecha2').focus();
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

						document.id('cias').focus();
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

						document.id('pros').focus();
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

						document.id('facs').focus();
					}
				}
			});

			document.id('facs').addEvents(
			{
				keydown: function(e)
				{
					if (e.key == 'enter')
					{
						e.stop();

						document.id('folios').focus();
					}
				}
			});
			
			document.id('nuevo').addEvent('click', nuevo);
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
		url: 'ReportePolizasEntregadasContadores.php',
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

				$$('img[id=mostrar]').each(function(el)
				{
					var folio = el.get('alt');

					el.addEvent('click', mostrar_reporte.pass(folio));
				});
				
				document.id('regresar').addEvent('click', inicio);
				
				boxProcessing.close();
			}
			else
			{
				boxAlert.setContent('No hay resultados').open();

				inicio();
			}
		}
	}).send();
}

var mostrar_reporte = function(folio)
{
	var url = 'ReportePolizasEntregadasContadores.php';
	var param = '?accion=mostrar_reporte&folio=' + folio;
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';

	var win = window.open(url + param, 'reporte', opt);

	win.focus();
}

var nuevo = function() {
	new Request({
		url: 'ReportePolizasEntregadasContadores.php',
		data: 'accion=nuevo&num_cia=' + arguments[0] + '&fecha=' + arguments[1],
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

			document.id('cancelar').addEvent('click', function()
			{
				document.removeEvent('keydown', obtener_teclas_pulsadas);

				inicio();
			});

			document.id('registrar').addEvent('click', function()
			{
				if (ids == '')
				{
					boxFailure.setContent('Debe agregar al menos una p&oacute;liza').open();

					return false;
				}
				else
				{
					var msg = '';

					if (validas > 0)
					{
						msg += 'Comprobantes v&aacute;lidos: ' + validas + '<br />';
					}

					if (invalidas > 0)
					{
						msg += 'Comprobantes inv&aacute;lidos: ' + invalidas + '<br />';
					}

					boxConfirm.setContent(msg + '<br />¿Son correctos todos los datos?').open();
				}
			});

			document.addEvent('keydown', obtener_teclas_pulsadas);

			barcode = '';
			ids = [];
			contador = null;
			validas = 0;
			invalidas = 0;
			
			boxProcessing.close();
		}
	}).send();
}

var do_nuevo = function() {
	boxConfirm.close();

	new Request({
		url: 'ReportePolizasEntregadasContadores.php',
		data: 'accion=do_nuevo&' + ids.map(function(id) { return 'id[]=' + id; }).join('&'),
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(folio) {
			if (folio != '')
			{
				mostrar_reporte(folio);
			}

			inicio();
		}
	}).send();
}

var obtener_teclas_pulsadas = function(e)
{
	var key_numbers = [ '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0 ];

	if (key_numbers.contains(e.key))
	{
		e.stop();

		barcode += e.key;
	}
	else if (e.key == 'backspace')
	{
		e.stop();

		barcode = barcode.substring(0, barcode.length - 1);
	}
	else if (e.key == 'enter')
	{
		e.stop();
			
		buscar_poliza(barcode.toInt(10));

		barcode = '';
	}
}

var buscar_poliza = function(id)
{
	if (ids.contains(id))
	{
		boxFailure.setContent('La p&oacute;liza ya est&aacute; en la lista').open();
	}
	else if (id > 0)
	{
		new Request(
		{
			url: 'ReportePolizasEntregadasContadores.php',
			data: 'accion=obtener_poliza&id=' + id,
			onRequest: function() {},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status == 1)
				{
					if (contador == null)
					{
						contador = data.contador;
					}

					if (contador != data.contador)
					{
						invalidas++;

						boxFailure.setContent('La p&oacute;liza no pertenece al contador ' + contador + ', pertenece al contador ' + data.contador).open();

						return false;
					}

					validas++;

					new_poliza(data);

					ids.push(id);
				}
				else if (data.status == -1)
				{
					boxFailure.setContent('La p&oacute;liza ya est&aacute; gestionada en un reporte anterior con folio ' + data.folio_conta).open();
				}
				else if (data.status == -2)
				{
					boxFailure.setContent('La p&oacute;liza no est&aacute; en el sistema').open();
				}
			}
		}).send();
	}
}

var new_poliza = function(data)
{
	var table = document.id('datos-table');
	var tbody = new Element('tbody', { id: 'poliza-' + data.id }).inject(table);

	var tr = [];
	var td1 = [];
	var td2 = [];
	var td3 = [];
	var td4 = [];
	var td5 = [];
	var td6 = [];
	var td7 = [];
	var td8 = [];
	var td9 = [];

	var total = 0;

	data.comprobantes.each(function(row, i)
	{
		tr[i] = new Element('tr').inject(tbody);

		td1[i] = new Element('td', { html: data.num_cia + ' ' + data.nombre_cia }).inject(tr[i]);
		td2[i] = new Element('td', { html: data.banco }).inject(tr[i]);
		td3[i] = new Element('td', { html: (data.cancelado ? ' [CANCELADO]' : '') + data.folio, class: 'left' + (data.cancelado ? ' red' : '') }).inject(tr[i]);
		td4[i] = new Element('td', { html: data.fecha, class: 'center' }).inject(tr[i]);
		td5[i] = new Element('td', { html: data.num_pro + ' ' + data.nombre_pro }).inject(tr[i]);
		td6[i] = new Element('td', { html: row.num_fact, class: 'right' }).inject(tr[i]);
		td7[i] = new Element('td', { html: row.fecha, class: 'center' }).inject(tr[i]);
		td8[i] = new Element('td', { html: data.gasto + ' ' + data.nombre_gasto }).inject(tr[i]);
		td9[i] = new Element('td', { html: row.importe.numberFormat(2, '.', ','), class: 'right' }).inject(tr[i]);

		total += row.importe;
	});

	if (data.comprobantes.length > 1)
	{
		var trf = new Element('tr').inject(tbody);
		var th1 = new Element('th', { html: 'Total', colspan: 8, class: 'right' }).inject(trf);
		var th2 = new Element('th', { html: total.numberFormat(2, '.', ','), class: 'right' }).inject(trf);
	}

	var trb2 = new Element('tr').inject(tbody);
	var tdb2 = new Element('td', { html: '&nbsp;', colspan: 9 }).inject(trb2);
}
