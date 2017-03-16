<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Movimientos de Expendios</p>
  <form action="./pan_exp_cap_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) sig.focus()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="sig" type="button" class="boton" id="sig" onClick="validar()" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_cia}";
<!-- END BLOCK : cia -->

function validar() {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (cia[parseInt(form.num_cia.value)] == null) {
		alert("La panaderia " + form.num_cia.value + " no le pertenece");
		form.num_cia.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Movimientos de Expendios</p>
<form action="./pan_exp_cap_v2.php" method="post" name="capExp">
<input name="tmp" type="hidden">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
<input name="fecha" type="hidden" id="fecha" value="{fecha}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Fecha</th>
  </tr>
  <tr>
    <td class="tabla" style="font-size:14pt; font-weight:bold;">{num_cia} {nombre_cia} </td>
    <td class="tabla" style="font-size:14pt; font-weight:bold;">{fecha}</td>
  </tr>
</table>

<br>
<table align="center" class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">Expendio</th>
    <th class="tabla" scope="col">Rezago<br>
    Anterior </th>
    <th class="tabla" scope="col">Partidas</th>
    <th class="tabla" scope="col">Devuelto</th>
    <th class="tabla" scope="col">Total</th>
    <th class="tabla" scope="col">Abono</th>
    <th class="tabla" scope="col">Rezago<br>
      Final</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla"><input name="NumExpendio[]" id="NumExpendio" type="hidden" value="{NumExpendio}">
      <input name="NombreExpendio[]" type="hidden" id="NombreExpendio" value="{Nombre}">
	<input name="PorGanancia[]" id="PorGanancia" type="hidden" value="{PorGanancia}">
	<input name="ImporteFijo[]" id="ImporteFijo" type="hidden" value="{ImporteFijo}">
	<input name="TotalFijo[]" type="hidden" id="TotalFijo" value="{TotalFijo}">
	<strong>{NumRef}</strong></td>
    <td class="vtabla"><strong>{Nombre}</strong></td>
    <td align="center" class="tabla"><input name="RezagoInicial[]" type="text" class="rnombre" id="RezagoInicial" style="font-size: 12pt; text-align: right; color: #0000CC;" value="{RezagoInicial}" size="10" readonly="true"></td>
	<td align="center" class="tabla"><input name="PanVenta[]" type="text" class="insert" id="PanVenta" style="font-size: 12pt; text-align: right;" onFocus="tmp.value=this.value; this.select()" onBlur="if (!partida({i})) this.select()" onClick="this.select()" onKeyDown="if (event.keyCode == 13) {
if (RezagoFinal.length == undefined) Devolucion.select();
else Devolucion[{i}].select();
}
else if (RezagoFinal.length != undefined) {
if (event.keyCode == 37) Abono[{i}].select();
else if (event.keyCode == 39) Devolucion[{i}].select();
else if (event.keyCode == 38) PanVenta[{back}].select();
else if (event.keyCode == 40) PanVenta[{next}].select();
}" value="{PanVenta}" size="10"></td>
	<td align="center" class="tabla"><input name="Devolucion[]" type="text" class="insert" id="Devolucion" style="font-size: 12pt; text-align: right;" onFocus="tmp.value=this.value; this.select()" onBlur="if (!devuelto({i})) this.select();" onClick="this.select()" onKeyDown="if (event.keyCode == 13) {
if (RezagoFinal.length == undefined) PanExpendio.select();
else PanExpendio[{i}].select();
}
else if (RezagoFinal.length != undefined) {
if (event.keyCode == 37) PanVenta[{i}].select();
else if (event.keyCode == 39) PanExpendio[{i}].select();
else if (event.keyCode == 38) Devolucion[{back}].select();
else if (event.keyCode == 40) Devolucion[{next}].select();
}" value="{Devolucion}" size="10" {readonly_dev}></td>
    <td align="center" class="tabla"><input name="PanExpendio[]" type="text" class="insert" id="PanExpendio" style="font-size: 12pt; text-align: right;" onFocus="tmp.value=this.value; this.select()" onBlur="if (!total({i})) this.select();" onClick="this.select()" onKeyDown="if (event.keyCode == 13) {
