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
		
		$('generar').addEvent('click', Generar);
		$('imprimir').addEvent('click', Imprimir);
		
		$('cias').focus();
	}
});

var Generar = function() {
	if ($('anyo').get('value').getVal() < 2000) {
		alert('Debe especificar el año');
		$('anyo').select();
	}
	else if (confirm('¿Desea generar/actualizar los datos de balance?')) {
		new Request({
			'url': 'generar_balance_pan.php',
			'method': 'get',
			'data': $('Datos').toQueryString(),
			'onRequest': function() {
				$('estatus').set('html', '<img src="imagenes/_loading.gif" width="16" height="16" align="bottom" /> Generando/Actualizando...');
			},
			'onSuccess': function(result) {
				if (result != '') {
					$('estatus').set('html', '<img src="imagenes/stop_round.png" width="16" height="16" align="bottom" /><strong> Ha ocurrido un error y es posible que algunos balances no hayan podido generarse/actualizarse<br /><br />&quot;' + result + '&quot;</strong>');
				}
				else {
					$('estatus').set('html', '<img src="imagenes/accept_green.png" width="16" height="16" align="bottom" /><strong> Se han generado/actualizado todos los datos de balance solicitados</strong>');
				}	
			}
		}).send();
	}
}

var Imprimir = function() {
	if ($('anyo').get('value').getVal() == 0) {
		alert('Debe especificar el año de consulta');
		$('anyo').select();
	}
	else {
		var url = 'balance_pan.php',
			arg = '?' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'balances_pan', opt);
		win.focus();
	}
}

var Consultar = function() {
	if ($('anyo').get('value').getVal() == 0) {
		alert('Debe especificar el año de consulta');
		$('anyo').select();
	}
	else if (!$chk($('cias').get('value').getVal()) && !$chk($('admin').get('value').getVal())) {
		alert('Debe seleccionar una compañía o un administrador para consulta');
		$('cias').focus();
	}
	else {
		var url = 'balance_pan.php',
			arg = '?' + $('Datos').toQueryString(),
			opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
			win;
		
		win = window.open(url + arg, 'balances_pan', opt);
		win.focus();
	}
}
