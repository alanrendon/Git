// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'FacturasElectronicasPanaderias.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('omitir').select();
					}
				}
			});
			
			$('omitir').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('anio').select();
					}
				}
			});
			
			$('anio').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('dia').select();
					}
				}
			});
			
			$('dia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('cias').select();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('cias').select();
		}
	}).send();
}

var Consultar = function() {
	if ($('anio').get('value').getNumericValue() == 0) {
		alert('Debe especificar el anio de consulta');
		$('anio').select();
	}
	else {
		new Request({
			'url': 'FacturasElectronicasPanaderias.php',
			'data': 'accion=consultar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Consultando...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('captura').empty().set('html', result);
					
					$$('input[id=checkblock]').each(function(el) {
						el.addEvent('click', CheckBlock.pass(el));
					});
					
					$$('input[id=datos]').each(function(el, index) {
						el.addEvent('click', CalcularFacturaDia.pass([el, index]));
					});
					
					$('regresar').addEvent('click', Inicio);
					
					$('generar').addEvent('click', Generar);
				}
				else {
					Inicio();
					
					alert('No hay resultados');
				}
			}
		}).send();
	}
}

var CalcularFacturaDia = function() {
	var el = arguments[0],
		index = arguments[1],
		data = JSON.decode(el.get('value')),
		arrastre_diferencia = $$('input[id=arrastre_diferencia][cia=' + data.num_cia + ']')[0];
	
	if (el.get('checked')) {
		if (index > 0
			&& !$$('input[id=datos]')[index - 1].get('disabled')
			&& !$$('input[id=datos]')[index - 1].get('checked')) {
			alert('No puede seleccionar este d\xeda si no ha seleccionado los d\xedas anteriores');
			
			el.set('checked', false);
		}
		else {
			data.facturas_panaderia = data.depositos - data.facturas_pagadas + arrastre_diferencia.get('value').getNumericValue();
			
			if (data.facturas_panaderia < 0) {
				data.diferencia = data.facturas_panaderia;
				
				data.facturas_panaderia = 0;
			}
			else {
				data.diferencia = data.depositos - data.facturas_pagadas - data.facturas_panaderia;
			}
			
			data.arrastre = arrastre_diferencia.get('value').getNumericValue();
			
			arrastre_diferencia.set('value', data.facturas_panaderia > 0 ? 0 : data.diferencia);
			
			$('fp-' + data.num_cia + '-' + data.dia).set('text', data.facturas_panaderia != 0 ? '(1) ' + data.facturas_panaderia.numberFormat(2, '.', ',') : '');
			$('dif-' + data.num_cia + '-' + data.dia).set('text', data.diferencia != 0 ? data.diferencia.numberFormat(2, '.', ',') : '').addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}
	else {
		if (index < $$('input[id=datos]').length - 1
			&& !$$('input[id=datos]')[index + 1].get('disabled')
			&& $$('input[id=datos]')[index + 1].get('checked')) {
				alert('No puede deseleccionar este d\xeda si no ha deseleccionado los d\xedas posteriores');
				
				el.set('checked', true);
		}
		else {
			data.facturas_panaderia = 0;
			
			data.diferencia = data.depositos - data.facturas_pagadas;
			
			arrastre_diferencia.set('value', data.arrastre);
			
			data.arrastre = 0;
			
			$('fp-' + data.num_cia + '-' + data.dia).set('text', '');
			$('dif-' + data.num_cia + '-' + data.dia).set('text', data.diferencia.numberFormat(2, '.', ',')).addClass(data.diferencia >= 0 ? 'blue' : 'red').removeClass(data.diferencia >= 0 ? 'red' : 'blue');
			el.set('value', JSON.encode(data));
		}
	}
	
	CalcularTotalCia(data.num_cia);
}

var CalcularTotalCia = function() {
	var cia = arguments[0],
		facturas_panaderia = 0,
		diferencia = 0;
	
	$$('input[id=datos][cia=' + cia + ']').each(function(el) {
		data = JSON.decode(el.get('value'));
		
		facturas_panaderia += data.facturas_panaderia;
		diferencia += data.diferencia;
	});
	
	$('tfp-' + cia).set('text', facturas_panaderia.numberFormat(2, '.', ','));
	$('tdif-' + cia).set('text', diferencia.numberFormat(2, '.', ',')).addClass(diferencia >= 0 ? 'blue' : 'red').removeClass(diferencia >= 0 ? 'red' : 'blue');
}

var CheckBlock = function() {
	var cia = arguments[0].get('cia'),
		checked = arguments[0].get('checked');
	
	if (!checked) {
		$$('input[id=datos][cia=' + cia + ']').filter(function(el) {
			return !el.get('disabled');
		}).reverse().each(function(el) {
			el.set('checked', false);
			el.fireEvent('click');
		});
	}
	else {
		$$('input[id=datos][cia=' + cia + ']').filter(function(el) {
			return !el.get('disabled');
		}).each(function(el) {
			el.set('checked', true);
			el.fireEvent('click');
		});
	}
}

var Generar = function() {
	if ($$('input[id=datos]:checked').length == 0) {
		alert('Debe seleccionar al menos un día');
	}
	else if (confirm('¿Desea generar las facturas electrónicas de los días seleccionados?')) {
		new Request({
			'url': 'FacturasElectronicasPanaderias.php',
			'data': 'accion=generar&' + $('TablaDatos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'html': ' Generando facturas electr&oacute;nicas, por favor espere a que el proceso termine...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				if (result == '') {
					Inicio();
				}
				else if (result == '-1') {
					alert('Error al conectar al servidor de CFD');
					
					Inicio();
				}
				else {
					$('captura').empty().set('html', result);
					
					$('terminar').addEvent('click', Inicio);
				}
			}
		}).send();
	}
}
