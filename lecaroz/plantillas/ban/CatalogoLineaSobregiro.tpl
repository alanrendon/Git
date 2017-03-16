<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cat&aacute;logo de L&iacute;nea Sobregiro Banorte </title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Cat&aacute;logo de L&iacute;nea Sobregiro Banorte </div>
  <div id="captura" align="center">
    <form action="" method="post" name="Captura" class="formulario" id="Captura">
      <table class="tabla_captura" id="TablaCaptura">
        <tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Mancomunada</th>
          <th scope="col">Cuenta<br />
          Sobregiro</th>
          <th scope="col">Cuenta
            <br />
          Efectivo</th>
          <th scope="col">Importe<br />
          Autorizado</th>
        </tr>
        <tr style="display:none;">
          <td align="center"><input name="num_cia_copy" type="text" class="cap toInt alignCenter" id="num_cia_copy" size="1" />
          <input name="nombre_cia_copy" type="text" class="disabled" id="nombre_cia_copy" size="30" /></td>
          <td align="center"><input name="num_sec_copy" type="text" class="cap toInt alignCenter" id="num_sec_copy" size="1" />
          <input name="nombre_sec_copy" type="text" class="disabled" id="nombre_sec_copy" size="30" /></td>
          <td align="center"><input name="cuenta_sobregiro_copy" type="text" class="cap onlyNumbers Red" id="cuenta_sobregiro_copy" size="11" maxlength="11" /></td>
          <td align="center"><input name="cuenta_efectivo_copy" type="text" class="cap onlyNumbers Blue" id="cuenta_efectivo_copy" size="11" maxlength="11" /></td>
          <td align="center"><input name="importe_autorizado_copy" type="text" class="cap numPosFormat2 alignRight" id="importe_autorizado_copy" size="12" /></td>
        </tr>
      </table>
      <p>
        <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('Captura');
	
	nuevaFila(0);
	
	$('siguiente').addEvent('click', validar);
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	var c = arguments[0];
	var n = arguments[1];
	var i = arguments[2];
	
	var num_cia = eval('f.form.' + c + '.length') == undefined ? eval('f.form.' + c) : eval('f.form.' + c)[i];
	var nombre_cia = eval('f.form.' + n + '.length') == undefined ? eval('f.form.' + n) : eval('f.form.' + n)[i];
	
	if (num_cia.value.toInt() > 0) {
		new Request({
			url: 'CatalogoLineaSobregiro.php',
			method: 'get',
			data: {
				c: num_cia.value
			},
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('La compañía ' + num_cia.value + ' no se encuentra en el catálogo');
					
					num_cia.value = '';
					nombre_cia.value = '';
				}
				else
					nombre_cia.value = nombre;
			}
		}).send();
	}
	else {
		num_cia.value = null;
		nombre_cia.value = null;
	}
}

