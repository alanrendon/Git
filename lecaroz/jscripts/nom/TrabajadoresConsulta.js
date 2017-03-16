// JavaScript Document

window.addEvent('domready', function() {
	Inicio.run();
});

var Inicio = function () {
	new Request({
		'url': 'TrabajadoresConsulta.php',
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

			$('buscar').addEvent('click', Buscar);

			$('cias').focus();
		}
	}).send();
}

var Buscar = function() {
	if ($('cias').get('value') == ''
		&& $('admin').get('value') == ''
		&& $('trabajadores').get('value') == ''
		&& $('nombre').get('value') == ''
		&& $('ap_paterno').get('value') == ''
		&& $('ap_materno').get('value') == ''
		&& $('rfc').get('value') == ''
		&& $('puesto').get('value') == ''
		&& $('turno').get('value') == ''
		&& confirm('No ha ingresado ningún parámetro de búsqueda por lo cual solo se mostraran los ultimos 20 registros. ¿Desea continuar?')) {
		BuscarTrabajadores.run();
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
		'url': 'TrabajadoresConsulta.php',
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

				$('alta').addEvent('click', AltaTrabajador);

				$$('img[id=alta_cia][src!=/lecaroz/iconos/plus_gray.png]').each(function(el) {
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

				$$('img[id=ver]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': VerTrabajador.pass(id)
					});
				});

				$$('img[id=documentos]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': VerDocumentos.pass(id)
					});
				});

				$$('img[id=cursos]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': Cursos.pass(id)
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

				$$('img[id=pension][src!=/lecaroz/imagenes/pension_gray.png]').each(function(el) {
					var id = el.get('alt').getNumericValue();

					el.removeProperty('alt').addEvents({
						'mouseover': function(e) {
							el.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							el.setStyle('cursor', 'default');
						},
						'click': PensionTrabajador.pass(id)
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

				$$('a[id=documentos_entregados]').each(function(a) {
					a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Informaci&oacute;n');
					a.store('tip:text', a.get('data-tooltip'));
				});

				$$('a[id=labora]').each(function(a) {
					a.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Lugar de trabajo');
					a.store('tip:text', a.get('alt'));

					a.removeProperty('alt');
				});

				tips = new Tips($$('img[id=observaciones], img[id^=chequeo_], a[id=documentos_entregados], a[id=labora]'), {
					'fixed': true,
					'className': 'Tip',
					'showDelay': 50,
					'hideDelay': 50
				});

				$('regresar').addEvent('click', Inicio);
			}
			else {
				alert('No hay resultados');

				Inicio.run();
			}
		}
	}).send();
}

