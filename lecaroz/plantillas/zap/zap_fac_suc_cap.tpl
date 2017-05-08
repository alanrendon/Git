<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Facturas (Tiendas con Sucursales) </p>
  <form action="./zap_fac_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <input name="action" type="hidden" id="action" value="{action}" />    
    <table class="tabla">
    <tr>
      <th colspan="5" class="tabla" scope="row">Datos de Factura</th>
      </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" colspan="3"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="movCursor(event.keyCode,num_pro,null,null,null,num_pro)" value="{num_cia}" size="3" />
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" style="font-size:12pt;" value="{nombre_cia}" size="45" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla" colspan="3"><input name="num_pro_old" type="hidden" id="num_pro_old" value="{num_proveedor}" />
      <input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="movCursor(event.keyCode,clave,null,clave,num_cia,num_fact)" value="{num_pro}" size="3" />
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" style="font-size:12pt;" value="{nombre_pro}" size="45" readonly="true" />
        <input name="clave" type="text" class="insert" id="clave" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,num_fact,num_pro,null,num_cia,num_fact)" value="{clave}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="vinsert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) validaFac()" onkeydown="movCursor(event.keyCode,entrada,null,entrada,num_pro,fecha)" value="{num_fact}" size="10" />
        <input name="num_fact_old" type="hidden" id="num_fact_old" value="{num_fact}" /></td>
      <th class="vtabla">Entrada no. </th>
      <td class="vtabla"><input name="entrada" type="text" class="rinsert" id="entrada" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,fecha,num_fact,null,num_pro,fecha)" value="{entrada}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Fecha factura</th>
      <td class="vtabla" colspan="3"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,fecha_rec,null,null,num_fact,fecha_rec)" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Fecha recibido </th>
      <td class="vtabla" colspan="3"><input name="fecha_rec" type="text" class="insert" id="fecha_rec" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,concepto,null,null,fecha,fecha_inv)" value="{fecha_rec}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Fecha inventario </th>
      <td class="vtabla" colspan="3"><input name="fecha_inv" type="text" class="insert" id="fecha_inv" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,concepto,null,null,fecha_rec,concepto)" value="{fecha_inv}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Concepto</th>
      <td class="vtabla" colspan="3"><input name="concepto" type="text" class="vinsert" id="concepto" onkeydown="movCursor(event.keyCode,codgastos,null,null,fecha_rec,codgastos)" value="{concepto}" size="50" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">C&oacute;digo de Gasto </th>
      <td class="vtabla" colspan="3"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="movCursor(event.keyCode,importe,null,null,concepto,importe)" value="{codgastos}" size="3" />
        <input name="desc" type="text" class="vnombre" id="desc" value="{desc}" size="30" readonly="true" /></td>
    </tr>
    <tr>
      <td colspan="5" class="tabla" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="5" class="tabla" scope="row">Importe de Facura </th>
      </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Importe</th>
      <td class="rtabla" colspan="3"><input name="importe" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,con_dif_precio,null,null,codgastos,con_dif_precio)" value="{importe}" /></td>
    </tr>
    <tr>
	  <th colspan="2" class="vtabla" scope="row"><input name="apl_iva" type="checkbox" id="apl_iva" onchange="calculaTotal()" value="1" checked="checked" />
	    I.V.A.</th>
	  <th class="rtabla" scope="row" colspan="3"><input name="iva_fac" type="text" disabled="true" class="rnombre" id="iva_fac" style="color:#990000;" value="{iva_fac}" /></th>
	  </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Total Facturado </th>
      <th class="rtabla" colspan="3"><input name="total_fac" type="text" disabled="true" class="rnombre" id="total_fac" style="color:#0000CC;" value="{total_fac}" /></th>
    </tr>
    <tr class="tabla">
      <td colspan="5" class="vtabla" scope="row">&nbsp;</td>
      </tr>
    <tr id="faltantes_cell" style="display:none;">
      <td colspan="5" class="vtabla" scope="row"><table width="100%" align="center">
        <tr>
          <th class="tabla" scope="col">Modelo</th>
          <th class="tabla" scope="col">Color</th>
          <th class="tabla" scope="col">Talla</th>
          <th class="tabla" scope="col">Piezas</th>
          <th class="tabla" scope="col">Precio</th>
          <th class="tabla" scope="col">Importe</th>
          </tr>
        <!-- START BLOCK : faltante -->
		<tr>
          <td class="tabla"><input name="modelo[]" type="text" class="vinsert" id="modelo" onfocus="this.select()" onkeydown="movCursor(event.keyCode,color[{i}],null,color[{i}],modelo[{back}],modelo[{next}])" value="{modelo}" size="6" /></td>
          <td class="tabla"><input name="color[]" type="text" class="vinsert" id="color" onfocus="this.select()" onkeydown="movCursor(event.keyCode,talla[{i}],modelo[{i}],talla[{i}],color[{back}],color[{next}])" value="{color}" size="20" maxlength="30" /></td>
          <td class="tabla"><input name="talla[]" type="text" class="rinsert" id="talla" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,piezas[{i}],color[{i}],piezas[{i}],talla[{back}],talla[{next}])" value="{talla}" size="4" /></td>
          <td class="tabla"><input name="piezas[]" type="text" class="rinsert" id="piezas" onfocus="tmp.value=this.value;this.select()" onchange="if (numberFormat(this,0,true)) calculaFaltante({i})" onkeydown="movCursor(event.keyCode,precio[{i}],talla[{i}],precio[{i}],piezas[{back}],piezas[{next}])" value="{piezas}" size="8" /></td>
          <td class="tabla"><input name="precio[]" type="text" class="rinsert" id="precio" onfocus="tmp.value=this.value;this.select()" onchange="if (numberFormat(this,2,true)) calculaFaltante({i})" onkeydown="movCursor(event.keyCode,modelo[{next}],piezas[{i}],null,precio[{back}],precio[{next}])" value="{precio}" size="6" /></td>
          <td class="tabla"><input name="importe_fal[]" type="text" class="rnombre" id="importe_fal" value="{importe_fal}" size="10" readonly="true" /></td>
          </tr>
		  <!-- END BLOCK : faltante -->
      </table></td>
    </tr>
	<tr>
      <th colspan="2" class="vtabla" scope="row" onclick="showFal()" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">Faltantes</th>
      <th class="rtabla" scope="row" colspan="3"><input name="faltantes" type="text" class="rnombre" id="faltantes" value="{faltantes}" readonly="true" /></th>
	</tr>
	<tr>
	  <td colspan="5" class="tabla" scope="row">&nbsp;</td>
	  </tr>
	  <tr>
	  <th colspan="2" class="vtabla" scope="row">Diferencia de precio </th>
	  <td colspan="2" class="tabla"><input name="con_dif_precio" type="text" class="vinsert" id="con_dif_precio" onfocus="this.select()" onkeydown="movCursor(event.keyCode,dif_precio,null,dif_precio,null,con_desc1)" value="{con_dif_precio}" maxlength="100" /></td>
	  <td class="rtabla"><input name="dif_precio" type="text" class="rinsert" id="dif_precio" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,con_desc1,con_dif_precio,null,importe,desc1)" value="{dif_precio}" /></td>
	  </tr>
	  <tr>
	  <td colspan="5" class="tabla" scope="row">&nbsp;</td>
	  </tr>
	<tr>
      <th colspan="2" class="vtabla" scope="row">Descuento 1</th>
      <td colspan="2" class="tabla"><input name="con_desc1" type="text" class="vinsert" id="con_desc1" onfocus="this.select()" onkeydown="movCursor(event.keyCode,pdesc1,null,pdesc1,importe,con_desc2)" value="{con_desc1}" maxlength="100" /></td>
      <td class="rtabla">
        <input name="pdesc1" type="text" class="rinsert" id="pdesc1" style="font-weight:bold;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,desc1,con_desc1,desc1,importe,pdesc2)" value="{pdesc1}" size="5" />
        %        <input name="desc1" type="text" class="rinsert" id="desc1" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,con_desc2,pdesc1,null,importe,desc2)" value="{desc1}" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Descuento 2 </th>
      <td colspan="2" class="tabla"><input name="con_desc2" type="text" class="vinsert" id="con_desc2" onfocus="this.select()" onkeydown="movCursor(event.keyCode,pdesc2,null,pdesc2,con_desc1,con_desc3)" value="{con_desc2}" maxlength="100" /></td>
      <td class="rtabla">
        <input name="pdesc2" type="text" class="rinsert" id="pdesc2" style="font-weight:bold;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,desc2,con_desc2,desc2,pdesc1,pdesc3)" value="{pdesc2}" size="5" />
        %        <input name="desc2" type="text" class="rinsert" id="desc2" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,con_desc3,pdesc2,null,desc1,desc3)" value="{desc2}" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Descuento 3 </th>
      <td colspan="2" class="tabla"><input name="con_desc3" type="text" class="vinsert" id="con_desc3" onfocus="this.select()" onkeydown="movCursor(event.keyCode,pdesc3,null,pdesc3,con_desc2,con_desc4)" value="{con_desc3}" maxlength="100" /></td>
      <td class="rtabla">
        <input name="pdesc3" type="text" class="rinsert" id="pdesc3" style="font-weight:bold;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,desc3,con_desc3,desc3,pdesc2,pdesc4)" value="{pdesc3}" size="5" />
        %        <input name="desc3" type="text" class="rinsert" id="desc3" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,con_desc4,pdesc3,null,desc2,desc4)" value="{desc3}" /></td>
    </tr>
	<tr>
      <th colspan="2" class="vtabla" scope="row">Descuento 4 </th>
      <td colspan="2" class="tabla"><input name="con_desc4" type="text" class="vinsert" id="con_desc4" onfocus="this.select()" onkeydown="movCursor(event.keyCode,pdesc4,null,pdesc4,con_desc3,null)" value="{con_desc4}" maxlength="100" /></td>
      <td class="rtabla">
        <input name="pdesc4" type="text" class="rinsert" id="pdesc4" style="font-weight:bold;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,desc4,con_desc4,desc4,pdesc3,null)" value="{pdesc4}" size="5" />
        %        <input name="desc4" type="text" class="rinsert" id="desc4" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,fletes,pdesc4,null,desc3,fletes)" value="{desc4}" /></td>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Total a Descontar </th>
      <th class="rtabla" colspan="3"><input name="total_desc" type="text" class="rnombre" id="total_desc" style="font-size:12pt; color:#0000CC;" value="{total_desc}" readonly="true" /></th>
    </tr>
    <tr>
      <td colspan="5" class="vtabla" scope="row">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Sub Total</th>
      <th class="rtabla" colspan="3"><input name="subtotal" type="text" class="rnombre" id="subtotal" value="{subtotal}" /></th>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">I.V.A.</th>
      <th class="rtabla" colspan="3"><input name="iva" type="text" class="rnombre" id="iva" style="color:#990000;" value="{iva}" readonly="true" /></th>
    </tr>
	<tr>
      <th rowspan="2" class="vtabla" scope="row"><input name="apl_ret" type="checkbox" id="apl_ret" value="1" onclick="calculaTotal()" /></th>
      <th class="vtabla" scope="row">Retenci&oacute;n I.S.R. </th>
      <th colspan="4" class="rtabla" scope="row"><input name="pisr" type="text" class="rinsert" id="fletes4" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal()" onkeydown="movCursor(event.keyCode,pivaret,null,null,desc4,ivaret)" value="10.00" size="5" maxlength="5" />
        %        <input name="isr" type="text" class="rnombre" id="isr" style="color:#990000;" value="{isr}" readonly="true" /></th>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Retenci&oacute;n I.V.A. </th>
      <th colspan="4" class="rtabla" scope="row"><input name="pivaret" type="text" class="rinsert" id="fletes5" onfocus="tmp.value=this.value;this.select()" onchange="if (isFloat(this,2,tmp)) calculaTotal()" onkeydown="movCursor(event.keyCode,fletes,null,null,pisr,fletes)" value="10.00" size="5" maxlength="5" />
        %        <input name="ivaret" type="text" class="rnombre" id="ivaret" style="color:#990000;" value="{ivaret}" readonly="true" /></th>
      </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Fletes</th>
      <th colspan="4" class="rtabla" scope="row"> <input name="fletes" type="text" class="rinsert" id="fletes" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,otros,null,null,desc4,otros)" value="{fletes}" /></th>
      </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Otros</th>
      <th colspan="4" class="rtabla" scope="row"><input name="con_otros" type="text" class="vinsert" id="con_otros" onfocus="this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,null,null,null,fletes,null)" value="{con_otros}" size="30" maxlength="255" />        <input name="otros" type="text" class="rinsert" id="otros" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,null,con_otros,null,fletes,null)" value="{otros}" /></th>
    </tr>
    <tr>
      <th colspan="2" class="vtabla" scope="row">Total a Pagar </th>
      <th class="rtabla" colspan="3"><input name="total" type="text" class="rnombre" id="total" style="font-size:14pt; color:#0000CC;" value="{total}" readonly="true" /></th>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" onclick="validar()" value="Siguiente" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array(), cod = new Array(), hidden = true;
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = new Array('{nombre}', '{con1}', {desc1}, '{con2}', {desc2}, '{con3}', {desc3}, '{con4}', {desc4}, {clave});
<!-- END BLOCK : pro -->
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

