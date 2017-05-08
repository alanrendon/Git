<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cortes de Caja por Panader&iacute;a</p>
  <form action="./pan_num_cor.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cortes</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia} {nombre} </td>
      <td class="tabla"><input name="cortes[]" type="text" class="insert" id="cortes" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cortes[{next}].select();
else if (event.keyCode == 38) cortes[{back}].select();" value="{cortes}" size="3" maxlength="1"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Actualizar" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (confirm("¿Desea actualizar los cortes?"))
		f.submit();
	else
		f.cortes[0].select();
}

window.onload = f.cortes[0].select();
//-->
</script>
</body>
</html>
