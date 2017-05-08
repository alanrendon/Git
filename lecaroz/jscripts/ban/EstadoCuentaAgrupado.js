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

	inicio();

});

var inicio = function () {
	new FormValidator(document.id('inicio'), {
		showErrors: true,
		selectOnFocus: true
	});

	document.id('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				document.id(/*'fecha1'*/'conciliado1').select();
			}
		}
	});

	document.id('banco').addEvents({
		change: function() {
			switch (this.get('value').getNumericValue()) {

				case 1:
					this.removeClass('logo_banco_2').addClass('logo_banco_1');
					break;

				case 2:
					this.removeClass('logo_banco_1').addClass('logo_banco_2');
					break;

				default:
					this.removeClass('logo_banco_1').removeClass('logo_banco_2');
			}

			// obtener_codigos();
		}

	}).fireEvent('change');

	// document.id('fecha1').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('fecha2').select();
	// 		}
	// 	}
	// });

	// document.id('fecha2').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('conciliado1').focus();
	// 		}
	// 	}
	// });

	document.id('conciliado1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();

				document.id('conciliado2').select();
			}
		}
	});

	document.id('conciliado2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();

				document.id(/*'pros'*/'cias').focus();
			}
		}
	});

	// document.id('pros').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('folios').focus();
	// 		}
	// 	}
	// });

	// document.id('folios').addEvents({
	// 	'keydown': function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('gastos').focus();
	// 		}
	// 	}
	// });

	// document.id('gastos').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('importes').focus();
	// 		}
	// 	}
	// });

	// document.id('importes').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('concepto').focus();
	// 		}
	// 	}
	// });

	// document.id('concepto').addEvents({
	// 	keydown: function(e) {
	// 		if (e.key == 'enter') {
	// 			e.stop();

	// 			document.id('cias').select();
	// 		}
	// 	}
	// });

	// obtener_codigos();

	document.id('consultar').addEvent('click', consultar);

	boxProcessing.close();

	document.id('cias').focus();
}

var obtener_codigos = function() {
	new Request({
		'url': 'EstadoCuentaAgrupado.php',
		'data': 'accion=obtener_codigos&' + $('inicio').toQueryString(),
		'onSuccess': function(codigos) {
			update_select($('codigos'), JSON.decode(codigos));
		}
	}).send();
}

var consultar = function() {
	if (document.id('conciliado1').get('value').trim() == '' && document.id('conciliado2').get('value').trim() == '') {
		alert('No ha especificado el periodo de búsqueda');

		document.id('conciliado1').select();

		return false;
	}

	var url = 'EstadoCuentaAgrupado.php',
		data = '?accion=consultar&' + document.id('inicio').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;

	win = window.open(url + data, '', opt);
	win.focus();
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
