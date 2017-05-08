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
<td align="center" valign="middle"><p class="title">Registro de N&oacute;minas Recibidas</p>
  <form action="./fac_nom_cap.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia0.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <br>  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th colspan="10" class="tabla" scope="col">De las semanas</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana1_{i}.select();
else if (event.keyCode == 37) semana10_{i}.select();
else if (event.keyCode == 38) num_cia{back}.select();
else if (event.keyCode == 40) num_cia{next}.select();" size="3" maxlength="3">
        <input name="nombre_cia{i}" type="text" disabled="true" class="vnombre" id="nombre_cia{i}" size="30" maxlength="30"></td>
      <td class="tabla"><input name="semana1_{i}" type="text" class="insert" id="semana1_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana2_{i}.select();
else if (event.keyCode == 37) num_cia{i}.select();
else if (event.keyCode == 38) semana1_{back}.select();
else if (event.keyCode == 40) semana1_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana2_{i}" type="text" class="insert" id="semana2_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana3_{i}.select();
else if (event.keyCode == 37) semana1_{i}.select();
else if (event.keyCode == 38) semana2_{back}.select();
else if (event.keyCode == 40) semana2_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana3_{i}" type="text" class="insert" id="semana3_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana4_{i}.select();
else if (event.keyCode == 37) semana2_{i}.select();
else if (event.keyCode == 38) semana3_{back}.select();
else if (event.keyCode == 40) semana3_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana4_{i}" type="text" class="insert" id="semana4_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana5_{i}.select();
else if (event.keyCode == 37) semana3_{i}.select();
else if (event.keyCode == 38) semana4_{back}.select();
else if (event.keyCode == 40) semana4_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana5_{i}" type="text" class="insert" id="semana5_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana6_{i}.select();
else if (event.keyCode == 37) semana4_{i}.select();
else if (event.keyCode == 38) semana5_{back}.select();
else if (event.keyCode == 40) semana5_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana6_{i}" type="text" class="insert" id="semana6_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana7_{i}.select();
else if (event.keyCode == 37) semana5_{i}.select();
else if (event.keyCode == 38) semana6_{back}.select();
else if (event.keyCode == 40) semana6_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana7_{i}" type="text" class="insert" id="semana2_{i}222223" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana8_{i}.select();
else if (event.keyCode == 37) semana6_{i}.select();
else if (event.keyCode == 38) semana7_{back}.select();
else if (event.keyCode == 40) semana7_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana8_{i}" type="text" class="insert" id="semana8_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana9_{i}.select();
else if (event.keyCode == 37) semana7_{i}.select();
else if (event.keyCode == 38) semana8_{back}.select();
else if (event.keyCode == 40) semana8_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana9_{i}" type="text" class="insert" id="semana2_{i}22222223" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) semana10_{i}.select();
else if (event.keyCode == 37) semana8_{i}.select();
else if (event.keyCode == 38) semana9_{back}.select();
else if (event.keyCode == 40) semana9_{next}.select();" size="3" maxlength="2"></td>
      <td class="tabla"><input name="semana10_{i}" type="text" class="insert" id="semana10_{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia{next}.select();
	 if (event.keyCode == 39) num_cia{i}.select();
else if (event.keyCode == 37) semana9_{i}.select();
else if (event.keyCode == 38) semana10_{back}.select();
else if (event.keyCode == 40) semana10_{next}.select();" size="3" maxlength="2"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.anio.select();
	}
	
	window.onload = document.form.anio.select();
</script>
</body>
</html>
