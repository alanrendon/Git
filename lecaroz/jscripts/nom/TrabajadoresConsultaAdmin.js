// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function () {
	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
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

						$('trabajadores').focus();
					}
				}
			});

			$('trabajadores').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('nombre').focus();
					}
				}
			});

			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('ap_paterno').focus();
					}
				}
			});

			$('ap_paterno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('ap_materno').focus();
					}
				}
			});

			$('ap_materno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('rfc').focus();
					}
				}
			});

			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						if ($('bajas').get('checked')) {
							$('meses_baja').focus();
						}
						else {
							$('cias').focus();
						}
					}
				}
			});

			$('meses_baja').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();

						$('cias').focus();
					}
				}
			});

			$('listado').addEvent('click', Listado);

			$('repetidos').addEvent('click', Repetidos);

			$('similares').addEvent('click', Similares);

			$('buscar').addEvent('click', Buscar);

			$('cias').focus();
		}
	}).send();
}

var Listado = function() {
	var url = 'TrabajadoresConsultaAdmin.php',
		_param = '?accion=listado&' + ($type(arguments[0]) == 'string' ? arguments[0] : $('Datos').toQueryString()),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;

	win = window.open(url + _param, '', opt);

	win.focus();
}

var Repetidos = function() {
	var url = 'TrabajadoresConsultaAdmin.php',
		_param = '?accion=repetidos',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;

	win = window.open(url + _param, '', opt);

	win.focus();
}

var Similares = function() {
	var url = 'TrabajadoresConsultaAdmin.php',
		_param = '?accion=similares',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;

	win = window.open(url + _param, '', opt);

	win.focus();
}

var Buscar = function() {
	if ($('cias').get('value') == ''
		&& $('trabajadores').get('value') == ''
		&& $('nombre').get('value') == ''
		&& $('ap_paterno').get('value') == ''
		&& $('ap_materno').get('value') == ''
		&& $('rfc').get('value') == ''
		&& $('puesto').get('value').getNumericValue() == 0
		&& $('turno').get('value').getNumericValue() == 0) {
		alert('Debe especificar algun parámetro de búsqueda')
	}
	else {
		BuscarTrabajadores.run();
	}
}

var BuscarTrabajadores = function() {
	if ($type(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=buscar&' + param,
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Buscando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);

				$$('tr[class^=linea_]').each(function(row, i) {
					row.addEvents({
						'mouseover': function(e) {
							e.stop();

							row.addClass('highlight');
						},
						'mouseout': function(e) {
							e.stop();

							row.removeClass('highlight');
						}
					});
				});

				/*
				@@@ PROVICIONAL
				*/
				$('calculadora').addEvent('click', function(e) {
					e.stop();

					var win = window.open('fac_cal_agu.php', 'calculadora','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=600,height=200');

					win.focus();
				});

				$('alta').addEvent('click', AltaTrabajador);

				$('recargar').addEvent('click', BuscarTrabajadores.pass(param));

				$$('select[id=puesto]').each(function(el) {
					el.store('data', el.get('data')).removeProperty('data');

					el.addEvent('change', Puesto.pass(el));
				});

				$$('select[id=turno]').each(function(el) {
					el.store('data', el.get('data')).removeProperty('data');

					el.addEvent('change', Turno.pass(el));
				});

				$$('input[id=aguinaldo]').each(function(el) {
					el.addEvent('change', Aguinaldo.pass(el));
				});

				$$('select[id=tipo]').each(function(el) {
					el.store('data', el.get('data')).removeProperty('data');

					el.addEvent('change', Tipo.pass(el));
				});

				$$('a[id=antiguedad]').each(function(el, i) {
					var id = el.get('alt');

					el.removeProperty('alt');

					if (el.get('bloqueado') == 'FALSE') {
						el.addEvent('click', Antiguedad.pass([i, id]));
					}
				});

				$$('a[id=aguinaldo_ant]').each(function(el, i) {
					var data = JSON.decode(el.get('alt'));

					el.removeProperty('alt');

					if (el.get('bloqueado') == 'FALSE') {
						el.addEvent('click', AguinaldoAnterior.pass([i, data.id, data.num_cia, data.anio]));
					}
				});

				$$('a[id=aguinaldo_act]').each(function(el, i) {
					var data = JSON.decode(el.get('alt'));

					el.removeProperty('alt');

					if (el.get('bloqueado') == 'FALSE') {
						el.addEvent('click', AguinaldoActual.pass([i, data.id, data.num_cia, data.anio]));
					}
				});

				$$('img[id=alta_cia]').each(function(el) {
					var num_cia = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': AltaTrabajador.pass(num_cia)
					});
				});

				$$('img[id=modificar][src!=/lecaroz/iconos/pencil_gray.png]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': ModificarTrabajador.pass(id)
					});
				});

				$$('img[id=baja][src!=/lecaroz/iconos/cancel_round_gray.png]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': BajaTrabajador.pass(id)
					});
				});

				$$('img[id=reactivar][src!=/lecaroz/iconos/accept_green_gray.png]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': ReactivarTrabajador.pass(id)
					});
				});

				$$('img[id=info]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': Info.pass(id)
					});
				});

				$$('img[id=observaciones]').each(function(img) {
					img.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Observaciones');
					img.store('tip:text', img.get('alt'));

					img.removeProperty('alt');
				});

				$$('img[id^=chequeo_]').each(function(img) {
					img.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n de checador');
					img.store('tip:text', img.get('alt'));

					img.removeProperty('alt');
				});

				$$('a[id=labora]').each(function(a) {
					a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Lugar de trabajo');
					a.store('tip:text', a.get('alt'));

					a.removeProperty('alt');
				});

				tips = new Tips($$('img[id=observaciones], img[id^=chequeo_], a[id=labora]'), {
					'fixed': true,
					'className': 'Tip',
					'showDelay': 50,
					'hideDelay': 50
				});

				$('listado').addEvent('click', Listado.pass(param));

				$('repetidos').addEvent('click', Repetidos);

				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');

				Inicio.run();
			}
		}
	}).send();
}

