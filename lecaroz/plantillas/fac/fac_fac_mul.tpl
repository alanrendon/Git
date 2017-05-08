<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Facturas</p>
  <form action="./fac_fac_mul.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">

    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" />
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" size="30" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) codgastos.select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="if (event.keyCode == 13) concepto.select()" size="3" />
        <input name="desc" type="text" class="vnombre" id="desc" size="30" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onkeydown="if (event.keyCode == 13) importe.select()" size="30" maxlength="100" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,tmp)) calculaTotal()" onkeydown="if (event.keyCode == 13) total.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="apl_iva" type="checkbox" id="apl_iva" onclick="calculaTotal()" value="1" />
        I.V.A.</th>
      <td class="vtabla"><input name="iva" type="text" class="rnombre" id="iva" size="10" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="apl_ret_iva" type="checkbox" id="apl_ret_iva" onclick="calculaTotal()" value="1" />
        Ret. I.V.A. </th>
      <td class="vtabla"><input name="ret_iva" type="text" class="rnombre" id="ret_iva" size="10" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="apl_ret_isr" type="checkbox" id="apl_ret_isr" onclick="calculaTotal()" value="1" />
        Ret. I.S.R. </th>
      <td class="vtabla"><input name="ret_isr" type="text" class="rnombre" id="ret_isr" size="10" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Total</th>
      <td class="vtabla"><input name="total" type="text" class="rinsert" id="total" style="font-size:12pt; font-weight:bold;" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13) num_pro.select()" size="10" />
      <a href="javascript:ajustar(0.01)" style="color:#00C;text-decoration:none;">[+]</a><a href="javascript:ajustar(-0.01)" style="color:#C00;text-decoration:none;">[-]</a></td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, pro = new Array(), cod = new Array();
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

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
		f.codgastos.select();
	}
}

function calculaTotal() {
	if (get_val(f.importe) <= 0) {
		f.iva.value = '';
		f.ret_iva.value = '';
		f.ret_isr.value = '';
		f.total.value = '';
	}
	else {
		var total = 0, iva = 0, ret = 0, importe = get_val(f.importe);

		iva = f.apl_iva.checked ? Round(importe * /*0.15*/0.16, 2) : 0;
		ret_iva = f.apl_ret_iva.checked ? Round(importe * /*0.10*/0.1066666667, 2) : 0;
		ret_isr = f.apl_ret_isr.checked ? Round(importe * 0.10, 2) : 0;
		total = importe + iva - ret_iva - ret_isr;

		f.iva.value = iva > 0 ? numberFormat(iva, 2) : '';
		f.ret_iva.value = ret_iva > 0 ? numberFormat(ret_iva, 2) : '';
		f.ret_isr.value = ret_isr > 0 ? numberFormat(ret_isr, 2) : '';
		f.total.value = numberFormat(total, 2);
	}
}

function Round(num, dec)
{
  var val = num;
  ndec = Math.pow(10, dec);
  val = val * ndec;

  val = Math.round(val);
  val = val / ndec;

  return val;
}

function ajustar(dif) {
	var total = get_val(f.total);
	var iva = get_val(f.iva);

	iva += dif;
	total += dif;

	f.iva.value = iva > 0 ? numberFormat(iva, 2) : '';
	f.total.value = numberFormat(total, 2);
}

function validar() {
	if (get_val(f.num_pro) <= 0) {
		alert('Debe especificar el proveedor');
		f.num_pro.select();
	}
	else if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha');
		f.fecha.select();
	}
	else if (get_val(f.codgastos) <= 0) {
		alert('Debe especificar el gasto');
		f.codgastos.select();
	}
	else if (get_val(f.importe) <= 0) {
		alert('Debe especificar el importe');
		f.importe.select();
	}
	else if (get_val(f.total) <= 0) {
		alert('Debe especificar el total');
		f.total.select();
	}
	/*else if (get_val(f.importe) + get_val(f.iva) - get_val(f.ret) > get_val(f.total)) {
		alert('El total no puede ser menor a la suma del importe y los impuestos');
		f.total.select();
	}*/
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.num_pro.select();
}

window.onload = f.num_pro.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Facturas</p>
  <form action="./fac_fac_mul.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="num_pro" type="hidden" id="num_pro" value="{num_pro}" />
        {num_pro} {nombre_pro} </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="fecha" type="hidden" id="fecha" value="{fecha}" />
        {fecha}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="codgastos" type="hidden" id="codgastos" value="{codgastos}" />
        {codgastos} {desc} </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="concepto" type="hidden" id="concepto" value="{concepto}" />
        {concepto}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="importe" type="hidden" id="importe" value="{importe}" />
        {importe}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">I.V.A.</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="iva" type="hidden" id="iva" value="{iva}" />
        {iva}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Ret. I.V.A. </th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="ret_iva" type="hidden" id="ret_iva" value="{ret_iva}" />
        {ret_iva}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Ret. I.S.R. </th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="ret_isr" type="hidden" id="ret_isr" value="{ret_isr}" />
        {ret_isr}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Total</th>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="total" type="hidden" id="total" value="{total}" />
        {total}</td>
    </tr>
  </table>
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Factura</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,num_fact[{i}],null,num_fact[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      <td class="tabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g,'');this.value=this.value.toUpperCase();" onkeydown="movCursor(event.keyCode,num_cia[{next}],num_cia[{i}],null,num_fact[{back}],num_fact[{next}])" size="10" /></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./fac_fac_mul.php'" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
	}
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.num_cia[0].select();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}
//-->

window.onload = f.num_cia[0].select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
