// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha2').select();
			}
		}
	});
	
	$('fecha2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').select();
			}
		}
	});
	
	$('consultar').addEvent('click', function() {
		if ($('fecha1').get('value').length < 8) {
			alert('Debe especificar el periodo de consulta');
			$('fecha1').focus();
		}
		else {
			var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
			var win = window.open('', 'gastos', opt);
			
			f.form.submit();
			win.focus();
		}
	});
	
	$('num_cia').select();
});

var cambiaCia = function() {
	if ($('num_cia').get('value') == 0) {
		$('num_cia').set('value', '');
		$('nombre').set('value', '');
	}
	else {
		new Request({
			url: 'ConsultaGastosMecanicos.php',
			data: {
				accion: 'getCia',
				num_cia: $('num_cia').get('value')
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', Formulario.tmp);
					$('num_cia').select();
				}
				else
					$('nombre').set('value', data);
			}
		}).send();
	}
}
