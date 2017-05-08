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
	
	boxAlert = new mBox.Modal({
		id: 'box_alert',
		title: '<img src="/lecaroz/iconos/info.png" width="16" height="16" /> Mensaje del sistema',
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
		closeInTitle: true
	});
	
	boxBaja = new mBox.Modal({
		id: 'box_baja',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Baja de riesgo de trabajo',
		content: '&iquest;Desea dar de baja el riesgo de trabajo?',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					do_baja_riesgo_trabajo();
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
		closeInTitle: true
	});
	
	boxAltaIncapacidad = new mBox.Modal({
		id: 'box_alta_incapacidad',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Alta de incapacidad',
		content: 'alta_incapacidad_wrapper',
		buttons: [
			{ title: 'Cancelar' },
			{
				title: 'Aceptar',
				event: function() {
					do_alta_incapacidad();
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
			new FormValidator(document.id('alta_incapacidad'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('fecha_nueva_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_inicio_nueva_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('folio_nueva_incapacidad').select();
					}
				}
			});
			
			document.id('fecha_inicio_nueva_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('dias_nueva_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_nueva_incapacidad').select();
					}
				}
			});
			
			document.id('dias_nueva_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('folio_nueva_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_inicio_nueva_incapacidad').select();
					}
				}
			});
			
			document.id('folio_nueva_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_nueva_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dias_nueva_incapacidad').select();
					}
				}
			});
			
		},
		onOpen: function() {
			$$('#fecha_nueva_incapacidad, #fecha_inicio_nueva_incapacidad, #dias_nueva_incapacidad, #folio_nueva_incapacidad').set('value', '');
		},
		onOpenComplete: function() {
			document.id('fecha_nueva_incapacidad').select();
		}
	});
	
	boxDigitalizarIncapacidad = new mBox.Modal({
		id: 'box_digitalizar_incapacidad',
		title: '<img src="/lecaroz/iconos/pencil.png" width="16" height="16" /> Digitalizar incapacidad',
		content: '',
		width: 610,
		height: 480,
		buttons: [ { title: 'Cancelar' } ],
		overlay: true,
		overlayStyles: {
			color: 'white',
			opacity: 0.8
		},
		draggable: true,
		closeOnEsc: false,
		closeOnBodyClick: false,
		closeInTitle: true,
		onBoxReady: function() {},
		onOpen: function() {},
		onOpenComplete: function() {}
	});
	
	inicio();
	
});

var inicio = function () {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=inicio',
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			document.id('captura').empty().set('html', result);
			
			new FormValidator(document.id('inicio'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('cias').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha1').select();
					}
				}
			});
			
			document.id('fecha1').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha2').select();
					}
				}
			});
			
			document.id('fecha2').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('cias').focus();
					}
				}
			});
			
			document.id('consultar').addEvent('click', consultar);
			
			boxProcessing.close();
			
			document.id('cias').focus();
		}
	}).send();
}

var obtener_cia = function() {
	if (document.id('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			url: 'RiesgosTrabajo.php',
			data: 'accion=obtener_cia&num_cia=' + document.id('num_cia').get('value'),
			onRequest: function() {
			},
			onSuccess: function(result) {
				if (result != '') {
					var data = JSON.decode(result);
					
					document.id('nombre_cia').set('value', data.nombre_cia);
					
					update_select(document.id('idtrabajador'), data.trabajadores);
				} else {
					document.id('num_cia').set('value', document.id('num_cia').retrieve('tmp'));
					
					boxAlert.onCloseComplete = function() {
						document.id('num_cia').focus();
					};
					
					boxAlert.setContent('La compañía no se encuentra en el catálogo').open();
				}
			}
		}).send();
	} else {
		$$('#num_cia, #nombre_cia').set('value', '');
		
		update_select(document.id('idtrabajador'), []);
	}
}

