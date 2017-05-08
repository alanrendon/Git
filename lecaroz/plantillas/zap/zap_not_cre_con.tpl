<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Notas de Cr&eacute;dito</p>
  <form action="./zap_not_cre_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) folio.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="rinsert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha1.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estado</th>
      <td class="vtabla"><input name="status" type="radio" value="0" checked="checked" />
        Pendientes<br />
        <input name="status" type="radio" value="1" />
        Acreditados<br />
        <input name="status" type="radio" value="2" />
        Aplicados<br />
        <input name="status" type="radio" value="-1" />
        Todos</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./zap_not_cre_con.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre_cia.value = result;
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./zap_not_cre_con.php', 'GET', 'p=' + get_val(f.num_pro), obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	else
		f.nombre_pro.value = result;
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Notas de Cr&eacute;dito</p>
  <form action="./zap_not_cre_con.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col"><img src="./menus/insert.gif" width="16" height="16" /></th>
      <th class="tabla" scope="col"><img src="./menus/delete.gif" width="16" height="16" /></th>
      <th class="tabla" scope="col">Mod</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Estatus</th>
      <th class="tabla" scope="col">Aplicado a </th>
      <th class="tabla" scope="col">Cheque</th>
      <th class="tabla" scope="col">Banco</th>
    </tr>
    <!-- START BLOCK : row -->
	<tr>
	  <td class="tabla"><input name="ok[]" type="checkbox" id="ok" value="{id}"{ok_dis} /></td>
      <td class="tabla"><input name="x[]" type="checkbox" id="x" value="{id}"{x_dis} /></td>
      <td class="tabla"><input type="button" class="boton" value="." onclick="mod({id})"{mod_dis} /></td>
      <td class="vtabla">{num_cia} {nombre_cia} </td>
      <td class="vtabla">{num_pro} {nombre_pro} </td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{folio}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
      <td class="tabla">{status}</td>
      <td class="vtabla">{num_cia_apl} {nombre_cia_apl} </td>
      <td class="tabla">{folio_cheque}</td>
      <td class="tabla">{banco}</td>
    </tr>
	<!-- END BLOCK : row -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./zap_not_cre_con.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Borrar" onclick="del()" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Acreditar" onclick="acre()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function acre() {
	var cont = 0;
	
	if (f.ok.length == undefined)
		cont = f.ok.checked ? 1 : 0;
	else
		for (var i = 0; i < f.ok.length; i++)
			cont += f.ok[i].checked ? 1 : 0;
	
	if (cont == 0)
		alert('Debe seleccionar al menos una nota');
	else if (confirm('¿Desea acreditar las notas seleccionadas?')) {
		f.action = './zap_not_cre_con.php?accion=acre';
		f.submit();
	}
}

function del() {
	var cont = 0;
	
	if (f.x.length == undefined)
		cont = f.x.checked ? 1 : 0;
	else
		for (var i = 0; i < f.x.length; i++)
			cont += f.x[i].checked ? 1 : 0;
	
	if (cont == 0)
		alert('Debe seleccionar al menos una nota');
	else if (confirm('¿Desea borrar las notas seleccionadas?')) {
		f.action = './zap_not_cre_con.php?accion=del';
		f.submit();
	}
}

function mod(id) {
	var win = window.open("zap_not_cre_mod.php?id=" + id, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=400");
	win.focus();
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
