// JavaScript Document

var f, r;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				this.blur();
			}
		}
	});
	
	$('buscar').addEvent('click', buscar);
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		$('num_cia').set('value', '');
		$('nombre').set('value', '');
	}
	else {
		new Request({
			url: 'ConsultaSolicitudesModificacionNotasPastel.php',
			method: 'post',
			data: {
				accion: 'cia',
				num_cia: $('num_cia').get('value')
			},
			onSuccess: function(result) {
				if (result != '') {
					$('nombre').set('value', result);
				}
				else {
					alert('La compañía no se encuentra en el catálogo');
					$('num_cia').set('value', Formulario.tmp);
				}
			}
		}).send();
	}
}

var buscar = function() {
	new Request({
		url: 'ConsultaSolicitudesModificacionNotasPastel.php',
		method: 'post',
		data: {
			accion: 'search',
			num_cia: $('num_cia').get('value'),
			idoperadora: $('idoperadora').get('value')
		},
		onSuccess: function(result) {
			if (result != '') {
				$('resultado').set('html', result);
				
				r = new Formulario('Resultado');
				
				$('checkall').addEvent('click', function() {
					$$('input[id=id]').each(function(el) {
						el.checked = this.checked;
					}.bind(this));
				});
				
				$('autorizar').addEvent('click', autorizar);
			}
			else {
				$('resultado').set('html', '');
				alert('No hay resultados');
			}
		}
	}).send();
}

var autorizar = function() {
	if (confirm('¿Desea autorizar los registros seleccionados?')) {
		new Request({
			url: 'ConsultaSolicitudesModificacionNotasPastel.php',
			method: 'post',
			onSuccess: function() {
				buscar();
			}
		}).send($('Resultado').toQueryString() + '&accion=authorize');
	}
}

function checkBlock(ini, fin, checked) {
	var el = $$('input[id=id]');
	
	for (var i = ini; i <= fin; i++)
		el[i].checked = checked;
}