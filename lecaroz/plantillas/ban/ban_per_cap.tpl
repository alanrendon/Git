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
<td align="center" valign="middle"><p class="title">Captura de Perdidas</p>
  <form action="./ban_per_cap.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Monto</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="num_cia[]" type="text" class="nombre" id="num_cia" value="{num_cia}" size="3" maxlength="3" readonly="true">
        {nombre_cia}</td>
      <td class="tabla"><input name="monto[]" type="text" class="rinsert" id="monto" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) monto[{next}].select()" value="{monto}" size="10" maxlength="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.monto[0].select();
	}
	
	window.onload = document.form.monto[0].select();
</script>
</body>
</html>
