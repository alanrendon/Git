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
<td align="center" valign="middle"><p class="title">Reimpresi&oacute;n de P&oacute;lizas </p>
  <form action="./ban_rei_pol.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="row">Compa&ntilde;&iacute;a</th>
      <th class="tabla">Banco</th>
      <th class="tabla">Folio</th>
    </tr>
    <!-- START BLOCK : fila_cap -->
	<tr>
      <td class="tabla" scope="row"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,folio[{i}],null,folio[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" class="vnombre" id="nombre" size="20" /></td>
      <td class="tabla"><select name="banco[]" class="insert" id="banco">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,num_cia[{next}],num_cia[{i}],null,folio[{back}],folio[{next}])" size="8" /></td>
    </tr>
	<!-- END BLOCK : fila_cap -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre[i].value = '';
		f.folio[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia[i].value = f.tmp.value;
		return false;
	}
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

window.onload = f.num_cia[0].select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Reimpresi&oacute;n de P&oacute;lizas</p>
  <p>&nbsp;</p></td>
</tr>
</table>
<!-- END BLOCK : result -->
</body>
</html>
