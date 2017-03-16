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
<td align="center" valign="middle"><p class="title">Cheques Guardados por Folio</p>
  <form action="./ban_che_res.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) next.focus()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cheques Guardados por Folio </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    </tr>
    <tr>
      <th class="tabla">{num_cia} - {nombre_cia} </th>
    </tr>
  </table>  
  <br>
  <form action="./ban_che_res.php" method="post" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">C&oacute;digo de Gasto </th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><select name="folio[]" class="insert" id="folio" onChange="validarFolio(this,{i})">
        <option value="" selected>--------</option>
        <!-- START BLOCK : folio -->
		<option value="{folio}">{folio}</option>
		<!-- END BLOCK : folio -->
      </select></td>
      <td class="tabla"><input name="num_proveedor[]" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaProveedor(this,nombre_proveedor[{i}])" onKeyDown="if (event.keyCode == 13) fecha[{i}].select()" size="4" maxlength="4">        
        <input name="nombre_proveedor[]" type="text" class="vnombre" id="nombre_proveedor" size="30" readonly="true"></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) codgastos[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaGasto(this,nombre_gasto[{i}])" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" size="4" maxlength="4">
        <input name="nombre_gasto[]" type="text" disabled="true" class="vnombre" id="nombre_gasto" size="20"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) num_proveedor[{next}].select()" size="30" maxlength="200"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="./ban_che_res.php">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var proveedor = new Array();
	var gasto = new Array();
	
	<!-- START BLOCK : proveedor -->
	proveedor[{num_proveedor}] = "{nombre_proveedor}";
	<!-- END BLOCK : proveedor -->
	<!-- START BLOCK : gasto -->
	gasto[{codgastos}] = "{nombre_gasto}";
	<!-- END BLOCK : gasto -->
	
	function cambiaProveedor(num, nombre) {
		if (num.value == "")
			nombre.value = "";
		else if (proveedor[num.value] != null)
			nombre.value = proveedor[num.value];
		else {
			alert("El proveedor no se encuentra en el catalogo");
			num.value = num.form.temp.value;
			num.select();
		}
	}
	
	function cambiaGasto(cod, nombre) {
		if (cod.value == "")
			nombre.value = "";
		else if (gasto[cod.value] != null)
			nombre.value = gasto[cod.value];
		else {
			alert("El gasto no se encuentra en el catalogo");
			cod.value = cod.form.temp.value;
			cod.select();
		}
	}
	
	function validarFolio(folio, index) {
		// Verificar que el folio no ha sido seleccionado para otro cheque
		if (folio.value != "") {
			// Recorrer todos los select y revisar que no se haya seleccionado
			for (i = 0; i < folio.form.folio.length; i++)
				if (i != index && folio.value == folio.form.folio[i].value) {
					folio.selectedIndex = 0;
					alert("El folio seleccionado ya se encuentra en uso");
					return false;
				}
		}
	}
	
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_proveedor[0].select();
	}
	
	window.onload = document.form.num_proveedor[0].select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
