// JavaScript Document

var f;

window.addEvent('domready', function() {
	formDatos();
});

var formDatos = function() {
	new Request({
		url: 'ProcesoPagosAutomaticoZap.php',
		data: {
			accion: 'datos'
		},
		onSuccess: function(result) {
			$('captura').set('html', result);
			
			f = new Formulario('Datos');
			
			$('cias_intervalo').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('pros_intervalo').focus();
				}
			});
			
			$('pros_intervalo').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha_corte').focus();
				}
			});
			
			$('fecha_corte').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha_cheque').focus();
				}
			});
			
			$('fecha_cheque').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('dias_deposito').focus();
				}
			});
			
			$('dias_deposito').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('pros_sin_pago').focus();
				}
			});
			
			$('pros_sin_pago').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('cias_no_pago').focus();
				}
			});
			
			$('cias_no_pago').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('pagos_obligados').focus();
				}
			});
			
			$('pagos_obligados').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('num_cheques').focus();
				}
			});
			
			$('num_cheques').addEvent('keydown', function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('cias_intervalo').focus();
				}
			});
			
			$('siguiente').addEvent('click', formResult);
			
			$('cias_intervalo').focus();
		}
	}).send();
}

var formResult = function() {
	new Request({
		url: 'ProcesoPagosAutomaticoZap.php',
		data: 'accion=buscar&' + $('Datos').toQueryString(),
		onRequest: function() {
			$('captura').empty();
			
			var p = new Element('p', {
				html: 'Buscando facturas...',
				'class': 'font14 bold'
			}).inject($('captura'));
			
			new Element('img', {
				src: 'imagenes/ajax-loader.gif'
			}).inject(p);
		},
		onSuccess: function(result) {$('captura').set('html', result);
			if (result.toInt() < 0) {
				switch (result.toInt()) {
					case -1:
						alert('No hay saldos para las compañías seleccionadas');
						formDatos();
					break;
					
					case -2:
						alert('No hay facturas para pagar');
						formDatos();
					break;
				}
			}
			
			$('checkall').addEvent('click', function() {
				$$('input[id=id]').set('checked', this.get('checked'));
				
				$$('input[id=checkblock]').set('checked', this.get('checked'));
				
				$$('input[id=checkblock]').fireEvent('change');
			});
			
			$$('input[id=checkblock]').addEvents({
				click: function() {
					$$('input[id=id][alt=' + this.get('alt') + ']').set('checked', this.get('checked'));
				},
				change: function() {
					calculaTotalCia(this.get('alt'));
				}
			});
			
			$$('input[id=id]').addEvent('change', function() {
				calculaTotalCia(this.get('alt'));
			});
			
			$('regresar').addEvent('click', function() {
				formDatos();
			});
			
			$('siguiente').addEvent('click', function() {
				var ids = [];
				
				ids = $$('input[id=id]').filter(function(el) {
					return el.checked;
				}).get('value').map(function(el) {
					return 'id[]=' + encodeURIComponent(el);
				}).join('&');
				
				if (ids == '') {
					alert('Debe seleecionar al menos un movimiento');
					return fasle;
				}
				
				new Request({
					url: 'ProcesoPagosAutomaticoZap.php',
					data: 'accion=generar&fecha_cheque=' + $('fecha_cheque').get('value') + '&' + ids,
					onRequest: function() {
						$('captura').empty();
			
						var p = new Element('p', {
							html: 'Realizando pagos...',
							'class': 'font14 bold'
						}).inject($('captura'));
						
						new Element('img', {
							src: 'imagenes/ajax-loader.gif'
						}).inject(p);
					},
					onSuccess: function(result) {
						$('captura').set('html', result);
						
						$('regresar').addEvent('click', formDatos);
					}
				}).send();
			});
		}
	}).send();
}

var calculaTotalCia = function() {
	var num_cia = arguments[0];
	var total = 0;
	
	$$('input[id=id][alt=' + num_cia + ']').each(function(el) {
		total += el.get('checked') ? el.get('value').split('|')[4].getVal() : 0;
	});
	
	$('total' + num_cia).set('text', total.numberFormat(2, '.', ','));
	
	calculaTotal();
}

var calculaTotal = function() {
	var total = 0;
	
	$$('[id^=total]').each(function(el) {
		total += el.get('text').getVal();
	});
	
	$('gran_total').set('text', total.numberFormat(2, '.', ','));
	$('num_facts').set('text', $$('input[id=id]').filter(function(el) {
		return el.checked;
	}).length.numberFormat(0, '', ','));
}