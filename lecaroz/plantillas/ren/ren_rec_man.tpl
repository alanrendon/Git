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
<td align="center" valign="middle"><p class="title">Recibos de Rentas Manuales</p>
  <form action="./ren_rec_man.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="id" type="hidden" id="id" value="{id}"> 
    <input name="arr" type="hidden" id="arr" value="{arr}">   
    <input type="hidden" name="tipo_local" id="tipo_local" value="{tipo_local}">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td colspan="2" class="vtabla"><input name="local" type="text" class="insert" id="local" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaLocal()" onKeyDown="if (event.keyCode == 13) recibo.select()" value="{local}" size="4">
        <input name="nombre_local" type="text" class="vnombre" id="nombre_local" value="{nombre_local}" size="30" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td colspan="2" class="vtabla"><input name="arrendador" type="text" class="vnombre" id="arrendador" style="width: 100%;" value="{arrendador}" size="30" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendatario</th>
      <td colspan="2" class="vtabla"><input name="arrendatario" type="text" class="vnombre" id="arrendatario" style="width: 100%;" value="{arrendatario}" size="30" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td colspan="2" class="vtabla"><input name="bloque" type="text" class="vnombre" id="bloque" style="width: 100%;" value="{bloque}" size="30" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Recibo </th>
      <td colspan="2" class="vtabla" style=" background-color: #{color};"><input name="recibo" type="text" class="insert" id="recibo" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" value="{recibo}" size="8"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td colspan="2" class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td colspan="2" class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) meses.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Meses a Pagar </th>
      <td colspan="2" class="vtabla"><input name="meses" type="text" class="insert" id="meses" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) calculaTotal()" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{meses}" size="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td colspan="2" class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto2" style="width: 100%;" onKeyDown="if (event.keyCode == 13) importe_renta.select()/*local.select()*/" value="{concepto}" size="30" maxlength="50"></td>
    </tr>
    <tr>
      <th rowspan="8" class="vtabla" scope="row">Datos del Recibo </th>
      <td class="vtabla"><!--<input name="renta" type="checkbox" id="renta" value="1" onClick="calculaTotal()" checked>-->
        Renta</td>
      <td class="vtabla"><input name="importe_renta" type="text" class="rinsert" id="importe_renta" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this, 2, true)) calculaTotalCampos()" onKeyDown="if (event.keyCode == 13) importe_mant.select()" value="{importe_renta}" size="8"></td>
    </tr>
    <tr>
      <td class="vtabla"><!--<input name="mant" type="checkbox" id="mant" value="1" onClick="calculaTotal()" checked>-->
        Mantenimiento</td>
      <td class="vtabla"><input name="importe_mant" type="text" class="rinsert" id="importe_mant" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaTotalCampos()" onKeyDown="if (event.keyCode == 13) importe_agua.select()" value="{importe_mant}" size="8"></td>
    </tr>
    <tr>
      <th class="vtabla">        Subtotal</th>
      <th class="vtabla"><input name="subtotal" type="text" class="rnombre" id="subtotal" style="width: 100%;" value="{subtotal}" size="8" readonly="true"></th>
    </tr>
    <tr>
      <td class="vtabla"><!--<input name="iva" type="checkbox" id="iva" value="1" onClick="calculaTotal()" checked>-->
        I.V.A.</td>
      <td class="vtabla"><input name="importe_iva" type="text" class="rinsert" id="importe_iva" style="width: 100%;" value="{importe_iva}" size="8" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><!--<input name="agua" type="checkbox" id="agua" value="1" onClick="calculaTotal()" checked>-->
        Agua</td>
      <td class="vtabla"><input name="importe_agua" type="text" class="rinsert" id="importe_agua" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaTotalCampos()" onKeyDown="if (event.keyCode == 13) local.select()" value="{importe_agua}" size="8"></td>
    </tr>
    <tr>
      <td class="vtabla"><!--<input name="ret_iva" type="checkbox" id="ret_iva" value="1" onClick="ret_isr.checked=this.checked;calculaTotal()" checked>-->
        Ret. I.V.A. </td>
      <td class="vtabla"><input name="importe_ret_iva" type="text" class="rinsert" id="importe_ret_iva" style="width: 100%;" value="{importe_ret_iva}" size="8" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><!--<input name="ret_isr" type="checkbox" id="ret_isr" value="1" onClick="ret_iva.checked=this.checked;calculaTotal()" checked>-->
        Ret. I.S.R.</td>
      <td class="vtabla"><input name="importe_ret_isr" type="text" class="rinsert" id="importe_ret_isr" style="width: 100%;" value="{importe_ret_isr}" size="8" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total" type="text" class="rnombre" id="total" style="width: 100%;" value="{total}" size="8" readonly="true"></th>
    </tr>
    <!--<tr>
      <th class="vtabla" scope="row">Total del Recibo </th>
      <th colspan="2" class="vtabla"><input name="total_recibo" type="text" class="rnombre" id="total_recibo" style="width: 100%;" value="{total_recibo}" size="8" readonly="true"></th>
      </tr>-->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, local = new Array();

