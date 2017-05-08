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
<td align="center" valign="middle"><p class="title">Generar Aguinaldos</p>
  <form action="./fac_tra_agu.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) incremento.select()" value="{fecha}" size="10" maxlength="10" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">% Incremento</th>
      <td class="vtabla"><input name="incremento" type="text" class="insert" id="incremento" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{incremento}" size="5" maxlength="5"></td>
    </tr>
  </table>  
  <p>
    <input name="generar" type="button" class="boton" id="generar" onClick="validar(this.form)" value="Generar Aguinaldos">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.fecha.value.length < 6) {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.incremento.value < 0 || form.incremento.value > 20 || form.incremento.value == "") {
			alert("EL incremento no puede ser mayor al 20%");
			form.reset();
			form.incremento.select();
			return false;
		}
		else {
			if (confirm("¿Desea generar los aguinaldos para la fecha especificada?"))
				form.submit();
			else
				form.fecha.select();
		}
	}
	
	window.onload = document.form.fecha.select();
</script>
</body>
</html>
