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
<td align="center" valign="middle"><p class="title">Consulta de Conceptos de Descuentos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Tipo</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla">{cod}</td>
      <td class="vtabla">{concepto}</td>
      <td class="tabla" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" onclick="mod({cod})">{tipo}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function mod(cod) {
	var win = window.open("./zap_con_des_mod.php?cod=" + cod, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
	win.focus();
}
//-->
</script>
</body>
</html>
