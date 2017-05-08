// JavaScript Document

window.addEvent('domready', function() {
	new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	new FormStyles($('Datos'));
	
	if ($('cias').get('tag') == 'select') {
		$('dias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					this.blur();
				}
			}
		});
	}
	else {
		$('cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('productos').select();
				}
			}
		});
		
		$('productos').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('omitir_cias').select();
				}
			}
		});
				
		$('omitir_cias').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('omitir_mp').select();
				}
			}
		});
		
		$('omitir_mp').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('dias').select();
				}
			}
		});
		
		$('dias').addEvents({
			'change': function() {
				this.removeClass('red');
			},
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					$('cias').select();
				}
			}
		});
		
		$('cias').select();
	}
	
	$('simular').addEvent('click', Simular);
});

var Simular = function() {
	var url = 'SimulacionPedidosAutomaticos.php',
		arg = '?accion=simular&' + $('Datos').toQueryString(),
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + arg, 'reporte', opt);
	win.focus();
}
