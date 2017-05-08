<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
	if(document.form.fecha.value=="" || document.form.fecha1.value==""){
		alert("Revisar fechas");
		document.form.fecha.select();
		}
	else if(document.form.tipo_con.value==0 && document.form.compania.value==""){
		alert("Falta compañía");
		document.form.compania.select();
		}
	else document.form.submit();

	}

function actualiza_fecha(campo_fecha) {
	var fecha = campo_fecha.value;
	var anio_actual = {anio_actual};

	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8)
	{
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0)) == 0)
			dia = parseInt(fecha.charAt(1));
		else
			dia = parseInt(fecha.substring(0,2));
		if (parseInt(fecha.charAt(2)) == 0)
			mes = parseInt(fecha.charAt(3));
		else
			mes = parseInt(fecha.substring(2,4));
		anio = parseInt(fecha.substring(4));

		// El año de captura de ser el año en curso
	//			if (anio == anio_actual) {
			// Generar dias por mes
			var diasxmes = new Array();
			diasxmes[1] = 31; // Enero
			if (anio%4 == 0)
				diasxmes[2] = 29; // Febrero año bisiesto
			else
				diasxmes[2] = 28; // Febrero
			diasxmes[3] = 31; // Marzo
			diasxmes[4] = 30; // Abril
			diasxmes[5] = 31; // Mayo
			diasxmes[6] = 30; // Junio
			diasxmes[7] = 31; // Julio
			diasxmes[8] = 31; // Agosto
			diasxmes[9] = 30; // Septiembre
			diasxmes[10] = 31; // Octubre
			diasxmes[11] = 30; // Noviembre
			diasxmes[12] = 31; // Diciembre

			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia == diasxmes[mes] && mes < 12) {
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				else if (dia == diasxmes[mes] && mes == 12) {
					campo_fecha.value = dia+"/"+mes+"/"+anio;

				}
				else {
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
			}
			else {

				campo_fecha.value = "";
				alert("Rango de fecha no valido");
				campo_fecha.select();
				return;
			}
	//}
//			else {

//				document.form.fecha1.value = "";
//				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
//				document.form.fecha1.focus();
//				return;
//			}
		}
		else if (fecha.length == 6) {
			// Descomponer fecha en dia, mes y año
			if (parseInt(fecha.charAt(0)) == 0)
				dia = parseInt(fecha.charAt(1));
			else
				dia = parseInt(fecha.substring(0,2));
			if (parseInt(fecha.charAt(2)) == 0)
				mes = parseInt(fecha.charAt(3));
			else
				mes = parseInt(fecha.substring(2,4));
			anio = parseInt(fecha.substring(4)) + 2000;

			// El año de captura de ser el año en curso
//			if (anio == (anio_actual)) {
				// Generar dias por mes
				var diasxmes = new Array();
				diasxmes[1] = 31; // Enero
				if (anio%4 == 0)
					diasxmes[2] = 29; // Febrero año bisiesto
				else
					diasxmes[2] = 28; // Febrero
				diasxmes[3] = 31; // Marzo
				diasxmes[4] = 30; // Abril
				diasxmes[5] = 31; // Mayo
				diasxmes[6] = 30; // Junio
				diasxmes[7] = 31; // Julio
				diasxmes[8] = 31; // Agosto
				diasxmes[9] = 30; // Septiembre
				diasxmes[10] = 31; // Octubre
				diasxmes[11] = 30; // Noviembre
				diasxmes[12] = 31; // Diciembre

				if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
					if (dia == diasxmes[mes] && mes < 12) {
						campo_fecha.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						campo_fecha.value = dia+"/"+mes+"/"+anio;
					}
					else {
						campo_fecha.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					campo_fecha.value = "";
					alert("Rango de fecha no valido");
					campo_fecha.select();
					return;
				}
//			}
//			else {
//				document.form.fecha1.value = "";
//				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
//				document.form1.fecha.focus();
//				return;
//			}
		}
		else {
			campo_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			campo_fecha.select();
			return;
		}
	}
function disappear()
{
    compania.style.visibility="hidden";
	document.form.compania.visibility="hidden";
    }
function reappear()
{
    compania.style.visibility="visible";
	document.form.compania.visibility="visible";
    }

</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Reporte de Facturas POLLOS GUERRA </p>
<form name="form" action="./ros_fac_con.php" method="get">
<table class="tabla">
	<tr>
		<th class="vtabla">Fecha Inicial </th>
		<td class="vtabla">
		<input class="insert" name="fecha" type="text" id="fecha" size="10" maxlength="10" onchange="actualiza_fecha(this)" onkeydown="if (event.keyCode == 13) form.fecha1.focus();">
		</td>
	    <th class="vtabla">Fecha Final </th>
	    <td class="vtabla"><input class="insert" name="fecha1" type="text" id="fecha1" size="10" maxlength="10" onchange="actualiza_fecha(this)" onkeydown="if (event.keyCode == 13) form.compania.focus();"></td>
	</tr>
	<tr>
	  <th colspan="2" class="vtabla">Proveedor</th>
	  <td colspan="2" class="vtabla"><select name="num_pro" class="insert" id="num_pro">
