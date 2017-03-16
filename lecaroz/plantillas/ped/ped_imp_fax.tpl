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
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Faxes de Pedidos </p>
  <form action="./ped_imp_fax.php" method="get" name="form" target="fax">
  <input name="fax" type="hidden" value="1">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) codmp.select();
else if (event.keyCode == 38) num_proveedor.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Materia Prima </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_proveedor.select();
else if (event.keyCode == 38) num_cia.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select();
else if (event.keyCode == 38) codmp.select();" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?")) {
			window.open("","fax","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
			form.submit();
		}
		else
			form.num_cia.select();
	}
	
	document.form.num_cia.select();
</script>
</body>
</html>
