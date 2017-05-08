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
<td align="center" valign="middle"><p class="title">Registro de Gastos</p>
  <form action="./ban_gas_zap.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" style="font-size:14pt; font-weight:bold;" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{num_cia}" size="3">
        <input name="nombre" type="text" class="vnombre" id="nombre" style="font-size:14pt; font-weight:bold;" value="{nombre}" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" style="font-size:14pt; font-weight:bold;" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) codgastos[0].select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Proveedor</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) concepto[{i}].select();
else if (event.keyCode == 38) codgastos[{back}].select();
else if (event.keyCode == 40) codgastos[{next}].select()" value="{codgastos}" size="3">
        <input name="desc[]" type="text" class="vnombre" id="desc" value="{desc}" size="30" readonly="true"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) importe[{i}].select();
else if (event.keyCode == 37) codgastos[{i}].select();
else if (event.keyCode == 38) concepto[{back}].select();
else if (event.keyCode == 40) concepto[{next}].select()" size="30" maxlength="255"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) Total();" onKeyDown="if (codgastos[{i}].value != 6 && event.keyCode == 13) codgastos[{next}].select();
if (codgastos[{i}].value == 6 && (event.keyCode == 13 || event.keyCode == 39)) num_pro[{i}].select();
else if (event.keyCode == 37) concepto[{i}].select();
else if (event.keyCode == 38) importe[{back}].select();
else if (event.keyCode == 40) importe[{next}].select();" value="{importe}" size="8"></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) codgastos[{next}].select();
else if (event.keyCode == 37) importe[{i}].select();
else if (event.keyCode == 38 && codgastos == 6) num_pro[{back}].select();
else if (event.keyCode == 38 && codgastos != 6) importe[{back}].select();
else if (event.keyCode == 40 && codgastos == 6) num_pro[{next}].select();
else if (event.keyCode == 40 && codgastos != 6) importe[{next}].select();" value="{num_pro}" size="3">
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" readonly="true"></td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="8" readonly="true"></th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), gasto = new Array(), limite = new Array(), pro = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_corto}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : pro -->

function cambiaCia() {
	if (f.num_cia.value == "" || f.num_cia.value == "0") {
		f.num_cia.value = "";
		f.nombre.value = "";
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro(i) {
	if (f.num_pro[i].value == "" || f.num_pro[i].value == "0") {
		f.num_pro[i].value = "";
		f.nombre_pro[i].value = "";
	}
	else if (pro[get_val(f.num_pro[i])] != null)
		f.nombre_pro[i].value = pro[get_val(f.num_pro[i])];
	else {
		alert("El proveedor no se encuentra en el catálogo");
		f.num_pro[i].value = f.tmp.value;
		f.num_pro[i].select();
	}
}

function cambiaGasto(i) {
	if (f.codgastos[i].value == "" || f.codgastos[i].value == "0") {
		f.codgastos[i].value = "";
		f.desc[i].value = "";
		f.importe[i].value = "";
		Total();
	}
	else if (gasto[get_val(f.codgastos[i])] != null)
		f.desc[i].value = gasto[get_val(f.codgastos[i])];
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		f.codgastos[i].value = f.tmp.value;
		f.codgastos[i].select();
	}
}

function Total() {
	var total = 0;
	
	for (var i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = number_format(total, 2);
}

function validar() {
	if (get_val(f.num_cia) == 0) {
		alert("Debe especificar la compañía");
		f.num_cia.select();
		return false;
	}
	else if (f.fecha.value.length < 8) {
		alert("Debe especificar la fecha");
		f.fecha.select();
		return false;
	}
	
	// Validar registros
	for (var i = 0; i < f.codgastos.length; i++)
		if (get_val(f.codgastos[i]) > 0 && get_val(f.importe[i]) == 0) {
			alert("Debe especificar el importe del gasto '" + f.desc[i].value + "'");
			f.importe[i].select();
			return false;
		}
		else if (get_val(f.codgastos[i]) == 0 && get_val(f.importe[i]) > 0) {
			alert("No puede capturar un importe sin haber capturado el código de gasto");
			f.codgastos[i].select();
			return false
		}
		else if (get_val(f.codgastos[i]) == 6 && get_val(f.num_pro[i]) == 0) {
			alert("Para los fletes debe codificar el número de proveedor");
			f.num_pro[i].select();
			return false;
		}
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.codgastos[0].select();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame ? top.mainFrame.document.form : top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	f.eval(campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : valid -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	if (top.mainFrame)
		top.mainFrame.location = './ban_gas_zap.php';
	else
		top.location = './ban_gas_zap.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
