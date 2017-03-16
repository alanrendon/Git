<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Autom&aacute;tico a Proveedores</p>
<form name="form" method="post" action="./ban_pau_pro.php?prelistado=1">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla"><input name="rango" type="radio" value="todas" checked>
      Todas&nbsp;&nbsp;
      <input name="rango" type="radio" value="panaderias">
      Panader&iacute;as&nbsp;&nbsp;
      <input name="rango" type="radio" value="rosticerias">
      Rosticer&iacute;as</th>
      </tr>
    <tr>
      <th class="vtabla">Fecha de corte <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla">
        <input name="fecha_corte" type="text" class="insert" id="fecha_corte" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dias_deposito.select();
else if (event.keyCode == 38) form.obligado29.select();" value="{fecha_corte}" size="10" maxlength="10">
      </td>
      </tr>
    <tr>
      <th class="vtabla">D&iacute;as de dep&oacute;sito</th>
      <td class="vtabla"><input name="dias_deposito" type="text" class="insert" id="dias_deposito" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.sin_pago0.select();
else if (event.keyCode == 38) form.fecha_corte.select();" value="1" size="5" maxlength="5"></td>
      </tr>
    <tr>
      <th colspan="2" class="tabla"><input name="criterio" type="radio" value="antiguedad" checked>
        Por antig&uuml;edad&nbsp;&nbsp;
        <input name="criterio" type="radio" value="prioridad">
        Por prioridad  </th>
      </tr>
  </table>  
  <br>  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedores sin pago </th>
      <td class="vtabla"><input name="sin_pago0" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago1.select();
else if (event.keyCode == 37) form.sin_pago9.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago1" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago2.select();
else if (event.keyCode == 37) form.sin_pago0.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago2" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago3.select();
else if (event.keyCode == 37) form.sin_pago1.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago3" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago4.select();
else if (event.keyCode == 37) form.sin_pago2.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago4" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago5.select();
else if (event.keyCode == 37) form.sin_pago3.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago5" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago6.select();
else if (event.keyCode == 37) form.sin_pago4.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago6" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago7.select();
else if (event.keyCode == 37) form.sin_pago5.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago7" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago8.select();
else if (event.keyCode == 37) form.sin_pago6.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago8" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago9.select();
else if (event.keyCode == 37) form.sin_pago7.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago9" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago0.select();
else if (event.keyCode == 37) form.sin_pago8.select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan0.select();" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as que no pagaran </th>
      <td class="vtabla"><input name="no_pagan0" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan1.select();
else if (event.keyCode == 37) form.no_pagan9.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan1" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan2.select();
else if (event.keyCode == 37) form.no_pagan0.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan2" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan3.select();
else if (event.keyCode == 37) form.no_pagan1.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan3" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan4.select();
else if (event.keyCode == 37) form.no_pagan2.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan4" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan5.select();
else if (event.keyCode == 37) form.no_pagan3.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan5" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan6.select();
else if (event.keyCode == 37) form.no_pagan4.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan6" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan7.select();
else if (event.keyCode == 37) form.no_pagan5.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan7" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan8.select();
else if (event.keyCode == 37) form.no_pagan6.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan8" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan9.select();
else if (event.keyCode == 37) form.no_pagan7.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan9" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan0.select();
else if (event.keyCode == 37) form.no_pagan8.select();
else if (event.keyCode == 38) form.sin_pago0.select();
else if (event.keyCode == 40) form.obligado0.select();" size="3" maxlength="3"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th rowspan="3" class="vtabla" scope="row">Pago obligado a proveedores </th>
      <td class="vtabla"><input name="obligado0" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado1.select();
