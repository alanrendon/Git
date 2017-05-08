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

	boxSeleccionarCliente = new mBox.Modal({
		id: 'box_seleccionar_cliente',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Seleccionar cliente',
		content: 'datos_cliente_wrapper',
		buttons: [
			{ title: 'Cancelar' }
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

	boxStatus = new mBox.Modal({
		id: 'box_status',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Estatus de factura',
		content: '',
		buttons: [
			{
				title: 'Aceptar',
				event: function() {
					reset_form();

					boxStatus.close();

					self.close();
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

	boxFailure = new mBox.Modal({
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

	inicializar();

});

var inicializar = function()
{
	validator = new FormValidator(document.id('fe_form'),
	{
		showErrors: true,
		selectOnFocus: true
	});
	
	document.id('rfc').addEvents(
	{
		'change': function()
		{
			if (this.get('value') != '')
			{
				buscar_datos_cliente();
			}
		},
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('nombre_cliente').focus();
			}
		}
	}).focus();

	document.id('buscar').addEvent('click', buscar_datos_cliente);

	document.id('nombre_cliente').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('calle').focus();
			}
		}
	});
	
	document.id('calle').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('no_exterior').focus();
			}
		}
	});
	
	document.id('no_exterior').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('no_interior').focus();
			}
		}
	});
	
	document.id('no_interior').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('colonia').focus();
			}
		}
	});
	
	document.id('colonia').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('localidad').focus();
			}
		}
	});
	
	document.id('localidad').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('referencia').focus();
			}
		}
	});
	
	document.id('referencia').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('municipio').focus();
			}
		}
	});
	
	document.id('municipio').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('estado').focus();
			}
		}
	});
	
	document.id('estado').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('pais').focus();
			}
		}
	});
	
	document.id('pais').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('codigo_postal').focus();
			}
		}
	});
	
	document.id('codigo_postal').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('email_cliente').focus();
			}
		}
	});
	
	document.id('email_cliente').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('cuenta_pago').focus();
			}
		}
	});

	document.id('cuenta_pago').addEvents(
	{
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				document.id('descripcion').focus();
			}
		}
	});
	
	document.id('conceptos').empty();

	nueva_fila(0);
	
	document.id('registrar').addEvent('click', registrar);
}

var buscar_datos_cliente = function()
{
	if (document.id('rfc').get('value') != '')
	{
		new Request({
			url: 'FacturaElectronicaPanaderias.php',
			data: 'accion=buscar_cliente&rfc=' + encodeURIComponent(document.id('rfc').get('value')) + '&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function()
			{
				document.id('rfc_status').set('html', '<img src="/lecaroz/imagenes/_loading.gif" width="16" height="16" /> Buscando datos del cliente...');
			},
			onSuccess: function(result)
			{
				if (result != '')
				{
					var data = JSON.decode(result);

					if (data.length == 1)
					{
						asignar_datos_cliente(data[0]);
					}
					else
					{
						var clientes = document.id('clientes');

						document.id('rfc_span').set('html', document.id('rfc').get('value'));

						clientes.empty();

						data.each(function(el, i)
						{
							var tr = new Element('tr').inject(clientes);

							var td1 = new Element('td',
							{
								align: 'center',
								class: 'cliente_td'
							}).addEvent('click', function(e)
							{
								asignar_datos_cliente(el);

								boxSeleccionarCliente.close();
							}).inject(tr);

							var td2 = new Element('td',
							{
								align: 'center'
							}).inject(tr);

							var nombre = new Element('div',
							{
								html: el.nombre,
								class: 'bold'
							}).inject(td1);

							var domicilio = new Element('div',
							{
								html: el.domicilio
							}).inject(td1);

							var opciones_pago = new Element('div',
							{
								html: el.opciones_pago
							}).inject(td1);

							var cross = new Element('img',
							{
								src: '/lecaroz/iconos/cancel.png',
								class: 'icono'
							}).addEvent('click', function()
							{
								borrar_datos_cliente(el.id, tr);
							}).inject(td2);

						});

						boxSeleccionarCliente.open();
					}

					document.id('rfc_status').set('html', '');
				}
				else
				{
					document.id('rfc_status').set('html', 'Nuevo cliente');

					$$('#nombre_cliente, #calle, #no_exterior, #no_interior, #colonia, #localidad, #referencia, #municipio, #codigo_postal, #email_cliente, #cuenta_pago').set('value', '');

					document.id('pais').set('value', 'MEXICO');

					document.id('estado').selectedIndex = 0;
					document.id('tipo_pago').selectedIndex = 0;
					document.id('condiciones_pago').selectedIndex = 0;

					document.id('conceptos').empty();
			
					nueva_fila(0);

					$$('#total_ieps, #subtotal, #iva, #total').set('value', '');
				}
			},
			onFailure: function(xhr)
			{
				var title = xhr.status + ' ' + xhr.statusText;
				var content = 'Ha ocurrido un error, favor de comunicarse con el administrador de sistemas.<div style="border: solid 1px #333; background:#eee; margin:20px 10px 10px 10px; padding:4px; width:600px; height:200px;overflow:auto;">' + xhr.responseText + '</div>';
			
				boxFailure.setTitle(title).setContent(content).open();
			}
		}).send();
	}
	else
	{
		document.id('rfc_status').set('html', '');

		$$('#rfc, #nombre_cliente, #calle, #no_exterior, #no_interior, #colonia, #localidad, #referencia, #municipio, #codigo_postal, #email_cliente, #cuenta_pago').set('value', '');

		document.id('pais').set('value', 'MEXICO');

		document.id('estado').selectedIndex = 0;
		document.id('tipo_pago').selectedIndex = 0;
		document.id('condiciones_pago').selectedIndex = 0;

		document.id('conceptos').empty();
			
		nueva_fila(0);

		$$('#total_ieps, #subtotal, #iva, #total').set('value', '');
	}
}

