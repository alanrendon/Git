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
<td align="center" valign="middle"><form action="ros_prv_minimod_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="codmp" type="hidden" id="codmp" value="{codmp}"> 
    <input name="i" type="hidden" id="i" value="{i}">   
    <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Producto</th>
    <th class="vtabla">{codmp} {nombre} </th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Precio de Venta </th>
    <td class="vtabla"><input name="precio" type="text" class="insert" id="precio" onFocus="tmp.value=this.value" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{precio}" size="8"></td>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.precio.value <= 0) {
		alert("Debe especificar el precio de venta");
		form.precio.select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
	else {
		form.precio_venta.select();
		return false;
	}
}

window.onload = document.form.precio.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	var i = {i}, form = window.opener.document.form;
	
	if (form.precio.length == undefined) {
		form.precio.value = "{precio}";
		window.opener.calculaImporte(i);
	}
	else {
		form.precio[i].value = "{precio}";
		window.opener.calculaImporte(i);
	}
	
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
