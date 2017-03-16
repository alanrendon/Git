// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	$('num_cia').addEvents({
		'change': CambiarCia,
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('importe').select();
			}
		}
	});
	
	$('importe').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('comprobante').select();
			}
		}
	});
	
	$('comprobante').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('encargado').select();
			}
		}
	});
	
	$('encargado').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				
				$('num_cia').select();
			}
		}
	});
	
	$('scan').addEvent('click', EscanearDocumento);
	
	$('generar').addEvent('click', Generar);
	
	$('num_cia').select();
});

var CambiarCia = function() {
	if ($('num_cia').get('value').getNumericValue() > 0) {
		new Request({
			'url': 'CometraCartaSolicitudVideo.php',
			'data': 'accion=cambiarCia&num_cia=' + $('num_cia').get('value'),
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('nombre_cia').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', $('num_cia').retrieve('tmp', '')).select();
				}
			}
		}).send();
	}
	else {
		$$('#num_cia, #nombre_cia, #importe, #comprobante, #fecha').set('value', '');
	}
}

var EscanearDocumento = function() {
	new Request({
		'url': 'CometraCartaSolicitudVideo.php',
		'data': 'accion=escanear',
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			popup = new Popup(result, '<img src="/lecaroz/iconos/pictures.png" /> Escanear documento', 630, 550, popupOpen, popupClose);
		}
	}).send();
}

var popupOpen = function() {
	$('cancelar').addEvent('click', function(e) {
		e.stop();
		
		popup.Close();
	});
}

var popupClose = function() {
}

var ObtenerMiniaturas = function() {
	var id = arguments[0];
	
	if ($('documentos').getElements('div').length == 0) {
		$('documentos').empty();
	}
	
	var documento = new Element('div', {
		'id': 'documentoID' + id,
		'styles': {
			'float': 'left',
			'width': '134px',
			'margin': '2px',
			'padding': '2px',
			'border': 'solid 1px #000'
		}
	}).inject($('documentos'));
	
	new Element('img', {
		'id': 'del' + id,
		'name': 'del' + id,
		'src': '/lecaroz/iconos/cancel.png',
		'styles': {
			'float': 'right'
		}
	}).addEvents({
		'mouseover': function(e) {
			e.stop();
			
			this.setStyle('cursor', 'pointer');
		},
		'mouseout': function(e) {
			e.stop();
			
			this.setStyle('cursor', 'default');
		},
		'click': BorrarMiniatura.pass(id)
	}).inject(documento);
	
	new Element('img', {
		'id': 'img' + id,
		'name': 'img' + id,
		'src': 'CometraCartaSolicitudVideo.php?accion=obtenerMiniatura&id=' + id + '&width=128'
	}).inject(documento);
	
	AjustarDocumentos.run();
}

var BorrarMiniatura = function() {
	var id = arguments[0];
	
	new Request({
		'url': 'CometraCartaSolicitudVideo.php',
		'data': 'accion=borrarMiniatura&id=' + id,
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			$('documentoID' + id).destroy();
			
			AjustarDocumentos.run();
		}
	}).send();
}

var AjustarDocumentos = function() {
	if ($$('div[id^=documentoID]').length > 0) {
		$('documentos').setStyle('height', $$('div[id^=documentoID]').getHeight().max() + 2 + 'px');
	}
	else {
		$('documentos').empty().set('html', 'No hay documentos<br />escaneados').setStyle('height', 'auto');
	}
}

var Generar = function() {
	if ($('num_cia').get('value').getNumericValue() == 0) {
		alert('Debe especificar la compañía');
		
		$('num_cia').select();
	}
	else if ($('importe').get('value').getNumericValue() == 0) {
		alert('Debe especificar el importe');
		
		$('importe').select();
	}
	else if ($('comprobante').get('value') == '') {
		alert('Debe especificar el número de comprobante');
		
		$('comprobante').select();
	}
	else if ($('fecha').get('value') == '') {
		alert('Debe especificar la fecha');
		
		$('fecha').select();
	}
	else if ($('encargado').get('value') == '') {
		alert('Debe especificar el nombre del encargado');
		
		$('fecha').select();
	}
	else {
		var url = 'CometraCartaSolicitudVideo.php',
			param = '?accion=pdf&' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + param, '', opt);
		
		win.focus();
		
		$$('#num_cia, #nombre_cia, #importe, #comprobante, #fecha').set('value', '');
		
		$('documentos').empty().set('html', 'No hay documentos<br />escaneados').setStyle('height', 'auto');
	}
}
