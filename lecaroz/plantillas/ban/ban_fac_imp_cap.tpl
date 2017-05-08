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
<td align="center" valign="middle"><p class="title">Captura de Importes de Facturas</p>
  <form action="./ban_fac_imp_cap.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
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
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function validar() {
	if (get_val(f.num_cia) == 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.anio) == 0) {
		alert('Debe especificar el año');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Importes de Facturas</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{anio}</td>
    </tr>
  </table>  
  <br />
  <form action="./ban_fac_imp_cap.php" method="post" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
    <input name="mes" type="hidden" id="mes" value="{mes}" />
    <input name="anio" type="hidden" id="anio" value="{anio}" />
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">D&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="dia[]" type="hidden" id="dia" value="{dia}" />
        {dia}</td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,tmp)) calculaTotal()" onkeydown="movCursor(event.keyCode,importe[{next}],null,null,importe[{back}],importe[{next}])" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th class="tabla">Total</th>
      <th class="tabla"><input name="total" type="text" disabled="disabled" class="rnombre" id="total" value="{total}" size="10" /></th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='./ban_fac_imp_cap.php'" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function calculaTotal() {
	var total = 0;
	
	for (var i = 0; i < f.dia.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = numberFormat(total, 2);
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.importe[0].select();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

window.onload = f.importe[0].select();
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
