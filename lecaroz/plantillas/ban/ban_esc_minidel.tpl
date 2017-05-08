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
<!-- START BLOCK : borrar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./ban_esc_minidel.php" method="post" name="form">
<input name="id" type="hidden" value="{id}">
<p><font face="Arial, Helvetica, sans-serif">&iquest;Desea borrar el movimiento?</font></p>
  <p><font face="Arial, Helvetica, sans-serif">(</font>
    <input name="saldo_libros" type="checkbox" id="saldo_libros" value="TRUE" checked>
    <font face="Arial, Helvetica, sans-serif">Afectar al saldo)</font></p>  <p>
    <input type="button" class="boton" value="No" onClick="self.close()">
&nbsp;&nbsp;&nbsp;
<input type="submit" class="boton" value="Si"> 
</p></form>
</td>
</tr>
</table>
<!-- END BLOCK : borrar -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : cerrar_error -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : cerrar_error -->
</body>
</html>
