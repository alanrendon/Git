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
<td align="center" valign="middle"><p class="title">Alta de Concepto de Descuento de Proveedores</p>
  <form action="zap_con_des_alta.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="cod" type="hidden" value="{cod}" />{cod}
        <input name="concepto" type="text" class="vinsert" id="concepto" onkeydown="if (event.keyCode == 13) this.blur()" size="30" maxlength="100" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked="checked" />
        Compra
          <input name="tipo" type="radio" value="2" />
          Pago</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Alta" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.concepto.value == '') {
		alert('Debe poner el concepto del descuento');
		f.concepto.select();
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.concepto.select();
}

window.onload = f.concepto.select();
//-->
</script>
</body>
</html>
