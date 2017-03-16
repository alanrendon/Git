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
<!-- START BLOCK : data -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura Manual de Cheques</p>
  <form action="./ban_che_man_v4.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Cuenta</th>
      <td colspan="6" class="vtabla"><select name="cuenta" class="insert" id="cuenta" onChange="cambiaSaldo()">
        <option value="1" {cuenta1}>BANORTE</option>
        <option value="2" {cuenta2}>SANTANDER</option>
      </select></td>
      </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td colspan="4" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia)" onKeyDown="mov(event.keyCode,fecha,null,null,null,fecha)" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="50" readonly="true">        </td>
      <th class="vtabla">Saldo</th>
      <td class="vtabla"><input name="saldo" type="text" class="rnombre" id="saldo" style="width: 100%; font-size:14pt;" value="{saldo}" size="12" readonly="true"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Tipo de Pago </th>
      <td colspan="6" class="vtabla"><input name="pago" type="radio" value="1" onClick="cambiaSaldo()" checked>
        Cheque
          <input name="pago" type="radio" value="2" onClick="cambiaSaldo()">
          Transferencia Electr&oacute;nica </td>
      </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Fecha</th>
      <td colspan="6" class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="mov(event.keyCode,num_pro,null,null,num_cia,num_pro)" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Proveedor</th>
      <td colspan="6" class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaPro(this,nombre_pro)" onKeyDown="mov(event.keyCode,concepto,null,null,fecha,concepto)" value="{num_pro}" size="3" maxlength="4">
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" style="width: auto;" value="{nombre_pro}" size="68" readonly="true">
        <input name="tipo" type="hidden" id="tipo"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Concepto</th>
      <td colspan="6" class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" style="width: 100%;" onKeyDown="mov(event.keyCode,codgastos,null,null,num_pro,codgastos)" value="{concepto}" size="50" maxlength="200"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">C&oacute;digo de Gasto</th>
      <td colspan="6" class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaGasto(this,nombre_gasto)" onKeyDown="mov(event.keyCode,num_fact[0],null,null,concepto,num_fact[0])" value="{codgastos}" size="3" maxlength="4">
        <input name="nombre_gasto" type="text" class="vnombre" id="nombre_gasto" style="width: auto;" value="{nombre_gasto}" size="68" readonly="true"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th rowspan="11" class="vtabla" scope="row">Facturas</th>
      <th class="tabla">No. Factura </th>
      <th class="tabla">Importe</th>
      <th class="tabla">Descuento 1 </th>
      <th class="tabla">Descuento 2 </th>
      <th class="tabla">Descuento 3</th>
      <th class="tabla">Total</th>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[0],null,importe[0],codgastos,num_fact[1])" value="{num_fact0}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(0)" onKeyDown="mov(event.keyCode,desc1[0],num_fact[0],desc1[0],codgastos,importe[1])" value="{importe0}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(0)" onKeyDown="mov(event.keyCode,desc2[0],importe[0],desc2[0],codgastos,desc1[1])" value="{desc1_0}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(0)" onKeyDown="mov(event.keyCode,desc3[0],desc1[0],desc3[0],codgastos,desc2[1])" value="{desc2_0}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(0)" onKeyDown="mov(event.keyCode,num_fact[1],desc2[0],null,codgastos,desc3[1])" value="{desc3_0}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total0}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[1],null,importe[1],num_fact[0],num_fact[2])" value="{num_fact1}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(1)" onKeyDown="mov(event.keyCode,desc1[1],num_fact[1],desc1[1],importe[0],importe[2])" value="{importe1}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(1)" onKeyDown="mov(event.keyCode,desc2[1],importe[1],desc2[1],desc1[0],desc1[2])" value="{desc1_1}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(1)" onKeyDown="mov(event.keyCode,desc3[1],desc1[1],desc3[1],desc2[0],desc2[2])" value="{desc2_1}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(1)" onKeyDown="mov(event.keyCode,num_fact[2],desc2[1],null,desc3[0],desc3[2])" value="{desc3_1}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total1}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[2],null,importe[2],num_fact[1],num_fact[3])" value="{num_fact2}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(2)" onKeyDown="mov(event.keyCode,desc1[2],num_fact[2],desc1[2],importe[1],importe[3])" value="{importe2}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(2)" onKeyDown="mov(event.keyCode,desc2[2],importe[2],desc2[2],desc1[1],desc1[3])" value="{desc1_2}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(2)" onKeyDown="mov(event.keyCode,desc3[2],desc1[2],desc3[2],desc2[1],desc2[3])" value="{desc2_2}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(2)" onKeyDown="mov(event.keyCode,num_fact[3],desc2[2],null,desc3[1],desc3[3])" value="{desc3_2}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total2}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[3],null,importe[3],num_fact[2],num_fact[4])" value="{num_fact3}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(3)" onKeyDown="mov(event.keyCode,desc1[3],num_fact[3],desc1[3],importe[2],importe[4])" value="{importe3}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(3)" onKeyDown="mov(event.keyCode,desc2[3],importe[3],desc2[3],desc1[2],desc1[4])" value="{desc1_3}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(3)" onKeyDown="mov(event.keyCode,desc3[3],desc1[3],desc3[3],desc2[2],desc2[4])" value="{desc2_3}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(3)" onKeyDown="mov(event.keyCode,num_fact[4],desc2[3],null,desc3[2],desc3[4])" value="{desc3_3}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total3}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[4],null,importe[4],num_fact[3],num_fact[5])" value="{num_fact4}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(4)" onKeyDown="mov(event.keyCode,desc1[4],num_fact[4],desc1[4],importe[3],importe[5])" value="{importe4}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(4)" onKeyDown="mov(event.keyCode,desc2[4],importe[4],desc2[4],desc1[3],desc1[5])" value="{desc1_4}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(4)" onKeyDown="mov(event.keyCode,desc3[4],desc1[4],desc3[4],desc2[3],desc2[5])" value="{desc2_4}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(4)" onKeyDown="mov(event.keyCode,num_fact[5],desc2[4],null,desc3[3],desc3[5])" value="{desc3_4}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total4}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[5],null,importe[5],num_fact[4],num_fact[6])" value="{num_fact5}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(5)" onKeyDown="mov(event.keyCode,desc1[5],num_fact[5],desc1[5],importe[4],importe[6])" value="{importe5}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(5)" onKeyDown="mov(event.keyCode,desc2[5],importe[5],desc2[5],desc1[4],desc1[6])" value="{desc1_5}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(5)" onKeyDown="mov(event.keyCode,desc3[5],desc1[5],desc3[5],desc2[4],desc2[6])" value="{desc2_5}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(5)" onKeyDown="mov(event.keyCode,num_fact[6],desc2[5],null,desc3[4],desc3[6])" value="{desc3_5}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total5}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[6],null,importe[6],num_fact[5],num_fact[7])" value="{num_fact6}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(6)" onKeyDown="mov(event.keyCode,desc1[6],num_fact[6],desc1[6],importe[5],importe[7])" value="{importe6}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(6)" onKeyDown="mov(event.keyCode,desc2[6],importe[6],desc2[6],desc1[5],desc1[7])" value="{desc1_6}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(6)" onKeyDown="mov(event.keyCode,desc3[6],desc1[6],desc3[6],desc2[5],desc2[7])" value="{desc2_6}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(6)" onKeyDown="mov(event.keyCode,num_fact[7],desc2[6],null,desc3[5],desc3[7])" value="{desc3_6}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total6}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[7],null,importe[7],num_fact[6],num_fact[8])" value="{num_fact7}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(7)" onKeyDown="mov(event.keyCode,desc1[7],num_fact[7],desc1[7],importe[6],importe[8])" value="{importe7}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(7)" onKeyDown="mov(event.keyCode,desc2[7],importe[7],desc2[7],desc1[6],desc1[8])" value="{desc1_7}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(7)" onKeyDown="mov(event.keyCode,desc3[7],desc1[7],desc3[7],desc2[6],desc2[8])" value="{desc2_7}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(7)" onKeyDown="mov(event.keyCode,num_fact[8],desc2[7],null,desc3[6],desc3[8])" value="{desc3_7}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total7}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[8],null,importe[8],num_fact[7],num_fact[9])" value="{num_fact8}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(8)" onKeyDown="mov(event.keyCode,desc1[8],num_fact[8],desc1[8],importe[7],importe[9])" value="{importe8}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(8)" onKeyDown="mov(event.keyCode,desc2[8],importe[8],desc2[8],desc1[7],desc1[9])" value="{desc1_8}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(8)" onKeyDown="mov(event.keyCode,desc3[8],desc1[8],desc3[8],desc2[7],desc2[9])" value="{desc2_8}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(8)" onKeyDown="mov(event.keyCode,num_fact[0],desc2[8],null,desc3[7],desc3[9])" value="{desc3_8}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total8}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="mov(event.keyCode,importe[9],null,importe[9],num_fact[8],dev)" value="{num_fact9}" size="10"></td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalFac(9)" onKeyDown="mov(event.keyCode,desc1[9],num_fact[9],desc1[9],importe[8],dev)" value="{importe9}" size="10"></td>
      <td class="tabla"><input name="desc1[]" type="text" class="rinsert" id="desc1" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(9)" onKeyDown="mov(event.keyCode,desc2[9],importe[9],desc2[9],desc1[8],dev)" value="{desc1_9}" size="10"></td>
      <td class="tabla"><input name="desc2[]" type="text" class="rinsert" id="desc2" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(9)" onKeyDown="mov(event.keyCode,desc3[9],desc1[9],desc3[9],desc2[8],dev)" value="{desc2_9}" size="10"></td>
      <td class="tabla"><input name="desc3[]" type="text" class="rinsert" id="desc3" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_desc(this)) totalFac(9)" onKeyDown="mov(event.keyCode,dev,desc2[9],null,desc3[8],dev)" value="{desc3_9}" size="10"></td>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total9}" size="10" readonly="true"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row">Devoluciones</th>
      <th class="vtabla"><input name="dev" type="text" class="rinsert" id="dev" style="width:100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalCheque()" onKeyDown="mov(event.keyCode,fal,null,null,desc3[9],fal)" value="{dev}" size="10"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row">Faltantes</th>
      <th class="vtabla"><input name="fal" type="text" class="rinsert" id="fal" style="width:100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalCheque()" onKeyDown="mov(event.keyCode,flete,null,null,dev,flete)" value="{fal}" size="10"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row"><input name="aplica_iva" type="checkbox" id="aplica_iva" onChange="totalCheque()" value="1" checked>
        I.V.A.</th>
      <th class="vtabla"><input name="iva" type="text" class="rnombre" id="iva" style="width:100%;" value="{iva}" size="10"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row">SubTotal</th>
      <th class="vtabla"><input name="subtotal" type="text" class="rnombre" id="subtotal" style="width:100%;" value="{subtotal}" size="10"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row">Flete</th>
      <th class="vtabla"><input name="flete" type="text" class="rinsert" id="flete" style="width:100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) totalCheque()" onKeyDown="mov(event.keyCode,num_cia,null,null,fal,num_cia)" value="{flete}" size="10"></th>
    </tr>
    <tr>
      <th colspan="6" class="rtabla" scope="row" style="font-size: 12pt;">Total Cheque </th>
      <th class="vtabla"><input name="total_cheque" type="text" class="rnombre" id="total_cheque" style="width: 100%; font-size: 12pt;" value="{total_cheque}" size="10" readonly="true"></th>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array();