var Info = function() {
	var id = arguments[0];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=info&id=' + id,
		'onSuccess': function(content) {
			popup = new Popup(content, '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de aguinaldos del trabajador', 800, 300, InfoOpen, null);
		}
	}).send();
}

var InfoOpen = function() {
	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}

var Puesto = function() {
	var data = JSON.decode(arguments[0].retrieve('data')),
		puesto = arguments[0].get('value');

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=puesto&id=' + data.id + '&puesto=' + puesto,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
		}
	}).send();
}

var Turno = function() {
	var data = JSON.decode(arguments[0].retrieve('data')),
		turno = arguments[0].get('value');

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=turno&id=' + data.id + '&turno=' + turno,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
		}
	}).send();
}

var Aguinaldo = function() {
	var id = arguments[0].get('value'),
		status = arguments[0].get('checked') ? 'TRUE' : 'FALSE';

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=aguinaldo&id=' + id + '&status=' + status,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
		}
	}).send();
}

var Tipo = function() {
	var data = JSON.decode(arguments[0].retrieve('data')),
		tipo = arguments[0].get('value');

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=tipo&id=' + data.id + '&tipo=' + tipo,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
		}
	}).send();
}

var Antiguedad = function() {
	var i = arguments[0],
		id = arguments[1];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=antiguedad&id=' + id + '&i=' + i,
		'onSuccess': function(content) {
			popup = new Popup(content, 'Antig&ugrave;edad del trabajador', 450, 250, AntiguedadOpen, null);
		}
	}).send();
}

var AntiguedadOpen = function() {
	new FormValidator($('DatosAntiguedad'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('DatosAntiguedad'));

	$('anios').addEvent('change', function() {
		$$('input[name=tipo_antiguedad]')[0].set('checked', true);
	});

	$('meses').addEvent('change', function() {
		$$('input[name=tipo_antiguedad]')[0].set('checked', true);
	});

	$('fecha_alta').addEvents({
		'change': function() {
			$$('input[name=tipo_antiguedad]')[1].set('checked', true);
		},
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				this.blur();
				this.focus();
			}
		}
	});

	$('cancelar').addEvent('click', function() {
		popup.Close();
	});

	$('actualizar').addEvent('click', ActualizarAntiguedad);
}

var ActualizarAntiguedad = function() {
	if ($$('input[name=tipo_antiguedad]')[0].get('checked') && $('anios').get('value').getNumericValue() == 0 && $('meses').get('value').getNumericValue() == 0) {
		alert('La antigüedad del trabajador debe ser al menos de 1 mes');
	}
	else if ($$('input[name=tipo_antiguedad]')[1].get('checked') && $('fecha_alta').get('value') == '') {
		alert('Debe especificar la fecha de alta');

		$('fecha_alta').focus();
	}
	else {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=actualizarAntiguedad&' + $('DatosAntiguedad').toQueryString(),
			'onSuccess': function(result) {
				var data = JSON.decode(result);

				$$('a[id=antiguedad]')[data.i].set('text', data.antiguedad);

				popup.Close();
			}
		}).send();
	}
}

