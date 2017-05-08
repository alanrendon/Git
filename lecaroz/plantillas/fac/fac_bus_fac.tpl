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
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Facturas</p>
  <form action="./fac_bus_fac.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) fecha1.select()" value="{num_pro}" size="3" />
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="50" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_fact[0].select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Facturas</th>
      <td class="vtabla">
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[1].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[2].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[3].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[4].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[5].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[6].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[7].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[8].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[9].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[10].select()" size="5" />
	  <br />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[11].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[12].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[13].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[14].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[15].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[16].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[17].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[18].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[19].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[20].select()" size="5" />
	  <br />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[21].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[22].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[23].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[24].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[25].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[26].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[27].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[28].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_fact[29].select()" size="5" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="5" />	  </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estatus</th>
      <td class="vtabla"><input name="status" type="radio" value="0" checked="checked" />
        Todas<br />
        <input name="status" type="radio" value="1" />
        Pendientes<br />
        <input name="status" type="radio" value="2" />
        Pagadas<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="pag" type="radio" value="0" checked="checked" />
        Todas<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="pag" type="radio" value="1" />
        Pendientes<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="pag" type="radio" value="2" />
        Cobradas</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Por usuario </th>
      <td class="vtabla"><input name="user" type="checkbox" id="user" value="1"{checked} />
        Si</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()"{disabled} />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : pro -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no esta en el catálogo');
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
		alert('El proveedor no esta en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

function validar() {
	var cont = 0;
	
	for (var i = 0; i < f.num_fact.length; i++)
		cont += get_val(f.num_fact[i]) > 0 ? 1 : 0;
	
	/*if (get_val(f.num_pro) == 0) {
		alert('Debe especificar el proveedor');
		f.num_pro.select();
	}
	else if (cont == 0) {
		alert('Debe capturar al menos un número de factura');
		f.num_fact[0].select();
	}
	else*/
		f.submit();
}

window.onload = function () { f.num_cia.select(); showAlert = true; };
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Facturas </p>
  <table class="tabla">
    <!-- START BLOCK : prov -->
	<tr>
      <th colspan="10" class="vtabla" scope="col" style="font-size:12pt;"> {num_pro} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Gasto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Pagado</th>
      <th class="tabla" scope="col">Banco</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Cobrado</th>
      </tr>
    <!-- START BLOCK : fac -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
      <td class="tabla"{detalle}>{num_fact}</td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{num_cia} {nombre} </td>
      <td class="vtabla">{concepto}</td>
      <td class="vtabla"{edit}>{codgastos} {desc} </td>
      <td class="rtabla">{importe}</td>
      <td class="tabla">{fecha_cheque}</td>
      <td class="tabla">{banco}</td>
      <td class="tabla">{folio}</td>
      <td class="tabla">{fecha_con}</td>
      </tr>
	<!-- END BLOCK : fac -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');"{detalle}>
	  <th colspan="5" class="rtabla">Total</th>
	  <th class="rtabla" style="font-size:12pt;">{total}</th>
	  <th colspan="4" class="tabla">&nbsp;</th>
	  </tr>
	<tr>
	  <td colspan="10" class="tabla">&nbsp;</td>
	  </tr>
	<!-- END BLOCK : prov -->
	<!-- START BLOCK : no_fac -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
	  <td class="tabla">{num_fact}</td>
	  <td colspan="9" class="tabla">NO SE ENCONTRO LA FACTURA </td>
	  </tr>
	<!-- END BLOCK : no_fac -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./fac_bus_fac.php'" />
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function detalle(num_cia, num_pro, num_fact, tipo) {
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600';
	var url = './fac_fac_det.php?num_cia=' + num_cia + '&num_pro=' + num_pro + '&num_fact=' + num_fact + '&tipo=' + tipo;
	var win = window.open(url, 'detalle', opt);
	win.focus();
}

function edit(id) {
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=200';
	var url = './fac_gas_minimod_v2.php?id=' + id;
	var win = window.open(url, 'mod_gas', opt);
	win.focus();
}
-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