var consultar = function() {
	if (typeOf(arguments[0]) == 'string') {
		param = arguments[0];
	}
	else {
		param = document.id('inicio').toQueryString();
	}
	
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=consultar&' + param,
		onRequest: function() {
			boxProcessing.open();
			
			document.id('captura').empty();
		},
		onSuccess: function(result) {
			if (result != '') {
				document.id('captura').empty().set('html', result);
				
				document.id('alta_riesgo').addEvent('click', alta_riesgo_trabajo);
				
				$$('img[id=info]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', info_riesgo.pass(id));
					
					img.removeProperty('alt');
				});
				
				$$('img[id=mod]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', modificar_riesgo_trabajo.pass(id));
					
					img.removeProperty('alt');
				});
				
				$$('img[id=ver]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', ver_reporte.pass(id));
					
					img.removeProperty('alt');
				});
				
				$$('img[id=download]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', descargar_reporte.pass(id));
					
					img.removeProperty('alt');
				});
				
				$$('img[id=baja][src!=/lecaroz/iconos/cancel_gray.png]').each(function(img, i) {
					var id = img.get('alt');
					
					img.addEvent('click', baja_riesgo_trabajo.pass(id));
					
					img.removeProperty('alt')
				});
				
				document.id('regresar').addEvent('click', inicio);
				
				boxProcessing.close();
			}
			else {
				inicio();
				
				boxProcessing.close();
				
				alert('No hay resultados');
			}
		}
	}).send();
}

var alta_riesgo_trabajo = function(num_cia) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=alta_riesgo' + (num_cia > 0 ? '&num_cia=' + num_cia : ''),
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(result) {
			document.id('captura').empty().set('html', result);
			
			new FormValidator(document.id('alta_riesgo'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('num_cia').addEvents({
				change: obtener_cia,
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('tipo_identificacion').focus();
					}
				}
			});
			
			document.id('tipo_identificacion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('umf_adscripcion').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('num_cia').select();
					}
				}
			});
			
			document.id('umf_adscripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('delegacion_imss').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('tipo_identificacion').focus();
					}
				}
			});
			
			document.id('delegacion_imss').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('umf_adscripcion').focus();
					}
				}
			});
			
			document.id('dia_descanso_previo_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('hora_entrada').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('delegacion_imss').focus();
					}
				}
			});
			
			document.id('hora_entrada').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_salida').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_accidente').select();
					}
				}
			});
			
			document.id('hora_salida').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					}
				}
			});
			
			document.id('fecha_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('hora_entrada').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					}
				}
			});
			
			document.id('hora_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('hora_entrada').select();
					}
				}
			});
			
			document.id('fecha_suspenso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_suspenso').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_accidente').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					}
				}
			});
			
			document.id('hora_suspenso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_accidente').select();
					}
				}
			});
			
			document.id('fecha_servicio_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_servicio_medico').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					}
				}
			});
			
			document.id('hora_servicio_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					}
				}
			});
			
			document.id('descripcion_accidente').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('descripcion_lesiones').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					}
				}
			});
			
			document.id('descripcion_lesiones').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('impresion_diagnostica').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					}
				}
			});
			
			document.id('impresion_diagnostica').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('tratamientos').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('descripcion_lesiones').focus();
					}
				}
			});
			
			document.id('tratamientos').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_servicio_medico_externo').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('impresion_diagnostica').focus();
					}
				}
			});
			
			document.id('nombre_servicio_medico_externo').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_inicio_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('tratamientos').focus();
					}
				}
			});
			
			document.id('fecha_inicio_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('folio_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					}
				}
			});
			
			document.id('folio_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('dias_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_inicio_incapacidad').select();
					}
				}
			});
			
			document.id('dias_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('nombre_servicio').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('folio_incapacidad').select();
					}
				}
			});
			
			document.id('nombre_servicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('nombre_medico').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dias_incapacidad').select();
					}
				}
			});
			
			document.id('nombre_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('matricula_medico').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_servicio').focus();
					}
				}
			});
			
			document.id('matricula_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('unidad_medica').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_medico').focus();
					}
				}
			});
			
			document.id('unidad_medica').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('ocupacion_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('matricula_medico').focus();
					}
				}
			});
			
			document.id('ocupacion_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('antiguedad_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('unidad_medica').focus();
					}
				}
			});
			
			document.id('antiguedad_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('salario_diario').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('ocupacion_trabajador').focus();
					}
				}
			});
			
			document.id('salario_diario').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('matricula_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('antiguedad_trabajador').focus();
					}
				}
			});
			
			document.id('matricula_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('clave_presupuestal_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('salario_diario').focus();
					}
				}
			});
			
			document.id('clave_presupuestal_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('matricula_trabajador').focus();
					}
				}
			});
			
			document.id('descripcion_area_trabajo').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_informante').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('clave_presupuestal_trabajador').focus();
					}
				}
			});
			
			document.id('nombre_informante').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('cargo_informante').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					}
				}
			});
			
			document.id('cargo_informante').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					}
				}
			});
			
			document.id('fecha_informe_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_informe_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_informante').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					}
				}
			});
			
			document.id('hora_informe_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_informante').focus();
					}
				}
			});
			
			document.id('informacion_testigos').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('informacion_autoridades').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					}
				}
			});
			
			document.id('informacion_autoridades').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('observaciones').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					}
				}
			});
			
			document.id('observaciones').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('informacion_autoridades').focus();
					}
				}
			});
			
			document.id('nombre_representante_legal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('lugar').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('observaciones').focus();
					}
				}
			});
			
			document.id('lugar').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					}
				}
			});
			
			document.id('fecha').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('lugar').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', consultar.pass(param));
			
			document.id('alta').addEvent('click', do_alta_riesgo_trabajo);
			
			boxProcessing.close();
			
			document.id('num_cia').focus();
		}
	}).send();
}

