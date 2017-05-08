<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./fac_gas_minimod_v2.php" method="post" name="form">
<input name="tmp" type="hidden" id="tmp" />
<input name="id" type="hidden" id="id" value="{id}" />
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo</th>
    <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaGasto()" value="{cod}" size="4" />
      <input name="desc" type="text" disabled="true" class="vnombre" id="desc" value="{desc}" size="50" /></td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Actualizar" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array();
<!-- START BLOCK : gasto -->
gasto[{cod}] = '{desc}';
<!-- END BLOCK : gasto -->

function cambiaGasto() {
	if (f.codgastos.value == '' || f.codgastos.value == '0') {
		f.codgastos.value = '';
		f.desc.value = '';
	}
	else if (gasto[get_val(f.codgastos)] != null)
		f.desc.value = gasto[get_val(f.codgastos)];
	else {
		alert('El código no se encuentra el catálogo de gastos');
		f.codgastos.value = f.tmp.value;
		f.codgastos.select();
	}
}

function validar() {
	if (get_val(f.codgastos) <= 0) {
		alert('Debe especificar el código de gasto');
		f.codgastos.select();
		return false;
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.codgastos.select();
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
