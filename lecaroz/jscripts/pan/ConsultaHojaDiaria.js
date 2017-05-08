// JavaScript Document

var tips;

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('fecha1').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha2').select();
				}
			}
		});
		
		$('fecha2').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha1').select();
				}
			}
		});
		
		new Calendar(
			{
				'fecha1': 'd/m/Y',
				'fecha2': 'd/m/Y'
			},
			{
				'days': ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
				'months': ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
			}
		);
		
		$('consultar').addEvent('click', Consultar);
		
		$('fecha').select();
	}
	else {
		$('cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha1').select();
				}
			}
		});
		
		$('fecha1').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('fecha2').select();
				}
			}
		});
		
		$('fecha2').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					$('cias').select();
				}
			}
		});
		
		$('consultar').addEvent('click', Consultar);
		
		$$('img[id^=help]').store('tip:title', '<img src="imagenes/question.png" /> Ayuda');
		
		$('help1').store('tip:text', 'Escriba n&uacute;meros e intervalos separados por comas.<br />Por ejemplo, escriba 1, 3, 5-12, ... etc.');
		$('help2').store('tip:text', 'Al escribir las dos fechas la b&uacute;squeda se har&aacute; entre ese periodo.<br />Si solo escribe la primera fecha la b&uacute;squeda ser&aacute; de ese d&iacute;a en adelante.<br />Si solo escribe la segunda fecha la b&uacute;squeda se har&aacute; ese d&iacute;a.');
		
		tips = new Tips($$('img[id^=help]'), {
			'fixed': true,
			'className': 'Tip',
			'showDelay': 50,
			'hideDelay': 50
		});
		
		$('cias').focus();
	}
});

var Consultar = function() {
	var url = 'ConsultaHojaDiaria.php',
		data = '?accion=consultar&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + data, 'hoja', opt);
	win.focus();
}
