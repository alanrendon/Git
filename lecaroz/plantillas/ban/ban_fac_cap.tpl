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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Facturas de Clientes</p>
  <form action="./ban_fac_cap.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">ID del Cliente </th>
    <th class="tabla" scope="col">Folio</th>
    <th class="tabla" scope="col">Tama&ntilde;o</th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha.select();
else if (event.keyCode == 37) idcliente.select();
else if (event.keyCode == 40) cantidad0.select();" size="3" maxlength="3">
      <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="50" maxlength="50"></td>
    <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) idcliente.select();
else if (event.keyCode == 37) num_cia.select();
else if (event.keyCode == 40) cantidad0.select();" value="{fecha}" size="10" maxlength="10"></td>
    <td class="tabla"><input name="idcliente" type="text" class="insert" id="idcliente2" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cliente(this,nombrecliente)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) folio.select();
else if (event.keyCode == 37) fecha.select();
else if (event.keyCode == 40) cantidad0.select();" value="{idcliente}" size="5" maxlength="5">
      <input name="nombrecliente" type="text" disabled="true" class="vnombre" id="nombrecliente" size="50"></td>
    <td class="tabla"><input name="folio" type="text" class="insert" id="folio" onKeyDown="if (event.keyCode == 13) cantidad0.select();" size="12" maxlength="12"></td>
    <td class="tabla"><input name="tamano" type="radio" value="carta" checked>
      Carta&nbsp;&nbsp;
      <input name="tamano" type="radio" value="oficio">
      Oficio</td>
  </tr>
</table>

  <br>
  <table class="tabla">
    <tr>
      <th height="27" class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">Precio Unidad </th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
	  <td class="tabla"><input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcula_importe(this,precio_unidad{i},importe{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) descripcion{i}.select();
else if (event.keyCode == 37) importe{i}.select();
else if (event.keyCode == 38) cantidad{back}.select();
else if (event.keyCode == 40) cantidad{next}.select();" size="5" maxlength="5"></td>
      <td class="tabla"><input name="descripcion{i}" type="text" class="vinsert" id="descripcion{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) precio_unidad{i}.select();
else if (event.keyCode == 37) cantidad{i}.select();
else if (event.keyCode == 38) descripcion{back}.select();
else if (event.keyCode == 40) descripcion{next}.select();" value="{descripcion}" size="50" maxlength="100"></td>
      <td class="tabla"><input name="precio_unidad{i}" type="text" class="rinsert" id="precio_unidad{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,4,temp)) calcula_importe(cantidad{i},this,importe{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) importe{i}.select();
else if (event.keyCode == 37) descripcion{i}.select();
else if (event.keyCode == 38) precio_unidad{back}.select();
else if (event.keyCode == 40) precio_unidad{next}.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cantidad{next}.select();
else if (event.keyCode == 37) precio_unidad{i}.select();
else if (event.keyCode == 38) importe{back}.select();
else if (event.keyCode == 40) importe{next}.select();" value="{importe}" size="10" maxlength="10"></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function calcula_importe(cantidad,precio,importe) {
		var cantidad_value = !isNaN(parseFloat(cantidad.value))?parseFloat(cantidad.value):0;
		var precio_value = !isNaN(parseFloat(precio.value))?parseFloat(precio.value):0;
		var importe_value = 0;
		
		if (cantidad_value > 0 && precio_value > 0) {
			importe_value = cantidad_value * precio_value;
			importe.value = importe_value.toFixed(2);
		}
	}
	
	function actualiza_compania(num_cia, nombre) {
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
	
	function actualiza_cliente(id, nombre) {
		// Arreglo con los nombres de las materias primas
		cliente = new Array();				// Materias primas
		<!-- START BLOCK : cliente -->
		cliente[{idcliente}] = '{cliente}';
		<!-- END BLOCK : cliente -->
		
		if (parseInt(id.value) > 0) {
			if (cliente[parseInt(id.value)] == null) {
				alert("Cliente no. "+parseInt(id.value)+" no esta en el catálogo de clientes");
				id.value = "";
				nombre.value  = "";
				id.focus();
				return false;
			}
			else {
				id.value = parseFloat(id.value);
				nombre.value  = cliente[parseInt(id.value)];
				return;
			}
		}
		else if (id.value == "") {
			id.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar el cliente");
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
			alert("Debe especificar la fecha de la factura");
			form.fecha.select();
			return false;
		}
		else if (form.idcliente.value <= 0) {
			alert("Debe especificar el ID del cliente");
			form.idcliente.select();
			return false;
		}
		else if (form.folio.value <= 0) {
			alert("Debe especificar el folio de la factura");
			form.folio.select();
			return false;
		}
		/*else if (form.importe.value <= 0) {
			alert("Debe especificar el importe de la factura");
			form.importe.select();
			return false;
		}*/
		else
			if (confirm("¿Son correctos los datos?")) {
				window.open("","facturas","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
				form.target = "facturas";
				form.submit();
			}
			else
				form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : captura -->

</body>
</html>