if (RezagoFinal.length == undefined) Abono.select();
else Abono[{i}].select();
}
else if (RezagoFinal.length != undefined) {
if (event.keyCode == 37) Devolucion[{i}].select();
else if (event.keyCode == 39) Abono[{i}].select();
else if (event.keyCode == 38) PanExpendio[{back}].select();
else if (event.keyCode == 40) PanExpendio[{next}].select();
}" value="{PanExpendio}" size="10" {readonly}></td>
    <td align="center" class="tabla"><input name="Abono[]" type="text" class="insert" id="Abono" style="font-size: 12pt; text-align: right;" onFocus="tmp.value=this.value; this.select()" onBlur="if (!abono({i})) this.select();" onClick="this.select()" onKeyDown="if (event.keyCode == 13) {
if (RezagoFinal.length == undefined) PanVenta.select();
else PanVenta[{next}].select();
}
else if (RezagoFinal.length != undefined) {
if (event.keyCode == 37) PanExpendio[{i}].select();
else if (event.keyCode == 39) PanVenta[{i}].select();
else if (event.keyCode == 38) Abono[{back}].select();
else if (event.keyCode == 40) Abono[{next}].select();
}" value="{Abono}" size="10"></td>
    <td align="center" class="tabla"><input name="RezagoFinal[]" type="text" class="rnombre" id="RezagoFinal" style="font-size: 12pt; color: #0000CC;" value="{RezagoFinal}" size="10" readonly="true"></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" align="right" class="tabla">Total</th>
    <th align="center" class="tabla"><input name="TotalRezagoInicial" type="text" disabled="true" class="rnombre" id="TotalRezagoInicial" style="font-size: 12pt; color: #0000CC;" value="{TotalRezagoInicial}" size="10"></th>
    <th align="center" class="tabla"><input name="TotalPanVenta" type="text" disabled="true" class="rnombre" id="TotalPanVenta" style="font-size: 12pt; color: #0000CC;" value="{TotalPanVenta}" size="10"></th>
    <th align="center" class="tabla"><input name="TotalDevolucion" type="text" disabled="true" class="rnombre" id="TotalDevolucion" style="font-size: 12pt; color: #0000CC;" value="{TotalDevolucion}" size="10"></th>
    <th align="center" class="tabla"><input name="TotalPanExpendio" type="text" disabled="true" class="rnombre" id="TotalPanExpendio" style="font-size: 12pt; color: #0000CC;" value="{TotalPanExpendio}" size="10"></th>
    <th align="center" class="tabla"><input name="TotalAbono" type="text" class="rnombre" id="TotalAbono" style="font-size: 12pt; color: #0000CC;" value="{TotalAbono}" size="10" readonly="true"></th>
    <th align="center" class="tabla"><input name="TotalRezagoFinal" type="text" disabled="true" class="rnombre" id="TotalRezagoFinal" style="font-size: 12pt; color: #0000CC;" value="{TotalRezagoFinal}" size="10"></th>
  </tr>
</table>
<br>
<p align="center">
  <input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_exp_cap_v2.php'">
&nbsp;&nbsp;  
<input name="sig" type="button" class="boton" id="sig" onClick="validar()" value="Siguiente >>">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript">
<!--
var capExp = document.capExp;

