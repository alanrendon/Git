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
<td align="center" valign="middle"><p class="title">Modificar Movimiento</p>
  <form action="./ban_ret_mov_minimod_v2.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}">
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}"> 
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      </tr>
      <tr>
        <th class="tabla" style="font-size:12pt; ">{num_cia} - {nombre} </th>
      </tr>
    </table>   
    <br>
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha_con" type="hidden" id="fecha_con" value="{fecha_con}">
      <input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_mov" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}" {selected}>{cod_mov} {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{concepto}" size="50" maxlength="200"></td>
      <td class="rtabla"><input name="importe" type="text" class="rnombre" id="importe" value="{importe}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha de movimiento");
		form.fecha.focus();
		return false;
	}
	else if (form.concepto.value.length < 3) {
		alert("Debe especificar el concepto del movimiento");
		form.concepto.focus();
		return false;
	}
	else if (confirm("¿Desea modificar y conciliar el movimiento?")) {
		form.submit();
	}
	else {
		form.fecha.select();
	}
}

window.onload = document.form.fecha.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	
	var openerWin = window.opener;
	
	openerWin.document.location.reload();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
