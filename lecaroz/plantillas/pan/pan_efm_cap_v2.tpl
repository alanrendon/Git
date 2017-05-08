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
<td align="center" valign="middle"><p class="title">Captura de Movimiento de Efectivos</p>
  <form action="./pan_efm_cap_v2.php" method="post" name="form" target="validar">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" style="font-size:14pt; color:#FF0000;" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th rowspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th colspan="2" class="tabla" scope="col">AM</th>
      <th colspan="2" class="tabla" scope="col">PM</th>
      <th rowspan="2" class="tabla" scope="col">Pastel</th>
      <th rowspan="2" class="tabla" scope="col">Venta<br>
        Puerta</th>
      <th rowspan="2" class="tabla" scope="col">Pastillaje</th>
      <th rowspan="2" class="tabla" scope="col">Otros</th>
      <th rowspan="2" class="tabla" scope="col">Clientes</th>
      <th rowspan="2" class="tabla" scope="col">Corte <br>
        Pan </th>
      <th rowspan="2" class="tabla" scope="col">Corte <br>
        Pastel </th>
      <th rowspan="2" class="tabla" scope="col">Descuento<br>
        Pastel</th>
    </tr>
    <tr>
      <th class="tabla" scope="col">Total</th>
      <th class="tabla" scope="col">Error</th>
      <th class="tabla" scope="col">Total</th>
      <th class="tabla" scope="col">Error</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) am[{i}].select();
else if (event.keyCode == 38) num_cia[{back}].select();
else if (event.keyCode == 40) num_cia[{next}].select();" value="{num_cia}" size="3"></td>
      <td class="tabla"><input name="am[]" type="text" class="rinsert" id="am" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaPuerta({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) am_error[{i}].select();
else if (event.keyCode == 37) num_cia[{i}].select();
else if (event.keyCode == 38) am[{back}].select();
else if (event.keyCode == 40) am[{next}].select();" value="{am}" size="5"></td>
      <td class="tabla"><input name="am_error[]" type="text" class="rinsert" id="am_error" style="color:#CC0000;" onFocus="tmp.value=this.value;this.select()" onBlur="input_format(this,2,true)" onChange="if (input_format(this,2,true)) ventaPuerta({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pm[{i}].select();
else if (event.keyCode == 37) am[{i}].select();
else if (event.keyCode == 38) am_error[{back}].select();
else if (event.keyCode == 40) am_error[{next}].select();" value="{am_error}" size="5"></td>
      <td class="tabla"><input name="pm[]" type="text" class="rinsert" id="pm" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaPuerta({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pm_error[{i}].select();
else if (event.keyCode == 37) am_error[{i}].select();
else if (event.keyCode == 38) pm[{back}].select();
else if (event.keyCode == 40) pm[{next}].select();" value="{pm}" size="5"></td>
      <td class="tabla"><input name="pm_error[]" type="text" class="rinsert" id="pm_error" style="color:#CC0000;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaPuerta({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pastel[{i}].select();
else if (event.keyCode == 37) pm[{i}].select();
else if (event.keyCode == 38) pm_error[{back}].select();
else if (event.keyCode == 40) pm_error[{next}].select();" value="{pm_error}" size="5"></td>
      <td class="tabla"><input name="pastel[]" type="text" class="rinsert" id="pastel" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaPuerta({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pastillaje[{i}].select();
else if (event.keyCode == 37) pm_error[{i}].select();
else if (event.keyCode == 38) pastel[{back}].select();
else if (event.keyCode == 40) pastel[{next}].select();" value="{pastel}" size="5"></td>
      <td class="tabla"><input name="venta_pta[]" type="text" class="rnombre" id="venta_pta" style="color:#0000CC;" value="{venta_pta}" size="5" readonly="true"></td>
      <td class="tabla"><input name="pastillaje[]" type="text" class="rinsert" id="pastillaje" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) otros[{i}].select();
else if (event.keyCode == 37) pastel[{i}].select();
else if (event.keyCode == 38) pastillaje[{back}].select();
else if (event.keyCode == 40) pastillaje[{next}].select();" value="{pastillaje}" size="5"></td>
      <td class="tabla"><input name="otros[]" type="text" class="rinsert" id="otros" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) ctes[{i}].select();
else if (event.keyCode == 37) pastillaje[{i}].select();
else if (event.keyCode == 38) otros[{back}].select();
else if (event.keyCode == 40) otros[{next}].select();" value="{otros}" size="5"></td>
      <td class="tabla"><input name="ctes[]" type="text" class="rinsert" id="ctes" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,-1,true)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) corte1[{i}].select();
else if (event.keyCode == 37) otros[{i}].select();
else if (event.keyCode == 38) ctes[{back}].select();
else if (event.keyCode == 40) ctes[{next}].select();" value="{ctes}" size="5"></td>
      <td class="tabla"><input name="corte1[]" type="text" class="rinsert" id="corte1" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) corte2[{i}].select();
else if (event.keyCode == 37) ctes[{i}].select();
else if (event.keyCode == 38) corte1[{back}].select();
else if (event.keyCode == 40) corte1[{next}].select();" value="{corte1}" size="5"></td>
      <td class="tabla"><input name="corte2[]" type="text" class="rinsert" id="corte2" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) desc_pastel[{i}].select();
