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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Validaci&oacute;n Copia Original de Factura</p>
  <form action="./zap_cop_fac.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="if (event.keyCode == 13) num_pro[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" size="30" readonly="true"></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) num_fact[{i}].select()" size="3" maxlength="4">
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" size="30" readonly="true"></td>
      <td class="tabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" onFocus="tmp.value=this.value;this.select()" onChange="this.value=this.value.trim().toUpperCase();" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="8"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->

function cambiaCia(i) {
	if (f.num_cia[i].value == "" || f.num_cia[i].value == "0") {
		f.num_cia[i].value = "";
		f.nombre_cia[i].value = "";
	}
	else if (cia[f.num_cia[i].value] != null) {
		f.nombre_cia[i].value = cia[f.num_cia[i].value];
		if (i > 0) {
			f.num_pro[i].value = f.num_pro[i - 1].value;
			f.nombre_pro[i].value = f.nombre_pro[i - 1].value;
		}
	}
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia[i].value = tmp.value;
		f.num_cia[i].select();
	}
}

function cambiaPro(i) {
	if (f.num_pro[i].value == "" || f.num_pro[i].value == "0") {
		f.num_pro[i].value = "";
		f.nombre_pro[i].value = "";
	}
	else if (pro[f.num_pro[i].value] != null)
		f.nombre_pro[i].value = pro[f.num_pro[i].value];
	else {
		alert("El proveedor no se encuentra en el catálogo");
		f.num_pro[i].value = tmp.value;
		f.num_pro[i].select();
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.num_cia[0].select();
}

window.onload = f.num_cia[0].select();
-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : errores -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt;">Las siguientes facturas no corresponden a la compa&ntilde;&iacute;a capturada </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a Factura </th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a Capturada </th>
    </tr>
    <!-- START BLOCK : fac -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{num_pro} {nombre_pro}</td>
      <td class="tabla">{factura}</td>
      <td class="vtabla">{num_cia_fac} {nombre_cia_fac} </td>
      <td class="vtabla">{num_cia_cap} {nombre_cia_cap} </td>
    </tr>
	<!-- END BLOCK : fac -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="document.location='./zap_cop_fac.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : errores -->
</body>
</html>