var AguinaldoAnterior = function() {
	var i = arguments[0],
		id = arguments[1],
		num_cia = arguments[2],
		anio = arguments[3];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=aguinaldoAnterior&id=' + id + '&i=' + i + '&num_cia=' + num_cia + '&anio=' + anio,
		'onSuccess': function(content) {
			popup = new Popup(content, 'Aguinaldo "' + anio + '" del trabajador', 450, 250, AguinaldoAnteriorOpen, null);
		}
	}).send();
}

var AguinaldoAnteriorOpen = function() {
	new FormValidator($('DatosAguinaldo'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('DatosAguinaldo'));

	$('importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				this.blur();
				this.focus();
			}
		}
	}).select();

	$('cancelar').addEvent('click', function() {
		popup.Close();
	});

	$('actualizar').addEvent('click', ActualizarAguinaldoAnterior);
}

var ActualizarAguinaldoAnterior = function() {
	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=actualizarAguinaldoAnterior&' + $('DatosAguinaldo').toQueryString(),
		'onSuccess': function(result) {
			var data = JSON.decode(result);

			$$('a[id=aguinaldo_ant]')[data.i].set('html', data.importe > 0 ? '<span style="float:left;" class="orange">(' + data.tipo + ')</span>&nbsp;' + data.importe.numberFormat(2, '.', ',') : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

			TotalAguinaldoAnterior.run(data.num_cia);

			popup.Close();
		}
	}).send();
}

var TotalAguinaldoAnterior = function() {
	var num_cia = arguments[0],
		total = 0;

	total = $$('a[id=aguinaldo_ant][num_cia=' + num_cia + ']').get('text').getNumericValue().sum();

	$$('th[id=total_aguinaldo_ant][num_cia=' + num_cia + ']').set('html', total.numberFormat(2, '.', ','));
}

var AguinaldoActual = function() {
	var i = arguments[0],
		id = arguments[1],
		num_cia = arguments[2],
		anio = arguments[3];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=aguinaldoActual&id=' + id + '&i=' + i + '&num_cia=' + num_cia + '&anio=' + anio,
		'onSuccess': function(content) {
			popup = new Popup(content, 'Aguinaldo "' + anio + '" del trabajador', 450, 250, AguinaldoActualOpen, null);
		}
	}).send();
}

var AguinaldoActualOpen = function() {
	new FormValidator($('DatosAguinaldo'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('DatosAguinaldo'));

	$('importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();

				this.blur();
				this.focus();
			}
		}
	}).select();

	$('cancelar').addEvent('click', function() {
		popup.Close();
	});

	$('actualizar').addEvent('click', ActualizarAguinaldoActual);
}

var ActualizarAguinaldoActual = function() {
	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=actualizarAguinaldoActual&' + $('DatosAguinaldo').toQueryString(),
		'onSuccess': function(result) {
			var data = JSON.decode(result);

			$$('a[id=aguinaldo_act]')[data.i].set('html', data.importe > 0 ? '<span style="float:left;" class="orange">(' + data.tipo + ')</span>&nbsp;' + data.importe.numberFormat(2, '.', ',') : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

			TotalAguinaldoActual.run(data.num_cia);

			popup.Close();
		}
	}).send();
}

var TotalAguinaldoActual = function() {
	var num_cia = arguments[0],
		total = 0;

	total = $$('a[id=aguinaldo_act][num_cia=' + num_cia + ']').get('text').getNumericValue().sum();

	$$('th[id=total_aguinaldo_act][num_cia=' + num_cia + ']').set('html', total.numberFormat(2, '.', ','));
}

