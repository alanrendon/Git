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
<td align="center" valign="middle"><p class="title">Detalle de Camioneta</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Camioneta </th>
      <td class="vtabla">{id}</td>
      <th class="vtabla" scope="row">Entidad</th>
      <td class="vtabla">{entidad}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Modelo</th>
      <td class="vtabla">{modelo}</td>
      <th class="vtabla">Estatus</th>
      <td class="vtabla">{estatus}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla">{anio}</td>
      <th class="vtabla">Fecha de Venta </th>
      <td class="vtabla">{fecha_venta}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Placas</th>
      <td bgcolor="#{color}" class="tabla"><strong>{placas}</strong></td>
      <th class="vtabla">Clave Vehicular </th>
      <td class="vtabla">{clave_vehicular}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Panader&iacute;a</th>
      <td class="vtabla">{num_cia} - {nombre_cia}</td>
      <th class="vtabla">N&uacute;mero de P&oacute;liza</th>
      <td class="vtabla">{num_poliza}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Propietario</th>
      <td class="vtabla">{propietario}</td>
      <th class="vtabla">Inciso</th>
      <td class="vtabla">{inciso}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Usuario</th>
      <td class="vtabla">{usuario}</td>
      <th class="vtabla">Plan</th>
      <td class="vtabla">{plan}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Serie </th>
      <td class="vtabla">{num_serie}</td>
      <th class="vtabla">Localizaci&oacute;n de Factura </th>
      <td class="vtabla">{localizacion_fac}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Motor</th>
      <td class="vtabla">{num_motor}</td>
      <th class="vtabla">Vencimiento</th>
      <td class="vtabla">{vencimiento}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Combustible </th>
      <td class="vtabla">{tipo_combustible}</td>
      <th class="vtabla">Cambio de Motor </th>
      <td class="vtabla">{cambio_motor}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de veh&iacute;culo </th>
      <td class="vtabla">{tipo_unidad}</td>
      <th class="vtabla">Fecha de expedici&oacute;n tarjeta de circulaci&oacute;n</th>
      <td class="vtabla">{fecha_tarjeta_circulacion}</td>
    </tr>
  </table>  
  <br />
  <!-- START BLOCK : doc -->
  <table class="tabla">
	<tr>
      <th class="tabla" scope="col" colspan="{colspan}">{tipo_doc}</th>
    </tr>
    <!-- START BLOCK : img -->
	<tr>
      <td class="tabla"><a href="./img_camioneta.php?id={id_img}&width=965" target="_blank"><img src="./img_camioneta.php?id={id_img}&width=180"><br>Hoja {indice}</a></td>
    </tr>
	<!-- END BLOCK : img -->
  </table>
  <br>
  <!-- END BLOCK : doc -->
  <p class="title">
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  </p></td>
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