var do_alta_riesgo_trabajo = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		boxAlert.onCloseComplete = function() {
			document.id('num_cia').focus();
		};
		
		boxAlert.setContent('Debe especificar la compañía donde ocurrio el percance').open();
	} else if (document.id('idtrabajador').get('value').getNumericValue() == 0) {
		boxAlert.onCloseComplete = function() {
			document.id('idtrabajador').focus();
		};
		
		boxAlert.setContent('Debe seleccionar al trabajador que tuvo el percance').open();
	} else if (document.id('fecha').get('value') == '') {
		boxAlert.onCloseComplete = function() {
			document.id('fecha').focus();
		};
		
		boxAlert.setContent('Debe especificar la fecha').open();
	} else {
		new Request({
			url: 'RiesgosTrabajo.php',
			data: 'accion=do_alta_riesgo&' + document.id('alta_riesgo').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
			},
			onSuccess: function(id) {
				boxProcessing.close();
				
				generar_reporte(id);
				
				consultar(param);
			}
		}).send();
	}
}

var generar_reporte = function(id) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=generar_reporte&id=' + id,
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(id) {
			boxProcessing.close();
			
			ver_reporte(id);
		}
	}).send();
}

var ver_reporte = function(id) {
	var url = 'RiesgosTrabajo.php',
		param = '?accion=ver_reporte&id=' + id,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, '', opt);
	
	win.focus();
}

var descargar_reporte = function(id) {
	var url = 'RiesgosTrabajo.php',
		param = '?accion=descargar_reporte&id=' + id,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1';
	
	window.open(url + param, '', opt);
}

