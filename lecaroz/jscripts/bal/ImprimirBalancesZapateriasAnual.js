// JavaScript Document

var tips;

window.addEvent('domready', function() {
	new Formulario('Datos');
			
	if (Browser.Platform.name == 'ipod') {
		$('anyo').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					$('cias').select();
					e.stop();
				}
			}
		});
		
		$('consultar').addEvent('click', Consultar);
		
		$('anio').focus();
	}
	else {
		$('cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					$('anyo').select();
					e.stop();
				}
			}
		});
		
		$('anyo').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					$('cias').select();
					e.stop();
				}
			}
		});
		
		$('info').store('tip:title', '<img src="imagenes/question.png" /> Ayuda');
		$('info').store('tip:text', 'Escriba n&uacute;meros e intervalos separados por comas.<br />Por ejemplo, escriba 1, 3, 5-12, ... etc.');
		
		tips = new Tips($('info'), {
			'fixed': true,
			'className': 'Tip',
			'showDelay': 50,
			'hideDelay': 50
		});
		
		$('imprimir').addEvent('click', Imprimir);
		
		$('cias').focus();
	}
});

var Imprimir = function() {
	if ($('anyo').get('value').getVal() == 0) {
		alert('Debe especificar el año');
		$('anyo').select();
	}
	else {
		var url = 'balance_zap_anual.php',
			arg = '?' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'balances_zap_anual', opt);
		win.focus();
	}
}

var Consultar = function() {
	if ($('anyo').get('value').getVal() == 0) {
		alert('Debe especificar el año');
		$('anyo').select();
	}
	else if (!$chk($('cias').get('value').getVal())) {
		alert('Debe seleccionar una compañía para consulta');
		$('cias').focus();
	}
	else {
		var url = 'balance_zap_anual.php',
			arg = '?' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'balances_zap_anual', opt);
		win.focus();
	}
}