var validarRegistro = function() {
	var i = arguments[0];
	
	var num_cia = eval('f.form.num_cia.length') == undefined ? eval('f.form.num_cia').get('value').toInt() : eval('f.form.num_cia')[i].get('value').toInt();
	var num_sec = eval('f.form.num_sec.length') == undefined ? eval('f.form.num_sec').get('value').toInt() : eval('f.form.num_sec')[i].get('value').toInt();
	
	if (num_cia > 0 && num_sec > 0) {
		new Request({
			url: 'CatalogoLineaSobregiro.php',
			method: 'get',
			data: {
				nc: num_cia,
				ns: num_sec
			},
			onSuccess: function(result) {
				var r = result.split('|');
				
				if (r[0].toInt() < 0) {
					var nombre_cia = eval('f.form.nombre_cia.length') == undefined ? eval('f.form.nombre_cia').get('value') : eval('f.form.nombre_cia')[i].get('value');
					var nombre_sec = eval('f.form.nombre_sec.length') == undefined ? eval('f.form.nombre_sec').get('value') : eval('f.form.nombre_sec')[i].get('value');
					
					var cs = eval('f.form.cuenta_sobregiro.length') == undefined ? eval('f.form.cuenta_sobregiro') : eval('f.form.cuenta_sobregiro')[i];
					var ce = eval('f.form.cuenta_efectivo.length') == undefined ? eval('f.form.cuenta_efectivo') : eval('f.form.cuenta_efectivo')[i];
					var im = eval('f.form.importe_autorizado.length') == undefined ? eval('f.form.importe_autorizado') : eval('f.form.importe_autorizado')[i];
					
					cs.set({
						value: r[1],
						readonly: true
					});
					ce.set({
						value: r[2],
						readonly: true
					});
					im.set({
						value: r[3],
						readonly: true
					});
					
					var msg = '';
					msg += "La compañía " + num_cia + " '" + nombre_cia + "' con sucursal " + num_sec + " '" + nombre_sec + "' ya contiene los siguientes datos:\n";
					msg += "\nCuenta Sobregiro:\t\t" + r[1];
					msg += "\nCuenta Efectivo:\t\t" + r[2];
					msg += "\nImporte Autorizado:\t" + r[3].toFloat().numberFormat(2, '.', ',');
					msg += "\n\n¿Desea modificar estos datos?";
					
					if (confirm(msg)) {
						cs.set('readonly', false);
						ce.set('readonly', false);
						im.set('readonly', false);
					}
				}
			}
		}).send();
	}
	else {
		var cs = eval('f.form.cuenta_sobregiro.length') == undefined ? eval('f.form.cuenta_sobregiro') : eval('f.form.cuenta_sobregiro')[i];
		var ce = eval('f.form.cuenta_efectivo.length') == undefined ? eval('f.form.cuenta_efectivo') : eval('f.form.cuenta_efectivo')[i];
		var im = eval('f.form.importe_autorizado.length') == undefined ? eval('f.form.importe_autorizado') : eval('f.form.importe_autorizado')[i];
		
		cs.set('readonly', false);
		ce.set('readonly', false);
		im.set('readonly', false);
	}
}

var validar = function() {
	if (!$defined(f.form.num_cia.length)) {
		if (f.form.num_cia.get('value').toInt() > 0 && f.form.num_sec.get('value').toInt() > 0) {
			if (f.form.cuenta_sobregiro.get('value').length != 11) {
				alert('Debe especificar la cuenta de sobregiro');
				f.form.cuenta_sobregiro.select();
				return false;
			}
			if (f.form.cuenta_efectivo.get('value').length != 11) {
				alert('Debe especificar la cuenta de efectivo');
				f.form.cuenta_efectivo.select();
				return false;
			}
			if (f.form.importe_autorizado.get('value').getVal() == 0) {
				alert('Debe especificar el importe autorizado');
				f.form.importe_autorizado.select();
				return false;
			}
		}
	}
	else {
		for (var i = 0, l = f.form.num_cia.length; i < l; i++) {
			if (f.form.num_cia[i].get('value').toInt() > 0 && f.form.num_sec[i].get('value').toInt() > 0) {
				if (f.form.cuenta_sobregiro[i].get('value').length != 11) {
					alert('Debe especificar la cuenta de sobregiro');
					f.form.cuenta_sobregiro[i].select();
					return false;
				}
				if (f.form.cuenta_efectivo[i].get('value').length != 11) {
					alert('Debe especificar la cuenta de efectivo');
					f.form.cuenta_efectivo[i].select();
					return false;
				}
				if (f.form.importe_autorizado[i].get('value').getVal() == 0) {
					alert('Debe especificar el importe autorizado');
					f.form.importe_autorizado[i].select();
					return false;
				}
			}
		}
	}
	
	if (confirm('¿Son correctos todos los datos?'))
		f.form.submit();
}

