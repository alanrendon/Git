<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
var f = window.opener.document.data;

var anio = {anio};
var mes = {mes};
var num_cia = {num_cia};

function cerrar(i, inv) {
	var dif, exi, total;

	if (f.inventario.length == undefined) {
		exi = get_val(f.existencia);
		dif = inv - exi;
		costo = get_val(f.costo);
		total = dif * costo;
		f.inventario.value = inv != 0 ? number_format(inv, 2) : "";
		f.costo.value = number_format(costo, 4);
		f.falta.value = dif < 0 ? number_format(Math.abs(dif), 2) : "";
		f.sobra.value = dif > 0 ? number_format(Math.abs(dif), 2) : "";
		f.total.value = total != 0 ? number_format(Math.abs(total), 2) : "";
		f.total.style.color = total > 0 ? "#0000CC" : "#CC0000";
	}
	else {
		exi = get_val(f.existencia[i]);
		dif = inv - exi;
		costo = get_val(f.costo[i]);
		total = dif * costo;
		f.inventario[i].value = inv != 0 ? number_format(inv, 2) : "";
		f.costo[i].value = number_format(costo, 4);
		f.falta[i].value = dif < 0 ? number_format(Math.abs(dif), 2) : "";
		f.sobra[i].value = dif > 0 ? number_format(Math.abs(dif), 2) : "";
		f.total[i].value = total != 0 ? number_format(Math.abs(total), 2) : "";
		f.total[i].style.color = total > 0 ? "#0000CC" : "#CC0000";
	}

	if (total == 0)
		window.opener.document.getElementById("row" + i).style.display = 'none';

	totales();

	window.opener.actualizar_observaciones_extra(num_cia, anio, mes);

	self.close();
}

function totales() {
	var falta = 0, sobra = 0;
	if (f.total.length == undefined) {
		falta = get_val(f.falta) * get_val(f.costo);
		sobra = get_val(f.sobra) * get_val(f.costo);
	}
	else
		for (var i = 0; i < f.total.length; i++) {
			falta += get_val(f.falta[i]) * get_val(f.costo[i]);
			sobra += get_val(f.sobra[i]) * get_val(f.costo[i]);
		}

	f.contra.value = falta > 0 ? number_format(falta, 2) : "";
	f.favor.value = sobra > 0 ? number_format(sobra, 2) : "";
	f.gran_total.value = falta - sobra != 0 ? number_format(Math.abs(falta - sobra), 2) : "";
	f.gran_total.style.color = falta - sobra > 0 ? "#CC0000" : "#0000CC";
}

window.onload = cerrar({i}, {inv});
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar Inventario</p>
<form name="form" method="post" action="./bal_ifm_minimod_v3.php">
<input name="tmp" type="hidden">
<input name="id" type="hidden" value="{id}">
<input name="i" type="hidden" value="{i}">
<table class="tabla">
  <tr>
    <th colspan="3" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
    </tr>
  <tr>
    <th rowspan="2" class="tabla" scope="col">Producto</th>
    <th colspan="2" class="tabla" scope="col">Existencia</th>
    </tr>
  <tr>
    <th class="tabla" scope="col">C&oacute;mputo</th>
    <th class="tabla" scope="col">F&iacute;sica</th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">{codmp} {nombre}</th>
    <td class="tabla"><strong>{existencia}</strong></td>
    <td class="tabla"><input name="inventario" type="text" class="insert" id="inventario" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) enviar.focus()" value="{inventario}" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Actualizar" onClick="validar()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
var f = document.form;

function validar() {
	if (get_val(f.inventario) < 0) {
		alert("Debe especificar la existencia física");
		f.inventario.select();
		return false;
	}
	else {
		f.submit();
	}
}

function seleccionar() {
	window.focus();
	f.inventario.select();
}

window.onload = seleccionar();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
