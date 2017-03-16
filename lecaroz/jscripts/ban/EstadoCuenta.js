// JavaScript Document

var tips;

window.addEvent('domready', function() {
	new Formulario('Datos');
			
	$('cias').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha1').select();
				e.stop();
			}
		}
	});
	
	$('fecha1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha2').select();
				e.stop();
			}
		}
	});
	
	$('fecha2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha_con1').select();
				e.stop();
			}
		}
	});
	
	$('fecha_con1').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('fecha_con2').select();
				e.stop();
			}
		}
	});
	
	$('fecha_con2').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('folios').select();
				e.stop();
			}
		}
	});
	
	$('folios').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('importes').select();
				e.stop();
			}
		}
	});
	
	$('importes').addEvents({
		'keydown': function(e) {
			if (e.key == 'enter') {
				$('cias').select();
				e.stop();
			}
		}
	});
	
	$('banco').addEvents({
		'change': function() {
			switch (this.get('value')) {
				case '1':
					this.setStyle('background-image', 'url(imagenes/Banorte16x16.png)');
				break;
				
				case '2':
					this.setStyle('background-image', 'url(imagenes/Santander16x16.png)');
				break;
				
				default:
					this.setStyle('background-image', 'none');
			}
			
			Codigos();
		}
	});
	
	$$('#abonos', '#cargos').addEvent('change', Codigos);
	
	$('consultar').addEvent('click', Consultar);
	
	Codigos();
	
	$$('img[id^=help]').store('tip:title', '<img src="imagenes/question.png" /> Ayuda');
	
	$('help1').store('tip:text', 'Escriba n&uacute;meros e intervalos separados por comas.<br />Por ejemplo, escriba 1, 3, 5-12, ... etc.');
	$('help2').store('tip:text', 'Al escribir las dos fechas la b&uacute;squeda se har&aacute; entre ese periodo.<br />Si solo escribe la primera fecha la b&uacute;squeda ser&aacute; de ese d&iacute;a en adelante.<br />Si solo escribe la segunda fecha la b&uacute;squeda se har&aacute; ese d&iacute;a.');
	$('help3').store('tip:text', 'Escriba n&uacute;meros e intervalos separados por comas.<br />Por ejemplo, escriba 1, 3, 5-12, ... etc.');
	$('help4').store('tip:text', 'Escriba n&uacute;meros e intervalos separados por comas.<br />Por ejemplo, escriba 100, 300, 500-1200, ... etc.');
	$('help5').store('tip:text', 'Puede seleccionar y deseleccionar varias opciones con solo<br />dejar presionada la tecla CTRL');
	
	tips = new Tips($$('img[id^=help]'), {
		'fixed': true,
		'className': 'Tip',
		'showDelay': 50,
		'hideDelay': 50
	});
	
	$('cias').focus();
});

var Codigos = function() {
	new Request({
		'url': 'EstadoCuenta.php',
		'data': 'accion=codigos&' + $('Datos').toQueryString(),
		'onRequest': function() {
		},
		'onSuccess': function(result) {
			updSelect($('codigos'), JSON.decode(result));
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
	}
	else {
		Select.length = 1;
		$each(Select.options, function(el, i) {
			el.set({
				'value': '',
				'text': ''
			});
		});
	}
}

var Consultar = function() {
	if (!$('abonos').get('checked') && !$('cargos').get('checked')) {
		alert('Debe seleccionar \'Abonos\', \'Cargos\' o ambos');
		return false;
	}
	
	if (!$('pendientes').get('checked') && !$('conciliados').get('checked')) {
		alert('Debe seleccionar \'Pendientes\', \'Conciliados\' o ambos');
		return false;
	}
	
	if ($('fecha1').get('value').trim() == '' && $('fecha2').get('value').trim() == '') {
		if (!confirm('No ha especificado el periodo de búsqueda, para evitar saturar el sistema se usará el mes actual. ¿Desea continuar?')) {
			return false;
		}
	}
	
	
	var url = 'EstadoCuenta.php',
		data = '?accion=consultar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + data, 'reporte', opt);
	win.focus();
}
