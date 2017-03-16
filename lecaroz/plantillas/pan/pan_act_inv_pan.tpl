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
<td align="center" valign="middle"><p class="title">Actualizar Inventarios de Inicio de Mes<br />
  en Panader&iacute;as</p>
  <form action="pan_act_inv_pan.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" />
      <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
  </tr>
</table>
    <p>
    <input type="button" class="boton" value="Actualizar" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
}

function validar() {
	if (confirm('¿Desea actualizar los inventarios de panaderias?'))
		f.submit();
	//document.location = './pan_act_inv_pan.php?go=1';
}
{onload}
//-->
</script>
<!-- END BLOCK : datos -->
</body>
</html>