function partida(i) {
	if (capExp.NumExpendio.length == undefined) {
		if (capExp.PanVenta.value == "" || capExp.PanVenta.value == 0) {
			capExp.PanVenta.value = "";
			if (capExp.TotalFijo.value == "t") {
				capExp.PanExpendio.value = "";
			}
		}
		else {
			if (isNaN(parseFloat(capExp.PanVenta.value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.PanVenta.value = capExp.tmp.value;
				return false;
			}
			
			var PanVenta = parseFloat(capExp.PanVenta.value.replace(",", "."));
			
			if (PanVenta < 0) {
				alert("'Partidas no puede ser menor a 0");
				capExp.PanVenta.value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(PanVenta);
			capExp.PanVenta.value = temp.formato(2, true);
			
			if (capExp.TotalFijo.value == "t") {
				var por_gan = !isNaN(parseFloat(capExp.PorGanancia.value)) != null ? parseFloat(capExp.PorGanancia.value) : null;
				var fijo = !isNaN(parseFloat(capExp.ImporteFijo.value)) ? parseFloat(capExp.ImporteFijo.value) : null;
				
				if (por_gan != null) {
					PanExp = PanVenta * (100 - por_gan) / 100;
				}
				else {
					PanExp = PanVenta - fijo;
				}
				
				temp = new oNumero(PanExp);
				capExp.PanExpendio.value = temp.formato(2, true);
			}
		}
		rezago(i);
	}
	else {
		if (capExp.PanVenta[i].value == "" || capExp.PanVenta[i].value == 0) {
			capExp.PanVenta[i].value = "";
			if (capExp.TotalFijo[i].value == "t") {
				capExp.PanExpendio[i].value = "";
			}
		}
		else {
			if (isNaN(parseFloat(capExp.PanVenta[i].value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.PanVenta[i].value = capExp.tmp.value;
				return false;
			}
			
			var PanVenta = parseFloat(capExp.PanVenta[i].value.replace(/\,/g, ''));
			
			if (PanVenta < 0) {
				alert("'Partidas' no puede ser menor a 0");
				capExp.PanVenta[i].value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(PanVenta);
			capExp.PanVenta[i].value = temp.formato(2, true);
			
			if (capExp.TotalFijo[i].value == "t") {
				var por_gan = !isNaN(parseFloat(capExp.PorGanancia[i].value)) != null ? parseFloat(capExp.PorGanancia[i].value) : null;
				var fijo = !isNaN(parseFloat(capExp.ImporteFijo[i].value)) ? parseFloat(capExp.ImporteFijo[i].value) : null;
				
				if (por_gan != null) {
					PanExp = (PanVenta * (100 - por_gan) / 100);
				}
				else {
					PanExp = PanVenta - fijo;
				}
				
				temp = new oNumero(PanExp);
				capExp.PanExpendio[i].value = temp.formato(2, true);
			}
		}
		rezago(i);
	}
	
	totales();
	return true;
}

function devuelto(i) {
	if (capExp.NumExpendio.length == undefined) {
		if (capExp.Devolucion.value == "" || capExp.Devolucion.value == 0) {
			capExp.Devolucion.value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.Devolucion.value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.Devolucion.value = capExp.tmp.value;
				return false;
			}
			
			var Devolucion = parseFloat(capExp.Devolucion.value.replace(/\,/g, ''));
			
			if (Devolucion < 0) {
				alert("'Devoluci\u00F3n' no puede ser menor a 0");
				capExp.Devolucion.value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(Devolucion);
			capExp.Devolucion.value = temp.formato(2, true);
		}
		rezago(i);
	}
	else {
		if (capExp.Devolucion[i].value == "" || capExp.Devolucion[i].value == 0) {
			capExp.Devolucion[i].value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.Devolucion[i].value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.Devolucion[i].value = capExp.tmp.value;
				return false;
			}
			
			var Devolucion = parseFloat(capExp.Devolucion[i].value.replace(/\,/g, ''));
			
			if (Devolucion < 0) {
				alert("'Devoluci\u00F3n' no puede ser menor a 0");
				capExp.Devolucion[i].value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(Devolucion);
			capExp.Devolucion[i].value = temp.formato(2, true);
		}
		rezago(i);
	}
	
	totales();
	return true;
}

function total(i) {
	if (capExp.NumExpendio.length == undefined) {
		if (capExp.PanExpendio.value == "" || capExp.PanExpendio.value == 0) {
			capExp.PanExpendio.value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.PanExpendio.value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.PanExpendio.value = capExp.tmp.value;
				return false;
			}
			
			var PanExpendio = parseFloat(capExp.PanExpendio.value.replace(/\,/g, ''));
			
			if (PanExpendio < 0) {
				alert("'Total' no puede ser menor a 0");
				capExp.PanExpendio.value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(PanExpendio);
			capExp.PanExpendio.value = temp.formato(2, true);
		}
		rezago(i);
	}
	else {
		if (capExp.PanExpendio[i].value == "" || capExp.PanExpendio[i].value == 0) {
			capExp.PanExpendio[i].value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.PanExpendio[i].value = capExp.tmp.value;
				return false;
			}
			
			var PanExpendio = parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, ''));
			
			if (PanExpendio < 0) {
				alert("'Total' no puede ser menor a 0");
				capExp.PanExpendio[i].value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(PanExpendio);
			capExp.PanExpendio[i].value = temp.formato(2, true);
		}
		rezago(i);
	}
	
	totales();
	return true;
}

function abono(i) {
	if (capExp.NumExpendio.length == undefined) {
		if (capExp.Abono.value == "" || capExp.Abono.value == 0) {
			capExp.Abono.value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.Abono.value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.Abono.value = capExp.tmp.value;
				return false;
			}
			
			var Abono = parseFloat(capExp.Abono.value.replace(/\,/g, ''));
			
			if (Abono < 0) {
				alert("'Abono' no puede ser menor a 0");
				capExp.Abono.value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(Abono);
			capExp.Abono.value = temp.formato(2, true);
		}
		rezago(i);
	}
	else {
		if (capExp.Abono[i].value == "" || capExp.Abono[i].value == 0) {
			capExp.Abono[i].value = "";
		}
		else {
			if (isNaN(parseFloat(capExp.Abono[i].value.replace(/\,/g, '')))) {
				alert("Solo se permiten n\u00FAmeros");
				capExp.Abono[i].value = capExp.tmp.value;
				return false;
			}
			
			var Abono = parseFloat(capExp.Abono[i].value.replace(/\,/g, ''));
			
			if (Abono < 0) {
				alert("'Total' no puede ser menor a 0");
				capExp.Abono[i].value = capExp.tmp.value;
				return false;
			}
			
			temp = new oNumero(Abono);
			capExp.Abono[i].value = temp.formato(2, true);
		}
		rezago(i);
	}
	
	totales();
	return true;
}

function rezago(i) {
	// Recabar datos
	if (capExp.NumExpendio.length == undefined) {
		var rezago_ant   = !isNaN(parseFloat(capExp.RezagoInicial.value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoInicial.value.replace(/\,/g, '')) : 0;
		var pan_venta    = !isNaN(parseFloat(capExp.PanVenta.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta.value.replace(/\,/g, '')) : 0;
		var devolucion   = !isNaN(parseFloat(capExp.Devolucion.value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion.value.replace(/\,/g, '')) : 0;
		var pan_expendio = !isNaN(parseFloat(capExp.PanExpendio.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio.value.replace(/\,/g, '')) : 0;
		var abono        = !isNaN(parseFloat(capExp.Abono.value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono.value.replace(/\,/g, '')) : 0;
		var por_gan      = get_val(capExp.PorGanancia);
	}
	else {
		var rezago_ant   = !isNaN(parseFloat(capExp.RezagoInicial[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoInicial[i].value.replace(/\,/g, '')) : 0;
		var pan_venta    = !isNaN(parseFloat(capExp.PanVenta[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta[i].value.replace(/\,/g, '')) : 0;
		var devolucion   = !isNaN(parseFloat(capExp.Devolucion[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion[i].value.replace(/\,/g, '')) : 0;
		var pan_expendio = !isNaN(parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, '')) : 0;
		var abono        = !isNaN(parseFloat(capExp.Abono[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono[i].value.replace(/\,/g, '')) : 0;
		var por_gan      = get_val(capExp.PorGanancia[i]);
	}
	
	// Calcular rezago
	var rezago = rezago_ant + pan_expendio - abono - devolucion * (100 - por_gan) / 100;
	var temp = new oNumero(rezago);
	
	// Asignar nuevo rezago
	if (capExp.NumExpendio.length == undefined) {
		capExp.RezagoFinal.value = temp.formato(2, true);
		
		if (rezago >= 0) {
			capExp.RezagoFinal.style.color = "#0000CC";
		}
		else {
			capExp.RezagoFinal.style.color = "#CC0000";
		}
	}
	else {
		capExp.RezagoFinal[i].value = temp.formato(2, true);
		
		if (rezago >= 0) {
			capExp.RezagoFinal[i].style.color = "#0000CC";
		}
		else {
			capExp.RezagoFinal[i].style.color = "#CC0000";
		}
	}
}

function totales() {
	var total_pan_venta = 0;
	var total_devolucion = 0;
	var total_pan_expendio = 0;
	var total_abono = 0;
	var total_rezago = 0;
	var por_gan = 0;
	
	if (capExp.NumExpendio.length == undefined) {
		total_pan_venta += !isNaN(parseFloat(capExp.PanVenta.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta.value.replace(/\,/g, '')) : 0;
		total_devolucion += !isNaN(parseFloat(capExp.Devolucion.value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion.value.replace(/\,/g, '')) : 0;
		total_pan_expendio += !isNaN(parseFloat(capExp.PanExpendio.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio.value.replace(/\,/g, '')) : 0;
		total_abono += !isNaN(parseFloat(capExp.Abono.value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono.value.replace(/\,/g, '')) : 0;
		total_rezago += !isNaN(parseFloat(capExp.RezagoFinal.value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoFinal.value.replace(/\,/g, '')) : 0;
		
		var temp = new oNumero(total_pan_venta);
		capExp.TotalPanVenta.value = temp.formato(2, true);
		temp = new oNumero(total_devolucion);
		capExp.TotalDevolucion.value = temp.formato(2, true);
		temp = new oNumero(total_pan_expendio);
		capExp.TotalPanExpendio.value = temp.formato(2, true);
		temp = new oNumero(total_abono);
		capExp.TotalAbono.value = temp.formato(2, true);
		temp = new oNumero(total_rezago);
		capExp.TotalRezagoFinal.value = temp.formato(2, true);
	}
	else {
		for (i = 0; i < capExp.NumExpendio.length; i++) {
			total_pan_venta += !isNaN(parseFloat(capExp.PanVenta[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta[i].value.replace(/\,/g, '')) : 0;
			total_devolucion += !isNaN(parseFloat(capExp.Devolucion[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion[i].value.replace(/\,/g, '')) : 0;
			total_pan_expendio += !isNaN(parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, '')) : 0;
			total_abono += !isNaN(parseFloat(capExp.Abono[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono[i].value.replace(/\,/g, '')) : 0;
			total_rezago += !isNaN(parseFloat(capExp.RezagoFinal[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoFinal[i].value.replace(/\,/g, '')) : 0;
		}
		
		var temp = new oNumero(total_pan_venta);
		capExp.TotalPanVenta.value = temp.formato(2, true);
		temp = new oNumero(total_devolucion);
		capExp.TotalDevolucion.value = temp.formato(2, true);
		temp = new oNumero(total_pan_expendio);
		capExp.TotalPanExpendio.value = temp.formato(2, true);
		temp = new oNumero(total_abono);
		capExp.TotalAbono.value = temp.formato(2, true);
		temp = new oNumero(total_rezago);
		capExp.TotalRezagoFinal.value = temp.formato(2, true);
		capExp.TotalRezagoFinal.style.color = total_rezago >= 0 ? "#0000CC" : "#CC0000";
	}
}

function validar() {
	var pan_venta;
	var devolucion;
	var pan_expendio;
	var abono;
	var rezago;
	var tope;
	
	var por_gan;
	var fijo;
	var tope;
	
	var expendio;
	
	var temp;
	
	if (capExp.NumExpendio.length == undefined) {
		pan_venta = !isNaN(parseFloat(capExp.PanVenta.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta.value.replace(/\,/g, '')) : 0;
		devolucion = !isNaN(parseFloat(capExp.Devolucion.value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion.value.replace(/\,/g, '')) : 0;
		pan_expendio = !isNaN(parseFloat(capExp.PanExpendio.value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio.value.replace(/\,/g, '')) : 0;
		abono = !isNaN(parseFloat(capExp.Abono.value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono.value.replace(/\,/g, '')) : 0;
		rezago = !isNaN(parseFloat(capExp.RezagoFinal.value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoFinal.value.replace(/\,/g, '')) : 0;
		
		if (pan_venta != 0 || devolucion != 0 || pan_expendio != 0 || abono != 0) {
			// Validar Pan p/Venta
			if (pan_venta < 0) {
				alert("'Partida' no puede ser menor a 0");
				capExp.PanVenta.select();
				return false;
			}
			
			// Validar Abono
			if (abono < 0) {
				alert("'Abono' no puede ser menor a 0");
				capExp.Abono.select();
				return false;
			}
			
			// Validar Pan p/Expendio
			por_gan = !isNaN(parseFloat(capExp.PorGanancia.value)) ? parseFloat(capExp.PorGanancia.value) : null;
			fijo = !isNaN(parseFloat(capExp.ImporteFijo.value)) ? parseFloat(capExp.ImporteFijo.value) : null;
			
			if (por_gan != null) {
				tope = (pan_venta * (100 - por_gan) / 100) - 0.30;
			}
			else {
				tope = pan_venta - fijo - 0.30;
			}
			
			if (pan_venta < pan_expendio) {
				alert("'Total' no puede ser mayor a 'Partidas'");
				capExp.PanExpendio.select();
				return false;
			}
			else if (pan_expendio > 0 && fijo == null && por_gan == 0 && pan_expendio > pan_venta) {
				temp = new oNumero(tope);
				alert("'Total' debe ser igual o menor a " + temp.formato(2, true));
				capExp.PanExpendio.select();
			}
			else if (pan_expendio > 0 && fijo == null && por_gan > 0 && pan_expendio < tope) {
				temp = new oNumero(tope);
				alert("'Total' debe ser igual o mayor a " + temp.formato(2, true));
				capExp.PanExpendio.select();
				return false;
			}
			else if (pan_expendio > 0 && fijo > 0 && por_gan == null && pan_expendio > tope) {
				temp = new oNumero(tope);
				alert("'Total' debe ser igual o menor a " + temp.formato(2, true));
				capExp.PanExpendio.select();
				return false;
			}
			
			// Validar Devuelto
			if (!(capExp.num_cia.value == 71 && ((capExp.NumExpendio.value == 3 && capExp.NombreExpendio.value == "AGRICOLA S/D")
			 || (capExp.NumExpendio.value == 4 && capExp.NombreExpendio.value == "ISABEL LA CATOLICA S/D"))) && devolucion > abono) {
				alert("'Devuelto' no puede ser mayor a 'Abono'");
				capExp.Devolucion.select();
				return false;
			}
			
			// Validar Rezago
			if (rezago < 0) {
				alert("'Rezago Final' no puede ser negativo");
				capExp.PanVenta.select();
				return false;
			}
		}
		if (confirm("\u00BFSon correctos todos los datos?")) {
			capExp.submit();
		}
		else {
			capExp.PanVenta.select();
			return false;
		}
	}
	else {
		for (i = 0; i < capExp.NumExpendio.length; i++) {
			pan_venta = !isNaN(parseFloat(capExp.PanVenta[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanVenta[i].value.replace(/\,/g, '')) : 0;
			devolucion = !isNaN(parseFloat(capExp.Devolucion[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Devolucion[i].value.replace(/\,/g, '')) : 0;
			pan_expendio = !isNaN(parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.PanExpendio[i].value.replace(/\,/g, '')) : 0;
			abono = !isNaN(parseFloat(capExp.Abono[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.Abono[i].value.replace(/\,/g, '')) : 0;
			rezago = !isNaN(parseFloat(capExp.RezagoFinal[i].value.replace(/\,/g, ''))) ? parseFloat(capExp.RezagoFinal[i].value.replace(/\,/g, '')) : 0;
			
			expendio = "Expendio: " + capExp.NumExpendio[i].value + " - " + capExp.NombreExpendio[i].value + "\n\n";
			
			if (pan_venta != 0 || devolucion != 0 || pan_expendio != 0 || abono != 0) {
				// Validar Pan p/Venta
				if (pan_venta < 0) {
					alert(expendio + "'Partida' no puede ser menor a 0");
					capExp.PanVenta[i].select();
					return false;
				}
				
				// Validar Abono
				if (abono < 0) {
					alert(expendio + "'Abono' no puede ser menor a 0");
					capExp.Abono[i].select();
					return false;
				}
				
				// Validar Pan p/Expendio
				por_gan = !isNaN(parseFloat(capExp.PorGanancia[i].value)) ? parseFloat(capExp.PorGanancia[i].value) : null;
				fijo = !isNaN(parseFloat(capExp.ImporteFijo[i].value)) ? parseFloat(capExp.ImporteFijo[i].value) : null;
				
				if (por_gan != null) {
					tope = (pan_venta * (100 - por_gan) / 100) - 0.30;
				}
				else if (fijo != null) {
					tope = pan_venta - fijo - 0.30;
				}
				
				if (pan_venta < pan_expendio) {
					alert(expendio + "'Total' no puede ser mayor a 'Partidas'");
					capExp.PanExpendio[i].select();
					return false;
				}
				else if (pan_expendio > 0 && fijo == null && por_gan == 0 && pan_expendio > pan_venta) {
					temp = new oNumero(tope);
					alert(expendio + "'Total' debe ser igual o menor a " + temp.formato(2, true));
					capExp.PanExpendio[i].select();
					return false;
				}
				else if (pan_expendio > 0 && fijo == null && por_gan > 0 && pan_expendio < tope) {
					temp = new oNumero(tope);
					alert(expendio + "'Total' debe ser igual o mayor a " + temp.formato(2, true));
					capExp.PanExpendio[i].select();
					return false;
				}
				else if (pan_expendio > 0 && fijo > 0 && por_gan == null && pan_expendio > tope) {
					temp = new oNumero(tope);
					alert(expendio + "'Total' debe ser igual o menor a " + temp.formato(2, true));
					capExp.PanExpendio[i].select();
					return false;
				}
				
				// Validar Devuelto
				if (!(capExp.num_cia.value == 71 && ((capExp.NumExpendio[i].value == 3 && capExp.NombreExpendio[i].value == "AGRICOLA S/D")
				 || (capExp.NumExpendio[i].value == 4 && capExp.NombreExpendio[i].value == "ISABEL LA CATOLICA S/D"))) && devolucion > abono) {
					alert(expendio + "'Devuelto' no puede ser mayor a 'Abono'");
					capExp.Devolucion[i].select();
					return false;
				}
				
				// Validar Rezago
				if (rezago < 0) {
					alert(expendio + "'Rezago Final' no puede ser negativo");
					capExp.PanVenta[i].select();
					return false;
				}
			}
		}
		
		if (confirm("\u00BFSon correctos todos los datos?")) {
			capExp.sig.disabled = true;
			capExp.submit();
		}
		else {
			if (capExp.NumExpendio.length == undefined) {
				capExp.PanVenta.select();
			}
			else {
				capExp.PanVenta[0].select();
			}
			return false;
		}
	}
}

function seleccionar_primero() {
	if (capExp.NumExpendio.length == undefined) {
		capExp.PanVenta.select();
	}
	else {
		capExp.PanVenta[0].select();
	}
}

// Seleccionar primer campo al cargar página
window.onload = seleccionar_primero();
-->
</script>
<!-- END BLOCK : captura -->