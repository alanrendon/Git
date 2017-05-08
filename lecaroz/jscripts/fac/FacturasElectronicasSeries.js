// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'FacturasElectronicasSeries.php',
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
						
						this.blur();
						this.select();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('cias').select();
		}
	}).send();
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	new Request({
		'url': 'FacturasElectronicasSeries.php',
		'data': 'accion=consultar&' + param,
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
				
				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('img[id=alta]').each(function(el) {
					el.addEvents({
						'click': Alta,
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						}
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=modificar]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						},
						'click': Modificar.pass(el.get('alt'))
					});
					
					el.removeProperty('alt');
				});
				
				$$('img[id=baja]').each(function(el) {
					el.addEvents({
						'mouseover': function() {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function() {
							el.setStyle('cursor', 'default');
						}
					});
					
					el.removeProperty('alt');
				});
				
				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'FacturasElectronicasSeries.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando pantalla de alta...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('num_cia').addEvents({
				'change': obtenerCia,
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('serie').select();
					}
				}
			});
			
			$('serie').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_inicial').select();
					}
				}
			});
			
			$('folio_inicial').addEvents({
				'change': function() {
					$('folio_actual').set('value', $('folio_inicial').get('value').getNumericValue() - 1);
				},
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_final').select();
					}
				}
			});
			
			$('folio_final').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_actual').select();
					}
				}
			});
			
			$('folio_actual').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('no_aprobacion').select();
					}
				}
			});
			
			$('no_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('fecha_aprobacion').select();
					}
				}
			});
			
			$('fecha_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('anio_aprobacion').select();
					}
				}
			});
			
			$('anio_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('serie_certificado').select();
					}
				}
			});
			
			$('serie_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('archivo_certificado').select();
					}
				}
			});
			
			$('archivo_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('contrasenia_certificado').select();
					}
				}
			});
			
			$('contrasenia_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('archivo_llave').select();
					}
				}
			});
			
			$('archivo_llave').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('contrasenia_llave').select();
					}
				}
			});
			
			$('contrasenia_llave').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('num_cia').select();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('alta').addEvent('click', registrarAlta);
			
			$('num_cia').focus();
		}
	}).send();
}

var obtenerCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'FacturasElectronicasSeries.php',
			'data': 'accion=obtenerCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compa\xf1\xeda no se encuentra en el cat\xe1logo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', ''));
				}
			}
		}).send();
	}
	else {
		$$('#num_cia', '#nombre_cia').set('value', '');
	}
}

var registrarAlta = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compa\xf1\xcda');
		$('num_cia').select();
	}
	else if (!$chk($('folio_inicial').get('value').getNumericValue())) {
		alert('Debe especificar el folio inicial');
		$('folio_inicial').select();
	}
	else if (!$chk($('folio_final').get('value').getNumericValue())) {
		alert('Debe especificar el folio final');
		$('folio_final').select();
	}
	else if ($('folio_inicial').get('value').getNumericValue() >= $('folio_final').get('value').getNumericValue()) {
		alert('El folio inicial no puede ser igual o mayor al folio final');
		$('folio_inicial').select();
	}
	else if (!$chk($('folio_actual').get('value').getNumericValue())) {
		alert('Debe especificar el folio actual');
		$('folio_actual').select();
	}
	else if ($('no_aprobacion').get('value').getNumericValue() == 0) {
		alert('Debe especificar el n\xfamero de aprobaci\xf3n');
		$('no_aprobacion').select();
	}
	else if ($('fecha_aprobacion').get('value') == '') {
		alert('Debe especificar la fecha de aprobaci\xf3n');
		$('fecha_aprobacion').select();
	}
	else if ($('anio_aprobacion').get('value').getNumericValue() == 0) {
		alert('Debe especificar el a\xf1o de aprobaci\xf3n');
		$('anio_aprobacion').select();
	}
	else if ($('serie_certificado').get('value').length < 20) {
		alert('Debe especificar la serie del certificado');
		$('serie_certificado').select();
	}
	else if ($('archivo_certificado').get('value') == '') {
		alert('Debe especificar el nombre del archivo de certificado');
		$('archivo_certificado').select();
	}
	else if ($('contrasenia_certificado').get('value') == '') {
		alert('Debe especificar la contrase\xf1a del archivo de certificado');
		$('contrasenia_certificado').select();
	}
	else if ($('archivo_llave').get('value') == '') {
		alert('Debe especificar el nombre del archivo llave');
		$('archivo_llave').select();
	}
	else if ($('contrasenia_llave').get('value') == '') {
		alert('Debe especificar la contrase\xf1a del archivo llave');
		$('contrasenia_llave').select();
	}
	else if (confirm('Desea dar de alta la nueva serie')) {
		new Request({
			'url': 'FacturasElectronicasSeries.php',
			'data': 'accion=registrarAlta&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Registrando serie...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consultar(param);
			}
		}).send();
	}
}

