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
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Recibos de Renta</p>
  <form action="./ren_rec_can.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaArr()" onkeydown="if (event.keyCode == 13) num.select()" size="3" />
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Recibo</th>
      <td class="vtabla"><input name="num" type="text" class="insert" id="num" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) arr.select()" size="6" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()"{disabled} /> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, arr = new Array();
<!-- START BLOCK : arr -->
arr[{cod}] = '{nombre}';
<!-- END BLOCK : arr -->

function cambiaArr() {
	if (f.arr.value == '' || f.arr.value == '0') {
		f.arr.value = '';
		f.nombre.value = '';
	}
	else if (arr[get_val(f.arr)] != null)
		f.nombre.value = arr[get_val(f.arr)];
	else {
		alert('El arrendador no se encuentra en el catálogo');
		f.arr.value = f.tmp.value;
		f.arr.select();
	}
}

function validar() {
	if (get_val(f.arr) == 0) {
		alert('Debe especificar el código de arrendador')
		f.arr.select();
	}
	else if (get_val(f.num) == 0) {
		alert('Debe especificar el número de recibo');
		f.num.select();
	}
	else
		f.submit();
}

window.onload = f.arr.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Recibos de Renta</p>
  <form action="./ren_rec_can.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}" />
    <table class="tabla">
    <tr>
      <th colspan="10" class="vtabla" scope="col" style="font-size:12pt;">{cod_arr} {nombre} </th>
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
      <td class="tabla" style="font-weight:bold;">{num_recibo}</td>
      <td class="vtabla">{num_local} {nombre_local} </td>
      <td class="vtabla" style="font-weight:bold;">{art}</td>
      <td class="rtabla">{renta}</td>
      <td class="rtabla">{agua}</td>
      <td class="rtabla">{mant}</td>
      <td class="rtabla">{iva}</td>
      <td class="rtabla">{ret_isr}</td>
      <td class="rtabla">{ret_iva}</td>
      <td class="rtabla" style="font-weight:bold;">{neto}</td>
    </tr>
    <!-- START BLOCK : concepto -->
	<tr>
      <td colspan="10" class="vtabla">{concepto}</td>
      </tr>
	  <!-- END BLOCK : concepto -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./ren_rec_can.php'" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Cancelar Recibo" onclick="validar()" /> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (confirm('¿Desea cancelar el recibo?'))
		f.submit();
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
