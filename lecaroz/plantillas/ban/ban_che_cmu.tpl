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
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n M&uacute;ltiple de Cheques </p>
  <form action="./ban_che_cmu.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha de cancelaci&oacute;n</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">&iquest;Regresar facturas a pasivo?</th>
      <td class="vtabla"><input name="pasivo" type="checkbox" id="pasivo" value="1">
        Si</td>
    </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Folio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) folio[{i}].select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30"></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" value="{folio}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="this.form.submit()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		cia = new Array();
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

	window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n M&uacute;ltiple de Cheques </p>
  <form action="./ban_che_cmu.php" method="post" name="form">
  <input name="fecha_cancelacion" type="hidden" value="{fecha_cancelacion}">
  <input name="pasivo" type="hidden" value="{pasivo}">
  <input name="cuenta" type="hidden" value="{cuenta}">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Beneficiario</th>
      <th class="tabla" scope="col">Facturas</th>
      <th class="tabla" scope="col">Estatus</th>
    </tr>
    <!-- START BLOCK : cheque -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla">{cuenta}</td>
      <td class="tabla"><input name="importe[]" type="hidden" id="importe" value="{importe}">
        {fimporte}</td>
      <td class="tabla"><input name="folio[]" type="hidden" id="folio" value="{folio}">
        {folio}</td>
      <td class="tabla"><input name="fecha[]" type="hidden" id="fecha" value="{fecha}">
        {fecha}</td>
      <td class="vtabla">{beneficiario}</td>
      <td class="vtabla">{facturas}</td>
      <td class="vtabla">{estatus}</td>
	</tr>
	<!-- END BLOCK : cheque -->
	<!-- START BLOCK : no_cheque -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="rtabla">{num_cia}</td>
	  <td class="vtabla">{nombre_cia}</td>
	  <td class="tabla">{cuenta}</td>
	  <td class="tabla">{fimporte}</td>
    <td class="tabla">{folio}</td>
	  <td class="tabla">{fecha}</td>
	  <td class="vtabla">{beneficiario}</td>
	  <td class="vtabla">{facturas}</td>
	  <td class="vtabla">{estatus}</td>
	  </tr>
	  <!-- END BLOCK : no_cheque -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './ban_che_cmu.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar Cheques" onClick="validar(this.form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (!form.num_cia) {
			alert('No hay cheques que puedan ser cancelados porque estan conciliados o ya cancelados');
			return false;
		}
		else if (confirm("¿En realidad desea cancelar los cheques?"))
			form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
