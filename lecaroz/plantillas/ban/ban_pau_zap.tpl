<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Autom&aacute;tico a Proveedores</p>
<form name="form" method="post" action="./ban_pau_zap.php">
<input name="temp" type="hidden">
<input name="prelistado" type="hidden" value="1">
  <table class="tabla">
    <tr>
      <th class="vtabla">Fecha de corte <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha_corte" type="text" class="insert" id="fecha_corte" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) dias_deposito.select();" value="{fecha_corte}" size="10" maxlength="10">      </td>
      </tr>
    <tr>
      <th class="vtabla">D&iacute;as de dep&oacute;sito</th>
      <td class="vtabla"><input name="dias_deposito" type="text" class="insert" id="dias_deposito" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.sin_pago[0].select();
else if (event.keyCode == 38) form.fecha_corte.select();" value="1" size="5" maxlength="5">
        <input name="dep_tra" type="checkbox" id="dep_tra" value="1" checked>
        Aplicar a transferencias </td>
      </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de Pago </th>
      <td class="vtabla"><input name="tipo" type="radio" value="0" checked>        
        Ambos<br>
        <input name="tipo" type="radio" value="1">
          Cheque<br>
          <input name="tipo" type="radio" value="2">
          Transferencia Electr&oacute;nica </td></tr>
    <tr>
      <th class="vtabla">Reservar Saldo para Impuestos </th>
      <td class="vtabla"><input name="reservar" type="checkbox" id="reservar" value="1">
        Si</td>
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
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[1].select();
else if (event.keyCode == 37) form.sin_pago[9].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[2].select();
else if (event.keyCode == 37) form.sin_pago[0].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[3].select();
else if (event.keyCode == 37) form.sin_pago[1].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[4].select();
else if (event.keyCode == 37) form.sin_pago[2].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[5].select();
else if (event.keyCode == 37) form.sin_pago[3].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[6].select();
else if (event.keyCode == 37) form.sin_pago[4].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[7].select();
else if (event.keyCode == 37) form.sin_pago[5].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[8].select();
else if (event.keyCode == 37) form.sin_pago[6].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[9].select();
else if (event.keyCode == 37) form.sin_pago[7].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="sin_pago[]" type="text" class="insert" id="sin_pago" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.sin_pago[0].select();
else if (event.keyCode == 37) form.sin_pago[8].select();
else if (event.keyCode == 38) form.dias_deposito.select();
else if (event.keyCode == 40) form.no_pagan[0].select();" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as que no pagar&aacute;n </th>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[1].select();
else if (event.keyCode == 37) form.no_pagan[9].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[2].select();
else if (event.keyCode == 37) form.no_pagan[0].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[3].select();
else if (event.keyCode == 37) form.no_pagan[1].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[4].select();
else if (event.keyCode == 37) form.no_pagan[2].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[5].select();
else if (event.keyCode == 37) form.no_pagan[3].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[6].select();
else if (event.keyCode == 37) form.no_pagan[4].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[7].select();
else if (event.keyCode == 37) form.no_pagan[5].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[8].select();
else if (event.keyCode == 37) form.no_pagan[6].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[9].select();
else if (event.keyCode == 37) form.no_pagan[7].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
      <td class="vtabla"><input name="no_pagan[]" type="text" class="insert" id="no_pagan" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.no_pagan[0].select();