var modificar_riesgo_trabajo = function(id) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=modificar_riesgo&id=' + id,
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(result) {
			document.id('captura').empty().set('html', result);
			
			new FormValidator(document.id('modificar_riesgo'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			document.id('num_cia').addEvents({
				change: obtener_cia,
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('tipo_identificacion').focus();
					}
				}
			})/*.fireEvent('change')*/;
			
			/*Array.each(document.id('banco').options, function(op, i) {
				if (op.get('value').getNumericValue() == data.banco) {
					document.id('banco').selectedIndex = i;
				}
			});*/
			
			document.id('tipo_identificacion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('umf_adscripcion').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('num_cia').select();
					}
				}
			});
			
			document.id('umf_adscripcion').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('delegacion_imss').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('tipo_identificacion').focus();
					}
				}
			});
			
			document.id('delegacion_imss').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('umf_adscripcion').focus();
					}
				}
			});
			
			document.id('dia_descanso_previo_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('hora_entrada').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('delegacion_imss').focus();
					}
				}
			});
			
			document.id('hora_entrada').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_salida').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_accidente').select();
					}
				}
			});
			
			document.id('hora_salida').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					}
				}
			});
			
			document.id('fecha_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('hora_entrada').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					}
				}
			});
			
			document.id('hora_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('hora_entrada').select();
					}
				}
			});
			
			document.id('fecha_suspenso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_suspenso').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_accidente').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					}
				}
			});
			
			document.id('hora_suspenso').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_accidente').select();
					}
				}
			});
			
			document.id('fecha_servicio_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('hora_servicio_medico').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					}
				}
			});
			
			document.id('hora_servicio_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_suspenso').select();
					}
				}
			});
			
			document.id('descripcion_accidente').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('descripcion_lesiones').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('fecha_servicio_medico').select();
					}
				}
			});
			
			document.id('descripcion_lesiones').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('impresion_diagnostica').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('descripcion_accidente').focus();
					}
				}
			});
			
			document.id('impresion_diagnostica').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('tratamientos').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('descripcion_lesiones').focus();
					}
				}
			});
			
			document.id('tratamientos').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_servicio_medico_externo').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('impresion_diagnostica').focus();
					}
				}
			});
			
			document.id('nombre_servicio_medico_externo').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_inicio_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('tratamientos').focus();
					}
				}
			});
			
			document.id('fecha_inicio_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('folio_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dia_descanso_previo_accidente').focus();
					}
				}
			});
			
			document.id('folio_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('dias_incapacidad').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('fecha_inicio_incapacidad').select();
					}
				}
			});
			
			document.id('dias_incapacidad').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('nombre_servicio').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('folio_incapacidad').select();
					}
				}
			});
			
			document.id('nombre_servicio').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('nombre_medico').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('dias_incapacidad').select();
					}
				}
			});
			
			document.id('nombre_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('matricula_medico').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_servicio').focus();
					}
				}
			});
			
			document.id('matricula_medico').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('unidad_medica').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_medico').focus();
					}
				}
			});
			
			document.id('unidad_medica').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('ocupacion_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('matricula_medico').focus();
					}
				}
			});
			
			document.id('ocupacion_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('antiguedad_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('unidad_medica').focus();
					}
				}
			});
			
			document.id('antiguedad_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('salario_diario').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('ocupacion_trabajador').focus();
					}
				}
			});
			
			document.id('salario_diario').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('matricula_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('antiguedad_trabajador').focus();
					}
				}
			});
			
			document.id('matricula_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('clave_presupuestal_trabajador').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('salario_diario').focus();
					}
				}
			});
			
			document.id('clave_presupuestal_trabajador').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('matricula_trabajador').focus();
					}
				}
			});
			
			document.id('descripcion_area_trabajo').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_informante').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('clave_presupuestal_trabajador').focus();
					}
				}
			});
			
			document.id('nombre_informante').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('cargo_informante').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					}
				}
			});
			
			document.id('cargo_informante').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('descripcion_area_trabajo').focus();
					}
				}
			});
			
			document.id('fecha_informe_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {


						e.stop();
						
						document.id('hora_informe_accidente').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_informante').focus();
					} else if (e.key == 'down') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					}
				}
			});
			
			document.id('hora_informe_accidente').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_informante').focus();
					}
				}
			});
			
			document.id('informacion_testigos').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('informacion_autoridades').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('fecha_informe_accidente').select();
					}
				}
			});
			
			document.id('informacion_autoridades').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('observaciones').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('informacion_testigos').focus();
					}
				}
			});
			
			document.id('observaciones').addEvents({
				keydown: function(e) {
					if ((e.key == 'enter' || e.key == 'down') && e.control) {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					} else if (e.key == 'up' && e.control) {
						e.stop();
						
						document.id('informacion_autoridades').focus();
					}
				}
			});
			
			document.id('nombre_representante_legal').addEvents({
				keydown: function(e) {
					if (e.key == 'enter' || e.key == 'down') {
						e.stop();
						
						document.id('lugar').focus();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('observaciones').focus();
					}
				}
			});
			
			document.id('lugar').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('fecha').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					}
				}
			});
			
			document.id('fecha').addEvents({
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						document.id('lugar').select();
					} else if (e.key == 'up') {
						e.stop();
						
						document.id('nombre_representante_legal').focus();
					}
				}
			});
			
			document.id('cancelar').addEvent('click', consultar.pass(param));
			
			document.id('modificar').addEvent('click', do_modificar_riesgo_trabajo);
			
			boxProcessing.close();
			
			document.id('num_cia').focus();
		}
	}).send();
}

var do_modificar_riesgo_trabajo = function() {
	if (document.id('num_cia').get('value').getNumericValue() == 0) {
		boxAlert.onCloseComplete = function() {
			document.id('num_cia').focus();
		};
		
		boxAlert.setContent('Debe especificar la compañía donde ocurrio el percance').open();
	} else if (document.id('idtrabajador').get('value').getNumericValue() == 0) {
		boxAlert.onCloseComplete = function() {
			document.id('idtrabajador').focus();
		};
		
		boxAlert.setContent('Debe seleccionar al trabajador que tuvo el percance').open();
	} else if (document.id('fecha').get('value') == '') {
		boxAlert.onCloseComplete = function() {console.log('here');
			document.id('fecha').focus();
		};
		
		boxAlert.setContent('Debe especificar la fecha').open();
	} else {
		new Request({
			url: 'RiesgosTrabajo.php',
			data: 'accion=do_modificar_riesgo&' + document.id('modificar_riesgo').toQueryString(),
			onRequest: function() {
				boxProcessing.open();
			},
			onSuccess: function(id) {
				boxProcessing.close();
				
				generar_reporte(id);
				
				consultar(param);
			}
		}).send();
	}
}

