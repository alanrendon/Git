<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Validar Facturas</p>
  <form action="./zap_val_fac.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) clave.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="true" class="vnombre" id="nombre_pro" size="40" />
        <input name="clave" type="text" class="insert" id="clave" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha1.select()" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10" />
      al
        <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" maxlength="10" />
        <input name="cred" type="checkbox" id="cred" value="1" />
        <span style="font-size:8pt;">30 d&iacute;as de credito</span> </td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="this.form.submit()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null)
		f.nombre_pro.value = pro[get_val(f.num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

window.onload = function() { showAlert = true; f.num_cia.select(); };
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Validar Facturas </p>
  <form action="./zap_val_fac.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <!-- START BLOCK : pro -->
	<tr>
      <th colspan="12" class="vtabla" scope="col" style="font-size:12pt;">{num_pro} {nombre} </th>
      <th class="vtabla" scope="col"><input type="checkbox" name="checkbox" value="checkbox" /></th>
	</tr>
    <tr>
      <th class="tabla">Tienda</th>
	  <th class="tabla">Fecha</th>
      <th class="tabla">Factura</th>
      <th class="tabla">Gastos</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Desc 1 </th>
      <th class="tabla">Desc 2 </th>
      <th class="tabla">Desc 3 </th>
      <th class="tabla">Desc 4 </th>
      <th class="tabla">Faltantes</th>
      <th class="tabla">I.V.A.</th>
      <th class="tabla">Total</th>
      <th class="tabla">Aut</th>
    </tr>
    <!-- START BLOCK : fac -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla" style="font-weight:bold;">{num_cia} {nombre} </td>
	  <td class="tabla" style="color:#0000CC; font-weight:bold;">{fecha}</td>
      <td class="tabla" style="color:#006633; font-weight:bold;">{num_fact}</td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaGasto({i})" onkeydown="movCursor(event.keyCode,pdesc1{index},null,pdesc1{index},obs{back},obs{index})" value="{codgastos}" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" value="{desc}" size="20" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="{importe}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="pdesc1[]" type="text" class="rinsert" id="pdesc1" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal({i})" onkeydown="movCursor(event.keyCode,pdesc2{index},codgastos{index},pdesc2{index},obs{back},obs{index})" value="{pdesc1}" size="5" /></td>
      <td class="tabla"><input name="pdesc2[]" type="text" class="rinsert" id="pdesc2" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal({i})" onkeydown="movCursor(event.keyCode,pdesc3{index},pdesc1{index},pdesc3{index},obs{back},obs{index})" value="{pdesc2}" size="5" /></td>
      <td class="tabla"><input name="pdesc3[]" type="text" class="rinsert" id="pdesc3" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal({i})" onkeydown="movCursor(event.keyCode,pdesc4{index},pdesc2{index},pdesc4{index},obs{back},obs{index})" value="{pdesc3}" size="5" /></td>
      <td class="tabla"><input name="pdesc4[]" type="text" class="rinsert" id="pdesc4" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal({i})" onkeydown="movCursor(event.keyCode,obs{index},pdesc3{index},null,obs{back},obs{index})" value="{pdesc4}" size="5" /></td>
      <td class="tabla"><input name="falt[]" type="text" class="rnombre" id="falt" value="{falt}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="iva[]" type="text" class="rnombre" id="iva" value="{iva}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="total_ant[]" type="hidden" id="total_ant" value="{total}" />
      <input name="total[]" type="text" class="rnombre" id="total" value="{total}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="por_aut{i}" type="checkbox" id="por_aut{i}" value="{id}" onchange="ok({i})" /></td>
    </tr>
    <tr>
      <td class="vtabla">&nbsp;</td>
      <th class="vtabla">Obs.</th>
      <td colspan="11" class="tabla"><input name="obs[]" type="text" class="vinsert" id="obs" style="width:100%;" onkeydown="movCursor(event.keyCode,codgastos{next},null,null,codgastos{index},codgastos{next})" value="{obs}" maxlength="255" /></td>
      </tr>
	  <!-- END BLOCK : fac -->
	      <tr>
      <td colspan="13" class="vtabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : pro -->
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='./zap_val_fac.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Autorizar" onclick="validar()" /> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array();
<!-- START BLOCK : g -->
gasto[{cod}] = '{desc}';
<!-- END BLOCK : g -->

function cambiaGasto(i) {
	var codgastos = f.codgastos[i] ? f.codgastos[i] : f.codgastos;
	var desc = f.desc[i] ? f.desc[i] : f.desc;
	
	if (codgastos.value == '' || codgastos.value == '0') {
		codgastos.value = '';
		desc.value = '';
	}
	else if (gasto[get_val(codgastos)] != null)
		desc.value = gasto[get_val(codgastos)];
	else {
		alert('El código no se encuentra en el catálogo');
		codgastos.value = f.tmp.value;
		codgastos.select();
	}
}

function calculaTotal(i) {
	var fimporte, fpdesc1, fpdesc2, fpdesc3, fpdesc4, ffalt, fiva, ftotal;
	var importe, pdesc1, pdesc2, pdesc3, pdesc4, desc1, desc2, desc3, desc4, falt, iva, total;
	
	if (f.codgastos[i]) {
		fimporte = f.importe[i];
		fpdesc1 = f.pdesc1[i];
		fpdesc2 = f.pdesc2[i];
		fpdesc3 = f.pdesc3[i];
		fpdesc4 = f.pdesc4[i];
		ffalt = f.falt[i];
		fiva = f.iva[i];
		ftotal = f.total[i];
	}
	else {
		fimporte = f.importe;
		fpdesc1 = f.pdesc1;
		fpdesc2 = f.pdesc2;
		fpdesc3 = f.pdesc3;
		fpdesc4 = f.pdesc4;
		ffalt = f.falt;
		fiva = f.iva;
		ftotal = f.total;
	}
	
	importe = get_val(fimporte);
	falt = get_val(ffalt);
	desc1 = get_val(fpdesc1) > 0 ? Round((importe - falt) * get_val(fpdesc1) / 100, 2) : get_val(fpdesc1);
	desc2 = get_val(fpdesc2) > 0 ? Round((importe - falt - desc1) * get_val(fpdesc2) / 100, 2) : get_val(fpdesc2);
	desc3 = get_val(fpdesc3) > 0 ? Round((importe - falt - desc1 - desc2) * get_val(fpdesc3) / 100, 2) : get_val(fpdesc3);
	desc4 = get_val(fpdesc4) > 0 ? Round((importe - falt - desc1 - desc2 - desc3) * get_val(fpdesc4) / 100, 2) : get_val(fpdesc4);
	subtotal = importe - falt - desc1 - desc2 - desc3 - desc4;
	iva = get_val(fiva) > 0 ? subtotal * 0.15 : 0;
	total = subtotal + iva;
	
	fiva.value = iva > 0 ? numberFormat(iva, 2) : '';
	ftotal.value = numberFormat(total, 2);
}

function ok(i) {
	if (f.codgastos[i]) {
		var edit = f.eval('por_aut' + i).checked;
		
		f.codgastos[i].className = edit ? 'nombre' : 'insert';
		f.pdesc1[i].className = edit ? 'rnombre' : 'rinsert';
		f.pdesc2[i].className = edit ? 'rnombre' : 'rinsert';
		f.pdesc3[i].className = edit ? 'rnombre' : 'rinsert';
		f.pdesc4[i].className = edit ? 'rnombre' : 'rinsert';
		f.obs[i].className = edit ? 'vnombre' : 'vinsert';
		
		f.codgastos[i].readOnly = edit;
		f.pdesc1[i].readOnly = edit;
		f.pdesc2[i].readOnly = edit;
		f.pdesc3[i].readOnly = edit;
		f.pdesc4[i].readOnly = edit;
		f.obs[i].readOnly = edit;
	}
	else {
		var edit = f.por_aut.checked;
		
		f.codgastos.className = edit ? 'nombre' : 'insert';
		f.pdesc1.className = edit ? 'rnombre' : 'rinsert';
		f.pdesc2.className = edit ? 'rnombre' : 'rinsert';
		f.pdesc3.className = edit ? 'rnombre' : 'rinsert';
		f.pdesc4.className = edit ? 'rnombre' : 'rinsert';
		f.obs.className = edit ? 'vnombre' : 'vinsert';
		
		f.codgastos.readOnly = edit;
		f.pdesc1.readOnly = edit;
		f.pdesc2.readOnly = edit;
		f.pdesc3.readOnly = edit;
		f.pdesc4.readOnly = edit;
		f.obs.readOnly = edit;
	}
}

function validar() {
	if (confirm('¿Desea validar y modificar las facturas seleccionadas?'))
		f.submit();
}

function Round(Numero, decimales) {
	//Convertimos el valor a entero de acuerdo al # de decimales
	var Valor = Numero;
	var ndecimales = Math.pow(10, decimales);
	Valor = Valor * ndecimales;
	
	//Redondeamos y luego dividimos por el # de decimales
	Valor = Math.round(Valor);
	Valor = Valor / ndecimales;
	return Valor;
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

window.onload = f.codgastos[0] ? f.codgastos[0].select() : f.codgastos.select();
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
