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
	
	boxConciliar = new mBox.Modal({
		id: 'box_conciliar',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Conciliar movimientos de inversión',
		content: 'conciliar_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					do_conciliar();
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
			new FormValidator(document.id('conciliar_movimientos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('concepto').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						this.blur();
						this.focus();
					}
				}
			});
			
		},
		onOpenComplete: function() {
			document.id('concepto').focus();
		}
	});
	
	boxComplete = new mBox.Modal({
		id: 'box_complete',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Movimientos conciliados',
		content: 'complete_wrapper',
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
		closeInTitle: true
	});
	
	consultar();
	
});

var consultar = function () {
	new Request({
		url: 'ConciliarMovimientosInversionSantander.php',
		data: 'accion=consultar',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);
			
			if (!!document.id('conciliar')) {
				document.id('conciliar').addEvent('click', function() {
					$$('#fecha_movimientos, #fecha_complete').set('text', document.getElementsByName('fecha')[0].get('value'));
					
					document.id('concepto').set('value', 'INVERSION A 14 DIAS');
					
					boxConciliar.open();
				});
			}
			
			boxProcessing.close();
		}
	}).send();
}

var do_conciliar = function() {
	if (document.id('concepto').get('value') == '') {
		alert('Debe especificar el concepto para conciliar los movimientos');
		
		document.id('concepto').focus();
	} else {
		new Request({
			url: 'ConciliarMovimientosInversionSantander.php',
			data: 'accion=conciliar&fecha=' + document.getElementsByName('fecha')[0].get('value') + '&concepto=' + encodeURI(document.id('concepto').get('value')),
			onRequest: function() {
				boxConciliar.close();
				
				boxProcessing.open();
				
				document.id('captura').empty();
			},
			onSuccess: function(result) {
				consultar();
				
				boxComplete.open();
			}
		}).send();
	}
}
