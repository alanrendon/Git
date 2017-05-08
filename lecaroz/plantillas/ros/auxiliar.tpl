<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
/*		if(document.form.compania.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.compania.select();
		}
		else if(document.form.fecha.value == "") {
			alert('Debe especificar la fecha');
			document.form.fecha.select();
		}
		else {
		*/		document.form.submit();
		//}
	}

function actualiza_fecha() {
		var fecha = document.form.fecha.value;
		var anio_actual = {anio_actual};
			
		// Si la fecha tiene el formato ddmmaaaa
		if (fecha.length == 8) {
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
			if (anio == anio_actual) {
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
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
						}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha.focus();
					return;
				}
			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
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
			if (anio == (anio_actual)) {
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
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha.focus();
					return;
				}
			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;

			}
		}
		else {
			document.form.fecha.value = "";
			document.form.fecha1.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha.focus();
			return;
		}
	}
function actualiza_fecha1() {
		var fecha = document.form.fecha1.value;
		var anio_actual = {anio_actual};
			
		// Si la fecha tiene el formato ddmmaaaa
		if (fecha.length == 8) {
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
			if (anio == anio_actual) {
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
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;

					}
					else {
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {

					document.form.fecha1.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha1.focus();
					return;
				}
			}
			else {

				document.form.fecha1.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha1.focus();
				return;
			}
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
			if (anio == (anio_actual)) {
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
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha1.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha1.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha1.focus();
					return;
				}
			}
			else {
				document.form.fecha1.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form1.fecha.focus();
				return;
			}
		}
		else {
			document.form.fecha1.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha1.focus();
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
<p class="title">Consulta de Hoja Diaria </p>
<form name="form" action="./ros_hoja_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr>
		<th class="vtabla">Fecha Inicial </th>
		<td class="vtabla">
		<input class="insert" name="fecha" type="text" id="fecha" size="10" maxlength="10" onChange="actualiza_fecha()">
		</td>
	    <th class="vtabla">Fecha Final </th>
	    <td class="vtabla"><input class="insert" name="fecha1" type="text" id="fecha1" size="10" maxlength="10" onChange="actualiza_fecha1()"></td>
	</tr>
	<tr>
		<td class="vtabla" colspan="2">
			<input name="tipo_cia" type="radio" value="cia"  onSelect="reappear();"checked> 
			Compañía <input class="insert" name="compania" type="text" id="compania" size="10" maxlength="10">
			<br>
            <input name="tipo_cia" type="radio" value="todas" onSelect="disappear();">			
          Todas<br></td>
			<td class="vtabla" colspan="2"><input name="totales" type="radio" value="desgloce" checked>
Desglozado&nbsp;<br>
<input name="totales" type="radio" value="total">
Solo totales </td>
	</tr>
	<tr>
	  <th class="tabla" colspan="4" align="center">&nbsp;
	  </th>
	  
    </tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_dia -->
<table width="100%">
	<tr>
		<td class="print_encabezado" align="center">Consulta de Hoja diaria</td>
	</tr>
	<tr>
		<td class="print_encabezado" align="center">DEL {fecha} A {fecha1}</td>
	</tr>
	
</table>
<br>
<table width="100%">
<!-- START BLOCK : rosticeria -->
  	<tr>
		<th class="vprint" colspan="5"><strong>{num_cia}&#8212;{nombre_cia} {fecha}</strong></th>
	</tr>
	
	<tr>
		<th class="print" width="20%">Materia Prima </th>
		<th class="print" width="20%">Unidades</th>
		<th class="print" width="20%">Precio Unitario</th>
		<th class="print" width="20%">Precio Total</th>
		
	</tr>
	<!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vprint" width="20%">{codmp}&#8212;{nom}</td>
		<td class="print" width="20%">{unidades}</td>
		<td class="print" width="20%">{unitario}</td>
		<td class="print" width="20%">{precio}</td>
		
		
	</tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : totales -->
	<tr>
		<th class="print" width="20%"><font size="1"></font>Totales</th>
		<th class="print_total" width="20%"><font size="1">{total_unidades}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_unitario}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_precio}</font></th>
  <tr><td height="20" colspan="5"></td></tr>
	</tr>
	<!-- END BLOCK : totales -->
	<!-- END BLOCK : rosticeria -->
	<!-- START BLOCK : encabezado_solo_totales -->
	<tr>
		<th class="print" width="40%" ><font size="1">Rosticería</font></th>
		<td class="print" width="20%"><font size="1">Total pago crédito</font></td>
		<td class="print" width="20%"><font size="1">Total pago contado</font></td>
		<td class="print" width="20%"><font size="1">Total Facturación</font></td>
  	<tr>
	<!-- END BLOCK : encabezado_solo_totales -->	
	<!-- START BLOCK : solo_totales -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<th class="vprint" width="40%" ><font size="1">{num_cia}&#8212;{nombre_cia}</font></th>
		<td class="print" width="20%"><font size="1">{total_credito}</font></td>
		<td class="print" width="20%"><font size="1">{total_contado}</font></td>
		<td class="print" width="20%"><font size="1">{total_factura}</font></td>
  	<tr>
	<!-- END BLOCK : solo_totales -->
	
	
	</tr>
	<!-- START BLOCK : totalGeneral -->
	<tr>
		<th class="print" width="20%">Total General </th>
		<th class="print_total" width="20%">{totalgral_credito}</th>
		<th class="print_total" width="20%">{totalgral_contado}</th>
		<th class="print_total" width="20%">{totalgral_factura}</th>
	</tr>
	<!-- END BLOCK : totalGeneral -->
	<!-- START BLOCK : solo_totalGeneral -->
	<tr>
		<th class="print" width="40%" ><font size="1">Total General</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_unidades}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_unitario}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_precio}</font></th>
	</tr>
	<!-- END BLOCK : solo_totalGeneral -->

</table>
<!-- END BLOCK : listado_dia -->
<br>
