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
<!-- START BLOCK : question -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><font face="Geneva, Arial, Helvetica, sans-serif">&iquest;Desea eliminar el movimiento?</font>
  </p>
  <form action="./ban_fal_del.php" method="post" name="form">
  <input name="id" type="hidden" value="{id}">
  <p>
    <input type="button" class="boton" value="No" onClick="self.close()">
    &nbsp;&nbsp;
    <input name="Submit" type="submit" class="boton" value="Si"> 
    </p></form></td>
</tr>
</table>
<!-- END BLOCK : question -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
