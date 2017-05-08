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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Compra Directa de Materia Prima</p>
  <form action="./pan_com_dir.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) fecha.select();" size="3" maxlength="3"></td>
      <th class="vtabla" scope="col">Fecha</th>
      <td class="vtabla" scope="col"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) num_cia.select();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(num_cia)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(num_cia) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no es tuya");
				num_cia.value = "";
				num_cia.select();
				return false;
			}
			else {
				document.form.submit();
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			return false;
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Compra Directa de Materia Prima</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla"><strong>{num_cia} - {nombre_cia} </strong></td>
      <td class="tabla"><strong>{fecha}</strong></td>
    </tr>
  </table>  
  <br>
  <form action="./pan_com_dir.php" method="post" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="fecha" type="hidden" value="{fecha}">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo de Materia Prima </th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Precio Unidad </th>
      <th class="tabla" scope="col">Total</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_mp(this, nombre_mp{i}, precio_unidad{i}, cantidad{i}, total{i}, temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cantidad{i}.select();
else if (event.keyCode == 38) codmp{back}.select();
else if (event.keyCode == 40) codmp{next}.select();" size="4" maxlength="4">
        <input name="nombre_mp{i}" type="text" disabled="true" class="vnombre" id="nombre_mp{i}" size="30" maxlength="30"></td>
      <td class="tabla"><input name="cantidad{i}" type="text" class="rinsert" id="cantidad{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) total(this,precio_unidad{i},total{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) precio_unidad{i}.select();
else if (event.keyCode == 37) codmp{i}.select();
else if (event.keyCode == 38) cantidad{back}.select();
else if (event.keyCode == 40) cantidad{next}.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="precio_unidad{i}" type="text" class="rinsert" id="precio_unidad{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,3,temp)) total(cantidad{i},this,total{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) codmp{next}.select();
else if (event.keyCode == 37) cantidad.select();
else if (event.keyCode == 38) precio_unidad{back}.select();
else if (event.keyCode == 40) precio_unidad{next}.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="total{i}" type="text" class="rinsert" id="total{i}" size="10" maxlength="10" readonly="true"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar">
 &nbsp;&nbsp;
 <input type="button" class="boton" value="Siguiente"> 
 </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_mp(codmp, nombre, precio, cantidad, total, temp) {
		mp = new Array();// Materias primas
		<!-- START BLOCK : mp -->
		mp[{codmp}] = '{nombre}';
		<!-- END BLOCK : mp -->
				
		if (parseInt(codmp.value) > 0) {
			if (mp[parseInt(codmp.value)] == null) {
				alert("Código "+codmp.value+" no esta en el inventario de la compañía");
				codmp.value = temp.value;
				codmp.focus();
				return false;
			}
			else {
				nombre.value = mp[parseInt(codmp.value)];
				precio.value = "";
				cantidad.value = "";
				total.value = "";
				
				return true;
			}
		}
		else if (codmp.value == "") {
			codmp.value = "";
			nombre.value  = "";
			precio.value = "";
			cantidad.value = "";
			total.value = "";
			
			return false;
		}
	}
	
	function total(cantidad,precio,total) {
		var _cantidad = !isNaN(parseFloat(cantidad.value)) ? parseFloat(cantidad.value) : 0;
		var _precio   = !isNaN(parseFloat(precio.value)) ? parseFloat(precio.value) : 0;
		var _total    = !isNaN(parseFloat(total.value)) ? parseFloat(total.value) : 0;
		
		_total = _cantidad * _precio;
		total.value = _total > 0 ? _total.toFixed(2) : "";
		
		return;
	}
	
	function
</script>
<!-- END BLOCK : captura -->
</body>
</html>
