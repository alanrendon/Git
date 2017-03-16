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
		url: 'RosticeriasCatalogoDistribucionSemanal.php',
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

			document.id('cias').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();

						this.blur();
						this.focus();
					}
				}
			}).focus();

			document.id('consultar').addEvent('click', consultar);

			boxProcessing.close();
		}
	}).send();
}

var consultar = function () {
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}

	new Request({
		url: 'RosticeriasCatalogoDistribucionSemanal.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();

			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').set('html', result);

			$$('img[id^=dia_icon]').each(function(img)
			{
				img.addEvents(
				{
					'click': function(e)
					{
						var id = img.get('data-id');
						var num_cia = img.get('data-cia');
						var num_pro = img.get('data-pro');
						var dia = img.get('data-dia');
						var fecha = document.id('fecha_inicio_semana').get('value');

						new Request(
						{
							'url': 'RosticeriasCatalogoDistribucionSemanal.php',
							'data': 'accion=cambiar_status&fecha=' + fecha + '&num_pro=' + num_pro + '&num_cia=' + num_cia + '&dia=' + dia + '&id=' + id,
							'onRequest': function()
							{
								img.set('src', '/lecaroz/imagenes/_loading.gif');
							},
							'onSuccess': function(result)
							{
								var data = JSON.decode(result);

								if (data.status == 1)
								{
									img.set('src', '/lecaroz/iconos/accept.png');
									img.set('data-id', data.id);
									$$('span[id=porc][data-cia=' + num_cia + ']').set('html', data.porc > 0 ? data.porc.numberFormat(2, '.', ',') : '');
								}
								else if (data.status == 0)
								{
									img.set('src', '/lecaroz/iconos/accept_blank.png');
									img.set('data-id', null);
									$$('span[id=porc][data-cia=' + num_cia + ']').set('html', data.porc > 0 ? data.porc.numberFormat(2, '.', ',') : '');
								}
							}
						}).send();
					}
				});
			});

			document.id('regresar').addEvent('click', inicio);

			boxProcessing.close();
		}
	}).send();
}