else if (event.keyCode == 37) form.no_pagan[8].select();
else if (event.keyCode == 38) form.sin_pago[0].select();
else if (event.keyCode == 40) form.obligado[0].select();" size="3" maxlength="3"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th rowspan="3" class="vtabla" scope="row">Pago obligado a proveedores </th>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[1].select();
else if (event.keyCode == 37) form.obligado[29].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[10].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[2].select();
else if (event.keyCode == 37) form.obligado[0].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[11].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[3].select();
else if (event.keyCode == 37) form.obligado[1].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[12].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[4].select();
else if (event.keyCode == 37) form.obligado[2].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[13].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[5].select();
else if (event.keyCode == 37) form.obligado[3].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[14].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[6].select();
else if (event.keyCode == 37) form.obligado[4].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[15].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[7].select();
else if (event.keyCode == 37) form.obligado[5].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[16].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[8].select();
else if (event.keyCode == 37) form.obligado[6].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[17].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[9].select();
else if (event.keyCode == 37) form.obligado[7].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[18].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[10].select();
else if (event.keyCode == 37) form.obligado[8].select();
else if (event.keyCode == 38) form.no_pagan[0].select();
else if (event.keyCode == 40) form.obligado[19].select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[11].select();
else if (event.keyCode == 37) form.obligado[9].select();
else if (event.keyCode == 38) form.obligado[0].select();
else if (event.keyCode == 40) form.obligado[20].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[12].select();
else if (event.keyCode == 37) form.obligado[10].select();
else if (event.keyCode == 38) form.obligado[1].select();
else if (event.keyCode == 40) form.obligado[21].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[13].select();
else if (event.keyCode == 37) form.obligado[11].select();
else if (event.keyCode == 38) form.obligado[2].select();
else if (event.keyCode == 40) form.obligado[22].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[14].select();
else if (event.keyCode == 37) form.obligado[12].select();
else if (event.keyCode == 38) form.obligado[3].select();
else if (event.keyCode == 40) form.obligado[23].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[15].select();
else if (event.keyCode == 37) form.obligado[13].select();
else if (event.keyCode == 38) form.obligado[4].select();
else if (event.keyCode == 40) form.obligado[24].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[16].select();
else if (event.keyCode == 37) form.obligado[14].select();
else if (event.keyCode == 38) form.obligado[5].select();
else if (event.keyCode == 40) form.obligado[25].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[17].select();
else if (event.keyCode == 37) form.obligado[15].select();
else if (event.keyCode == 38) form.obligado[6].select();
else if (event.keyCode == 40) form.obligado[26].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[18].select();
else if (event.keyCode == 37) form.obligado[16].select();
else if (event.keyCode == 38) form.obligado[7].select();
else if (event.keyCode == 40) form.obligado[27].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[19].select();
else if (event.keyCode == 37) form.obligado[17].select();
else if (event.keyCode == 38) form.obligado[8].select();
else if (event.keyCode == 40) form.obligado[28].select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[20].select();
else if (event.keyCode == 37) form.obligado[18].select();
else if (event.keyCode == 38) form.obligado[9].select();
else if (event.keyCode == 40) form.obligado[29].select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[21].select();
else if (event.keyCode == 37) form.obligado[19].select();
else if (event.keyCode == 38) form.obligado[10].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[22].select();
else if (event.keyCode == 37) form.obligado[20].select();
else if (event.keyCode == 38) form.obligado[11].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[23].select();
else if (event.keyCode == 37) form.obligado[21].select();
else if (event.keyCode == 38) form.obligado[12].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[24].select();
else if (event.keyCode == 37) form.obligado[22].select();
else if (event.keyCode == 38) form.obligado[13].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[25].select();
else if (event.keyCode == 37) form.obligado[23].select();
else if (event.keyCode == 38) form.obligado[14].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[26].select();
else if (event.keyCode == 37) form.obligado[24].select();
else if (event.keyCode == 38) form.obligado[15].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[27].select();
else if (event.keyCode == 37) form.obligado[25].select();
else if (event.keyCode == 38) form.obligado[16].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[28].select();
else if (event.keyCode == 37) form.obligado[26].select();
else if (event.keyCode == 38) form.obligado[17].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.obligado[29].select();
else if (event.keyCode == 37) form.obligado[27].select();
else if (event.keyCode == 38) form.obligado[18].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
      <td class="vtabla"><input name="obligado[]" type="text" class="insert" id="obligado" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha_corte.select();