var AltaTrabajador = function() {
	var num_cia = $chk(arguments[0]) ? arguments[0] : 0;

	new Request({
		'url': 'TrabajadoresConsulta.php',
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

						$('curp').select();
					}
					else if (e.key == 'up') {
						$('ap_materno').select();
					}
				}
			});

			$('curp').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_nac').select();
					}
					else if (e.key == 'up') {
						$('rfc').select();
					}
				}
			});

			$('fecha_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('lugar_nac').select();
					}
					else if (e.key == 'up') {
						$('curp').select();
					}
				}
			});

			$('lugar_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('calle').select();
					}
					else if (e.key == 'up') {
						$('fecha_nac').select();
					}
				}
			});

			$('calle').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('colonia').select();
					}
					else if (e.key == 'up') {
						$('lugar_nac').select();
					}
				}
			});

			$('colonia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('del_mun').select();
					}
					else if (e.key == 'up') {
						$('calle').select();
					}
				}
			});

			$('del_mun').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('entidad').select();
					}
					else if (e.key == 'up') {
						$('colonia').select();
					}
				}
			});

			$('entidad').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('cod_postal').select();
					}
					else if (e.key == 'up') {
						$('del_mun').select();
					}
				}
			});

			$('cod_postal').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('telefono_casa').select();
					}
					else if (e.key == 'up') {
						$('entidad').select();
					}
				}
			});

			$('telefono_casa').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('telefono_movil').select();
					}
					else if (e.key == 'up') {
						$('cod_postal').select();
					}
				}
			});

			$('telefono_movil').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('email').select();
					}
					else if (e.key == 'up') {
						$('telefono_casa').select();
					}
				}
			});

			$('email').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta').select();
					}
					else if (e.key == 'up') {
						$('telefono_movil').select();
					}
				}

			});

			$('fecha_alta').addEvents({
				'keydown': function(e) {


					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('salario').focus();
					}
					else if (e.key == 'up') {
						e.stop();

						$('email').select();
					}
				}
			});

			$('salario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('salario_integrado').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta').select();
					}
				}
			});

			$('salario_integrado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta_imss').select();
					}
					else if (e.key == 'up') {
						$('salario').select();
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
						$('salario_integrado').select();
					}
				}
			});

			$('num_afiliacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('no_infonavit').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta_imss').select();
					}
				}
			});

			$('no_infonavit').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('aguinaldo').select();
					}
					else if (e.key == 'up') {
						$('num_afiliacion').select();
					}
				}
			});

			$('aguinaldo').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_vencimiento_licencia_manejo').select();
					}
					else if (e.key == 'up') {
						$('no_infonavit').select();
					}
				}
			});

			$('fecha_vencimiento_licencia_manejo').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('observaciones').select();
					}
					else if (e.key == 'up') {
						$('aguinaldo').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('uniforme').select();
					}
					else if (e.key == 'up') {
						$('fecha_vencimiento_licencia_manejo').select();
					}
				}
			});

			$('uniforme').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('deposito_bata').select();
					}
					else if (e.key == 'up') {
						$('observaciones').select();
					}
				}
			});

			$('deposito_bata').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('idempleado').select();
					}
					else if (e.key == 'up') {
						$('uniforme').select();
					}
				}
			});

			$('idempleado').addEvents({
				change: function() {
					 ObtenerDatosChecador($('idempleado'));
				},
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia').select();
					}
					else if (e.key == 'up') {
						$('deposito_bata').select();
					}
				}
			});

			$('cancelar').addEvent('click', BuscarTrabajadores.pass(param));

			$('alta').addEvent('click', ValidarDatos.pass('alta'));

			$('num_cia').select();
		}
	}).send();
}

var VerTrabajador = function() {
	new Request({
		'url': 'TrabajadoresConsulta.php',
		'data': 'accion=info&id=' + arguments[0],
		'onSuccess': function(content) {
			popup = new Popup(content, 'Informaci&oacute;n del trabajador', 1000, 700, InformacionOpen, null);
		}
	}).send();
}

var InformacionOpen = function() {
	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}

var VerDocumentos = function() {
	new Request({
		'url': 'TrabajadoresConsulta.php',
		'data': 'accion=documentos&id=' + arguments[0],
		'onSuccess': function(content) {
			popup = new Popup(content, 'Documentos del trabajador', 800, 400, DocumentosOpen, null);
		}
	}).send();
}

