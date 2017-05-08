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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Actualizar Precios de Compra de Pollos Guerra</p>
  <form action="./ros_pre_act.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Precio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="codmp[]" type="hidden" id="codmp" value="{codmp}">
        {codmp} {nombre} </td>
      <td class="tabla"><input name="precio[]" type="text" class="rinsert" id="precio" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) precio[{next}].select();
else if (event.keyCode == 38) precio[{back}].select()" value="{precio}" size="5"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Actualizar" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	var ok = false;
	// Validar que se hayan puesto todos los precios
	for (var i = 0; i < f.precio.length; i++)
		if (f.precio[i].value != '')
			ok = true;
	
	if (!ok) {
		alert("Debe especificar al menos el precio de un producto");
		f.precio[0].select();
		return false;
	}
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.precio[0].select();
}

window.onload = f.precio[0].select();
//-->
</script>
</body>
</html>