var asignar_datos_cliente = function(data)
{
	document.id('nombre_cliente').set('value', data.nombre);
	document.id('calle').set('value', data.calle);
	document.id('no_exterior').set('value', data.no_exterior);
	document.id('no_interior').set('value', data.no_interior);
	document.id('colonia').set('value', data.colonia);
	document.id('localidad').set('value', data.localidad);
	document.id('referencia').set('value', data.referencia);
	document.id('municipio').set('value', data.municipio);
	document.id('pais').set('value', data.pais);
	document.id('codigo_postal').set('value', data.codigo_postal);
	document.id('email_cliente').set('value', data.email).fireEvent('change');
	document.id('cuenta_pago').set('value', data.cuenta_pago);

	document.id('estado').getElements('option').each(function(option, i) {
		if (option.get('value') == data.estado)
		{
			document.id('estado').selectedIndex = i;
		}
	});

	document.id('tipo_pago').getElements('option').each(function(option, i) {
		if (option.get('value') == data.tipo_pago)
		{
			document.id('tipo_pago').selectedIndex = i;
		}
	});

	document.id('condiciones_pago').getElements('option').each(function(option, i) {
		if (option.get('value') == data.condiciones_pago)
		{
			document.id('condiciones_pago').selectedIndex = i;
		}
	});

	$$('input[id=descripcion]')[0].focus();
}

var borrar_datos_cliente = function(id, row)
{
	new Request({
		url: 'FacturaElectronicaPanaderias.php',
		data: 'accion=baja_cliente&id=' + id,
		onRequest: function() {},
		onSuccess: function()
		{
			row.destroy();
		}
	}).send();
}

var calcular_importe = function()
{
	var index = arguments[0];
	var cantidad = $$('input[id=cantidad]')[index].get('value').getNumericValue();
	var precio = $$('input[id=precio]')[index].get('value').getNumericValue();
	var ieps = 0;
	
	importe = cantidad * precio;
	
	if ($$('input[id=aplicar_ieps]')[index].get('checked') && cantidad > 0 && precio > 0)
	{
		ieps = (importe - importe / 1.08).round(2);
	}

	$$('input[id=ieps]')[index].set('value', ieps);
	$$('input[id=importe]')[index].set('value', importe > 0 || cantidad > 0 ? (importe - ieps).numberFormat(2, '.', ',') : '');
	
	calcular_total();
}

