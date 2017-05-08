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
<td align="center" valign="middle"><p class="title">Captura de Cheques de N&oacute;mina y Gastos </p>
  <form action="./ban_che_mul.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo de Gasto </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Cuenta</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onFocus="temp.value=this.value" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) codgastos.select();
else if (event.keyCode == 40) num_cia0.select();" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_gasto(this,nombre_gasto)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) concepto.select();
else if (event.keyCode == 37) fecha.select();
else if (event.keyCode == 40) num_cia0.select();" size="4" maxlength="4">
        <input name="nombre_gasto" type="text" disabled="true" class="vnombre" id="nombre_gasto" size="30"></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia0.select();
else if (event.keyCode == 37) codgastos.select();" size="50" maxlength="50"></td>
      <td class="tabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia{i},num_proveedor{i},nombre_proveedor{i},importe{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_proveedor{i}.select();
else if (event.keyCode == 38) num_cia{back}.select();
else if (event.keyCode == 40) num_cia{next}.select();" size="3" maxlength="3">          
        <input name="nombre_cia{i}" type="text" disabled="true" class="vnombre" id="nombre_cia{i}" size="40"></td><td class="tabla"><input name="num_proveedor{i}" type="text" class="insert" id="num_proveedor{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_proveedor(this,nombre_proveedor{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) importe{i}.select();
else if (event.keyCode == 37) num_cia{i}.select();
else if (event.keyCode == 38) num_proveedor{back}.select();
else if (event.keyCode == 40) num_proveedor{next}.select();" size="4" maxlength="4">
        <input name="nombre_proveedor{i}" type="text" class="vnombre" id="nombre_proveedor{i}" size="40" readonly="true"></td>
      <td class="tabla"><input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="temp.value=this.value" onChange="if ((num_cia{i}.value > 0 && num_proveedor{i}.value > 0) || this.value == '') isFloat(this,2,temp);
else {this.value=''; alert('Debe especificar la compañía y el proveedor'); num_cia{i}.select();}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia{next}.select();
else if (event.keyCode == 37) num_proveedor{i}.select();
else if (event.keyCode == 38) importe{back}.select();
else if (event.keyCode == 40) importe{next}.select();" size="10" maxlength="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	// Validar y actualizar número y nombre de compañía
	function actualiza_cia(num_cia, nombre, num_proveedor, nombre_proveedor, importe) {
		// Arreglo con los nombres de las compañías
		cia = new Array();
		num_pro = new Array();
		nombre_pro = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		num_pro[{num_cia}] = {num_pro};
		nombre_pro[{num_cia}] = '{nombre_pro}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_proveedor.value = "";
				nombre_proveedor.value = "";
				importe.value = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				num_proveedor.value = num_pro[parseInt(num_cia.value)];
				nombre_proveedor.value = nombre_pro[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			num_proveedor.value = "";
			nombre_proveedor.value = "";
			importe.value = "";
			
			return false;
		}
	}
	
	// Validar y actualizar número y nombre del proveedor
	function actualiza_proveedor(num_pro, nombre) {
		// Arreglo con los nombres de los proveedores
		pro = new Array();
		<!-- START BLOCK : nombre_pro -->
		pro[{num_pro}] = '{nombre_pro}';
		<!-- END BLOCK : nombre_pro -->
		
		if (parseInt(num_pro.value) > 0) {
			if (pro[parseInt(num_pro.value)] == null) {
				alert("Proveedor "+parseInt(num_pro.value)+" no esta en el catálogo de proveedores");
				num_pro.value = "";
				nombre.value  = "";
				num_pro.focus();
				return false;
			}
			else {
				num_pro.value = parseFloat(num_pro.value);
				nombre.value  = pro[parseInt(num_pro.value)];
				return;
			}
		}
		else if (num_pro.value == "") {
			num_pro.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	// Validar y actualizar código de gasto
	function actualiza_gasto(codgasto, nombre) {
		// Arreglo con los nombres de los gastos
		gas = new Array();
		<!-- START BLOCK : nombre_gasto -->
		gas[{codgasto}] = '{nombre_gasto}';
		<!-- END BLOCK : nombre_gasto -->
		
		if (parseInt(codgasto.value) > 0) {
			if (gas[parseInt(codgasto.value)] == null) {
				alert("Código "+parseInt(codgasto.value)+" no esta en el catálogo de gastos");
				codgasto.value = "";
				nombre.value  = "";
				codgasto.focus();
				return false;
			}
			else {
				codgasto.value = parseFloat(codgasto.value);
				nombre.value  = gas[parseInt(codgasto.value)];
				return;
			}
		}
		else if (codgasto.value == "") {
			codgasto.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.codgastos.value <= 0) {
			alert("Debe especificar el código de gasto");
			form.cod_gastos.select();
			return false;
		}
		else if (form.concepto.value == "") {
			alert("Debe especificar el concepto");
			form.concepto.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.fecha.select();
	}
	
	window.onload = document.form.fecha.select();
</script>
</body>
</html>
