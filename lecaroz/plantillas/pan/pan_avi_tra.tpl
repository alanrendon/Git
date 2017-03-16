<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<style type="text/css" media="print">
.noPrint {
	display:none;
}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Traspaso de Av&iacute;o</p>
<form action="./pan_avi_tra.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a que traspasa </th>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a que recibe </th>
    <th class="tabla" scope="col">Fecha</th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia_traspasa" type="text" class="insert" id="num_cia_traspasa" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia_recibe.select();
else if (event.keyCode == 37) fecha.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="num_cia_recibe" type="text" class="insert" id="num_cia_recibe" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha.select();
else if (event.keyCode == 37) num_cia_traspasa.select();" size="3" maxlength="3"></td>
    <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia_traspasa.select();
else if (event.keyCode == 37) num_cia_recibe.select();" value="{fecha}" size="10" maxlength="10"></td>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia_traspasa.value <= 0) {
			alert("Debe especificar la compañía que traspasa el avio");
			form.num_cia_traspasa.select();
			return false;
		}
		else if (form.num_cia_recibe.value <= 0) {
			alert("Debe especificar la compañía que recibe el avio");
			form.num_cia_recibe.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia_traspasa.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Traspaso de Av&iacute;o</p>
<form action="./pan_avi_tra.php" method="post" name="form">
<input name="temp_codmp" type="hidden">
<input name="temp_cantidad" type="hidden">
<input name="temp_precio" type="hidden">
<input name="temp" type="hidden" id="temp">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a que traspasa </th>
    <th class="tabla" scope="col">Fecha de traspaso </th>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a que recibe </th>
  </tr>
  <tr>
    <td class="tabla"><strong>
      <input name="num_cia_traspasa" type="hidden" id="num_cia_traspasa" value="{num_cia_traspasa}">
      {num_cia_traspasa} {nombre_cia_traspasa} </strong></td>
    <td class="tabla"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
      {fecha}</td>
    <td class="tabla"><strong>
      <input name="num_cia_recibe" type="hidden" id="num_cia_recibe" value="{num_cia_recibe}">
      {num_cia_recibe} {nombre_cia_recibe} </strong></td>
  </tr>
</table>

  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo y Materia Prima </th>
      <th class="tabla" scope="col">Existencia</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Total</th>
    </tr>
	<!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" onFocus="temp_codmp.value=this.value;
temp_cantidad.value=cantidad{i}.value;
temp_precio.value=precio{i}.value;" onChange="if (isInt(this,temp_codmp)) actualiza_mp(this, nombre_mp{i}, precio{i}, existencia{i}, cantidad{i}, total{i}, gran_total, temp_codmp, temp_precio, temp_cantidad)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cantidad{i}.select();
else if (event.keyCode == 37) cantidad{i}.select();
else if (event.keyCode == 38) codmp{back}.select();
else if (event.keyCode == 40) codmp{next}.select();" size="4" maxlength="4">        
        <input name="nombre_mp{i}" type="text" disabled="true" class="vnombre" id="nombre_mp{i}" size="50" maxlength="50"></td>
      <td class="tabla"><input name="existencia{i}" type="text" class="rnombre" id="existencia{i}" size="10" maxlength="10" readonly="true"></td>
      <td class="tabla"><input name="cantidad{i}" type="text" class="rinsert" id="cantidad{i}" onFocus="temp_cantidad.value=this.value" onChange="if (isFloat(this,2,temp_cantidad)) total(codmp{i}, this, precio{i}, existencia{i}, total{i}, gran_total, temp_cantidad, temp_precio)" onKeyDown="if (event.keyCode == 13) codmp{next}.select();
else if (event.keyCode == 37) codmp{i}.select();
else if (event.keyCode == 38) cantidad{back}.select();
else if (event.keyCode == 39) codmp{i}.select();
else if (event.keyCode == 40) cantidad{next}.select();" size="10" maxlength="10">
        <input name="precio{i}" type="hidden" id="precio{i}"></td>
      <td class="tabla"><input name="total{i}" type="text" class="rnombre" id="total{i}" size="10" maxlength="10" readonly="true"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="3" class="rtabla">Total del traspaso </th>
      <th class="tabla"><input name="gran_total" type="text" disabled="true" class="rnombre" id="gran_total" size="10" maxlength="10" onChange="total_mov.value=this.value"></th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="history.back()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			return false;
	}
	
	function actualiza_mp(codmp, nombre, _precio, _existencia, cantidad, total, gran_total, temp_codmp, temp_precio, temp_cantidad) {
		var value_total = 0;
		var value_gran_total = !isNaN(parseFloat(gran_total.value))?parseFloat(gran_total.value):0;
		var value_temp_cantidad = !isNaN(parseFloat(temp_cantidad.value))?parseFloat(temp_cantidad.value):0;
		var value_temp_precio = !isNaN(parseFloat(temp_precio.value))?parseFloat(temp_precio.value):0;
		
		mp = new Array();// Materias primas
		precio = new Array();
		existencia = new Array();
		<!-- START BLOCK : mp -->
		mp[{codmp}] = '{nombre}';
		precio[{codmp}] = {precio};
		existencia[{codmp}] = {existencia};
		<!-- END BLOCK : mp -->
				
		if (codmp.value > 0) {
			if (mp[codmp.value] == null) {
				alert("Código "+codmp.value+" no esta en el inventario de la compañía");
				codmp.value = temp_codmp.value;
				codmp.focus();
				return false;
			}
			else {
				nombre.value = mp[codmp.value];
				_precio.value = precio[codmp.value];
				_existencia.value = existencia[codmp.value];
				
				//alert(mp[codmp.value]+" "+precio[codmp.value]+" "+existencia[codmp.value]);
				//alert(nombre.value+" "+precio.value+" "+existencia.value);
				
				if (value_temp_cantidad > 0)
					value_gran_total -= value_temp_cantidad * value_temp_precio;
				
				cantidad.value = "";
				total.value = "";
				gran_total.value = value_gran_total.toFixed(2);
				return;
			}
		}
		else if (codmp.value == "") {
			codmp.value = "";
			nombre.value  = "";
			_precio.value = "";
			_existencia.value = "";
			
			if (value_temp_cantidad > 0)
				value_gran_total -= value_temp_cantidad * value_temp_precio;
			
			cantidad.value = "";
			total.value = "";
			gran_total.value = value_gran_total.toFixed(2);
			return false;
		}
	}
	
	function total(codmp, cantidad, precio, existencia, total, gran_total, temp_cantidad, temp_precio) {
		var value_codmp = !isNaN(parseInt(codmp.value))?parseInt(codmp.value):0;
		var value_cantidad = !isNaN(parseFloat(cantidad.value))?parseFloat(cantidad.value):0;
		var value_precio = !isNaN(parseFloat(precio.value))?parseFloat(precio.value):0;
		var value_existencia = !isNaN(parseFloat(existencia.value))?parseFloat(existencia.value):0;
		var value_total = 0;
		var value_gran_total = !isNaN(parseFloat(gran_total.value))?parseFloat(gran_total.value):0;
		var value_temp_cantidad = !isNaN(parseFloat(temp_cantidad.value))?parseFloat(temp_cantidad.value):0;
		var value_temp_precio = !isNaN(parseFloat(temp_precio.value))?parseFloat(temp_precio.value):0;
		
		if (value_codmp > 0) {
			if (value_cantidad > 0) {
				if (value_cantidad <= value_existencia) {
					if (value_temp_cantidad > 0)
						value_gran_total -= value_temp_cantidad * value_temp_precio;
					
					value_total = value_cantidad * value_precio;
					value_gran_total += value_total;
					
					total.value = value_total.toFixed(2);
					gran_total.value = value_gran_total.toFixed(2);
					return;
				}
				else {
					alert("No puede traspasar mas avio del que tiene la compañía");
					cantidad.value = temp_cantidad.value;
					cantidad.select();
					return false;
				}
			}
			else {
				if (value_temp_cantidad > 0)
					value_gran_total -= value_temp_cantidad * value_temp_precio;
				
				total.value = "";
				gran_total.value = value_gran_total.toFixed(2);
			}
		}
		else {
			alert("Debe especificar el código de materia prima");
			codmp.select();
			return false;
		}
	}
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td class="print_encabezado" align="right">Folio: {folio} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Traspaso de Avio de la Compa&ntilde;&iacute;a {num_cia1} a la {num_cia2} <br>
      el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th colspan="2" class="print" scope="col">C&oacute;digo y Nombre de la Materia Prima </th>
      <th class="print" scope="col">Precio</th>
      <th class="print" scope="col">Cantidad</th>
      <th class="print" scope="col">Total</th>
    </tr>
    <!-- START BLOCK : fila_mp -->
	<tr>
      <td class="print">{codmp}</td>
      <td class="vprint">{nombre}</td>
      <td class="rprint">{precio}</td>
      <td class="rprint">{cantidad}</td>
      <td class="rprint">{total}</td>
    </tr>
	<!-- END BLOCK : fila_mp -->
    <tr>
      <th colspan="4" class="rprint">Total del traspaso </th>
      <th class="rprint_total">{total}</th>
    </tr>
  </table>

  <p>
    <input type="button" class="boton noPrint" value="Regresar" onClick="document.location='./pan_avi_tra.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
