// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('cias').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('vehiculos').focus();
			}
		}
	});
	
	$('vehiculos').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('anio').focus();
			}
		}
	});
	
	$('anio').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('cias').focus();
			}
		}
	});
	
	$('color').addEvent('change', function() {
		switch (this.get('value').getVal()) {
			case 1:
				color = '#FF8';
			break;
			
			case 2:
				color = '#FCC';
			break;
			
			case 3:
				color = '#F66';
			break;
			
			case 4:
				color = '#AFA';
			break;
			
			case 5:
				color = '#09F';
			break;
			
			default:
				color = null;
		}
		
		this.setStyle('background-color', color);
	});
	
	$('generar').addEvent('click', Generar);
	
	$('cias').focus();
});

var Generar = function() {
	var url = 'GenerarCartasCertificadosVerificaciones.php';
	var arg = 'accion=generar&' + $('Datos').toQueryString();
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	var win = window.open(url + '?' + arg, 'cartas', opt);
	win.focus();
}