var AltaTrabajador = function() {
	var num_cia = $chk(arguments[0]) ? arguments[0] : 0;

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=alta' + (num_cia > 0 ? '&num_cia=' + num_cia : ''),
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Cargando pantalla...'
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
				'change': obtenerCia.pass([$('num_cia'), $('nombre_cia'), 'alta']),
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia_emp').focus();
					}
				}
			}).fireEvent('change');

			$('num_cia_emp').addEvents({
				'change': obtenerCia.pass([$('num_cia_emp'), $('nombre_cia_emp')]),
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('nombre').focus();
					}
					else if (e.key == 'up') {
						$('num_cia').select();
					}
				}
			});

			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('ap_paterno').select();
					}
					else if (e.key == 'up') {
						$('num_cia_emp').select();
					}
				}
			});

			$('ap_paterno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('ap_materno').select();
					}
					else if (e.key == 'up') {
						$('nombre').select();
					}
				}
			});

			$('ap_materno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('rfc').select();
					}
					else if (e.key == 'up') {
						$('ap_paterno').select();
					}
				}
			});

			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_nac').select();
					}
					else if (e.key == 'up') {
						$('ap_materno').select();
					}
				}
			});

			$('fecha_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta').select();
					}
					else if (e.key == 'up') {
						$('rfc').select();
					}
				}
			});

			$('fecha_alta').addEvents({
				'keydown': function(e) {


					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta_imss').focus();
					}
					else if (e.key == 'up') {
						e.stop();

						$('fecha_nac').select();
					}
				}
			});

			$('fecha_alta_imss').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_afiliacion').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta').select();
					}
				}
			});

			$('num_afiliacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('aguinaldo').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta_imss').select();
					}
				}
			});

			$('aguinaldo').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('observaciones').select();
					}
					else if (e.key == 'up') {
						$('num_afiliacion').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia').select();
					}
					else if (e.key == 'up') {
						$('aguinaldo').select();
					}
				}
			});

			$('cancelar').addEvent('click', BuscarTrabajadores.pass(param));

			$('alta').addEvent('click', ValidarDatos.pass('alta'));

			$('num_cia').select();
		}
	}).send();
}

var ModificarTrabajador = function() {
	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=modificar&id=' + arguments[0],
		'onRequest': function() {
			$('captura').empty();

			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));

			new Element('span', {
				'text': ' Obteniendo datos del trabajador...'
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
				'change': obtenerCia.pass([$('num_cia'), $('nombre_cia'), 'modificar']),
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia_emp').focus();
					}
				}
			});

			$('num_cia_emp').addEvents({
				'change': obtenerCia.pass([$('num_cia_emp'), $('nombre_cia_emp')]),
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('nombre').focus();
					}
					else if (e.key == 'up') {
						$('num_cia').select();
					}
				}
			});

			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('ap_paterno').select();
					}
					else if (e.key == 'up') {
						$('num_cia_emp').select();
					}
				}
			});

			$('ap_paterno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('ap_materno').select();
					}
					else if (e.key == 'up') {
						$('nombre').select();
					}
				}
			});

			$('ap_materno').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('rfc').select();
					}
					else if (e.key == 'up') {
						$('ap_paterno').select();
					}
				}
			});

			$('rfc').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_nac').select();
					}
					else if (e.key == 'up') {
						$('ap_materno').select();
					}
				}
			});

			$('fecha_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta').select();
					}
					else if (e.key == 'up') {
						$('rfc').select();
					}
				}
			});

			$('fecha_alta').addEvents({
				'keydown': function(e) {


					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta_imss').focus();
					}
					else if (e.key == 'up') {
						e.stop();

						$('fecha_nac').select();
					}
				}
			});

			$('fecha_alta_imss').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_afiliacion').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta').select();
					}
				}
			});

			$('num_afiliacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('observaciones').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta_imss').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia').select();
					}
					else if (e.key == 'up') {
						$('num_afiliacion').select();
					}
				}
			});

			$('cancelar').addEvent('click', BuscarTrabajadores.pass(param));

			$('actualizar').addEvent('click', ValidarDatos.pass('modificar'));

			$('num_cia').select();
		}
	}).send();
}

