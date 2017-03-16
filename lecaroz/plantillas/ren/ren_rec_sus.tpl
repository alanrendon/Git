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
<td align="center" valign="middle"><p class="title">Sustituci&oacute;n de Recibos</p>
  <form action="./ren_rec_sus.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_recibo.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Recibo</th>
      <td class="vtabla"><input name="num_recibo" type="text" class="insert" id="num_recibo" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) arr.select()" size="5" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" /> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.arr) == 0) {
		alert('Debe especificar el código de arrendador');
		f.arr.select();
		return false;
	}
	else if (get_val(f.num_recibo) == 0) {
		alert('Debe especificar el número de recibo');
		f.num_recibo.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.arr.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : recibo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Sustituci&oacute;n de Recibos</p>
  <form action="./ren_rec_sus.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th colspan="10" class="tabla" scope="col"><input name="cod_arr" type="hidden" id="cod_arr" value="{cod_arr}" />
        {cod_arr} {nombre_arr} </th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Recibo</th>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Arrendatario</th>
      <th class="tabla" scope="col">Renta</th>
      <th class="tabla" scope="col">Agua</th>
      <th class="tabla" scope="col">Mantenimiento</th>
      <th class="tabla" scope="col">I.V.A.</th>
      <th class="tabla" scope="col">I.S.R.<br />
        Retenido</th>
      <th class="tabla" scope="col">I.V.A.<br />
        Retenido</th>
      <th class="tabla" scope="col">Neto</th>
    </tr>
    <tr>
      <td class="tabla"><input name="idrecibo" type="hidden" id="idrecibo" value="{idrecibo}" />        <input name="num_recibo_old" type="hidden" id="num_recibo_old" value="{num_recibo}" />
        <input name="num_recibo" type="text" class="insert" id="num_recibo" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) renta.select();
else if (event.keyCode == 37) mantenimiento.select()" value="{num_recibo}" size="5" /></td>
      <td class="tabla"><input name="idlocal" type="hidden" id="idlocal" value="{idlocal}" />
        {num_local} {nombre_local} </td>
      <td class="tabla">{nombre_art} </td>
      <td class="tabla"><input name="renta" type="text" class="rinsert" id="renta" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,2,tmp)) calcula_neto()" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) agua.select();
else if (event.keyCode == 37) num_recibo.select()" value="{renta}" size="8" /></td>
      <td class="tabla"><input name="agua" type="text" class="rinsert" id="agua" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,2,tmp)) calcula_neto()" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) mantenimiento.select();
else if (event.keyCode == 37) renta.select()" value="{agua}" size="8" /></td>
      <td class="tabla"><input name="mantenimiento" type="text" class="rinsert" id="mantenimiento" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,2,tmp)) calcula_neto()" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_recibo.select();
else if (event.keyCode == 37) agua.select()" value="{mantenimiento}" size="8" /></td>
      <td class="tabla"><input name="iva" type="text" class="rnombre" id="iva" value="{iva}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="ret_isr" type="checkbox" id="ret_isr" onchange="calcula_neto()" value="1"{isr_checked} />        <input name="ret_isr_imp" type="text" class="rnombre" id="ret_isr_imp" value="{ret_isr_imp}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="ret_iva" type="checkbox" id="ret_iva" onchange="calcula_neto()" value="1"{iva_checked} />
        <input name="ret_iva_imp" type="text" class="rnombre" id="ret_iva_imp" value="{ret_iva_imp}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="neto" type="text" class="rnombre" id="neto" value="{neto}" size="8" readonly="true" /></td>
    </tr>
  </table>
  <!-- START BLOCK : error_folio -->
  <p style="font-family:Arial, Helvetica, sans-serif; font-size:14pt; color:#CC0000;">EL recibo no. {folio} ya existe en el sistema</p>
  <!-- END BLOCK : error_folio -->  
    <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./ren_rec_sus.php'" /> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Sustituir" onclick="validar()" /> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function calcula_neto() {
	var renta = get_val(f.renta),
	agua = get_val(f.agua),
	mant = get_val(f.mantenimiento),
	iva = 0, ret_iva = 0, ret_isr = 0, subtotal = 0, neto = 0;
	
	subtotal = renta + mant;
	iva = subtotal * 0.15;
	ret_isr = f.ret_isr.checked ? renta * 0.10 : 0;
	ret_iva = f.ret_iva.checked ? renta * 0.10 : 0;
	neto = subtotal + iva + agua - ret_isr - ret_iva;
	
	f.iva.value = number_format(iva, 2);
	f.ret_isr_imp.value = ret_isr != 0 ? number_format(ret_isr, 2) : null;
	f.ret_iva_imp.value = ret_iva != 0 ? number_format(ret_iva, 2) : null;
	f.neto.value = neto != 0 ? number_format(neto, 2) : null;
}

function validar() {
	if (get_val(f.num_recibo) == 0) {
		alert('Debe especificar el nuevo folio a usar');
		f.num_recibo.select();
		return false;
	}
	else if (get_val(f.neto) <= 0) {
		alert('El importe neto del recibo no puede ser menor o igual a cero');
		f.renta.select();
		return false
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.num_recibo.select();
}

window.onload = f.num_recibo.select();
//-->
</script>
<!-- END BLOCK : recibo -->
<!-- START BLOCK : impRecibo -->
<script language="javascript" type="text/javascript">
<!--
function imp(arr, ini, fin) {
	opt = "arr=" + arr + "&ini=" + ini + "&fin=" + fin;
	var win = window.open("./recibo_renta.php?" + opt, "rec", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
	win.focus();
	document.location = './ren_rec_sus.php';
}

window.onload = imp({arr}, {ini}, {fin});
//-->
</script>
<!-- END BLOCK : impRecibo -->
</body>
</html>