var DocumentosOpen = function() {
	if (!!$('guardar')) {
		$$('img[id=baja_doc]').each(function(el) {
			var id = el.get('alt');

			el.addEvents({
				mouseover: function() {
					this.setStyle('cursor', 'pointer');
				},
				mouseout: function() {
					this.setStyle('cursor', 'default');
				},
				click: function() {
					alert('¿Desea eliminar el documento asociado al empleado?');

					DocumentoBaja.run(id);
				}
			});
		});

		$('guardar').addEvent('click', function() {
			var request = new Request.File({
				url: 'TrabajadoresConsulta.php',
				onRequest: function() {
					new Element('img', {
						id: 'loading',
						src: 'imagenes/_loading.gif',
						width: 16,
						height: 16
					}).inject($('guardar'), 'after');
				},
				onSuccess: function(result) {
					$('loading').destroy();

					var data = JSON.decode(result);

					if (data.status == 1) {
						var table = $('documents'),
							tr = new Element('tr', {
								id: 'row_doc_' + data.id
							}).inject(table),
							td1 = new Element('td', {
								align: 'center',
								html: data.fecha
							}).inject(tr),
							td2 = new Element('td', {
								align: 'center',
								html: data.tipo,
								class: data.tipo == 'ALTA' ? 'blue' : (data.tipo == 'BAJA' ? 'red' : 'green')
							}).inject(tr),
							td3 = new Element('td').inject(tr),
							a = new Element('a', {
								href: data.href,
								target: '_new'
							}).inject(td3),
							img = new Element('img', {
								src: '/lecaroz/iconos/magnify.png',
								width: 16,
								height: 16,
								align: 'absbottom'
							}).inject(a, 'before');

						a.appendText(' ' + data.filename + ' ');

						var img = new Element('img', {
								src: '/lecaroz/iconos/cancel.png',
								width: 16,
								height: 16,
								align: 'absbottom'
							}).addEvents({
								mouseover: function() {
									this.setStyle('cursor', 'pointer');
								},
								mouseout: function() {
									this.setStyle('cursor', 'default');
								},
								click: function() {
									alert('¿Desea eliminar el documento asociado al empleado?');

									DocumentoBaja.run(data.id);
								}
							}).inject(a, 'after');
					} else if (data.status == -1) {
						alert('El archivo no es ZIP');
					} else if (data.status == -2) {
						alert('No fue posible abrir el archivo ZIP');
					} else if (data.status == -3) {
						alert('El archivo zip contiene más de un archivo');
					} else if (data.status == -4) {
						alert('No es posible guardar el archivo temporal');
					} else if (data.status == -5) {
						alert('El archivo ya ha sido cargado en el sistema con anterioridad');
					} else if (data.status == -6) {
						alert('El archivo contenido dentro del archivo ZIP no es un documento PDF');
					} else if (data.status == -7) {
						alert('El documento PDF no pudo ser cargado en el sistema');
					}
				}
			});

			request.append('accion', 'alta_documento');
			request.append('idempleado', $('idempleado').get('value'));
			request.append('tipo', $('tipo').get('value'));
			request.append('archivo', $('archivo').files[0]);

			request.send();
		});
	}

	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}

var DocumentoBaja = function(id) {
	new Request({
		url: 'TrabajadoresConsulta.php',
		data: 'accion=baja_documento&id=' + id,
		onRequest: function() {
			new Element('img', {
				id: 'loading',
				src: 'imagenes/_loading.gif',
				width: 16,
				height: 16
			}).inject($('guardar'), 'after');
		},
		onSuccess: function() {
			$('loading').destroy();

			$('row_doc_' + id).destroy();
		}
	}).send();
}