var obtenerCia = function() {
	var num_cia = arguments[0],
		nombre_cia = arguments[1],
		tipo = arguments[2];

	if (num_cia.get('value').getNumericValue() > 0) {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=obtenerCia&num_cia=' + num_cia.get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					var data = JSON.decode(result);

					nombre_cia.set('value', data.nombre_cia);

					if (num_cia.get('id') == 'num_cia') {
						$$('#num_cia_emp, #nombre_cia_emp').set('value', '')
					}

					if (tipo == 'modificar' && num_cia.get('id') == 'num_cia') {
						var puesto = $('cod_puestos').get('value'),
							turno = $('cod_turno').get('value');
					}

					if (num_cia.get('id') == 'num_cia') {
						updSelect($('cod_puestos'), data.puestos);
						updSelect($('cod_turno'), data.turnos);
					}

					if (tipo == 'modificar' && num_cia.get('id') == 'num_cia') {
						$each($('cod_puestos').options, function(el) {
							if (el.get('value') == puesto) {
								el.set('selected', 'selected');
							}
						});
						$each($('cod_turno').options, function(el) {
							if (el.get('value') == turno) {
								el.set('selected', 'selected');
							}
						});
					}
				}
				else {
					alert('La compañía no se encuentra en el catálogo');

					num_cia.set('value', num_cia.retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		if (tipo == 'modificar') {
			alert('Debe especificar forzosamente la compañía');

			num_cia.set('value', num_cia.retrieve('tmp', '')).select();
		}
		else {
			num_cia.set('value', '');
			nombre_cia.set('value', '');

			if (num_cia.get('id') == 'num_cia') {
				updSelect($('cod_puestos'), []);
				updSelect($('cod_turno'), []);
			}
		}
	}
}

var ValidarDatos = function() {
	if ($('nombre').get('value') == '') {
		alert('Debe especificar el nombre');

		$('nombre').focus();
	}
	else if ($('ap_paterno').get('value') == '') {
		alert('Debe epecificar el apellido paterno');

		$('ap_paterno').focus();
	}
	else if ($('fecha_alta').get('value') == '') {
		alert('Debe epecificar la fecha de alta');

		$('fecha_alta').focus();
	}
	else {
		ValidarEdad(arguments[0]);
	}

}

var ValidarListaNegra = function() {
	var tipo = arguments[0];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=validarListaNegra&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result);

				alert('El empleado esta en la lista negra con el folio ' + data.folio + ' y no puede ser ingresado/modificado por las siguientes razones:\n\n' + data.observaciones);

				$('nombre').focus();

				/*if (confirm('El empleado esta en la lista negra con el folio ' + data.folio + ' por las siguientes razones:\n\n' + data.observaciones + '\n\n¿Desea continuar con la actualización de los datos del trabajador?')) {
					ValidarNombre(tipo);
				}
				else {
					$('nombre').focus();
				}*/
			}
			else {
				ValidarNombre(tipo);
			}
		}
	}).send();
}

var ValidarNombre = function() {
	var tipo = arguments[0];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=validarNombre&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value') + '&rfc=' + $('rfc').get('value') + (tipo == 'modificar' ? '&id=' + $('id').get('value') : ''),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result),
					msg = data.admin
						? 'Se encontraron algunas coincidencias del trabajador en otras compañías'
						: 'Se encontraron algunas coincidencias del trabajador en otras compañías y no podrá ser ingresado/modificado:';

				data.empleados.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC:\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t' + rec.usuario;
				});

				if (data.admin) {
					msg += '\n\n¿Desea continuar con la actualización de los datos del trabajador?';

					if (confirm(msg)) {
						Actualizar(tipo);
					}
				} else {
					alert(msg);ç

					$('nombre').focus();
				}
			}
			else if (confirm('¿Son correctos todos los datos?')) {
				Actualizar(tipo);
			}
		}
	}).send();
}

var ValidarEdad = function() {
	var tipo = arguments[0];

	new Request({
		'url': 'TrabajadoresConsultaAdmin.php',
		'data': 'accion=validarEdad&fecha_nac=' + $('fecha_nac').get('value') + '&num_afiliacion=' + $('num_afiliacion').get('value'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result.getNumericValue() < 0) {
				alert('El trabajor es menor de edad y tiene número de afiliación del seguro social, solo administradores pueden dar de alta este tipo de trabajadores');
			}
			else {
				ValidarListaNegra(tipo);
			}
		}
	}).send();
}

var Actualizar = function() {
	if (arguments[0] == 'alta') {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=insertar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Registrando datos del nuevo trabajador...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result),
					msg = 'Datos de alta:\n\nCompañía:\t' + data.cia + '\nTrabajador:\t' + data.trabajador;

				BuscarTrabajadores.run(param);

				alert(msg);
			}
		}).send();
	}
	else if (arguments[0] == 'modificar') {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=actualizar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Actualizando datos del trabajador...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				BuscarTrabajadores.run(param);
			}
		}).send();
	}
}

var BajaTrabajador = function() {
	if (confirm('¿Esta seguro de dar de baja al trabajador?')) {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=baja&id=' + arguments[0],
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Actualizando datos del trabajador...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				BuscarTrabajadores.run(param);
			}
		}).send();
	}
}

var ReactivarTrabajador = function() {
	if (confirm('En caso de que haya dado de baja por equivocación a un trabajador podra recuperarlo mediante este proceso.\n¿Esta seguro de realizar la reactivación del trabajador?')) {
		new Request({
			'url': 'TrabajadoresConsultaAdmin.php',
			'data': 'accion=reactivar&id=' + arguments[0],
			'onRequest': function() {
				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Actualizando datos del trabajador...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				BuscarTrabajadores.run(param);
			}
		}).send();
	}
}

var updSelect = function() {
	var Select = arguments[0],
		Options = arguments[1];

	if (Options.length > 0) {
		Select.length = Options.length;

		$each(Select.options, function(el, i) {
			el.set(Options[i]);
		});

		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;

		Select.selectedIndex = -1;
	}
}
