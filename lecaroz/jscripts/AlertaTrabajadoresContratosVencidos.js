// JavaScript Document

window.addEvent('domready', function() {
	var width = $$('table[id=empleados]').getWidth().max();
	
	$$('table[id=empleados]').setStyle('width', width + 'px');
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	validator = new FormValidator($('Datos'), {
		showErrors: true,
		selectOnFocus: true
	});
	
	styles = new FormStyles($('Datos'));
	
	$$('input[id=meses]').each(function(el, i) {
		el.addEvents({
			'keydown': function(e) {
				if (e.key == 'enter') {
					e.stop();
					
					if ($chk($$('input[id=meses]')[i + 1])) {
						$$('input[id=meses]')[i + 1].select();
					}
					else {
						$$('input[id=meses]')[0].select();
					}
				}
			}
		});
	});
	
	$('cerrar').addEvent('click', function() {
		self.close();
	});
	
	$('renovar').addEvent('click', renovarContratosPopup);
	
	$('email').addEvent('click', enviarAvisosEmail);
	
	$$('input[id=meses]')[0].select();
});

var renovarContratosPopup = function() {
	var html = '<form method="post" name="Datos" class="FormValidator FormStyles" id="Datos"><p class="bold">¿Renovar los contrato(s)?</p><p><input type="button" name="cancelar" id="cancelar" value="Cancelar" />&nbsp;&nbsp;<input type="button" name="aceptar" id="aceptar" value="Aceptar" /></p></form>';
	
	popup = new Popup(html, 'Contratos Vencidos', 400, 100, openPopup, null);
}

var openPopup = function() {
	$('cancelar').addEvent('click', closePopup);
	
	$('aceptar').addEvent('click', renovarContratos);
}

var closePopup = function() {
	popup.Close();
}

var renovarContratos = function() {
	if ($$('input[id=meses]').get('value').getNumericValue().sum() == 0 && $$('input[id=ind]:checked').length == 0) {
		alert('Debe especificar los meses a renovar o por tiempo indeterminado en al menos un registro');
		
		popup.Close();
		
		$$('input[id=meses]')[0].select();
	}
	else if (!$$('input[id=meses]').get('value').getNumericValue().filter(function(el) { return el > 0; }).every(function(el) { return el >= 3 && el <= 12 })) {
		alert('El rango de renovación debe ser de 3 meses a 12 meses');
		
		popup.Close();
		
		$$('input[id=meses]')[0].select();
	}
	else {
		var queryString = [];
		
		$('Datos').getElements('input').each(function(el) {
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') {
				return;
			}
			
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt) {
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			
			$splat(value).each(function(val) {
				if (typeof val != 'undefined') {
					queryString.push(el.name + '=' + encodeURIComponent(val));
				}
			});
		});
		
		new Request({
			'url': 'AlertaTrabajadoresContratosVencidos.php',
			'data': 'accion=renovar&' + queryString.join('&'),
			'onRequest': function() {
				popup.Close();
				
				popup = new Popup('<img src="imagenes/_loading.gif" /> Generando contratos...', 'Contratos Vencidos', 400, 100, null, null);
			},
			'onSuccess': function(result) {
				popup.Close();
				
				JSON.decode(result).each(function(id) {
					var win = window.open('ContratoTrabajador.php?accion=contrato&renovar=1&id=' + id, '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
				});
				
				win.focus();
				
				self.close();
			}
		}).send();
		
	}
}

var enviarAvisosEmail = function() {
	new Request({
		'url': 'AlertaTrabajadoresContratosVencidos.php',
		'data': 'accion=email',
		'onRequest': function() {
			popup = new Popup('<img src="imagenes/_loading.gif" /> Enviando avisos por email...', 'Contratos Vencidos', 400, 100, null, null);
		},
		'onSuccess': function(result) {
			popup.Close();
		}
	}).send();
}