else if (event.keyCode == 37) corte1[{i}].select();
else if (event.keyCode == 38) corte2[{back}].select();
else if (event.keyCode == 40) corte2[{next}].select();" value="{corte2}" size="5"></td>
      <td class="tabla"><input name="desc_pastel[]" type="text" class="rinsert" id="desc_pastel" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select();
else if (event.keyCode == 37) corte2[{i}].select();
else if (event.keyCode == 38) desc_pastel[{back}].select();
else if (event.keyCode == 40) desc_pastel[{next}].select();" value="{desc_pastel}" size="5"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form>
  </td>
</tr>
</table>
<iframe src="" name="validar" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = new Array({ult_corte1}, {ult_corte2}, {cortes});
<!-- END BLOCK : cia -->

function ventaPuerta(i) {
	var am, am_error, pm, pm_error, pastel, venta;
	
	am = get_val(f.am[i]);
	am_error = get_val(f.am_error[i]);
	pm = get_val(f.pm[i]);
	pm_error = get_val(f.pm_error[i]);
	pastel = get_val(f.pastel[i]);
	
	venta = am + pm - am_error - pm_error + pastel;
	
	f.venta_pta[i].value = venta != 0 ? number_format(venta, 2) : null;
	f.venta_pta[i].style.color = venta >= 0 ? "#0000CC" : "CC0000";
}

function validar() {
	var n = new Array();
	
	// Buscar compañías repetidas
	for (var i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0) {
			for (var j = 0; j < n.length; j++)
				if (get_val(f.num_cia[i]) == n[j]) {
					alert("La compañía " + f.num_cia[i].value + " aparece duplicada en la captura");
					f.num_cia[i].select();
					return false;
				}
			n.push(get_val(f.num_cia[i]));
		}
	
	// Validar pertenencia de compañía
	for (i = 0; i < n.length; i++)
		if (cia[n[i]] == null) {
			alert("La compañía " + n[i] + " no esta asignada a la capturista");
			return false;
		}
	
	// Validar llenado de campos
	for (i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0) {
			/*if (get_val(f.am[i]) == 0) {
				alert("El total del primer corte (AM) no puede ser 0");
				f.am[i].select();
				return false;
			}
			else */if (get_val(f.pm[i]) == 0) {
				alert("El total del segundo corte (PM) no puede ser 0");
				f.pm[i].select();
				return false;
			}
			else if (get_val(f.am[i]) > 0 && get_val(f.am_error[i]) >= get_val(f.am[i])) {
				alert("Total AM no puede ser menor a el error");
				f.am_error[i].select();
				return false;
			}
			else if (get_val(f.pm_error[i]) >= get_val(f.pm[i])) {
				alert("Total PM no puede ser menor a el error");
				f.pm_error[i].select();
				return false;
			}
			else if (get_val(f.venta_pta[i]) == 0) {
				alert("La venta en puerta no puede ser 0");
				f.num_cia[i].select();
				return false;
			}
			else if (get_val(f.ctes[i]) == 0) {
				alert("Clientes no puede ser 0");
				f.ctes[i].select();
				return false;
			}
			else if (get_val(f.corte1[i]) == 0) {
				alert("Debe capturar 'Corte de Pan' para la panaderia " + f.num_cia[i].value);
				f.corte1[i].select();
				return false;
			}
		}
	
	// Validar Cortes de Pan
	/*for (i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0 && (get_val(f.corte1[i]) <= cia[f.num_cia[i].value][0] || get_val(f.corte1[i]) > cia[f.num_cia[i].value][0] + cia[f.num_cia[i].value][2])) {
			alert("Error en el ultimo Corte de Pan de la panaderia " + f.num_cia[i].value + ". Debe estar entre " + (cia[f.num_cia[i].value][0] + 1) + " y " + (cia[f.num_cia[i].value][0] + cia[f.num_cia[i].value][2]));
			f.corte1[i].select();
			return false;
		}
	
	// Validar Cortes de Pastel
	for (i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0 && get_val(f.corte2[i]) > 0 && (get_val(f.corte2[i]) <= cia[f.num_cia[i].value][1] || get_val(f.corte2[i]) > cia[f.num_cia[i].value][1] + cia[f.num_cia[i].value][2])) {
			alert("Error en el ultimo Corte de Pastel de la panaderia " + f.num_cia[i].value + ". Debe estar entre " + (cia[f.num_cia[i].value][1] + 1) + " y " + (cia[f.num_cia[i].value][1] + cia[f.num_cia[i].value][2]));
			f.corte1[i].select();
			return false;
		}*/
	
	// Validar fecha de captura
	if (f.fecha.value.length < 8) {
		alert("Debe especificar la fecha de captura");
		f.fecha.select();
		return false;
	}
	
	f.submit();
}

window.onload = f.fecha.select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : validar -->
<script language="javascript" type="text/javascript">
<!--
function validar(mensaje, campo) {
	alert(mensaje);
	eval('top.mainFrame.f.' + campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : validar -->
<!-- START BLOCK : confirmar -->
<script language="javascript" type="text/javascript">
<!--
function validar() {
	if (confirm("¿Son correctos todos los datos?"))
		document.location = './pan_efm_cap_v2.php?action=cap';
	else
		document.location = './pan_efm_cap_v2.php?action=cancel';
}

window.onload = validar();
//-->
</script>
<!-- END BLOCK : confirmar -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	top.mainFrame.location = './pan_efm_cap_v2.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
