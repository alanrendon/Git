<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturaci&oacute;n de Condimento</p>
  <form action="fac_fac_condimento_con.php" method="post" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" onclick="marcarTodo(this)" /></th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Kilos</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" /></td>
      <td class="vtabla">{num_cia} {nombre}</td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{kilos}</td>
      <td class="rtabla">{precio}</td>
      <td class="rtabla">{importe}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla">{kilos}</th>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla">{total}</th>
    </tr>
  </table>
    <br />
    <table class="tabla">
      <tr>
        <th class="tabla" scope="row">Factura Inicial </th>
        <td class="tabla"><input name="folio_ini" type="text" class="insert" id="folio_ini" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))validarFolio()" onkeydown="if(event.keyCode==13)this.blur()" size="10" maxlength="10" /></td>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Captura" onclick="document.location='fac_fac_condimento.php'" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Borrar" onclick="borrar()" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Imprimir" onclick="imprimir()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function borrar() {
	var cont = 0;
	
	if (f.id.length == undefined)
		cont += f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un registro');
		return false;
	}
	
	if (confirm('¿Desea borrar los registros seleccionados?')) {
		f.target = '_self';
		f.action = 'fac_fac_condimento_con.php?action=delete';
		f.submit();
	}
}

function imprimir() {
	var cont = 0;
	
	if (f.id.length == undefined)
		cont += f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un registro');
		return false;
	}
	
	if (get_val(f.folio_ini) == 0) {
		alert('Debe especificar la factura inicial');
		f.folio_ini.select();
		return false;
	}
	
	if (confirm('¿Desea imprimir los registros seleccionados?')) {
		if (!confirm('Introdusca en la impresora las facturas con folio ' + f.folio_ini.value + ' al ' + (get_val(f.folio_ini) + cont - 1) + ' (' + cont + ' facturas)'))
			return false;
		
		var win = window.open('', 'facturas', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		
		f.target = 'facturas';
		f.action = 'fac_fac_condimento_con.php?action=print';
		f.submit();
		
		document.location = 'fac_fac_condimento_con.php';
	}
}

function validarFolio() {
	if (f.folio_ini.value == '' || f.folio_ini.value == '0')
		f.folio_ini.value = '';
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fac_condimento_con.php', 'GET', 'f=' + get_val(f.folio_ini), resultFolio);
	}
}

var resultFolio = function (oXML) {
	var result = get_val2(oXML.responseText);
	
	if (result < 0) {
		alert('El rango no puede ser usado debido a que ya se han impreso facturas con los folios especificados');
		f.folio_ini.value = f.tmp.value;
		f.folio_ini.select();
	}
}

function marcarTodo(check) {
	if (f.id.length == undefined)
		f.id.checked = check.checked;
	else
		for (var i = 0; i < f.id.length; i++)
			f.id[i].checked = check.checked;
}
//-->
</script>
</body>
</html>