var calcular_total = function()
{
	var cantidad = 0;
	var subtotal = 0;
	var ieps = 0;
	var iva = 0;
	var total = 0;
	
	$$('input[id=importe]').each(function(el, i)
	{
		subtotal += el.get('value').getNumericValue();

		ieps += $$('input[id=ieps]')[i].get('value').getNumericValue();
		
		iva += $$('input[id=aplicar_iva]')[i].get('checked') ? (el.get('value').getNumericValue() * 0.16).round(2) : 0;
	});
	
	cantidad = $$('input[id=cantidad]').get('value').getNumericValue().sum();
	
	total = subtotal + ieps + iva;
	
	$('subtotal').set('value', subtotal > 0 || cantidad > 0 ? subtotal.numberFormat(2, '.', ',') : '');
	$('total_ieps').set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
	$('iva').set('value', iva > 0 ? iva.numberFormat(2, '.', ',') : '');
	$('total').set('value', total > 0 || cantidad > 0 ? total.numberFormat(2, '.', ',') : '');
}

var nueva_fila = function(i)
{
	var tr = new Element('tr',
	{
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	var td1 = new Element('td',
	{
		'align': 'center'
	});
	var td2 = new Element('td',
	{
		'align': 'center'
	});
	var td3 = new Element('td',
	{
		'align': 'center'
	});
	var td4 = new Element('td',
	{
		'align': 'center'
	});
	var td5 = new Element('td',
	{
		'align': 'center'
	});
	var td6 = new Element('td',
	{
		'align': 'center'
	});
	var td7 = new Element('td',
	{
		'align': 'center'
	});
	
	var descripcion = new Element('input',
	{
		'id': 'descripcion',
		'name': 'descripcion[]',
		'type': 'text',
		'class': 'valid toText toUpper cleanText',
		'size': 30,
		'maxlength': 100
	}).addEvent('keydown', function(e)
	{
		if (e.key == 'enter')
		{
			e.stop();
			
			$$('input[id=cantidad]')[i].select();
		}
	}).inject(td1);
	
	var cantidad = new Element('input',
	{
		'id': 'cantidad',
		'name': 'cantidad[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'size': 5,
		'precision': 2
	}).addEvents(
	{
		'change': calcular_importe.pass(i),
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				$$('input[id=precio]')[i].select();
			}
		}
	}).inject(td2);
	
	var precio = new Element('input',
	{
		'id': 'precio',
		'name': 'precio[]',
		'type': 'text',
		'class': 'valid Focus numberPosFormat right',
		'size': 8,
		'precision': 2
	}).addEvents(
	{
		'change': calcular_importe.pass(i),
		'keydown': function(e)
		{
			if (e.key == 'enter')
			{
				e.stop();
				
				$$('input[id=unidad]')[i].select();
			}
		}
	}).inject(td3);
	
	var unidad = new Element('input',
	{
		'id': 'unidad',
		'name': 'unidad[]',
		'type': 'text',
		'class': 'valid onlyText toUpper cleanText',
		'size': 10,
		'maxlength': 50
	}).addEvent('keydown', function(e)
	{
		if (e.key == 'enter')
		{
			e.stop();
			
			if ( ! ( !! $$('[id=descripcion]')[i + 1]))
			{
				nueva_fila(i + 1);
			}
			
			$$('[id=descripcion]')[i + 1].select();
		}
	}).inject(td4);
	
	var aplicar_ieps = new Element('input',
	{
		'id': 'aplicar_ieps',
		'name': 'aplicar_ieps[]',
		'type': 'checkbox',
		'value': i,
		'checked': false
	}).addEvent('change', calcular_importe.pass(i)).inject(td5);

	var ieps = new Element('input',
	{
		'id': 'ieps',
		'name': 'ieps[]',
		'type': 'hidden',
		'value': 0
	}).inject(td5);
	
	var aplicar_iva = new Element('input',
	{
		'id': 'aplicar_iva',
		'name': 'aplicar_iva[]',
		'type': 'checkbox',
		'value': i,
		'checked': false
	}).addEvent('change', calcular_total).inject(td6);
	
	var importe = new Element('input',
	{
		'id': 'importe',
		'name': 'importe[]',
		'type': 'text',
		'class': 'right',
		'size': 10,
		'readonly': true
	}).inject(td7);
	
	validator.addElementEvents(descripcion);
	validator.addElementEvents(cantidad);
	validator.addElementEvents(precio);
	validator.addElementEvents(unidad);
	
	td1.inject(tr);
	td2.inject(tr);
	td3.inject(tr);
	td4.inject(tr);
	td5.inject(tr);
	td6.inject(tr);
	td7.inject(tr);
	
	tr.inject(document.id('conceptos'));
}

