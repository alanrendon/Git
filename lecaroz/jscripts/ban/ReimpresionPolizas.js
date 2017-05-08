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
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n',
		content: '',
		buttons: [
			{
				title: 'Aceptar'
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

	validator = new FormValidator(document.id('captura_polizas'), {
		showErrors: true,
		selectOnFocus: true
	});

	document.id('reimprimir').addEvent('click', reimprimir);

	new_row(0);

	$$('input[id=num_cia]')[0].select();

});

var new_row = function(i) {
	var tbody = document.id('rows'),
		tr = new Element('tr').inject(tbody),
		td1 = new Element('td').inject(tr),
		td2 = new Element('td').inject(tr),
		td3 = new Element('td').inject(tr),
		td4 = new Element('td').inject(tr),
		num_cia = new Element('input', {
			id: 'num_cia',
			name: 'num_cia[]',
			type: 'text',
			class: 'validate focus toPosInt right',
			size: 3
		}).addEvents({
			change: obtener_cia.pass(i),
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=folio]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=num_cia]')[i - 1].select();
					} else {
						$$('input[id=num_cia]')[$$('input[id=num_cia]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=num_cia]').length - 1) {
						$$('input[id=num_cia]')[i + 1].select();
					} else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		}).inject(td1),
		nombre_cia = new Element('input', {
			id: 'nombre_cia',
			name: 'nombre_cia[]',
			type: 'text',
			size: 30,
			disabled: true
		}).inject(td1),
		banco = new Element('select', {
			id: 'banco',
			name: 'banco[]',
			class:'logo_banco'
		}).addEvent('change', function() {
			switch (this.get('value').getNumericValue()) {
				case 1:
					this.removeClass('logo_banco_2').addClass('logo_banco_1');
					break;

				case 2:
					this.removeClass('logo_banco_1').addClass('logo_banco_2');
					break;
			}
		}).inject(td2),
		folio = new Element('input', {
			id: 'folio',
			name: 'folio[]',
			type: 'text',
			class: 'validate focus toPosInt right',
			size: 6
		}).addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=fecha]')[i].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=num_cia]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=folio]')[i - 1].select();
					} else {
						$$('input[id=folio]')[$$('input[id=folio]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=folio]').length - 1) {
						$$('input[id=folio]')[i + 1].select();
					} else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		}).inject(td3);
		fecha = new Element('input', {
			id: 'fecha',
			name: 'fecha[]',
			type: 'text',
			class: 'validate focus toDate center',
			size: 6
		}).addEvents({
			keydown: function(e) {
				if (e.key == 'enter') {
					e.stop();

					if (i + 1 > $$('input[id=num_cia]').length - 1) {
						new_row(i + 1);
					}

					$$('input[id=num_cia]')[i + 1].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=num_cia]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=fecha]')[i - 1].select();
					} else {
						$$('input[id=fecha]')[$$('input[id=fecha]').length - 1].select();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=fecha]').length - 1) {
						$$('input[id=fecha]')[i + 1].select();
					} else {
						$$('input[id=num_cia]')[0].select();
					}
				}
			}
		}).inject(td4);

	validator.addElementEvents(num_cia);
	validator.addElementEvents(folio);
	validator.addElementEvents(fecha);

	update_select(banco, [
		{
			value: 1,
			text: 'BANORTE'
		},
		{
			value: 2,
			text: 'SANTANDER'
		}
	]);

	if (i > 0) {
		banco.selectedIndex = $$('select[id=banco]')[i - 1].selectedIndex
	} else {
		banco.selectedIndex = 0;
	}

	banco.fireEvent('change');
}

var obtener_cia = function(i) {
	if ($$('input[id=num_cia]')[i].get('value').getNumericValue() > 0) {
		new Request({
			url: 'ReimpresionPolizas.php',
			data: 'accion=obtener_cia&num_cia=' + $$('input[id=num_cia]')[i].get('value'),
			onSuccess: function(result) {
				if (result != '') {
					$$('input[id=nombre_cia]')[i].set('value', result);
				} else {
					$$('input[id=num_cia]')[i].set('value', $$('input[id=num_cia]')[i].retrieve('tmp', ''));

					alert('La compañía no esta en el catálogo');
				}
			}
		}).send();
	} else {
		$$('input[id=num_cia]')[i].set('value', '');
		$$('input[id=nombre_cia]')[i].set('value', '');
	}
}

var reimprimir = function() {
	new Request({
		url: 'ReimpresionPolizas.php',
		data: 'accion=reimprimir&' + document.id('captura_polizas').toQueryString(),
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(result) {
			document.id('rows').empty();

			new_row(0);

			boxProcessing.close();

			if (result != '') {
				box.setContent(result).open();
			}
		}
	}).send();
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
