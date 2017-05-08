<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : obs -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <td class="tabla" style="font-weight:bold;">{num_emp} {nombre} </td>
  </tr>
</table>
  <form action="fac_tra_obs.php" method="post" name="form"><p>
    <input name="id" type="hidden" id="id" value="{id}" />
      <textarea name="obs" cols="60" rows="8" class="insert" id="obs">{obs}</textarea>
  </p>
  <p>
    <input name="Cancelar" type="button" class="boton" id="Cancelar" value="Cancelar" onclick="self.close()" />
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

window.onload = f.obs.focus();
//-->
</script>
<!-- END BLOCK : obs -->
<!-- START BLOCK : close -->
<script language="javascript" type="text/javascript">
<!--
window.onload = self.close();
//-->
</script>
<!-- END BLOCK : close -->
</body>
</html>