var ModificarTrabajador = function() {
	new Request({
		'url': 'TrabajadoresConsulta.php',
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

						$('curp').select();
					}
					else if (e.key == 'up') {
						$('ap_materno').select();
					}
				}
			});

			$('curp').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_nac').select();
					}
					else if (e.key == 'up') {
						$('rfc').select();
					}
				}
			});

			$('fecha_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('lugar_nac').select();
					}
					else if (e.key == 'up') {
						$('curp').select();
					}
				}
			});

			$('lugar_nac').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('calle').select();
					}
					else if (e.key == 'up') {
						$('fecha_nac').select();
					}
				}
			});

			$('calle').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('colonia').select();
					}
					else if (e.key == 'up') {
						$('lugar_nac').select();
					}
				}
			});

			$('colonia').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('del_mun').select();
					}
					else if (e.key == 'up') {
						$('calle').select();
					}
				}
			});

			$('del_mun').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('entidad').select();
					}
					else if (e.key == 'up') {
						$('colonia').select();
					}
				}
			});

			$('entidad').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('cod_postal').select();
					}
					else if (e.key == 'up') {
						$('del_mun').select();
					}
				}
			});

			$('cod_postal').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('telefono_casa').select();
					}
					else if (e.key == 'up') {
						$('entidad').select();
					}
				}
			});

			$('telefono_casa').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('telefono_movil').select();
					}
					else if (e.key == 'up') {
						$('cod_postal').select();
					}
				}
			});

			$('telefono_movil').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('email').select();
					}
					else if (e.key == 'up') {
						$('telefono_casa').select();
					}
				}
			});

			$('email').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta').select();
					}
					else if (e.key == 'up') {
						$('telefono_movil').select();
					}
				}

			});

			$('fecha_alta').addEvents({
				'keydown': function(e) {


					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('salario').focus();
					}
					else if (e.key == 'up') {
						e.stop();

						$('email').select();
					}
				}
			});

			$('salario').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('salario_integrado').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta').select();
					}
				}
			});

			$('salario_integrado').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_alta_imss').select();
					}
					else if (e.key == 'up') {
						$('salario').select();
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
						$('salario_integrado').select();
					}
				}
			});

			$('num_afiliacion').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('no_infonavit').select();
					}
					else if (e.key == 'up') {
						$('fecha_alta_imss').select();
					}
				}
			});

			$('no_infonavit').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('fecha_vencimiento_licencia_manejo').select();
					}
					else if (e.key == 'up') {
						$('num_afiliacion').select();
					}
				}
			});

			$('fecha_vencimiento_licencia_manejo').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('observaciones').select();
					}
					else if (e.key == 'up') {
						$('no_infonavit').select();
					}
				}
			});

			$('observaciones').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('uniforme').select();
					}
					else if (e.key == 'up') {
						$('fecha_vencimiento_licencia_manejo').select();
					}
				}
			});

			$('uniforme').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('deposito_bata').select();
					}
					else if (e.key == 'up') {
						$('observaciones').select();
					}
				}
			});

			$('deposito_bata').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('idempleado').select();
					}
					else if (e.key == 'up') {
						$('uniforme').select();
					}
				}
			});

			$('idempleado').addEvents({
				change: function() {
					 ObtenerDatosChecador($('idempleado'));
				},
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();

						$('num_cia').select();
					}
					else if (e.key == 'up') {
						$('deposito_bata').select();
					}
				}
			}).fireEvent('change');

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
			'url': 'TrabajadoresConsulta.php',
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
							turno = $('cod_turno').get('value'),
							horario = $('cod_horario').get('value');
					}

					if (num_cia.get('id') == 'num_cia') {
						updSelect($('cod_puestos'), data.puestos);
						updSelect($('cod_turno'), data.turnos);
						updSelect($('cod_horario'), data.horarios);
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
						$each($('cod_horario').options, function(el) {
							if (el.get('value') == horario) {
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
				updSelect($('cod_horario'), []);
			}
		}
	}
}

var ValidarDatos = function() {
	var pattern = /^([a-zA-Z\u00D1\u00F1\u0026]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})?$/;

	var rfc = pattern.exec($('rfc').get('value'));

	if ($('nombre').get('value') == '') {
		alert('Debe especificar el nombre');

		$('nombre').focus();
	}
	else if ($('ap_paterno').get('value') == '') {
		alert('Debe epecificar el apellido paterno');

		$('ap_paterno').focus();
	}
	else if ($('rfc').get('value') == '') {
		alert('Debe especificar el R.F.C.');

		$('rfc').focus();
	}
	else if ($('num_afiliacion').get('value').trim() != '' && ! (!! rfc[5]))
	{
		alert('El R.F.C. del trabajador debe tener homoclave en el caso que tenga número de afiliación del I.M.S.S.');

		$('rfc').select();
	}
	else if ($('fecha_nac').get('value') == '') {
		alert('Debe especificar la fecha de nacimiento');

		$('fecha').focus();
	}
	else if ($('email').get('value') == '') {
		alert('Debe especificar el correo electrónico del trabajador');

		$('email').focus();
	}
	else if ([5, 915].contains($('cod_puestos').get('value').getNumericValue()) && $('fecha_vencimiento_licencia_manejo').get('value') == '') {
		alert('Debe especificar la fecha de vencimiento de la licencia de manejo del empleado');

		$('fecha_vencimiento_licencia_manejo').focus();
	}
	else {
		ValidarEdad(arguments[0]);
	}
}