function nuevaFila(i) {
	if ($defined($('row' + i)))
		return false;
	
	// Nueva elemento 'tr'
	var tr = new Element('tr', {
		id: 'row' + i,
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	
	// Crear elemento 'td'
	var td = new Element('td', {
		align: 'center'
	});
	
	// Nuevo elemento 'num_cia'
	var num_cia = null;
	// Clonar elemento 'num_cia_copy'
	num_cia = $('num_cia_copy').clone();
	// Clonar eventos de elemento 'num_cia_copy' a elemento 'num_cia'
	num_cia.cloneEvents($('num_cia_copy'));
	// Añadir al elemento 'num_cia' al evento 'onChange' la función 'cambiaCia'
	num_cia.addEvent('change', cambiaCia.pass(['num_cia', 'nombre_cia', i]));
	// Añadir al elemento 'num_cia' al evento 'onChange' la función 'validarRegistro'
	num_cia.addEvent('change', validarRegistro.pass(i));
	// Añadir propiedades al elemento 'num_cia'
	num_cia.set({
		id: 'num_cia',
		name: 'num_cia[]',
		onkeydown: "movCursor(event.keyCode," + i + ",false,'num_sec',null,'num_sec','num_cia','num_cia')"
	});
	
	// Nuevo elemento 'nombre_cia'
	var nombre_cia = null;
	// Clonar elemento 'nombre_cia_copy'
	nombre_cia = $('nombre_cia_copy').clone();
	// Añadir propiedades al elemento 'nombre_cia'
	nombre_cia.set({
		id: 'nombre_cia',
		name: 'nombre_cia[]',
		disabled: true
	});
	
	// Inyectar elementos 'num_cia' y 'nombre_cia' a elemento 'td'
	num_cia.inject(td);
	nombre_cia.inject(td);
	
	// Inyectar elemento 'td' a elemento 'tr'
	td.inject(tr);
	
	// Crear elemento 'td'
	var td = new Element('td', {
		align: 'center'
	});
	
	// Nuevo elemento 'num_sec'
	var num_sec = null;
	// Clonar elemento 'num_sec_copy'
	num_sec = $('num_sec_copy').clone();
	// Clonar eventos de elemento 'num_sec_copy' a elemento 'num_sec'
	num_sec.cloneEvents($('num_sec_copy'));
	// Añadir al elemento 'num_sec' al evento 'onChange' la función 'cambiaCia'
	num_sec.addEvent('change', cambiaCia.pass(['num_sec', 'nombre_sec', i]));
	// Añadir al elemento 'num_sec' al evento 'onChange' la función 'validarRegistro'
	num_sec.addEvent('change', validarRegistro.pass(i));
	// Añadir propiedades al elemento 'num_sec'
	num_sec.set({
		id: 'num_sec',
		name: 'num_sec[]',
		onkeydown: "movCursor(event.keyCode," + i + ",false,'cuenta_sobregiro','num_cia','cuenta_sobregiro','num_sec','num_sec')"
	});
	
	// Nuevo elemento 'nombre_sec'
	var nombre_sec = null;
	// Clonar elemento 'nombre_sec_copy'
	nombre_sec = $('nombre_sec_copy').clone();
	// Añadir propiedades al elemento 'nombre_sec'
	nombre_sec.set({
		id: 'nombre_sec',
		name: 'nombre_sec[]',
		disabled: true
	});
	
	// Inyectar elementos 'num_sec' y 'nombre_sec' a elemento 'td'
	num_sec.inject(td);
	nombre_sec.inject(td);
	
	// Inyectar elemento 'td' a elemento 'tr'
	td.inject(tr);
	
	// Crear elemento 'td'
	var td = new Element('td', {
		align: 'center'
	});
	
	// Nuevo elemento 'cuenta_sobregiro'
	var cuenta_sobregiro = null;
	// Clonar elemento 'cuenta_sobregiro_copy'
	cuenta_sobregiro = $('cuenta_sobregiro_copy').clone();
	// Clonar eventos de elemento 'cuenta_sobregiro_copy' a elemento 'cuenta_sobregiro'
	cuenta_sobregiro.cloneEvents($('cuenta_sobregiro_copy'));
	// Añadir propiedades al elemento 'cuenta_sobregiro'
	cuenta_sobregiro.set({
		id: 'cuenta_sobregiro',
		name: 'cuenta_sobregiro[]',
		onkeydown: "movCursor(event.keyCode," + i + ",false,'cuenta_efectivo','num_sec','cuenta_efectivo','cuenta_sobregiro','cuenta_sobregiro')"
	});
	
	// Inyectar elemento 'cuenta_sobregiro' a elemento 'td'
	cuenta_sobregiro.inject(td);
	
	// Inyectar elemento 'td' a elemento 'tr'
	td.inject(tr);
	
	// Crear elemento 'td'
	var td = new Element('td', {
		align: 'center'
	});
	
	// Nuevo elemento 'cuenta_efectivo'
	var cuenta_efectivo = null;
	// Clonar elemento 'cuenta_efectivo_copy'
	cuenta_efectivo = $('cuenta_efectivo_copy').clone();
	// Clonar eventos de elemento 'cuenta_efectivo_copy' a elemento 'cuenta_efectivo'
	cuenta_efectivo.cloneEvents($('cuenta_efectivo_copy'));
	// Añadir propiedades al elemento 'cuenta_efectivo'
	cuenta_efectivo.set({
		id: 'cuenta_efectivo',
		name: 'cuenta_efectivo[]',
		onkeydown: "movCursor(event.keyCode," + i +",false,'importe_autorizado','cuenta_sobregiro','importe_autorizado','cuenta_efectivo','cuenta_efectivo')"
	});
	
	// Inyectar elemento 'cuenta_efectivo' a elemento 'td'
	cuenta_efectivo.inject(td);
	
	// Inyectar elemento 'td' a elemento 'tr'
	td.inject(tr);
	
	// Crear elemento 'td'
	var td = new Element('td', {
		align: 'center'
	});
	
	// Nuevo elemento 'importe_autorizado'
	var importe_autorizado = null;
	// Clonar elemento 'importe_autorizado_copy'
	importe_autorizado = $('importe_autorizado_copy').clone();
	// Clonar eventos de elemento 'importe_autorizado_copy' a elemento 'importe_autorizado'
	importe_autorizado.cloneEvents($('importe_autorizado_copy'));
	// Añadir propiedades al elemento 'importe_autorizado'
	importe_autorizado.set({
		id: 'importe_autorizado',
		name: 'importe_autorizado[]',
		onkeydown: "if(event.keyCode==13&&!$defined(f.form.num_cia[" + (i + 1) + "]))nuevaFila(" + (i + 1) + ");movCursor(event.keyCode," + i + ",true,'num_cia','cuenta_efectivo',null,'importe_autorizado','importe_autorizado')"
	});
	
	// Inyectar elemento 'importe_autorizado' a elemento 'td'
	importe_autorizado.inject(td);
	
	// Inyectar elemento 'td' a elemento 'tr'
	td.inject(tr);
	
	// Inyectar elemento 'tr' en elemento 'TablaCaptura'
	tr.inject($('TablaCaptura'));
}

function movCursor(keyCode, index, next, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null) {
		if (index != null) {
			if (eval('f.form.' + enter + '.length') == undefined)
				eval('f.form.' + enter + '.select()');
			else if (eval('f.form.' + enter + '[' + (next ? index + 1 : index) + ']') != undefined)
				eval('f.form.' + enter + '[' + (next ? index + 1 : index) + '].select()');
		}
		else if ($(enter))
				$(enter).select();
	}
	else if (keyCode == 37 && lt != null) {
		if (index != null) {
			if (eval('f.form.' + lt + '.length') == undefined)
				eval('f.form.' + lt + '.select()');
			else if (eval('f.form.' + lt + '[' + index + ']') != undefined)
				eval('f.form.' + lt + '[' + index + '].select()');
		}
		else if ($(lt))
			$(lt).select();
	}
	else if (keyCode == 39 && rt != null) {
		if (index != null) {
			if (eval('f.form.' + rt + '.length') == undefined)
				eval('f.form.' + rt + '.select()');
			else if (eval('f.form.' + rt + '[' + index + ']') != undefined)
				eval('f.form.' + rt + '[' + index + '].select()');
		}
		else if ($(rt))
			$(rt).select();
	}
	else if (keyCode == 38 && up != null) {
		if (index != null) {
			if (eval('f.form.' + up + '.length') != undefined && eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + ']') != undefined)
				eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + '].select()');
		}
		else if ($(up))
			$(up).select();
	}
	else if (keyCode == 40 && dn != null) {
		if (index != null) {
			if (eval('f.form.' + dn + '.length') != undefined && eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + ']') != undefined)
				eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + '].select()');
		}
		else if ($(dn))
			$(dn).select();
	}
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
