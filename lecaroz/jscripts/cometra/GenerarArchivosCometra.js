// JavaScript Document

window.addEvent('domready', function() {
	$$('[id=row]').each(function(el) {
		el.addEvents({
			'mouseover': function() {
				this.addClass('highlight');
			},
			'mouseout': function() {
				this.removeClass('highlight');
			}
		});
	});
	
	$$('a[id=separar]').each(function(el) {
		el.addEvents({
			'click': modificarSeparar.pass(el.get('title'))
		});
	});
	
	$('archivo').addEvent('click', Archivo);
	$('archivo_banorte').addEvent('click', ArchivoBanorte);
	$('reporte_faltantes').addEvent('click', ReporteFaltantes);
	$('imprimir_comprobantes').addEvent('click', imprimirComprobantes);
	$('registrar').addEvent('click', Registrar);
});

var modificarSeparar = function() {
	var index = arguments[0],
		data = JSON.decode($$('input[index=' + index + ']')[0].get('value'));
	
	if (!data.registrado) {
		new Request({
			'url': 'GenerarArchivosCometra.php',
			'data': 'accion=modificarSeparar&index=' + index + '&importe=' + data.importe + '&separar=' + data.separar,
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				popup = new Popup(result, 'Modificar importe a separar', 500, 200, validarSeparar);
			}
		}).send();
	}
	else {
		alert('No puede modificar el importe a separar de este movimiento porque ya ha sido registrado en el estado de cuenta');
	}
}

var validarSeparar = function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('separar_importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				this.blur();
			}
		}
	});
	
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('modificar').addEvent('click', function() {
		if ($('separar_importe').get('value').getNumericValue() >= $('importe').get('value').getNumericValue()) {
			alert('El importe a separar no puede ser mayor o igual a ' + $('importe').get('value').getNumericValue().numberFormat(2, '.', ','));
		}
		else {
			var index = $('index').get('value'),
				data = JSON.decode($$('input[index=' + index + ']')[0].get('value'));
			
			data.separar = $('separar_importe').get('value').getNumericValue();
			data.total = data.importe - data.separar;
			
			$$('input[index=' + index + ']')[0].set('value', JSON.encode(data));
			$$('a[id=separar][title=' + index + ']')[0].set('html', data.separar != 0 ? data.separar.numberFormat(2, '.', ',') : '&nbsp;');
			$('total_' + index).set('html', data.total != 0 ? data.total.numberFormat(2, '.', ',') : '&nbsp;');
			
			actualizarImportes.run();
			
			popup.Close();
		}
	});
	
	$('separar_importe').select();
}

var actualizarImportes = function() {
	var comprobante = '',
		separar = 0,
		total = 0;
	
	$$('input[id=data]').each(function(el) {
		if (comprobante != el.get('comprobante')) {
			comprobante = el.get('comprobante');
			
			separar_comprobante = 0;
			total_comprobante = 0;
		}
		
		var data = JSON.decode(el.get('value'));
		
		separar_comprobante += data.separar;
		total_comprobante += data.total;
		
		$('separar_' + comprobante).set('html', separar_comprobante != 0 ? separar_comprobante.numberFormat(2, '.', ',') : '&nbsp;');
		$('total_' + comprobante).set('html', total_comprobante != 0 ? total_comprobante.numberFormat(2, '.', ',') : '&nbsp;');
		
		$('general_separar_' + comprobante).set('html', separar_comprobante != 0 ? separar_comprobante.numberFormat(2, '.', ',') : '&nbsp;');
		$('general_total_' + comprobante).set('html', total_comprobante != 0 ? total_comprobante.numberFormat(2, '.', ',') : '&nbsp;');
		
		separar += data.separar;
		total += data.total;
	});
	
	$('desglose_separar').set('html', separar != 0 ? separar.numberFormat(2, '.', ',') : '&nbsp;');
	$('desglose_total').set('html', total != 0 ? total.numberFormat(2, '.', ',') : '&nbsp;');
	
	$('general_separar').set('html', separar != 0 ? separar.numberFormat(2, '.', ',') : '&nbsp;');
	$('general_total').set('html', total != 0 ? total.numberFormat(2, '.', ',') : '&nbsp;');
}