var saldo1 = new Array();
var saldo2 = new Array();
var pro = new Array();
var gasto = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : saldo1 -->
saldo1[{num_cia}] = new Array();
saldo1[{num_cia}]['saldo'] = "{saldo}";
saldo1[{num_cia}]['saldo_real'] = "{saldo_real}";
<!-- END BLOCK : saldo1 -->
<!-- START BLOCK : saldo2 -->
saldo2[{num_cia}] = new Array();
saldo2[{num_cia}]['saldo'] = "{saldo}";
saldo2[{num_cia}]['saldo_real'] = "{saldo_real}";
<!-- END BLOCK : saldo2 -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = new Array();
pro[{num_pro}]['nombre'] = "{nombre}";
pro[{num_pro}]['trans'] = {trans};
pro[{num_pro}]['tipo'] = "{tipo}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{descripcion}";
<!-- END BLOCK : gasto -->

function mov(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function cambiaCia(num, nombre) {
	var saldo = eval("saldo" + form.cuenta.value);
	
	if (num.value == "") {
		nombre.value = "";
		form.saldo.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
		if (saldo[num.value] != null) {
			form.saldo.value = form.pago[0].checked ? saldo[num.value]['saldo'] : saldo[num.value]['saldo_real'];
			form.saldo.style.color = get_val(form.saldo) > 0 ? "#0000CC" : "#CC0000";
		}
		else {
			form.saldo.value = "0.00";
			form.saldo.style.color = "#CC0000";
		}
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function cambiaSaldo() {
	var saldo = eval("saldo" + form.cuenta.value);
	
	if (form.num_cia.value == "") {
		return false;
	}
	else {
		if (saldo[form.num_cia.value] != null) {
			form.saldo.value = form.pago[0].checked ? saldo[form.num_cia.value]['saldo'] : saldo[form.num_cia.value]['saldo_real'];
			form.saldo.style.color = get_val(form.saldo) > 0 ? "#0000CC" : "#CC0000";
		}
		else {
			form.saldo.value = "0.00";
			form.saldo.style.color = "#CC0000";
		}
	}
}

function cambiaPro(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
		form.saldo.value = "";
	}
	else if (pro[num.value] != null) {
		nombre.value = pro[num.value]['nombre'];
		form.tipo.value = pro[num.value]['tipo'];
		if (pro[num.value]['trans']) {
			form.pago[1].disabled = false;
			form.pago[1].checked = true;
		}
		else {
			form.pago[1].disabled = true;
			form.pago[0].checked = true;
		}
	}
	else {
		alert("El proveedor no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
	
	cambiaSaldo();
}

function cambiaGasto(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (gasto[num.value] != null)
		nombre.value = gasto[num.value];
	else {
		alert("El código de gasto no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function input_desc(input) {
	if (input.value == "" || input.value == "0") {
		input.value = "";
		return true;
	}
	
	if (isNaN(parseFloat(input.value.replace(/,|%/, "")))) {
		alert("Solo se permiten números");
		input.value = input.form.tmp.value;
		return false;
	}
	
	var value = parseFloat(input.value.replace(/,|%/g, ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		input.value = input.form.tmp.value;
		return false;
	}
	
	if (input.value.indexOf("%") >= 0 && value > 100) {
		alert("No puede manejar mas del 100% de descuento");
		input.value = input.form.tmp.value;
		return false;
	}
	
	input.value = number_format(value, 2) + (input.value.indexOf("%") >= 0 ? "%" : "");
	
	return true;
}

function totalFac(i) {
	var importe, desc1, desc2, desc3, iva, total;
	
	importe = get_val(form.importe[i]);
	//por_iva = form.iva.checked ? 0.15 : 0;
	if (form.desc1[i].value != '') importe -= form.desc1[i].value.indexOf("%") >= 0 ? importe * get_val(form.desc1[i]) / 100 : get_val(form.desc1[i]);
	if (form.desc2[i].value != '') importe -= form.desc2[i].value.indexOf("%") >= 0 ? importe * get_val(form.desc2[i]) / 100 : get_val(form.desc2[i]);
	if (form.desc3[i].value != '') importe -= form.desc3[i].value.indexOf("%") >= 0 ? importe * get_val(form.desc3[i]) / 100 : get_val(form.desc3[i]);
	
	form.total[i].value = number_format(importe, 2);
	
	totalCheque();
}

function totalCheque() {
	var total = 0, dev = 0, fal = 0, iva = 0, subtotal = 0, flete = 0, total_cheque = 0;
	
	for (var i = 0; i < form.total.length; i++)
		total += get_val(form.total[i]);
	
	por_iva = form.aplica_iva.checked ? 0.15 : 0;
	dev = get_val(form.dev);
	fal = get_val(form.fal);
	iva = (total - dev - fal) * por_iva;
	flete = get_val(form.flete);
	
	subtotal = total - dev - fal + iva;
	total_cheque = subtotal - flete;
	
	form.iva.value = number_format(iva, 2);
	form.subtotal.value = number_format(subtotal, 2);
	form.total_cheque.value = number_format(total_cheque, 2);
}

function validar() {
	if (get_val(form.num_cia) <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha");
		form.fecha.select();
		return false;
	}
	else if (get_val(form.num_pro) <= 0) {
		alert("Debe especificar el proveedor");
		form.num_pro.select();
		return false;
	}
	else if (form.concepto.value == "") {
		alert("Debe poner el concepto");
		form.concepto.select();
		return false;
	}
	else if (get_val(form.codgastos) <= 0) {
		alert("Debe especificar el código de gasto");
		form.codgastos.select();
		return false;
	}
	else if (get_val(form.total_cheque) <= 0) {
		alert("El importe del cheque no puede ser cero");
		form.importe[0].select();
		return false;
	}
	else if (form.pago[1].checked && get_val(form.total_cheque) > get_val(form.saldo)) {
		alert("No se puede hacer transferencia electrónica por que no hay suficiente saldo en la cuenta seleccionada");
		form.importe[0].select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		form.submit();
	else
		form.num_cia.select();
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : data -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame ? top.mainFrame.document.form : top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	f.eval(campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : valid -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	if (top.mainFrame)
		top.mainFrame.location = './ban_che_man_v4.php';
	else
		top.location = './ban_che_man_v4.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