var Modificar = function() {
	new Request({
		'url': 'FacturasElectronicasSeries.php',
		'data': 'accion=modificar&id=' + arguments[0],
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'html': ' Cargando pantalla de modificaci&oacute;n...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('serie').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_inicial').select();
					}
				}
			});
			
			$('folio_inicial').addEvents({
				'change': function() {
					$('folio_actual').set('value', $('folio_inicial').get('value').getNumericValue() - 1);
				},
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_final').select();
					}
				}
			});
			
			$('folio_final').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('folio_actual').select();
					}
				}
			});
			
			$('folio_actual').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('no_aprobacion').select();
					}
				}
			});
			
			$('no_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('fecha_aprobacion').select();
					}
				}
			});
			
			$('fecha_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('anio_aprobacion').select();
					}
				}
			});
			
			$('anio_aprobacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('serie_certificado').select();
					}
				}
			});
			
			$('serie_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('archivo_certificado').select();
					}
				}
			});
			
			$('archivo_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('contrasenia_certificado').select();
					}
				}
			});
			
			$('contrasenia_certificado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('archivo_llave').select();
					}
				}
			});
			
			$('archivo_llave').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('contrasenia_llave').select();
					}
				}
			});
			
			$('contrasenia_llave').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						$('serie').select();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('modificar').addEvent('click', registrarModificacion);
			
			$('serie').focus();
		}
	}).send();
}

var registrarModificacion = function() {
	if (!$chk($('folio_inicial').get('value').getNumericValue())) {
		alert('Debe especificar el folio inicial');
		$('folio_inicial').select();
	}
	else if (!$chk($('folio_final').get('value').getNumericValue())) {
		alert('Debe especificar el folio final');
		$('folio_final').select();
	}
	else if ($('folio_inicial').get('value').getNumericValue() >= $('folio_final').get('value').getNumericValue()) {
		alert('El folio inicial no puede ser igual o mayor al folio final');
		$('folio_inicial').select();
	}
	else if (!$chk($('folio_actual').get('value').getNumericValue())) {
		alert('Debe especificar el folio actual');
		$('folio_actual').select();
	}
	else if ($('no_aprobacion').get('value').getNumericValue() == 0) {
		alert('Debe especificar el n\xfamero de aprobaci\xf3n');
		$('no_aprobacion').select();
	}
	else if ($('fecha_aprobacion').get('value') == '') {
		alert('Debe especificar la fecha de aprobaci\xf3n');
		$('fecha_aprobacion').select();
	}
	else if ($('anio_aprobacion').get('value').getNumericValue() == 0) {
		alert('Debe especificar el a\xf1o de aprobaci\xf3n');
		$('anio_aprobacion').select();
	}
	else if ($('serie_certificado').get('value').length < 20) {
		alert('Debe especificar la serie del certificado');
		$('serie_certificado').select();
	}
	else if ($('archivo_certificado').get('value') == '') {
		alert('Debe especificar el nombre del archivo de certificado');
		$('archivo_certificado').select();
	}
	else if ($('contrasenia_certificado').get('value') == '') {
		alert('Debe especificar la contrase\xf1a del archivo de certificado');
		$('contrasenia_certificado').select();
	}
	else if ($('archivo_llave').get('value') == '') {
		alert('Debe especificar el nombre del archivo llave');
		$('archivo_llave').select();
	}
	else if ($('contrasenia_llave').get('value') == '') {
		alert('Debe especificar la contrase\xf1a del archivo llave');
		$('contrasenia_llave').select();
	}
	else if (confirm('Desea aplicar los cambios a la serie')) {
		new Request({
			'url': 'FacturasElectronicasSeries.php',
			'data': 'accion=registrarModificacion&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Modificando serie...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				Consultar(param);
			}
		}).send();
	}
}
