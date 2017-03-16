// JavaScript Document

window.addEvent('domready', function() {
	$$('tr[id=row]').addEvents({
		'mouseover': function() {
			this.addClass('highlight');
		},
		'mouseout': function() {
			this.removeClass('highlight');
		}
	});
	
	$('cancelar').addEvent('click', function() {
		document.location = 'ReporteAguinaldo.php';
	});
	
	$('registrar').addEvent('click', function() {
		document.location = 'ReporteAguinaldo.php?accion=registrar';
	});
});
