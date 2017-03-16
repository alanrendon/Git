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
<!-- START BLOCK : question -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; ">&iquest;Desea actualizar los datos de balance otra vez? </p>
  <form action="./bal_act_bal.php" method="get" name="form"><p>
    <input name="next" type="hidden" id="next" value="1">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="mes" type="hidden" id="mes" value="{mes}">
    <input name="anio" type="hidden" id="anio" value="{anio}">
    <input type="button" class="boton" value="No">
&nbsp;&nbsp;    
<input name="Submit" type="submit" class="boton" value="Si">
  </p>
  </form></td>
</tr>
</table>
<!-- END BLOCK : question -->
<!-- START BLOCK : wait -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; ">Actualizando datos de balance.<br>
    Por favor espere.</p>
  <p><img src="./imagenes/gears.gif" width="77" height="60"> </p></td>
</tr>
</table>
<!-- END BLOCK : wait -->
<!-- START BLOCK : finish -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; ">Datos de balance actualizados.</p>
  <p>
    <input type="button" class="boton" value="Cerrar">
  </p></td>
</tr>
</table>
<!-- END BLOCK : finish -->
</body>
</html>