else if (event.keyCode == 37) form.obligado[28].select();
else if (event.keyCode == 38) form.obligado[19].select();
else if (event.keyCode == 40) form.fecha_corte.select();" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha_corte.value == "") {
			alert("Debe especificar la fecha de corte");
			form.fecha_pago.select();
			return false;
		}
		else if (form.dias_deposito.value == "") {
			alert("Debe especificar los días de depósito");
			form.dias_deposito.select();
			return false;
		}
		else  {
			if (confirm("¿Son correctos todos los datos?"))
				form.submit();
			else {
				form.fecha_pago.select();
				return false;
			}
		}
	}
	
	window.onload = document.form.fecha_corte.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : reserva -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Autom&aacute;tico a Proveedores </p>
  <form action="./ban_pau_zap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="prelistado" type="hidden" id="prelistado" value="1">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Reserva</th>
    </tr>
    <!-- START BLOCK : reserva_row -->
	<tr>
      <td class="vtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia} {nombre} </td>
      <td class="tabla"><input name="reserva[]" type="text" class="rinsert" id="reserva" onFocus="tmp.value=this.value;this.select()" onChange="inputFormat(this,2)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) reserva[{next}].select(); else if (event.keyCode == 38) reserva[{back}].select()" size="10"></td>
    </tr>
	<!-- END BLOCK : reserva_row -->
  </table>  <p><input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_pau_zap.php?cancel=1'">&nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.reserva[0].select();
//-->
</script>
<!-- END BLOCK : reserva -->
<!-- START BLOCK : pre_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Autom&aacute;tico a Proveedores <br>
  Listado de Facturas por Pagar </p>
  <form action="./ban_pau_zap.php" method="post" name="form">
  <input name="generar" type="hidden" value="1">
  <input name="num_facturas" type="hidden" value="{num_facturas}">
  <table width="100%" class="tabla">
    <!-- START BLOCK : bloque_cia -->
	<tr>
      <th colspan="3" class="vtabla">{num_cia} - {nombre_cia}</th>
      <th colspan="2" class="rtabla">Saldo para Pagar</th>
      <th colspan="2" class="rtabla">{saldo}</th>
      </tr>
	<tr>
      <th class="tabla" scope="col"><input name="select_all[]" type="checkbox" id="select_all" onClick="seleccionar(this,form,{ini},{fin})" alt="Marcar | Desmarcar todos" checked></th>
      <th colspan="2" class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha de Pago </th>
      <th class="tabla" scope="col">N&uacute;m. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila_pre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" checked></td>
      <td width="5%" class="vtabla">{num_proveedor}</td>
      <td width="30%" class="vtabla">{nombre_proveedor}</td>
      <td width="10%" class="tabla">{fecha}</td>
      <td width="10%" class="tabla">{num_fact}</td>
      <td width="25%" class="vtabla">{concepto}</td>
      <th width="15%" class="rtabla">{importe}</th>
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
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_pau_zap.php?cancel=1'">
&nbsp;&nbsp; 
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function seleccionar(checkall,form,ini,fin) {
		if (checkall.checked == true)
			for (i = ini; i <= fin; i++)
				form.id[i].checked = true;
		else
			for (i = ini; i <= fin; i++)
				form.id[i].checked = false;
	}
	
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : pre_listado -->
<!-- START BLOCK : saldos -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Zapater&iacute;as Elite </td>
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
  <table align="center" class="print">
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
<br style="page-break-after:always;">
<!-- END BLOCK : saldos -->

<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Zapaterias Elite</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Datos para emisi&oacute;n de cheques y transferencias<br>
    del {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="2" class="print" scope="col">Cia.: {num_cia} </th>
    <th class="print" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="print" scope="col">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Folio</th>
    <th colspan="2" class="print">Proveedor</th>
    <th class="print">Facturas</th>
    <th class="print">Importe</th>
    <th class="print">Tipo</th>
  </tr>
  <!-- START BLOCK : fila_cheque -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{folio}</td>
    <td class="vprint">{num_proveedor}</td>
    <td class="vprint">{nombre_proveedor}</td>
    <td class="vprint">{facturas}</td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{tipo}</td>
  </tr>
  <!-- END BLOCK : fila_cheque -->
  <tr>
    <th colspan="4" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
    <th class="rprint_total">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="4" class="rprint">Gran Total</th>
	<th class="rprint_total">{gran_total}</th>
    <th class="rprint_total">&nbsp;</th>
  </tr>
</table>
  <!-- END BLOCK : listado -->
</body>
</html>
