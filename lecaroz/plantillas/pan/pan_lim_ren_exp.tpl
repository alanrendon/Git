<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de L&iacute;mites de Renta de Expendios</p>
  <form action="./pan_lim_ren_exp.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
	<!-- START BLOCK : fila -->
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,nombre[{i}],null,nombre[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre_cia[]" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
      <td class="tabla"><input name="nombre[]" type="text" class="vinsert" id="nombre" onkeydown="movCursor(event.keyCode,importe[{i}],num_cia[{i}],importe[{i}],nombre[{back}],nombre[{next}])" size="50" maxlength="100" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,num_cia[{next}],nombre[{i}],null,importe[{back}], importe[{next}])" size="10" /></td>
    </tr>
	 <!-- END BLOCK : fila -->
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

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value == '';
		f.nombre_cia[i].value == '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre_cia[i].value = cia[get_val(f.num_cia[i])];
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

window.onload = f.num_cia[0].select();
-->
</script>
</body>
</html>
