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
<td align="center" valign="middle"><p class="title">Alta de Camionetas</p>
  <form action="./doc_cam_altas.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Camioneta </th>
      <td class="vtabla"><input name="id" type="text" class="insert" id="id" value="{id}" size="5" maxlength="5" readonly></td>
      <th class="vtabla" scope="row">Entidad</th>
      <td class="vtabla"><select name="entidad" class="insert" id="entidad">
        <option value="1" selected>DISTRITO FEDERAL</option>
        <option value="2">ESTADO DE MEXICO</option>
		<option value="3">MORELOS</option>
        <option value="4">OTRO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Modelo</th>
      <td class="vtabla"><input name="modelo" type="text" class="vinsert" id="modelo" onKeyDown="if (event.keyCode == 13) anio.select()" size="30" maxlength="150"></td>
      <th class="vtabla">Estatus</th>
      <td class="vtabla"><select name="estatus" class="insert" onChange="if (this.value == '1')
fecha_venta.disabled = true;
else {
fecha_venta.disabled = false;
fecha_venta.select();
}">
        <option value="1" selected>EN USO</option>
        <option value="2">VENDIDA</option>
		<option value="3">ROBADA</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this)" onKeyDown="if (event.keyCode == 13) placas.select()" size="4" maxlength="4"></td>
      <th class="vtabla">Fecha de Venta </th>
      <td class="vtabla"><input name="fecha_venta" type="text" class="insert" id="fecha_venta" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) clave_vehicular.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Placas</th>
      <td class="vtabla"><input name="placas" type="text" class="vinsert" id="placas" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="12" maxlength="12"></td>
      <th class="vtabla">Clave Vehicular </th>
      <td class="vtabla"><input name="clave_vehicular" type="text" class="vinsert" id="clave_vehicular" onKeyDown="if (event.keyCode == 13) num_poliza.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Panader&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) propietario.select()" size="3" maxlength="3">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="25"></td>
	  <th class="vtabla">N&uacute;mero de P&oacute;liza</th>
      <td class="vtabla"><input name="num_poliza" type="text" class="vinsert" id="num_poliza" onKeyDown="if (event.keyCode == 13) ven_poliza.select()" size="15" maxlength="15"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Propietario</th>
      <td class="vtabla"><input name="propietario" type="text" class="vinsert" id="propietario" onKeyDown="if (event.keyCode == 13) usuario.select()" size="30" maxlength="150"></td>
      <th class="vtabla">Vencimiento de la P&oacute;liza </th>
      <td class="vtabla"><input name="ven_poliza" type="text" class="rinsert" id="ven_poliza" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) inciso.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Usuario</th>
      <td class="vtabla"><input name="usuario" type="text" class="vinsert" id="usuario" onKeyDown="if (event.keyCode == 13) num_serie.select()" size="30" maxlength="150"></td>
	  <th class="vtabla">Inciso</th>
      <td class="vtabla"><input name="inciso" type="text" class="vinsert" id="inciso" onKeyDown="if (event.keyCode == 13) localizacion_fac.select()" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Serie </th>
      <td class="vtabla"><input name="num_serie" type="text" class="vinsert" id="num_serie" onKeyDown="if (event.keyCode == 13) num_motor.select()" size="20" maxlength="30"></td>
	  <th class="vtabla">Plan</th>
      <td class="vtabla"><select name="plan" class="insert" id="plan">
        <option value="1" selected>TERCEROS</option>
        <option value="2">TODO RIESGO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Motor</th>
      <td class="vtabla"><input name="num_motor" type="text" class="vinsert" id="num_motor" onKeyDown="if (event.keyCode == 13) fecha_tarjeta_circulacion.select()" size="20" maxlength="20"></td>
      <th class="vtabla">Localizaci&oacute;n de Factura </th>
      <td class="vtabla"><input name="localizacion_fac" type="text" class="vinsert" id="localizacion_fac" onKeyDown="if (event.keyCode == 13) vencimiento.select()" size="15" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Combustible </th>
      <td class="vtabla"><select name="tipo_combustible" class="insert" id="tipo_combustible">
        <option value="TRUE" selected>GASOLINA</option>
        <option value="FALSE">GAS</option>
      </select></td>
	  <th class="vtabla">Vencimiento</th>
      <td class="vtabla"><input name="vencimiento" type="text" class="insert" id="vencimiento" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) modelo.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Cambio de Motor </th>
      <td class="vtabla"><select name="cambio_motor" class="insert" id="cambio_motor">
        <option value="TRUE">SI</option>
        <option value="FALSE" selected>NO</option>
      </select></td>
	  <th class="vtabla" scope="row">Tipo de veh&iacute;culo </th>
      <td class="vtabla"><select name="tipo_unidad" class="insert" id="tipo_unidad">
        <option value="1" selected>CARGA</option>
        <option value="2">PERSONAL</option>
		<option value="3">PARTICULAR</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de expedici&oacute;n tarjeta de circulaci&oacute;n</th>
      <td class="vtabla"><input name="fecha_tarjeta_circulacion" type="text" class="insert" id="fecha_tarjeta_circulacion" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha_venta.select()" size="10" maxlength="10"></td>
      <th class="vtabla" scope="row">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Alta" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var cia = new Array();
	<!-- START BLOCK : cia -->
	cia[{num_cia}] = "{nombre_cia}";
	<!-- END BLOCK : cia -->
	
	function cambiaCia(num, nombre) {
		if (num.value == "")
			nombre.value = "";
		else if (cia[num.value] != null)
			nombre.value = cia[num.value];
		else {
			alert("La compañía no se encuentra en el catálogo");
			num.value = num.form.temp.value;
			num.select();
		}
	}
	
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.modelo.select();
	}
	
	window.onload = document.form.modelo.select();
</script>
</body>
</html>
