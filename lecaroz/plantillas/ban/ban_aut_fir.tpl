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
<td align="center" valign="middle"><p class="title">Carta de Autorizaci&oacute;n de Firmas para Chequeras </p>
  <form action="./ban_aut_fir.php" method="get" name="form" target="carta">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Rango de cuentas </th>
      <td class="vtabla"><input name="num_cia1" type="text" class="insert" id="num_cia1" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia2.select()" size="3" />
        (a
          <input name="num_cia2" type="text" class="insert" id="num_cia2" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia1.select()" size="3" />
          )</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><input name="cuenta" type="radio" value="1" checked="checked" />
        Banorte<br />
        <input name="cuenta" type="radio" value="2" />
        Santander</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="abrir()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function abrir() {
	win = window.open("","carta","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600");
	f.submit();
	win.focus();
}

window.onload = f.num_cia1.select();
//-->
</script>
</body>
</html>
