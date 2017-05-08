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
<td align="center" valign="middle">
<form action="./zap_con_des_mod.php" method="post" name="form"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Tipo</th>
  </tr>
  <tr>
    <td class="tabla"><input name="cod" type="hidden" id="cod" value="{cod}" />
      {cod}</td>
    <td class="tabla">{concepto}</td>
    <td class="tabla"><input name="tipo" type="radio" value="1"{tipo_1} />
      Compra
        <input name="tipo" type="radio" value="2"{tipo_2} />
        Pago</td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Modificar" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}
//-->
</script>
<!-- END BLOCK : datos -->
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