var ValidarListaNegra = function() {
	var tipo = arguments[0];

	new Request({
		'url': 'TrabajadoresConsulta.php',
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
		'url': 'TrabajadoresConsulta.php',
		'data': 'accion=validarNombre&nombre=' + $('nombre').get('value') + '&ap_paterno=' + $('ap_paterno').get('value') + '&ap_materno=' + $('ap_materno').get('value') + '&rfc=' + $('rfc').get('value') + (tipo == 'modificar' ? '&id=' + $('id').get('value') : ''),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if (result != '') {
				var data = JSON.decode(result),
					msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías y no podrá ser ingresado/modificado:';

				data.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC:\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t' + rec.usuario;
				});

				alert(msg);

				$('nombre').focus();

				/*var data = JSON.decode(result),
					msg = 'Se encontraron algunas coincidencias del trabajador en otras compañías:';

				data.each(function(rec) {
					msg += '\n\nCompañía:\t\t' + rec.num_cia + ' ' + rec.nombre_cia;
					msg += '\nFecha de alta:\t' + rec.fecha_alta;
					msg += '\nTrabajador:\t\t' + rec.num_emp + ' ' + rec.nombre_trabajador;
					msg += '\nRFC:\t\t\t' + rec.rfc;
					msg += '\nUsuario:\t\t' + rec.usuario;
				});

				msg += '\n\n¿Desea continuar con la actualización de los datos del trabajador?';

				if (confirm(msg)) {
					Actualizar(tipo);
				}
				else {
					$('nombre').focus();
				}*/
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
		'url': 'TrabajadoresConsulta.php',
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
			'url': 'TrabajadoresConsulta.php',
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
			'url': 'TrabajadoresConsulta.php',
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
	new Request({
		'url': 'TrabajadoresConsulta.php',
		'data': 'accion=baja&id=' + arguments[0],
		'onSuccess': function(content) {
			popup = new Popup(content, 'Confirmar baja del trabajador', 500, 150, BajaTrabajadorOpen, null);
		}
	}).send();
}

var BajaTrabajadorOpen = function() {
	$('cancelar_baja').addEvent('click', function() {
		popup.Close();
	});

	$('aceptar_baja').addEvent('click', function() {
		new Request({
			'url': 'TrabajadoresConsulta.php',
			'data': 'accion=do_baja&id=' + $('id_trabajador').get('value') + '&tipo=' + $('tipo_baja').get('value'),
			'onRequest': function() {
				popup.Close();

				$('captura').empty();

				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));

				new Element('span', {
					'text': ' Actualizando datos del trabajador...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);

				BuscarTrabajadores.run(param);

				if (data.status < 0) {
					alert('Se realizó la baja con folio ' + data.folio + ', pero no ha sido posible generar el correo electrónico, el servidor ha retornado: ' + data.error);
				}
				else {
					alert('Se realizó la baja con folio ' + data.folio);
				}
			}
		}).send();
	})
}

var PensionTrabajador = function() {
	if (confirm('¿Esta seguro de pensionar al trabajador?')) {
		new Request({
			'url': 'TrabajadoresConsulta.php',
			'data': 'accion=pension&id=' + arguments[0],
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
			'url': 'TrabajadoresConsulta.php',
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

var Cursos = function(id) {
	new Request({
		'url': 'TrabajadoresConsulta.php',
		'data': 'accion=cursos&id=' + id,
		'onSuccess': function(content) {
			popup = new Popup(content, 'Cursos tomados por el trabajador', 800, 400, CursosOpen, null);
		}
	}).send();
}

var CursosOpen = function() {
	new FormValidator($('AltaCurso'), {
		showErrors: true,
		selectOnFocus: true
	});

	new FormStyles($('AltaCurso'));

	$$('img[id=baja_curso]').each(function(el) {
		el.addEvents({
			mouseover: function() {
				this.setStyle('cursor', 'pointer');
			},
			mouseout: function() {
				this.setStyle('cursor', 'default');
			},
			click: BajaCurso.pass(el.get('alt'))
		});
	});

	$('fecha').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();

				this.blur();
				this.focus();
			}
		}
	}).select();

	$('alta_curso').addEvent('click', AltaCurso);

	$('cerrar').addEvent('click', function() {
		popup.Close();
	});
}

var AltaCurso = function() {
	if ($('curso').get('value').getNumericValue() == 0) {
		alert('Debe seleccionar un curso de capacitación de la lista');
	} else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha en la que el empleado tomo el curso de capacitación');

		$('fecha').select();
	} else {
		new Request({
			url: 'TrabajadoresConsulta.php',
			data: 'accion=alta_curso&' + $('AltaCurso').toQueryString(),
			onRequest: function() {
				new Element('img', {
					id: 'loading',
					src: 'imagenes/_loading.gif',
					width: 16,
					height: 16
				}).inject($('alta_curso'), 'after');
			},
			onSuccess: function(result) {
				$('loading').destroy();

				var data = JSON.decode(result);

				var table = $('cursos_empleado'),
					tr = new Element('tr', {
						id: 'curso' + data.id
					}).inject(table),
					td1 = new Element('td', {
						align: 'center',
						html: data.nombre_curso
					}).inject(tr),
					td2 = new Element('td', {
						align: 'center',
						html: data.fecha,
						align: 'center'
					}).inject(tr),
					td3 = new Element('td', {
						align: 'center'
					}).inject(tr),
					img = new Element('img', {
						id: 'baja_curso',
						name: 'baja_curso',
						src: '/lecaroz/iconos/cancel.png',
						width: 16,
						height: 16,
						alt: data.id
					}).addEvents({
						mouseover: function() {
							this.setStyle('cursor', 'pointer');
						},
						mouseout: function() {
							this.setStyle('cursor', 'default');
						},
						click: BajaCurso.pass(data.id)
					}).inject(td3);
			}
		}).send();
	}
}

