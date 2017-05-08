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
<td align="center" valign="middle"><p class="title">Notas de Cr&eacute;dito</p>
  <form action="./zap_not_cre_cap.php" method="get" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Nota</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,nota[{i}],null,nota[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="20" /></td>
      <td class="tabla"><input name="nota[]" type="text" class="insert" id="nota" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,num_pro[{i}],num_cia[{i}],num_pro[{i}],nota[{back}],nota[{next}])" size="4" /></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],nota[{i}],fecha[{i}],num_pro[{back}],num_pro[{next}])" size="3" />
        <input name="nombre_pro[]" type="text" disabled="true" class="vnombre" id="nombre_pro" size="20" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,importe[{i}],num_pro[{i}],importe[{i}],fecha[{back}],fecha[{next}])" size="10" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,con[{i}],fecha[{i}],con[{i}],importe[{back}],importe[{next}])" size="10" /></td>
      <td class="tabla"><input name="con[]" type="text" class="vinsert" id="con" onkeydown="movCursor(event.keyCode,codgastos[{i}],importe[{i}],codgastos[{i}],con[{back}],con[{next}])" size="20" /></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod({i})" onkeydown="movCursor(event.keyCode,num_cia[{next}],con[{i}],null,codgastos[{back}],codgastos[{next}])" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" size="20" /></td>
      </tr>
	  <!-- END BLOCK : fila -->
	  <tr>
	  <th colspan="4" class="rtabla">&nbsp;</th>
	  <th class="tabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" value="0.00" size="10" /></th>
	  <th colspan="2" class="tabla">&nbsp;</th>
	  </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" />
  </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array(), cod = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : pro -->
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre_cia[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre_cia[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se ecuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
	}
}

function cambiaPro(i) {
	if (f.num_pro[i].value == '' || f.num_pro[i].value == '0') {
		f.num_pro[i].value = '';
		f.nombre_pro[i].value = '';
	}
	else if (pro[get_val(f.num_pro[i])] != null)
		f.nombre_pro[i].value = pro[get_val(f.num_pro[i])];
	else {
		alert('El proveedor no se ecuentra en el catálogo');
		f.num_pro[i].value = f.tmp.value;
		f.num_pro[i].select();
	}
}

function cambiaCod(i) {
	if (f.codgastos[i].value == '' || f.codgastos[i].value == '0') {
		f.codgastos[i].value = '';
		f.desc[i].value = '';
	}
	else if (cod[get_val(f.codgastos[i])] != null)
		f.desc[i].value = cod[get_val(f.codgastos[i])];
	else {
		alert('El código no se ecuentra en el catálogo');
		f.codgastos[i].value = f.tmp.value;
		f.codgastos[i].select();
	}
}

function calculaTotal() {
	var i = 0, total = 0;
	
	for (i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = numberFormat(total, 2);
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = function() { showAlert = true; f.num_cia[0].select(); };
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
