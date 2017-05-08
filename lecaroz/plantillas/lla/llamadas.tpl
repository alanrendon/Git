<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Llamadas</p>
  <form action="./llamadas.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Status</th>
      <td class="vtabla"><select name="status" class="insert" id="status">
        <option value="0" selected>NO CONTESTADAS</option>
        <option value="1">CONTESTADAS</option>
        <option value="">TODAS</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha");
		form.fecha.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Llamadas</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Usuario</th>
    </tr>
    <tr>
      <td class="tabla"><strong>{usuario}</strong></td>
    </tr>
  </table>  
  <br>
  <form action="./llamadas.php" method="post" name="form">
  <input name="status" type="hidden" value="{status}">
  <input name="fecha" type="hidden" value="{fecha}">
  <table class="tabla">
    <!-- START BLOCK : result -->
	<tr>
      <th class="tabla" scope="col"><input name="checkall" type="checkbox" id="checkAal" onClick="checkAll(this)"></th>
      <th class="tabla" scope="col">De</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Hora</th>
      <th class="tabla" scope="col">Recado</th>
      <th class="tabla" scope="col">Status</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="id[]" type="checkbox" id="id" value="{id}" {disabled}></td>
      <td class="vtabla">{de}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{hora}</td>
      <td class="vtabla">{recado}</td>
      <td class="tabla"><strong><font color="#{color}">{status}</font></strong></td>
      </tr>
	  <!-- END BLOCK : fila -->
	  <!-- END BLOCK : result -->
	  <!-- START BLOCK : no_result -->
	  <tr>
	    <th class="tabla" scope="col">No hay resultados</th>
	  </tr>
	  <!-- END BLOCK : no_result -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./llamadas.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cambiar Status" onClick="validar()"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
var form = document.form;

function checkAll(check) {
	if (form.id.length == undefined) {
		form.id.checked = check.checked ? true : false;
	}
	else {
		for (i = 0; i < form.id.length; i++) {
			form.id[i].checked = check.checked ? true : false;
		}
	}
}

function validar() {
	var count = 0;
	
	if (form.id.length == undefined) {
		count += form.id.checked ? 1 : 0;
	}
	else {
		for (i = 0; i < form.id.length; i++) {
			count += form.id[i].checked ? 1 : 0;
		}
	}
	
	if (count == 0) {
		alert("Debe seleccionar al menos un registro");
		return false;
	}
	else if (confirm("¿Desea cambiar el status de los registros seleccionados?")) {
		form.submit();
	}
	else {
		return false;
	}
}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
