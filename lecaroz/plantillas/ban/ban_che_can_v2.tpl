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
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Cheques</p>
  <form action="./ban_che_can_v2.php" method="get" name="form">
  <input name="pas" type="hidden" id="pas" value="1">
<input type="hidden" name="temp">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) folio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha_cancelacion.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de cancelaci&oacute;n <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha_cancelacion" type="text" class="insert" id="fecha_cancelacion" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <!-- START BLOCK : options -->
	<!-- <tr>
      <th class="vtabla">&iquest;Regresar facturas a pasivo?</th>
      <td class="vtabla"><input name="pas" type="checkbox" id="pas" value="1" checked>
        Si</td>
    </tr> -->
    <tr>
      <th class="vtabla">&iquest;Reimprimir cheque?</th>
      <td class="vtabla"><input name="reim" type="checkbox" id="reim" value="1" onClick="if (this.checked) pas.disabled=true; else pas.disabled=false;">
        Si</td>
    </tr>
	<!-- END BLOCK : options -->
  </table>
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="validar(this.form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.folio.value <= 0) {
			alert("Debe especificar el folio");
			form.folio.select();
			return false;
		}
		else if (form.fecha_cancelacion.value.length < 8) {
			alert("Debe especificar la fecha de cancelación");
			form.fecha_cancelacion.select();
			return false;
		}
		else
			form.submit();
	}

	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : info -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Cheques</p>
  <form action="./ban_che_can_v2.php" method="post" name="form">
  <input name="id" type="hidden" value="{id}">
  <input name="fecha_cancelacion" type="hidden" id="fecha_cancelacion" value="{fecha_cancelacion}">
  <input name="pas" type="hidden" id="pas" value="{pas}">
  <input name="dev" type="hidden" value="{dev}">
  <input name="reim" type="hidden" value="{reim}">
  <input name="inv" type="hidden" id="inv" value="{inv}">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Folio Cheque </th>
      <th class="tabla" scope="col">Beneficiario</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <td class="tabla">{num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla">{cuenta}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{folio}</td>
      <td class="tabla">{num_cheque}</td>
      <td class="vtabla">{a_nombre}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
    </tr>
  </table>
  {mensaje}
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_che_can_v2.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar Cheque" onClick="validar(this.form)" {disabled}>
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (confirm("¿Esta seguro de que desea cancelar el cheque?"))
			form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : info -->
</body>
</html>