else if (event.keyCode == 37) form.obligado29.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado10.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado1" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado2.select();
else if (event.keyCode == 37) form.obligado0.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado11.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado2" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado3.select();
else if (event.keyCode == 37) form.obligado1.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado12.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado3" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado4.select();
else if (event.keyCode == 37) form.obligado2.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado13.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado4" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado5.select();
else if (event.keyCode == 37) form.obligado3.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado14.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado5" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado6.select();
else if (event.keyCode == 37) form.obligado4.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado15.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado6" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado7.select();
else if (event.keyCode == 37) form.obligado5.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado16.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado7" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado8.select();
else if (event.keyCode == 37) form.obligado6.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado17.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado8" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado9.select();
else if (event.keyCode == 37) form.obligado7.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado18.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado9" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado10.select();
else if (event.keyCode == 37) form.obligado8.select();
else if (event.keyCode == 38) form.no_pagan0.select();
else if (event.keyCode == 40) form.obligado19.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="obligado10" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado11.select();
else if (event.keyCode == 37) form.obligado9.select();
else if (event.keyCode == 38) form.obligado0.select();
else if (event.keyCode == 40) form.obligado20.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado11" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado12.select();
else if (event.keyCode == 37) form.obligado10.select();
else if (event.keyCode == 38) form.obligado1.select();
else if (event.keyCode == 40) form.obligado21.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado12" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado13.select();
else if (event.keyCode == 37) form.obligado11.select();
else if (event.keyCode == 38) form.obligado2.select();
else if (event.keyCode == 40) form.obligado22.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado13" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado14.select();
else if (event.keyCode == 37) form.obligado12.select();
else if (event.keyCode == 38) form.obligado3.select();
else if (event.keyCode == 40) form.obligado23.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado14" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado15.select();
else if (event.keyCode == 37) form.obligado13.select();
else if (event.keyCode == 38) form.obligado4.select();
else if (event.keyCode == 40) form.obligado24.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado15" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado16.select();
else if (event.keyCode == 37) form.obligado14.select();
else if (event.keyCode == 38) form.obligado5.select();
else if (event.keyCode == 40) form.obligado25.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado16" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado17.select();
else if (event.keyCode == 37) form.obligado15.select();
else if (event.keyCode == 38) form.obligado6.select();
else if (event.keyCode == 40) form.obligado26.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado17" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado18.select();
else if (event.keyCode == 37) form.obligado16.select();
else if (event.keyCode == 38) form.obligado7.select();
else if (event.keyCode == 40) form.obligado27.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado18" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado19.select();
else if (event.keyCode == 37) form.obligado17.select();
else if (event.keyCode == 38) form.obligado8.select();
else if (event.keyCode == 40) form.obligado28.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado19" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado20.select();
else if (event.keyCode == 37) form.obligado18.select();
else if (event.keyCode == 38) form.obligado9.select();
else if (event.keyCode == 40) form.obligado29.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="obligado20" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado21.select();
else if (event.keyCode == 37) form.obligado19.select();
else if (event.keyCode == 38) form.obligado10.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado21" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado22.select();
else if (event.keyCode == 37) form.obligado20.select();
else if (event.keyCode == 38) form.obligado11.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado22" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado23.select();
else if (event.keyCode == 37) form.obligado21.select();
else if (event.keyCode == 38) form.obligado12.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado23" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado24.select();
else if (event.keyCode == 37) form.obligado22.select();
else if (event.keyCode == 38) form.obligado13.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado24" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado25.select();
else if (event.keyCode == 37) form.obligado23.select();
else if (event.keyCode == 38) form.obligado14.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado25" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado26.select();
else if (event.keyCode == 37) form.obligado24.select();
else if (event.keyCode == 38) form.obligado15.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado26" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado27.select();
else if (event.keyCode == 37) form.obligado25.select();
else if (event.keyCode == 38) form.obligado16.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado27" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado28.select();
else if (event.keyCode == 37) form.obligado26.select();
else if (event.keyCode == 38) form.obligado17.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado28" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado29.select();
else if (event.keyCode == 37) form.obligado27.select();
else if (event.keyCode == 38) form.obligado18.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado29" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha_corte.select();
else if (event.keyCode == 37) form.obligado28.select();
else if (event.keyCode == 38) form.obligado19.select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.fecha_corte.value == "") {
			alert("Debe especificar la fecha de corte");
			document.form.fecha_pago.select();
			return false;
		}
		else if (document.form.dias_deposito.value == "") {
			alert("Debe especificar los días de depósito");
			document.form.dias_deposito.select();
			return false;
		}
		else  {
			if (confirm("¿Son correctos todos los datos?"))
				document.form.submit();
			else {
				document.form.fecha_pago.select();
				return false;
			}
		}
	}
	
	window.onload = document.form.fecha_corte.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : pre_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Autom&aacute;tico a Proveedores <br>
  Listado de Facturas por Pagar </p>
  <form action="./ban_pau_pro.php?generar=1" method="post" name="form">
  <input name="num_facturas" type="hidden" value="{num_facturas}">
  <table width="100%" class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th colspan="2" class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha de Pago </th>
      <th class="tabla" scope="col">N&uacute;m. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : bloque_cia -->
	<tr>
      <th colspan="3" class="vtabla">{num_cia} - {nombre_cia}</th>
      <th colspan="2" class="rtabla">Saldo para Pagar</th>
      <th colspan="2" class="rtabla">{saldo}</th>
      </tr>
    <!-- START BLOCK : fila_pre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}" checked></td>
      <td class="vtabla">{num_proveedor}</td>
      <td class="vtabla">{nombre_proveedor}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{num_fact}</td>
      <td class="vtabla">{concepto}</td>
      <th class="rtabla">{importe}</th>
    </tr>
	<!-- END BLOCK : fila_pre -->
    <tr>
      <th colspan="6" class="rtabla">Total</th>
      <th class="rtabla"><font size="+1">{total}</font></th>
    </tr>
    <tr>
      <td colspan="7">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : bloque_cia -->
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_pau_pro.php'">
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
</script>
<!-- END BLOCK : pre_listado -->

