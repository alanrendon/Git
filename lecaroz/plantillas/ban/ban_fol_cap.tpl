<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura y Reserva de Folios de Cheques</p>
<form name="form" method="get" action="./ban_fol_cap.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="5" maxlength="3"></td>
  </tr>
</table>
<p>
  <input name="enviar" type="submit" class="boton" id="enviar" value="Capturar">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : inicial -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.folio.value < 0) {
			alert("Debe especificar el folio inicial");
			document.form.folio.select();
			return false;
		}
		else {
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura y Reserva de Folios de Cheques </p>
<form name="form" method="post" action="./ban_fol_cap.php?mov=alta">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="hidden" value="{num_cia}">{num_cia} - {nombre_cia}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Folio Inicial </th>
    <td class="vtabla"><input name="folio" type="text" class="vinsert" id="folio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="7" maxlength="7" onKeyDown="if (event.keyCode == 13) form.enviar.focus();"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Banco</th>
    <td class="vtabla"><select name="cuenta" id="cuenta" class="insert">
      <option value="1">BANORTE</option>
      <option value="2" selected>SANTANDER</option>
    </select></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
window.onload = document.form.folio.select();
</script>
<!-- END BLOCK : inicial -->

<!-- START BLOCK : reservar -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_folios.value <= 0) {
			alert("Debe especificar el folio inicial");
			document.form.num_folios.select();
			return false;
		}
		else {
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura y Reserva de Folios de Cheques </p>
<form name="form" method="post" action="./ban_fol_cap.php?mov=reserva">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="hidden" value="{num_cia}">{num_cia} - {nombre_cia}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Ultimo folio </th>
    <td class="vtabla"><input name="ultimo_folio" type="hidden" value="{ultimo_folio}">
      {ultimo_folio}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">N&uacute;mero de folios a reservar</th>
    <td class="vtabla"><input name="num_folios" type="text" class="vinsert" id="num_folios" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.enviar.focus();" size="5" maxlength="3"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Cuenta</th>
    <td class="vtabla"><select name="cuenta" id="cuenta" class="insert">
      <option value="1">BANORTE</option>
      <option value="2" selected>SANTANDER</option>
    </select></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : reservar -->
