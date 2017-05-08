window.addEvent('domready', function() {
	
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
	
	box = new mBox.Modal({
		id: 'box',
		title: 'Mensaje',
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

	document.id('xml_file').addEvent('change', validar_cfd);

	document.id('guardar').addEvent('click', guardar_archivos);

	document.id('cancelar').addEvent('click', cancelar);
	
});

var validar_cfd = function()
{
	if (document.id('xml_file').get('value') == '')
	{
		boxFailure.setContent('Debe seleccionar un documento XML').open();
	}
	else
	{
		var request = new Request.File({
			url: 'GuardarCFDZapaterias.php',
			onRequest: function()
			{
				boxProcessing.open();
				
				document.id('status').set('html', 'Validando archivo XML');
			},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status < 0)
				{
					document.id('status').set('html', '<span class="bold red">' + data.error + '</span>');

					document.id('pdf_file').set('disabled', true).set('value', '');

					document.id('xml_file').set('value', '');
					document.id('pdf_file').set('value', '');
					document.id('datos').set('value', '');
				}
				else
				{
					document.id('pdf_file').set('disabled', false).set('value', '');

					document.id('datos').set('value', result);

					if (data.xml_file != null)
					{
						document.id('status').set('html', 'La factura ya tiene un comprobante asociado, al guardar se perderan los anteriores');
					}
					else
					{
						document.id('status').set('html', 'Factura encontrada, seleccione el documento PDF asociado y presione "Guardar archivos" para terminar');
					}
				}
				
				boxProcessing.close();
			}
		});

		request.append('accion', 'validar_xml');
		request.append('xml_file', $('xml_file').files[0]);

		request.send();
	}
}

var guardar_archivos = function () {
	if (document.id('xml_file').get('value') == '')
	{
		boxFailure.setContent('Debe seleccionar un documento XML').open();
	}
	else if (document.id('pdf_file').get('value') == '')
	{
		boxFailure.setContent('Debe seleccionar el documento PDF correspondiente al comprobante fiscal digital').open();
	}
	else
	{
		var request = new Request.File({
			url: 'GuardarCFDZapaterias.php',
			onRequest: function()
			{
				boxProcessing.open();
				
				document.id('status').set('html', 'Guardando archivos');
			},
			onSuccess: function(result)
			{
				var data = JSON.decode(result);

				if (data.status < 0)
				{
					boxFailure.setContent(data.error).open();

					document.id('status').set('html', '<span class="bold red">' + data.error + '</span>');

					if (data.status == -4)
					{
						document.id('pdf_file').set('value', '');
					}
				}
				else
				{
					$$('#datos, #xml_file, #pdf_file').set('value', '');
					document.id('pdf_file').set('disabled', true);
					document.id('status').set('html', '<span class="bold blue">Archivos guardados</span>');
				}
				
				boxProcessing.close();
			}
		});

		request.append('accion', 'guardar_archivos');
		request.append('datos', document.id('datos').get('value'));
		request.append('xml_file', document.id('xml_file').files[0]);
		request.append('pdf_file', document.id('pdf_file').files[0]);

		request.send();
	}
}

var cancelar = function()
{
	$$('#datos, #xml_file, #pdf_file').set('value', '');
	document.id('status').set('html', '&nbsp;');
}
