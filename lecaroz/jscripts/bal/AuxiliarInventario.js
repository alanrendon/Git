// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('anio').focus();
			}
		}
	});
	
	$('anio').addEvent('keydown', function(e) {
		if (e.key == 'enter') {
			e.stop();
			$('codmp').focus();
		}
	});
	
	$('codmp').addEvents({
		change: cambiaMP,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').focus();
			}
		}
	});
	
	$('consultar').addEvent('click', function() {
		var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		var win = window.open('', 'auxinv', opt);
		
		$('Datos').submit();
		
		win.focus();
	});
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value') == 0) {
		$('num_cia').set('value', '');
		$('nombre_cia').set('value', '');
	}
	else {
		new Request({
			url: 'AuxiliarInventario.php',
			method: 'post',
			data: {
				accion: 'cia',
				num_cia: $('num_cia').get('value')
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', Formulario.tmp);
					$('num_cia').focus();
				}
				else
					$('nombre_cia').set('value', data);
			}
		}).send();
	}
}

var cambiaMP = function() {
	if ($('codmp').get('value') == 0) {
		$('codmp').set('value', '');
		$('nombre_mp').set('value', '');
	}
	else {
		new Request({
			url: 'AuxiliarInventario.php',
			method: 'post',
			data: {
				accion: 'mp',
				codmp: $('codmp').get('value')
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('El producto no se encuentra en el catálogo');
					$('codmp').set('value', Formulario.tmp);
					$('codmp').focus();
				}
				else
					$('nombre_mp').set('value', data);
			}
		}).send();
	}
}