<!-- START BLOCK : saldos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos para pagar<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <p></p>
  <table class="print">
    <tr>
      <th class="print" scope="col">Cia.</th>
      <th class="print" scope="col">Num. de Cuenta</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Saldo Libros</th>
      <th class="print" scope="col">Prom. Dep.</th>
      <th class="print" scope="col">Saldo p/pago </th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="print">{cuenta}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{saldo_libros}</td>
      <td class="rprint">{promedio}</td>
      <td class="rprint">{saldo_pago}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr>
      <th colspan="3" class="rprint">Total</th>
      <th class="rprint_total">{total_saldo_libros}</th>
      <th class="rprint_total">{total_promedio}</th>
      <th class="rprint_total">{total_saldo_pago}</th>
    </tr>
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : saldos -->

<!-- START BLOCK : cheques -->
<table width="100%"  height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Datos para emisi&oacute;n de cheques<br>
      del {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <p></p>
  <table class="print" width="100%">
    <tr>
      <th width="5%" class="print" scope="col">Cia</th>
      <th width="10%" class="print" scope="col">Num. Cta. </th>
      <th width="25%" class="print" scope="col">Nombre</th>
      <th width="5%" class="print" scope="col">Lote</th>
      <th width="35%" colspan="3" class="print" scope="col">N&uacute;mero y nombre del proveedor </th>
      <th width="10%" class="print" scope="col">Factura</th>
      <th width="10%" class="print" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : cia -->
	<!-- START BLOCK : lote -->
	<!-- START BLOCK : nombre_cia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="print" rowspan="{rowspan}">{num_cia}</th>
	  <th class="print" rowspan="{rowspan}">{cuenta}</th>
      <th class="vprint" rowspan="{rowspan}">{nombre_cia}</th>
      <td class="print" rowspan="{rowspan_lote}"><strong>{lote}</strong></td>
      <td width="5%" class="print" rowspan="{rowspan_lote}"><strong>{num_proveedor}</strong></td>
      <td width="30%" colspan="2" class="vprint" rowspan="{rowspan_lote}"><font size="-3"><strong>{nombre_proveedor}</strong></font></td>
      <td class="print">{factura}</td>
      <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : nombre_cia -->
	<!-- START BLOCK : nombre_proveedor -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="print" rowspan="{rowspan_lote}"><strong>{lote}</strong></td>
	  <td class="print" rowspan="{rowspan_lote}"><strong>{num_proveedor}</strong></td>
	  <td colspan="2" class="vprint" rowspan="{rowspan_lote}"><font size="-3"><strong>{nombre_proveedor}</strong></font></td>
	  <td class="print">{factura}</td>
	  <td class="rprint">{importe}</td>
	  </tr>
	  <!-- END BLOCK : nombre_proveedor -->
	<!-- START BLOCK : factura -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="print">{factura}</td>
	  <td class="rprint">{importe}</td>
	  </tr>
	<!-- END BLOCK : factura -->
    <!-- START BLOCK : total_lote -->
	<tr>
      <th class="rprint">Total del lote </th>
      <th class="rprint"><strong>{importe_lote}</strong></th>
    </tr>
	<!-- END BLOCK : total_lote -->
	<!-- END BLOCK : lote -->
	<tr>
      <th colspan="8" class="rprint">Total de la cuenta </th>
      <th class="rprint_total">{importe_total}</th>
    </tr>
	<tr>
	  <td colspan="9">&nbsp;</td>
	  </tr>
	<!-- END BLOCK : cia -->
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : cheques -->