var BajaCurso = function(id) {
	new Request({
		url: 'TrabajadoresConsulta.php',
		data: 'accion=baja_curso&id=' + id,
		onRequest: function() {
			new Element('img', {
				id: 'loading',
				src: 'imagenes/_loading.gif',
				width: 16,
				height: 16
			}).inject($$('img[id=baja_curso][alt=' + id + ']')[0], 'after');
		},
		onSuccess: function() {
			$('loading').destroy();

			$('curso' + id).destroy();
		}
	}).send();
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

var ObtenerDatosChecador = function(input)
{
	var id = input.get('value');

	if (id.getNumericValue() <= 0)
	{
		$$('#idempleado, #nombre_checador, #num_huellas').set('value', '');

		return false;
	}

	new Request({
		url: 'TrabajadoresConsulta.php',
		data: 'accion=obtener_datos_checador&id=' + id,
		onRequest: function() {},
		onSuccess: function(response) {
			if ( ! response)
			{
				$('idempleado').set('value', $('idempleado').retrieve('tmp', ''));

				alert('El ID de empleado no esta dado de alta en el checador');

				$('idempleado').select();

				return false;
			}

			var datos = JSON.decode(response);

			$('nombre_checador').set('value', datos.nombre_checador);
			$('num_huellas').set('value', datos.num_huellas);
		}
	}).send();
}
