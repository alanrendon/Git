<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.inventario.value < 0) {
			alert("Debe especificar la existencia física");
			document.form.inventario.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar Inventario</p>
<form name="form" method="post" action="./bal_ifm_minimod_v2.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="id" type="hidden" value="{id}">
<table class="tabla">
  <tr>
    <th colspan="4" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
    </tr>
  <tr>
    <th colspan="2" class="tabla" scope="col">Materia Prima </th>
    <th class="tabla" scope="col">Existencia<br> 
    c&oacute;mputo</th>
    <th class="tabla" scope="col">Existencia<br> 
    f&iacute;sica </th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">{cod}</th>
    <th class="vtabla" scope="row">{mp}</th>
    <td class="tabla"><strong>{existencia}</strong></td>
    <td class="tabla"><input name="inventario" type="text" class="insert" id="inventario" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" value="{inventario}" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()"> 
&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Actualizar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
function seleccionar() {
	window.focus();
	document.form.inventario.select();
}

window.onload=seleccionar();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
