// JavaScript Document

var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$$("input[id=num_cia]").each(function(el, i) {
		if (i > 0 && i < 9) {
			el.set('onkeydown', 'movCursor(event.keyCode,num_cia[' + (i + 1) + '],num_cia[' + (i - 1) + '],num_cia[' + (i + 1) + '],null,anio[' + i + '])');
		}
		else if (i == 0) {
			el.set('onkeydown', 'movCursor(event.keyCode,num_cia[1],null,num_cia[1],null,anio[0])');
		}
		else if (i == 9) {
			el.set('onkeydown', 'movCursor(event.keyCode,anio[0],num_cia[8],null,null,anio[9])');
		}
	});
	
	$$("input[id=anio]").each(function(el, i) {
		if (i > 0 && i < 9) {
			el.set('onkeydown', 'movCursor(event.keyCode,anio[' + (i + 1) + '],anio[' + (i - 1) + '],anio[' + (i + 1) + '],num_cia[' + i + '],null)');
		}
		else if (i == 0) {
			el.set('onkeydown', 'movCursor(event.keyCode,anio[1],null,anio[1],num_cia[0],null)');
		}
		else if (i == 9) {
			el.set('onkeydown', 'movCursor(event.keyCode,num_cia[0],anio[8],null,num_cia[9],null)');
		}
	});
	
	$('consultar').addEvent('click', function() {
		var sum = 0;
		
		$$("input[id=anio]").each(function(el) {
			sum += el.get('value').getVal();
		});
		
		if (sum == 0) {
			alert('Debe capturar los años de consulta');
			$$("input[id=anio]")[0].select();
			return false;
		}
		
		var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
		var win = window.open('', 'reporte', opt);
		
		$('Datos').submit();
		
		win.focus();
	});
	
	$('exportar').addEvent('click', function() {
		if (!$$('input[id=anio]').some(function(el) { return el.get('value').getVal() > 0; })) {
			alert('Debe capturar los años de consulta');
		}
		else {
			var queryString = [];
			
			$('Datos').getElements('input, select, radio').each(function(el) {
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
			
			document.location = 'ResultadoReporteAnual.php?accion=exportar&' + queryString.join('&');
		}
	});
	
	$$("input[id=num_cia]")[0].focus();
});

function movCursor(key, enter, lt, rt, up, dn) {
	if (key == 13 && enter != null)
		enter.focus();
	else if (key == 37 && lt != null)
		lt.focus();
	else if (key == 39 && rt != null)
		rt.focus();
	else if (key == 38 && up != null)
		up.focus();
	else if (key == 40 && dn != null)
		dn.focus();
}
