<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Borrar D&iacute;as</p>
  <form action="./zap_bor_dia.php" method="post" name="form">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <!-- START BLOCK : cia -->
	<!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="dia[]" type="checkbox" id="dia" value="{dia}" /></td>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size:12pt; font-weight:bold;">{fecha}</td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="3" class="tabla">&nbsp;</td>
	  </tr>
	  <!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" onclick="validar()" value="Borrar"{disabled} />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (!f.dia) return false;
	
	var cont = 0;
	
	if (f.dia.length == undefined)
		cont += f.dia.checked ? 1 : 0;
	else
		for (var i = 0; i < f.dia.length; i++)
			cont += f.dia[i].checked ? 1 : 0;
	
	if (cont == 0)
		alert('Debe seleccionar al menos un registro');
	else if (confirm('¿Desea borrar los registros seleccionados?'))
		f.submit();
}
-->
</script>
</body>
</html>
