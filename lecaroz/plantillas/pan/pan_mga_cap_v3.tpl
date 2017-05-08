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
  <form action="./pan_mga_cap_v3.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" style="font-size:14pt; font-weight:bold;" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{num_cia}" size="3">
        <input name="nombre" type="text" class="vnombre" id="nombre" style="font-size:14pt; font-weight:bold;" value="{nombre}" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" style="font-size:14pt; font-weight:bold;" onFocus="tmp.value=this.value;this.select()" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) codgastos[0].select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th rowspan="2" class="tabla" scope="col">C&oacute;digo</th>
      <th rowspan="2" class="tabla" scope="col">Concepto</th>
      <th rowspan="2" class="tabla" scope="col">Importe</th>
      <th colspan="6" class="tabla" scope="col">Turnos</th>
    </tr>
    <tr>
      <th class="tabla" scope="col">FD</th>
      <th class="tabla" scope="col">FN</th>
      <th class="tabla" scope="col">BD</th>
      <th class="tabla" scope="col">REP</th>
      <th class="tabla" scope="col">GEL</th>
      <th class="tabla" scope="col">PIC</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) concepto[{i}].select();
else if (event.keyCode == 38) codgastos[{back}].select();
else if (event.keyCode == 40) codgastos[{next}].select()" value="{codgastos}" size="3">
        <input name="desc[]" type="text" class="vnombre" id="desc" value="{desc}" size="30" readonly="true"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if ((event.keyCode == 13 || event.keyCode == 39) && codgastos[{i}].value != 23) importe[{i}].select();
else if ((event.keyCode == 13 || event.keyCode == 39) && codgastos[{i}].value == 23) fd[{i}].select();
else if (event.keyCode == 37) codgastos[{i}].select();
else if (event.keyCode == 38) concepto[{back}].select();
else if (event.keyCode == 40) concepto[{next}].select()" size="30" maxlength="255"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) Total();" onKeyDown="if (event.keyCode == 13) codgastos[{next}].select();
else if (event.keyCode == 37) concepto[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) fd[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) fd[{next}].select();" value="{importe}" size="8"></td>
      <td class="tabla"><input name="fd[]" type="text" class="rinsert" id="fd" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fn[{i}].select();
else if (event.keyCode == 37) concepto[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) fd[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) fd[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{fd}" size="5" readonly="true"></td>
	  <td class="tabla"><input name="fn[]" type="text" class="rinsert" id="fn" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) bd[{i}].select();
else if (event.keyCode == 37) fd[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) fn[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) fn[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{fn}" size="5" readonly="true"></td>
	  <td class="tabla"><input name="bd[]" type="text" class="rinsert" id="bd" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) rep[{i}].select();
else if (event.keyCode == 37) fn[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) bd[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) bd[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{bd}" size="5" readonly="true"></td>
	  <td class="tabla"><input name="rep[]" type="text" class="rinsert" id="rep" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) gel[{i}].select();
else if (event.keyCode == 37) bd[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) rep[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) rep[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{rep}" size="5" readonly="true"></td>
	  <td class="tabla"><input name="gel[]" type="text" class="rinsert" id="gel" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pic[{i}].select();
else if (event.keyCode == 37) rep[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) gel[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) gel[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{gel}" size="5" readonly="true"></td>
	  <td class="tabla"><input name="pic[]" type="text" class="rinsert" id="pic" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) sumaTurnos({i})" onKeyDown="if (event.keyCode == 13) codgastos[{next}].select();
else if (event.keyCode == 37) gel[{i}].select();
else if (event.keyCode == 38 && codgastos[{back}].value == 23) pic[{back}].select();
else if (event.keyCode == 38 && codgastos[{back}].value != 23) importe[{back}].select();
else if (event.keyCode == 40 && codgastos[{next}].value == 23) pic[{next}].select();
else if (event.keyCode == 40 && codgastos[{next}].value != 23) importe[{next}].select();" value="{pic}" size="5" readonly="true"></td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="8" readonly="true"></th>
      <th colspan="6" class="tabla">&nbsp;</th>
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
var f = document.form, cia = new Array(), gasto = new Array()/*, limite = new Array()*/;
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_corto}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->
<!-- START BLOCK : limites_cia -->
//limite[{num_cia}] = new Array();
<!-- START BLOCK : limite -->
//limite[{num_cia}][{codgastos}] = {limite};
<!-- END BLOCK : limite -->
<!-- END BLOCK : limites_cia -->

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

function cambiaGasto(i) {
	if (f.codgastos[i].value == "" || f.codgastos[i].value == "0") {
		f.codgastos[i].value = "";
		f.desc[i].value = "";
		f.importe[i].value = "";
		f.importe[i].readOnly = false;
		f.fd[i].readOnly = true;
		f.fn[i].readOnly = true;
		f.bd[i].readOnly = true;
		f.rep[i].readOnly = true;
		f.gel[i].readOnly = true;
		f.pic[i].readOnly = true;
		Total();
	}
	else if (gasto[get_val(f.codgastos[i])] != null) {
		f.desc[i].value = gasto[get_val(f.codgastos[i])];
		if (get_val(f.codgastos[i]) == 23 || get_val(f.codgastos[i]) == 9 || get_val(f.codgastos[i]) == 76) {
			f.importe[i].readOnly = true;
			f.fd[i].readOnly = false;
			f.fn[i].readOnly = false;
			f.bd[i].readOnly = false;
			f.rep[i].readOnly = false;
			f.gel[i].readOnly = false;
			f.pic[i].readOnly = false;
		}
		else {
			f.importe[i].readOnly = false;
			f.fd[i].readOnly = true;
			f.fn[i].readOnly = true;
			f.bd[i].readOnly = true;
			f.rep[i].readOnly = true;
			f.gel[i].readOnly = true;
			f.pic[i].readOnly = true;
		}
	}
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		f.codgastos[i].value = f.tmp.value;
		f.codgastos[i].select();
	}
}

function sumaTurnos(i) {
	var importe = get_val(f.fd[i]) + get_val(f.fn[i]) + get_val(f.bd[i]) + get_val(f.rep[i]) + get_val(f.gel[i]) + get_val(f.pic[i]);
	
	f.importe[i].value = importe != 0 ? number_format(importe, 2) : "";
	Total();
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
	
	// Validar los límites para cada gasto si aplica el caso
	/*for (i = 0; i < f.codgastos.length; i++)
		if (get_val(f.codgastos[i]) > 0 && get_val(f.importe[i]) > 0 && limite[get_val(f.num_cia)] && limite[get_val(f.num_cia)][get_val(f.codgastos[i])] != null && get_val(f.importe[i]) > limite[get_val(f.num_cia)][get_val(f.codgastos[i])]) {
			alert("El importe para '" + f.codgastos[i].value + " " + f.desc[i].value + "' no puede ser mayor a " + number_format(limite[get_val(f.num_cia)][get_val(f.codgastos[i])], 2));
			f.importe[i].select();
			return false;
		}*/
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.codgastos[0].select();
}

window.onload = function () { showAlert = true; f.num_cia.select() };
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
		top.mainFrame.location = './pan_mga_cap_v3.php';
	else
		top.location = './pan_mga_cap_v3.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
