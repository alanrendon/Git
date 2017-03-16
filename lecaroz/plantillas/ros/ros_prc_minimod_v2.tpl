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
<!-- START BLOCK : precios -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Precios de Compra</p>
  <form action="./ros_prc_minimod_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Precio<br>
        Actual</th>
      <th class="tabla" scope="col">Nuevo<br>
        Precio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value" onChange="if (isInt(this,tmp)) cambiaMP({i})" onKeyDown="if (event.keyCode == 13) precio_nuevo[{i}].select()" size="3"></td>
      <td class="tabla"><input name="nombre_mp[]" type="text" disabled="true" class="vnombre" id="nombre_mp" size="30"></td>
      <td class="tabla"><input name="precio_actual[]" type="text" disabled="true" class="rnombre" id="precio_actual" size="8"></td>
      <td class="tabla"><input name="precio_nuevo[]" type="text" class="rinsert" id="precio_nuevo" onFocus="tmp.value=this.value" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) codmp[{next}].select()" size="8"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var omp = window.opener.mp, mp = new Array(), form = document.form;

<!-- START BLOCK : mp -->
mp[{codmp}] = new Array();
mp[{codmp}]["nombre"] = "{nombre}";
mp[{codmp}]["precio"] = {precio};
mp[{codmp}]["min"] = {min};
mp[{codmp}]["max"] = {max};
<!-- END BLOCK : mp -->

function cambiaMP(i) {
	if (form.codmp[i].value == "") {
		form.nombre_mp[i].value = "";
		form.precio_actual[i].value = "";
	}
	else if (mp[form.codmp[i].value] != null) {
		form.nombre_mp[i].value = mp[form.codmp[i].value]["nombre"];
		form.precio_actual[i].value = !isNaN(parseFloat(mp[form.codmp[i].value]["precio"])) ? mp[form.codmp[i].value]["precio"].toFixed(2) : mp[form.codmp[i].value]["precio"];
	}
	else {
		alert("El producto no se encuentra en el catálogo");
		form.num_pro[i].value = form.tmp.value;
		form.num_pro[i].select();
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?")) {
		for (i = 0; i < form.codmp.length; i++) {
			if (form.codmp[i].value > 0 && form.precio_nuevo[i].value > 0) {
				if (omp[form.codmp[i].value] != null) {
					omp[form.codmp[i].value]["precio"] = parseFloat(form.precio_nuevo[i].value);
				}
				else {
					omp[form.codmp[i].value] = new Array();
					omp[form.codmp[i].value]["nombre"] = form.nombre_mp[i].value;
					omp[form.codmp[i].value]["precio"] = parseFloat(form.precio_nuevo[i].value);
					omp[form.codmp[i].value]["min"] = mp[form.codmp[i].value]["min"];
					omp[form.codmp[i].value]["max"] = mp[form.codmp[i].value]["max"];
				}
			}
		}
		
		form.submit();
	}
	else {
		form.codmp[0].select();
		return false;
	}
}

window.onload = form.codmp[0].select();
-->
</script>
<!-- END BLOCK : precios -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
