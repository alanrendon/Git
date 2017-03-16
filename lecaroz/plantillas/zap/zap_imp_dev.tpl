<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Vales de Devoluci&oacute;n</p>
  <form action="./zap_imp_dev.php" method="get" name="form" target="vales">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) this.blur()" size="5" /></td>
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
	if (get_val(f.folio) == 0 && confirm('¿Desea imprimir los últimos vales generados?')) {
		window.open("","vales","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600");
		f.submit();
	}
	else if (get_val(f.folio) > 0) {
		window.open("","vales","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600");
		f.submit();
	}
}

window.onload = f.folio.select();
//-->
</script>
</body>
</html>