var baja_riesgo_trabajo = function(id) {
	current_id = id;
	
	boxBaja.open();
}

var do_baja_riesgo_trabajo = function(id) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=do_baja_riesgo&id=' + current_id,
		onRequest: function() {
			boxBaja.close();
			
			boxProcessing.open();
		},
		onSuccess: function() {
			consultar(param);
		}
	}).send();
}

var info_riesgo = function(id) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=info_riesgo&id=' + id,
		onRequest: function() {
			boxProcessing.open();
		},
		onSuccess: function(result) {
			document.id('captura').empty().set('html', result);
			
			$$('img[id=mod]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', modificar_riesgo_trabajo.pass(id));
				
				img.removeProperty('alt');
			});
			
			$$('img[id=ver]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', ver_reporte.pass(id));
				
				img.removeProperty('alt');
			});
			
			$$('img[id=download]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', descargar_reporte.pass(id));
				
				img.removeProperty('alt');
			});
			
			$$('img[id=refresh]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', generar_reporte.pass(id));
				
				img.removeProperty('alt');
			});
			
			document.id('nueva_incapacidad').addEvent('click', alta_incapacidad);
			
			$$('img[id=digitalizar_incapacidad]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', digitalizar_incapacidad.pass(id));
				
				img.removeProperty('alt');
			});
			
			$$('img[id=baja_incapacidad]').each(function(img, i) {
				var id = img.get('alt');
				
				img.addEvent('click', do_baja_incapacidad.pass(id));
				
				img.removeProperty('alt');
			});
			
			document.id('regresar').addEvent('click', consultar.pass(param));
			
			boxProcessing.close();
		}
	}).send();
}

var alta_incapacidad = function() {
	boxAltaIncapacidad.open();
}

var do_alta_incapacidad = function() {
	if (document.id('fecha_nueva_incapacidad').get('value') == '') {
		alert('Debe especificar la fecha del nuevo documento de incapacidad');
		
		document.id('fecha_nueva_incapacidad').select();
	} else if (document.id('fecha_inicio_nueva_incapacidad').get('value') == '') {
		alert('Debe especificar la fecha de inicio de la incapacidad');
		
		document.id('fecha_inicio_nueva_incapacidad').select();
	} else if (document.id('dias_nueva_incapacidad').get('value').getNumericValue() == 0) {
		alert('Debe especificar los días otorgados de incapacidad');
		
		document.id('dias_nueva_incapacidad').select();
	} else if (document.id('folio_nueva_incapacidad').get('value') == '') {
		alert('Debe especificar el folio del nuevo documento de incapacidad');
		
		document.id('folio_nueva_incapacidad').select();
	} else {
		new Request({
			url: 'RiesgosTrabajo.php',
			data: 'accion=do_alta_incapacidad&id=' + document.id('id').get('value') + '&' + document.id('alta_incapacidad').toQueryString(),
			onRequest: function() {
				boxAltaIncapacidad.close();
				
				boxProcessing.open();
			},
			onSuccess: function(id) {
				info_riesgo(id);
			}
		}).send();
	}
}

var do_baja_incapacidad = function(id) {
	if (confirm('¿Desea borrar esta incapacidad?')) {
		new Request({
			url: 'RiesgosTrabajo.php',
			data: 'accion=do_baja_incapacidad&id=' + id,
			onRequest: function() {
				boxBaja.close();
				
				boxProcessing.open();
			},
			onSuccess: function(id) {
				info_riesgo(id);
			}
		}).send();
	}
}

var digitalizar_incapacidad = function(id) {
	new Request({
		url: 'RiesgosTrabajo.php',
		data: 'accion=digitalizar_incapacidad&id=' + id,
		onRequest: function() {},
		onSuccess: function(result) {
			boxDigitalizarIncapacidad.setContent(result).open();
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