var registrar = function()
{
	if (document.id('rfc').get('value') == '')
	{
		alert('Debe especificar el RFC del cliente');
		document.id('rfc').focus();
	}
	else if (document.id('nombre_cliente').get('value') == '')
	{
		alert('Debe especificar el nombre del cliente');
		document.id('nombre_cliente').focus();
	}
	else if (document.id('calle').get('value') == '')
	{
		alert('Debe especificar la calle');
		document.id('calle').focus();
	}
	else if (document.id('colonia').get('value') == '')
	{
		alert('Debe especificar la colonia');
		document.id('colonia').focus();
	}
	else if (document.id('municipio').get('value') == '')
	{
		alert('Debe especificar la delegación o municipio');
		document.id('municipio').focus();
	}
	else if (document.id('estado').get('value') == '')
	{
		alert('Debe especificar el estado');
		document.id('estado').focus();
	}
	else if (document.id('pais').get('value') == '')
	{
		alert('Debe especificar el pais');
		document.id('pais').set('value', 'MEXICO').focus();
	}
	else if (document.id('codigo_postal').get('value') == '')
	{
		alert('Debe especificar el código postal');
		document.id('codigo_postal').focus();
	}
	else if (confirm('¿Son correctos todos los datos?'))
	{
		new Request({
			'url': 'FacturaElectronicaPanaderias.php',
			'data': 'accion=registrar&' + document.id('fe_form').toQueryString(),
			'onRequest': function()
			{
				boxProcessing.open();
			},
			'onSuccess': function(result)
			{
				var data = JSON.decode(result);

				if (data.status < 0)
				{
					boxProcessing.close();

					var title = 'Error al procesar la factura';
					var content = 'Ha ocurrido un error, favor de comunicarse con la persona encargada de facturaci&oacute;n.<div style="border: solid 1px #333; background:#eee; margin:20px 10px 10px 10px; padding:4px; width:400px; height:100px;overflow:auto;">' + data.error + '</div>';
				
					boxFailure.setTitle(title).setContent(content).open();
				}
				else
				{
					boxProcessing.close();

					var content = data.email_status ? 'El comprobante ' + data.comprobante + ' ha sido enviado a los correos electr&oacute;nicos correspondientes.' : 'Error al enviar los comprobantes por correo electr&oacute;nico: , le sugerimos llamar a la oficina y solicitar el comprobante ' + data.comprobante;

					boxStatus.setContent(content).open();
				}
			},
			onFailure: function(xhr)
			{
				boxProcessing.close();

				var title = xhr.status + ' ' + xhr.statusText;
				var content = 'Ha ocurrido un error, favor de comunicarse con el administrador de sistemas.<div style="border: solid 1px #333; background:#eee; margin:20px 10px 10px 10px; padding:4px; width:600px; height:200px;overflow:auto;">' + xhr.responseText + '</div>';
			
				boxFailure.setTitle(title).setContent(content).open();
			}
		}).send();
	}
}

var reset_form = function()
{
	document.id('rfc_status').set('html', '');

	$$('#rfc, #nombre_cliente, #calle, #no_exterior, #no_interior, #colonia, #localidad, #referencia, #municipio, #codigo_postal, #email_cliente, #cuenta_pago').set('value', '');

	document.id('pais').set('value', 'MEXICO');

	document.id('estado').selectedIndex = 0;
	document.id('tipo_pago').selectedIndex = 0;
	document.id('condiciones_pago').selectedIndex = 0;

	document.id('conceptos').empty();
		
	nueva_fila(0);

	$$('#total_ieps, #subtotal, #iva, #total').set('value', '');
}