function showFal() {
	hidden = !hidden;
	
	if (hidden)
		document.getElementById("faltantes_cell").style.display = "none";
	else
		document.getElementById("faltantes_cell").style.display = "table-row";
}

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
		f.pdesc1.value = '';
		f.pdesc2.value = '';
		f.pdesc3.value = '';
		f.pdesc4.value = '';
		f.desc1.value = '';
		f.desc2.value = '';
		f.desc3.value = '';
		f.desc4.value = '';
		f.con_desc1.value = '';
		f.con_desc2.value = '';
		f.con_desc3.value = '';
		f.con_desc4.value = '';
		f.con_dif_precio.value = '';
		f.dif_precio.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null) {
		f.nombre_pro.value = pro[get_val(f.num_pro)][0];
		f.con_desc1.value = pro[get_val(f.num_pro)][1];
		f.pdesc1.value = pro[get_val(f.num_pro)][2] > 0 ? pro[get_val(f.num_pro)][2] : '';
		f.con_desc2.value = pro[get_val(f.num_pro)][3];
		f.pdesc2.value = pro[get_val(f.num_pro)][4] > 0 ? pro[get_val(f.num_pro)][4] : '';
		f.con_desc3.value = pro[get_val(f.num_pro)][5];
		f.pdesc3.value = pro[get_val(f.num_pro)][6] > 0 ? pro[get_val(f.num_pro)][6] : '';
		f.con_desc4.value = pro[get_val(f.num_pro)][7];
		f.pdesc4.value = pro[get_val(f.num_pro)][8] > 0 ? pro[get_val(f.num_pro)][8] : '';
	}
	else {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	
	validaFac();
	calculaTotal();
}