var Archivo = function() {
	new Request({
		'url': 'GenerarArchivosCometra.php',
		'data': 'accion=verificarBanco',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if ($chk(result)) {
				popup = new Popup(result, 'Seleccionar banco', 500, 200, seleccionarBancoParaArchivo, null);
			}
			else {
				document.location = 'GenerarArchivosCometra.php?accion=reporteCSV';
			}
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var ArchivoBanorte = function() {
	document.location = 'GenerarArchivosCometra.php?accion=archivoBanorte';
}

var seleccionarBancoParaArchivo = function() {
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aceptar').addEvent('click', function() {
		new Request({
			'url': 'GenerarArchivosCometra.php',
			'data': 'accion=actualizarBanco&banco=' + $$('input[id=banco]').filter(function(el) { return el.checked; }).get('value')[0],
			'onRequest': function() {
			},
			'onSuccess': function() {
				document.location = 'GenerarArchivosCometra.php?accion=reporteCSV';
				
				popup.Close();
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	});
}

var Registrar = function() {
	new Request({
		'url': 'GenerarArchivosCometra.php',
		'data': 'accion=verificarBanco',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			if ($chk(result)) {
				popup = new Popup(result, 'Seleccionar banco', 500, 200, seleccionarBancoParaRegistro, null);
			}
			else {
				ejecutarRegistro.run();
			}
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var seleccionarBancoParaRegistro = function() {
	$('cancelar').addEvent('click', function() {
		popup.Close();
	});
	
	$('aceptar').addEvent('click', function() {
		new Request({
			'url': 'GenerarArchivosCometra.php',
			'data': 'accion=actualizarBanco&banco=' + $$('input[id=banco]').filter(function(el) { return el.checked; }).get('value')[0],
			'onRequest': function() {
			},
			'onSuccess': function() {
				popup.Close();
				
				incluirReporteServicios.run();
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	});
}

var incluirReporteServicios = function() {
	new Request({
		'url': 'GenerarArchivosCometra.php',
		'data': 'accion=incluirReporteServicios',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			popup.Close();
			
			popup = new Popup(result, 'Seleccionar banco', 500, 150, actualizarReporteServicios, null);
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var actualizarReporteServicios = function() {
	$('no').addEvent('click', function() {
		new Request({
			'url': 'GenerarArchivosCometra.php',
			'data': 'accion=actualizarReporteServicios&status=FALSE',
			'onRequest': function() {
			},
			'onSuccess': function() {
				popup.Close();
				
				ejecutarRegistro.run();
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	});
	
	$('si').addEvent('click', function() {
		new Request({
			'url': 'GenerarArchivosCometra.php',
			'data': 'accion=actualizarReporteServicios&status=TRUE',
			'onRequest': function() {
			},
			'onSuccess': function() {
				popup.Close();
				
				ejecutarRegistro.run();
			},
			'onFailure': function(xhr) {
				alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
			}
		}).send();
	});
}

var ejecutarRegistro = function() {
	new Request({
		'url': 'GenerarArchivosCometra.php',
		'data': 'accion=registrarSistema&' + $$('input[id=data]').map(function(el) { return 'data[]=' + el.get('value'); }).join('&'),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			alert('Se registraron los movimientos en el estado de cuenta');
		},
		'onFailure': function(xhr) {
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var imprimirComprobantes = function() {
	new Request({
		'url': 'GenerarArchivosCometra.php',
		'data': 'accion=imprimirComprobantes',
		'onRequest': function() {
			popup = new Popup('Imprimiendo comprobantes...', 'Imprimir comprobantes', 500, 150, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
		},
		'onFailure': function(xhr) {
			popup.Close();
			
			alert('Error al sincronizar con el servidor, avisar al administrador\n\n' + xhr);
		}
	}).send();
}

var ReporteFaltantes = function() {
	var url = 'GenerarArchivosCometra.php',
		param = '?accion=reporteFaltantes',
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, 'reporte_faltantes', opt);
	
	win.focus();
}