<!-- START BLOCK : local -->
local[{local}] = new Array("{nombre_local}", "{arrendador}", "{arrendatario}", {renta}, {mant}, {agua}, {ret_iva}, {ret_isr}, "{bloque}", {id}, {arr}, {tipo_local});
<!-- END BLOCK : local -->

function cambiaLocal() {
	if (f.local.value == "" || f.local.value == "0") {
		f.local.value = "";
		f.nombre_local.value = "";
		f.arrendador.value = "";
		f.arrendatario.value = "";
		f.bloque.value = "";
		f.recibo.value = "";
		f.meses.value = "";
		f.importe_renta.value = "";
		f.importe_mant.value = "";
		f.subtotal.value = "";
		f.importe_iva.value = "";
		f.importe_agua.value = "";
		f.importe_ret_iva.value = "";
		f.importe_ret_isr.value = "";
		f.total.value = "";
		f.id.value = "";
		f.arr.value = "";
		f.tipo_local.value = '';
	}
	else if (local[f.local.value] != null) {
		f.id.value = local[f.local.value][9];
		f.arr.value = local[f.local.value][10];
		f.nombre_local.value = local[f.local.value][0];
		f.arrendador.value = local[f.local.value][1];
		f.arrendatario.value = local[f.local.value][2];
		f.bloque.value = local[f.local.value][8];
		f.tipo_local.value = local[f.local.value][11];
		f.importe_renta.value = local[f.local.value][3] != 0 ? number_format(local[f.local.value][3], 2) : "";
		f.importe_mant.value = local[f.local.value][4] != 0 ? number_format(local[f.local.value][4], 2) : "";
		f.importe_agua.value = local[f.local.value][5] != 0 ? number_format(local[f.local.value][5], 2) : "";
		//f.ret_iva.checked = local[f.local.value][6];
		//f.ret_isr.checked = local[f.local.value][7];
		
		var subtotal = 0, iva, isr, ret, total = 0;
		
		subtotal += /*f.renta.checked ? */local[f.local.value][3]/* : 0*/;
		subtotal += /*f.mant.checked ? */local[f.local.value][4]/* : 0*/;
		iva = /*f.iva.checked ? */iva = local[f.local.value][11] == 1 ? Round(subtotal * /*0.15*/0.16, 2) : 0/* : 0*/;
		agua = /*f.agua.checked ? */local[f.local.value][5]/* : 0*/;
		isr = /*f.ret_isr.checked ? */local[f.local.value][7] ? Round(/*local[f.local.value][3]*/subtotal * 0.10, 2) : 0;
		ret = /*f.ret_iva.checked ? */local[f.local.value][6] ? Round(/*local[f.local.value][3]*/subtotal * /*0.10*/0.1066, 2) : 0;
		total = Round(subtotal + iva + agua - isr - ret, 2);
		
		f.subtotal.value = number_format(subtotal, 2);
		f.importe_iva.value = iva != 0 ? number_format(iva, 2) : "";
		f.importe_ret_isr.value = isr != 0 ? number_format(isr, 2) : "";
		f.importe_ret_iva.value = ret != 0 ? number_format(ret, 2) : "";
		f.total.value = number_format(total, 2);
		//f.total_recibo.value = number_format(total, 2);
	}
	else {
		alert("El local no se encuentra en el catálogo");
		f.local.value = f.tmp.value;
		f.local.select();
	}
}

