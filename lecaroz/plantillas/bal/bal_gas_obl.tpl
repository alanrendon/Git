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
<td align="center" valign="middle"><p class="title">Captura de Gastos Obligatorios</p>
  <form action="./bal_gas_obl.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Gasto</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="if (event.keyCode == 13) codgastos[{i}].select()" size="3">
        <input name="nombre[]" type="text" disabled="true" class="vnombre" id="nombre" size="20"></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="3">
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" size="20"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), gasto = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : gasto -->
gasto[{cod}] = "{desc}";
<!-- END BLOCK : gasto -->

function cambiaCia(i) {
	if (f.num_cia[i].value == "" || f.num_cia[i].value == "0")
		f.nombre[i].value = "";
	else if (cia[f.num_cia[i].value] != null)
		f.nombre[i].value = cia[f.num_cia[i].value];
	else {
		alert("El número de compañía no se encuentra en el catálogo");
		f.num_cia[i].value = f.tmp.value;
	}
}

function cambiaGasto(i) {
	if (f.codgastos[i].value == "" || f.codgastos[i].value == "0")
		f.desc[i].value = "";
	else if (gasto[f.codgastos[i].value] != null)
		f.desc[i].value = gasto[f.codgastos[i].value];
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		f.codgastos[i].value = f.tmp.value;
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.num_cia[0].select();
}

window.onload = f.num_cia[0].select();
//-->
</script>
</body>
</html>
