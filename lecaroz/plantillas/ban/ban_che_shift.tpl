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
<!-- START BLOCK : OK -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle" class="title"><p class="title">Los cheques se han impreso correctamente </p></td>
</tr>
</table>
<!-- END BLOCK : OK -->
<!-- START BLOCK : pregunta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">&iquest;Se imprimieron bien los cheques?
  </p>
  <p>
    <input type="button" class="boton" value="No" onClick="document.location='./ban_che_shift.php?shift=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Si" onClick="validar()"> 
    </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar() {
		if (confirm("¿Esta seguro que los cheques se imprimieron correctamente?"))
			document.location = './ban_che_shift.php?ok=1';
		else
			return false;
	}
</script>
<!-- END BLOCK : pregunta -->
<!-- START BLOCK : folio -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recorrer Folios</p>
<p><font face="Geneva, Arial, Helvetica, sans-serif">Escriba el folio a partir del cual se recorreran los cheques</font></p>
  <form action="./ban_che_shift.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="num_cheque" type="text" class="insert" id="num_cheque" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_che_shift.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Recorrer">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cheque.value == "") {
			alert("Debe especificar el folio");
			form.num_cheque.select();
			return false;
		}
		else if (form.num_cheque.value <= {num_cheque}) {
			alert("El folio no puede ser menor a {num_cheque}");
			form.num_cheque.select();
			return false;
		}
		else if (confirm("¿Es correctos el folio?\n--------------------------------------\nFolio:  " + form.num_cheque.value))
			form.submit();
		else
			form.num_cheque.select();
	}
	
	window.onload = document.form.num_cheque.select();
</script>
<!-- END BLOCK : folio -->
</body>
</html>