function calculaTotal() {
	var renta, mant, subtotal, iva, agua, ret, isr, total, meses;
	
	meses = get_val(f.meses) > 1 ? get_val(f.meses) : 1;
	
	renta = /*f.renta.checked ? get_val(f.importe_renta) : 0*/local[f.local.value][3] * meses;
	mant = /*f.mant.checked ? get_val(f.importe_mant) : 0*/local[f.local.value][4] * meses;
	subtotal = renta + mant;
	iva = /*f.iva.checked ? */iva = local[f.local.value][11] == 1 ? subtotal * /*0.15*/0.16 : 0;/* : 0*/;
	agua = /*f.agua.checked ? get_val(f.importe_agua) : 0*/local[f.local.value][5] * meses;
	ret = /*f.ret_iva.checked ? renta * 0.10 : 0*/local[f.local.value][6] ? renta * /*0.10*/0.10666666667 : 0;
	isr = /*f.ret_isr.checked ? renta * 0.10 : 0*/local[f.local.value][7] ? renta * 0.10 : 0;
	total = subtotal + iva + agua - isr - ret;
	//total_recibo = meses > 1 ? total * meses : total;
	
	f.subtotal.value = number_format(subtotal, 2);
	f.importe_iva.value = iva != 0 ? number_format(iva, 2) : "";
	f.importe_ret_iva.value = ret != 0 ? number_format(ret, 2) : "";
	f.importe_ret_isr.value = isr != 0 ? number_format(isr, 2) : "";
	f.total.value = number_format(total, 2);
	//f.total_recibo.value = number_format(total_recibo, 2);
}

function calculaTotalCampos() {
	var renta, mant, subtotal, iva, agua, ret, isr, total, meses;
	
	meses = get_val(f.meses) > 1 ? get_val(f.meses) : 1;
	
	renta = get_val(f.importe_renta);
	mant = get_val(f.importe_mant);
	subtotal = renta + mant;
	iva = local[f.local.value][11] == 1 ? subtotal * 0.16 : 0;;
	agua = get_val(f.importe_agua);
	ret = local[f.local.value][6] ? renta * 0.10666666667 : 0;
	isr = local[f.local.value][7] ? renta * 0.10 : 0;
	total = subtotal + iva + agua - isr - ret;
	//total_recibo = meses > 1 ? total * meses : total;
	
	f.subtotal.value = number_format(subtotal, 2);
	f.importe_iva.value = iva != 0 ? number_format(iva, 2) : "";
	f.importe_ret_iva.value = ret != 0 ? number_format(ret, 2) : "";
	f.importe_ret_isr.value = isr != 0 ? number_format(isr, 2) : "";
	f.total.value = number_format(total, 2);
	//f.total_recibo.value = number_format(total_recibo, 2);
}

function validar() {
	if (f.local.value <= 0) {
		alert("Debe especificar el local");
		f.local.select();
		return false;
	}
	else if (f.recibo.value <= 0) {
		alert("Debe espcificar el número de recibo");
		f.recibo.select();
		return false;
	}
	else if (f.anio.value <= 0) {
		alert("Debe especificar el año del recibo");
		f.anio.select();
		return false;
	}
	else if (get_val(f.total) <= 0) {
		alert("El importe total del recibo no puede ser 0");
		f.renta.select();
		return false;
	}
	else if (confirm("¿Son correctos todos los datos?"))
		f.submit();
	else
		f.local.select();
}

function Round(Numero, decimales)
{
  //Convertimos el valor a entero de acuerdo al # de decimales
  Valor = Numero;
  ndecimales = Math.pow(10, decimales);
  Valor = Valor * ndecimales;

  //Redondeamos y luego dividimos por el # de decimales
  Valor = Math.round(Valor);
  Valor = Valor / ndecimales;
  return Valor;
}

window.onload = f.local.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : popup -->
<script language="javascript" type="text/javascript">
<!--
function popup() {
	var win;
	var url = "./recibo_renta.php?arr={arr}&ini={ini}&fin={fin}";
	win = window.open(url,"recibo","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600");
	document.location = "./ren_rec_man.php";
}

window.onload = popup();
//-->
</script>
<!-- END BLOCK : popup -->
</body>
</html>
