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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de cheques</p>
<form name="form" method="get">
<input type="hidden" name="temp">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.folio.select();
else if (event.keyCode == 38) form.fecha_cancelacion.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha_cancelacion.select();
else if (event.keyCode == 38) form.num_cia.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de cancelaci&oacute;n <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha_cancelacion" type="text" class="insert" id="fecha_cancelacion" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_cia.select();
else if (event.keyCode == 38) form.folio.select();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">&iquest;Regresar facturas a pasivo?</th>
      <td class="vtabla"><input name="return" type="checkbox" id="return" value="1" checked>
        Si</td>
    </tr>
  </table>  
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.folio.value <= 0) {
			alert("Debe especificar ewl folio del cheque a cancelar");
			document.form.folio.select();
			return false;
		}
		else if (document.form.fecha_cancelacion.value == "") {
			alert("Debe especificar la fecha de cancelación del cheque");
			document.form.fecha_cancelacion.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : info -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Informaci&oacute;n del cheque a cancelar </p>
<form name="form" method="post" action="./ban_che_can.php">
<input name="num_cia" type="hidden" value="{num_cia}">
<input name="folio" type="hidden" value="{folio}">
<input name="importe" type="hidden" value="{importe}">
<input name="fecha_cancelacion" type="hidden" value="{fecha_cancelacion}">
<input name="return" type="hidden" id="return" value="{return}">  
<table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">N&uacute;mero de compa&ntilde;&iacute;a y nombre </th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Beneficiario</th>
      <th class="tabla" scope="col">Facturas</th>
    </tr>
	<tr>
      <td class="tabla"><strong>{num_cia}</strong></td>
      <td class="vtabla"><strong>{nombre_cia}</strong></td>
      <td class="tabla"><strong>{cuenta}</strong></td>
      <td class="rtabla"><strong>{fimporte}</strong></td>
      <td class="tabla"><strong>{folio}</strong></td>
      <td class="vtabla"><strong>{a_nombre}</strong></td>
      <td class="vtabla"><strong>{facturas}</strong></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './ban_che_can.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar cheque" onClick="cancelar_cheque()"> 
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function cancelar_cheque() {
		if (confirm("¿Esta seguro de cancelar el cheque?"))
			document.form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : info -->
</body>
</html>
