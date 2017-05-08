<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <form action="./ban_efe_red_v2.php" method="get" name="form" target="efectivos">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="alert" type="hidden" id="alert" value="1" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha de Corte </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) ok.focus()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
  </table>  <p>
    <!--<input name="" type="button" class="boton" onclick="self.close()" value="Cancelar" />
&nbsp;&nbsp;-->
<input name="ok" type="button" class="boton" id="ok" onclick="validar()" value="Aceptar" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha de corte');
		f.fecha.select();
	}
	else {
		var win = window.open("", "efectivos", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		f.submit();
		win.focus();
		self.close();
	}
}

window.onload = f.fecha.select();
//-->
</script>
</body>
</html>
