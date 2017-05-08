<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Archivo para Portal Lecaroz</p>
  <form action="./ban_por_arc.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode==13)generar.focus()" size="5" /></td>
    </tr>
  </table>  
  <p>
    <input name="generar" type="button" class="boton" id="generar" value="Generar Archivo" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.folio) <= 0) {
		alert('Debe especificar el folio');
		f.folio.select();
	}
	else
		f.submit();
}

window.onload = f.folio.select();
//-->
</script>
</body>
</html>
