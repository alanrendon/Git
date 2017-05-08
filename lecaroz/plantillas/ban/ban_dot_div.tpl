<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : div -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><form action="./ban_dot_div.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <tr>
    <td class="tabla"><input name="id" type="hidden" id="id" value="{id}" />
      <input name="fecha" type="text" class="nombre" id="fecha" value="{fecha}" size="10" maxlength="10" readonly="true" /></td>
    <td class="tabla"><input name="importe" type="text" class="rnombre" id="importe" value="{importe}" size="10" readonly="true" /></td>
  </tr>
  <tr>
    <td colspan="2" class="tabla">&nbsp;</td>
    </tr>
  <tr>
    <th colspan="2" class="tabla">Dividir</th>
    </tr>
  <tr>
    <td class="tabla"><input name="fecha_div[]" type="text" class="insert" id="fecha_div" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,importe_div[0],null,importe_div[0],fecha_div[2],fecha_div[1])" value="{fecha}" size="10" maxlength="10" /></td>
    <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,fecha_div[1],fecha_div[0],null,importe_div[2],importe_div[1])" size="10" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="fecha_div[]" type="text" class="insert" id="fecha_div" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,importe_div[1],null,importe_div[1],fecha_div[0],fecha_div[2])" value="{fecha}" size="10" maxlength="10" /></td>
    <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,fecha_div[2],fecha_div[1],null,importe_div[0],importe_div[2])" size="10" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="fecha_div[]" type="text" class="insert" id="fecha_div" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,importe_div[2],null,importe_div[2],fecha_div[1],fecha_div[0])" value="{fecha}" size="10" maxlength="10" /></td>
    <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,fecha_div[0],fecha_div[2],null,importe_div[1],importe_div[0])" size="10" /></td>
  </tr>
  <tr>
    <th class="tabla">Total</th>
    <th class="tabla"><input name="total" type="text" disabled="disabled" class="rnombre" id="total" value="0.00" size="10" /></th>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Dividir" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function calculaTotal() {
	var total = 0;
	
	for (var i = 0; i < f.importe_div.length; i++)
		total += get_val(f.importe_div[i]);
	
	f.total.value = numberFormat(total, 2);
}

function validar() {
	if (get_val(f.importe) != get_val(f.total)) {
		alert('La suma total de los importes debe ser igual al deposito original');
		f.importe_div[0].select();
		return false;
	}
	
	for (var i = 0; i < f.fecha_div.length; i++)
		if (f.fecha_div[i].value.length < 8 && get_val(f.importe_div[i]) > 0) {
			alert('Debe especificar la fecha del importe ' + f.importe_div[i].value);
			f.fecha_div[i].select();
			return false;
		}
	
	if (confirm('¿Desea dividir el depósito?'))
		f.submit();
	else
		f.fecha_div[0].select();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = f.fecha_div[0].select();
//-->
</script>
<!-- END BLOCK : div -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