function cambiaCod() {
	if (f.codgastos.value == '' || f.codgastos.value == '0') {
		f.codgastos.value = '';
		f.desc.value = '';
	}
	else if (cod[get_val(f.codgastos)] != null)
		f.desc.value = cod[get_val(f.codgastos)];
	else {
		alert('El código no se encuentra en el catálogo');
		f.codgastos.value = f.tmp.value;
		f.desc.select();
	}
}

function calculaFaltante(i) {
	var piezas, precio, importe;
	
	piezas = get_val(f.piezas[i]);
	precio = get_val(f.precio[i]);
	
	if (piezas > 0 && precio > 0) {
		importe = piezas * precio;
		f.importe_fal[i].value = numberFormat(importe, 2);
	}
	else
		f.importe_fal[i].value = '';
	
	totalFaltantes();
}

function totalFaltantes() {
	var total = 0, i;
	
	for (i = 0; i < f.importe_fal.length; i++)
		total += get_val(f.importe_fal[i]);
	
	f.faltantes.value = total > 0 ? numberFormat(total, 2) : '';
	
	calculaTotal();
}

function calculaTotal() {
	var total_fac, iva_fac, subtotal, importe, desc1, desc2, desc3, desc4, dif_precio, dif_precio, total_desc, faltantes, iva, pisr, pivaret, isr, ivaret, fletes, otros, total;
	
	importe = get_val(f.importe);
	iva_fac = f.apl_iva.checked ? importe * 0.15 : 0;
	total_fac = importe + iva_fac;
	f.iva_fac.value = iva_fac > 0 ? numberFormat(iva_fac, 2) : '';
	f.total_fac.value = total_fac > 0 ? numberFormat(total_fac, 2) : '';
	
	faltantes = get_val(f.faltantes);
	dif_precio = get_val(f.dif_precio);
	
	desc1 = get_val(f.pdesc1) > 0 ? Round((importe - faltantes - dif_precio) * get_val(f.pdesc1) / 100, 2) : get_val(f.desc1);
	desc2 = get_val(f.pdesc2) > 0 ? Round((importe - faltantes - dif_precio - desc1) * get_val(f.pdesc2) / 100, 2) : get_val(f.desc2);
	desc3 = get_val(f.pdesc3) > 0 ? Round((importe - faltantes - dif_precio - desc1 - desc2) * get_val(f.pdesc3) / 100, 2) : get_val(f.desc3);
	desc4 = get_val(f.pdesc4) > 0 ? Round((importe - faltantes - dif_precio - desc1 - desc2 - desc3) * get_val(f.pdesc4) / 100, 2) : get_val(f.desc4);
	total_desc = desc1 + desc2 + desc3 + desc4;
	f.desc1.value = desc1 > 0 ? numberFormat(desc1, 2) : '';
	f.desc2.value = desc2 > 0 ? numberFormat(desc2, 2) : '';
	f.desc3.value = desc3 > 0 ? numberFormat(desc3, 2) : '';
	f.desc4.value = desc4 > 0 ? numberFormat(desc4, 2) : '';
	f.total_desc.value = total_desc > 0 ? numberFormat(total_desc, 2) : '';
	
	subtotal = importe - faltantes - dif_precio - total_desc;
	iva = f.apl_iva.checked ? subtotal * 0.15 : 0;
	pisr = get_val(f.pisr);
	isr = f.apl_ret.checked ? subtotal * pisr / 100 : 0;
	pivaret = get_val(f.pivaret);
	ivaret = f.apl_ret.checked ? subtotal * pivaret / 100 : 0;
	fletes = get_val(f.fletes);
	otros = get_val(f.otros);
	total = subtotal + iva - isr - ivaret - fletes + otros;
	
	f.subtotal.value = subtotal > 0 ? numberFormat(subtotal, 2) : '';
	f.iva.value = iva > 0 ? numberFormat(iva, 2) : '';
	f.isr.value = isr > 0 ? numberFormat(isr, 2) : '';
	f.ivaret.value = ivaret > 0 ? numberFormat(ivaret, 2) : '';
	f.total.value = total > 0 ? numberFormat(total, 2) : '';
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

function validaFac() {
	if (get_val(f.num_pro) <= 0 || get_val(f.num_fact) <= 0)
		return false;
	else if (get_val(f.num_pro) == get_val(f.num_pro_old) && get_val(f.num_fact) == get_val(f.num_fact_old))
		return false;
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	// Pedir datos
	myConn.connect("./zap_fac_cap.php", "GET", 'num_pro=' + get_val(f.num_pro) + '&num_fact=' + get_val(f.num_fact), resultFac);
}

var resultFac = function (oXML) {
	var result = oXML.responseText;
	
	if (parseInt(result) == 0) {
		alert('Ya existe una factura con el mismo proveedor y número, Favor de verificar.');
		f.num_fact.value = f.num_fact_old.value;
		return false;
	}
}

function validar() {
	/*var patron = /(\d{1,2})\/(\d{1,2})\/(\d{2,4})/;
	var array = patron.test(f.fecha.value) ? patron.exec(f.fecha.value) : false;
	var ts_mov = array.length > 0 ? Date.UTC(array[3], array[2], array[1]) : false;
	var array = patron.test(f.fecha_rec.value) ? patron.exec(f.fecha_rec.value) : false;
	var ts_rec = array.length > 0 ? Date.UTC(array[3], array[2], array[1]) : false;*/
	var array = f.fecha.value.split('/');
	var ts_mov = array.length > 0 ? Date.UTC(parseInt(array[2], 10), parseInt(array[1], 10), parseInt(array[0], 10)) : false;
	var array = f.fecha_rec.value.split('/');
	var ts_rec = array.length > 0 ? Date.UTC(parseInt(array[2], 10), parseInt(array[1], 10), parseInt(array[0], 10)) : false;
	
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañia');
		f.num_cia.select();
		return false;
	}
	else if (get_val(f.num_pro) <= 0) {
		alert('Debe especificar el proveedor');
		f.num_pro.select();
		return false;
	}
	else if (get_val(f.num_fact) <= 0) {
		alert('Debe especificar el número de factura');
		f.num_fact.select();
		return false;
	}
	else if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha de la factura');
		f.fecha.select();
		return false;
	}
	else if (get_val(f.codgastos) == 33 && f.fecha_rec.value.length < 8) {
		alert('Debe especificar la fecha de entrega de mercancia');
		f.fecha_rec.select();
		return false;
	}
	else if (get_val(f.codgastos) == 33 && ts_rec < ts_mov) {
		alert('La fecha de entrega no puede ser menor a la fecha de facturación');
		f.fecha_rec.select();
		return false;
	}
	else if (get_val(f.codgastos) <= 0) {
		alert('Debe especificar el código de gasto');
		f.codgastos.select();
		return false;
	}
	else if (get_val(f.codgastos) == 33 && get_val(f.entrada) == 0) {
		alert('Debe poner el número de entrada');
		f.entrada.select();
		return false;
	}
	else if (get_val(f.total) <= 0) {
		alert('El total de la factura debe ser mayor a cero');
		f.importe.select();
		return false;
	}
	else if (pro[get_val(f.num_pro)][9] > 0 && get_val(f.iva) == 0 && get_val(f.clave) == 0) {
		alert('Para facturas sin I.V.A. debe especificar la clave de seguridad');
		f.clave.select();
		return false;
	}
	else if (pro[get_val(f.num_pro)][9] > 0 && get_val(f.iva) == 0 && pro[get_val(f.num_pro)][9] != get_val(f.clave)) {
		alert('La clave de seguridad no corresponde con el proveedor');
		f.clave.select();
		return false;
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		f.next.disabled = true;
		f.submit();
	}
}

window.onload = function() { showAlert = true; f.num_cia.select(); };
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
