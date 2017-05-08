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
	
	boxDiferenciaSaldos = new mBox.Modal({
		id: 'box',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Diferencia en saldos',
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
		closeInTitle: true,
		onBoxReady: function() {
		},
		onOpenComplete: function() {
		}
	});
	
	box = new mBox.Modal({
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
	
	inicio();
	
});

var inicio = function () {
	new Request({
		url: 'ConciliacionBancariaAutomatica.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('siguiente').addEvent('click', cargar_archivo);
			
			boxProcessing.close();
			
			validar_diferencia_saldos();
		}
	}).send();
}

var validar_diferencia_saldos = function() {
	new Request({
		url: 'ConciliacionBancariaAutomatica.php',
		data: 'accion=validar_diferencia_saldos',
		onRequest: function() {},
		onSuccess: function(result) {
			if (result != '') {
				boxDiferenciaSaldos.setContent(result).open();
			}
		}
	}).send();
}

var cargar_archivo = function() {
	var request = new Request.File({
		url: 'ConciliacionBancariaAutomatica.php',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			inicio();
		},
		onFailure: function(xhr) {
			boxProcessing.close();
			
			var title = xhr.status + ' ' + xhr.statusText,
				content = 'Ha ocurrido un error, favor de comunicarse con el administrador de sistemas.<div style="border: solid 1px #333; background:#eee; margin:20px 10px 10px 10px; padding:4px; width:600px; height:200px;overflow:auto;">' + xhr.responseText + '</div>';
			
			boxFailure.setTitle(title).setContent(content).open();
		}
	});
	
	request.append('accion', 'procesar_archivo');
	request.append('archivo', document.id('archivo').files[0]);
	
	request.send();
}

var update_select = function() {
	var select = arguments[0],
		options = arguments[1];
	
	select.length = 0;
	
	if (options.length > 0) {
		select.length = options.length;
		
		Array.each(select.options, function(el, i) {
			el.set(options[i]);
		});
	} else {
		select.length = 1;
		Array.each(select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});
		
		select.selectedIndex = 0;
	}
}
