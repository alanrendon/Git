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
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : question -->
<form action="./ban_dep_rec.php" method="post" name="form">
<input name="dir" id="dir" type="hidden" value="{dir}">
<!-- START BLOCK : dia -->
<input name="dia[]" id="dia" type="hidden" value="{dia}">
<!-- END BLOCK : dia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p>
  <strong><font face="Arial, Helvetica, sans-serif">&iquest;Desea recorrer un d&iacute;a hacia {direccion} los dep&oacute;sitos seleccionados?</font></strong>
  </p>
  <p>
    <input type="button" class="boton" value="No" onClick="self.close()">
&nbsp;
<input type="submit" class="boton" value="Si">
</p></td>
</tr>
</table></form>
<!-- END BLOCK : question -->
</body>
</html>
