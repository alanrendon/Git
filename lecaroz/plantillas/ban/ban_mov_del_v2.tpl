<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : question -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold;">&iquest;Desea borrar los movimientos seleccionados?
  </p>
  <form action="./ban_mov_del_v2.php" method="post" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="cuenta" type="hidden" value="{cuenta}">
  <!-- START BLOCK : id -->
  <input name="id[]" type="hidden" value="{id}">
  <!-- END BLOCK : id -->
    <input type="button" class="boton" value="No" onClick="self.close()">
    &nbsp;&nbsp;
    <input type="submit" class="boton" value="Si"> 
    </form></td>
</tr>
</table>
<!-- END BLOCK : question -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	var doc = window.opener.document;
	
	doc.location = doc.location + "#{num_cia}";
	doc.location.reload();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
