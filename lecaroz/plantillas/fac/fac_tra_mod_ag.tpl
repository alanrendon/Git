<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">Nombre</th>
    </tr>
  <tr>
    <td colspan="2" class="tabla" scope="col"><strong>{nombre}</strong></td>
    </tr>
  <tr>
    <th class="tabla" scope="col">Puesto</th>
    <th class="tabla" scope="col">Turno</th>
    </tr>
  <tr>
    <td class="tabla"><strong>{puesto}</strong></td>
    <td class="tabla"><strong>{turno}</strong></td>
    </tr>
</table>
<br>
<form action="fac_tra_mod_ag.php" method="post" name="form">
  <input name="i" type="hidden" id="i" value="{i}">
  <input name="id" type="hidden" id="id" value="{id}">
  <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Aguinaldo</th>
    <td class="vtabla"><input name="check" type="radio" value="TRUE"{t}>
      Si
        <input name="check" type="radio" value="FALSE"{f}>
        No</td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;  
<input name="Submit" type="submit" class="boton" value="Modificar">
</p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	if (window.opener.document.form.aguinaldo.length == undefined)
		window.opener.document.form.ag.value = "{value}";
	else
		window.opener.document.form.ag[{i}].value = "{value}";
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
