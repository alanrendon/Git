<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Registro de Efectivos Completos</p>
  <form action="./pan_reg_efe.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false;"><table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><select name="num_cia" class="insert" id="num_cia">
        <!-- START BLOCK : num_cia -->
		<option value="{num_cia}">{num_cia} {nombre_cia}</option>
		<!-- END BLOCK : num_cia -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de &uacute;ltimo efectivo <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) {num_cia.focus();
fecha.select();}" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else {
			var cadena = "¿Son correctos los datos? (Verifica cuidadosamente antes de aceptar los cambios)\nCompañía <<"+form.num_cia.value+">>\nFecha <<"+form.fecha.value+">>";
			if (confirm(cadena))
				form.submit();
			else
				form.fecha.select();
		}
	}
	
	window.onload = document.form.fecha.select();
</script>
</body>
</html>