<option value=""></option>
<option value="13">13 POLLOS GUERRA</option>
		<!-- <option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
		<option value="1386">1386 EL RANCHERITO S.A. DE C.V.</option> -->
		<!-- START BLOCK : pro -->
		<option value="{value}">{value} {text}</option>
		<!-- END BLOCK : pro -->
		</select></td>
	  </tr>
	<tr>
		<td class="vtabla" colspan="2">
			<input name="tipo_cia" type="radio" value="cia"  onselect="reappear();"checked onchange="document.form.tipo_con.value=0;">
			Compañía <input class="insert" name="compania" type="text" id="compania" size="10" maxlength="10">
			<br>
            <input name="tipo_cia" type="radio" value="todas" onselect="disappear();" onchange="document.form.tipo_con.value=1;">
          Todas
          <input name="tipo_con" type="hidden" class="nombre" value="0" size="3">          <br></td>
			<td class="vtabla" colspan="2">
			<input name="tipo_consulta" type="radio" value="pendiente" checked>
			Pendientes<br>
			<input name="tipo_consulta" type="radio" value="pagado">
			Pagados<br>
			<input name="tipo_consulta" type="radio" value="todo">
			Todos</td>
	</tr>
	<tr>
	  <td class="tabla" colspan="4" align="center">
	  <input name="totales" type="radio" value="desgloce" checked>
	  Desglozado&nbsp;
	  <input name="totales" type="radio" value="total">
	  Solo totales
	  </td>

    </tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onclick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_dia -->
<p class="title" align="center">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </p>
<table width="100%">
	<tr>
		<td  class="print_encabezado"align="center">REPORTE DE FACTURAS DE COMPRAS DE POLLO </td>
	</tr>
	<tr>
		<td class="print_encabezado" align="center">DEL {fecha} A {fecha1}</td>
	</tr>

</table>
<br>
<table width="100%">
<!-- START BLOCK : rosticeria -->
  	<tr>
		<th class="vprint" colspan="5"><strong>{num_cia}&#8212;{nombre_cia}</strong></th>
	</tr>

	<tr>
		<th class="print" width="20%">N&uacute;mero de Folio </th>
		<th class="print" width="20%">Fecha</th>
		<th class="print" width="20%">Total de Crédito </th>
		<th class="print" width="20%">Total de Contado</th>
		<th class="print" width="20%">Total Factura </th>

	</tr>
	<!-- START BLOCK : fila -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
		<td class="vprint" width="20%">{num_fac}</td>
		<td class="vprint" width="20%">{fecha}</td>
		<td class="print" width="20%">{credito}</td>
		<td class="print" width="20%">{contado}</td>
		<td class="print" width="20%">{total_fac}</td>


	</tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : totales -->
	<tr>
		<th class="print" width="20%"><font size="1"></font>Totales</th>
		<th class="print_total" width="20%">&nbsp;</th>
		<th class="print_total" width="20%"><font size="1">{total_credito}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_contado}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_factura}</font></th>
  <tr><td height="20" colspan="5"></td></tr>
	</tr>
	<!-- END BLOCK : totales -->
	<!-- END BLOCK : rosticeria -->
	<!-- START BLOCK : encabezado_solo_totales -->
	<tr>
		<th class="print" width="40%" colspan="2"><font size="1">Rosticería</font></th>
		<td class="print" width="20%"><font size="1">Total pago crédito</font></td>
		<td class="print" width="20%"><font size="1">Total pago contado</font></td>
		<td class="print" width="20%"><font size="1">Total Facturación</font></td>
  	<tr>
	<!-- END BLOCK : encabezado_solo_totales -->
	<!-- START BLOCK : solo_totales -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
		<th class="vprint" width="40%" colspan="2"><font size="1">{num_cia}&#8212;{nombre_cia}</font></th>
		<td class="print" width="20%"><font size="1">{total_credito}</font></td>
		<td class="print" width="20%"><font size="1">{total_contado}</font></td>
		<td class="print" width="20%"><strong><font size="1">{total_factura}</font></strong></td>
  	<tr>
	<!-- END BLOCK : solo_totales -->


	</tr>
	<!-- START BLOCK : totalGeneral -->
	<tr>
		<th class="print" width="20%">Total General </th>
		<th class="print_total" width="20%">&nbsp;</th>
		<th class="print_total" width="20%">{totalgral_credito}</th>
		<th class="print_total" width="20%">{totalgral_contado}</th>
		<th class="print_total" width="20%">{totalgral_factura}</th>
	</tr>
	<!-- END BLOCK : totalGeneral -->
	<!-- START BLOCK : solo_totalGeneral -->
	<tr>
		<th class="print" width="40%" colspan="2"><font size="1">Total General</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_credito}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_contado}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_factura}</font></th>
	</tr>
	<!-- END BLOCK : solo_totalGeneral -->

</table>
<!-- END BLOCK : listado_dia -->
<br>
