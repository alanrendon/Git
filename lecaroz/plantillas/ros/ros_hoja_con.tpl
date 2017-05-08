<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.cia.value <= 0 || document.form.cia.value =="") {
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else if(document.form.fecha.value == "") {
			alert('Debe especificar la fecha');
			document.form.fecha.select();
		}
		else {
				document.form.submit();
		}
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

</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Hoja Diaria </p>
<form name="form" action="./ros_hoja_con.php" method="get">
<table class="tabla">
	<tr class="tabla">
		<th class="vtabla">Compa&ntilde;&iacute;a</th>
		<td class="vtabla">
		<input class="insert" name="cia" type="text" id="cia" size="10" maxlength="10" onChange="actualiza_fecha()" onKeyDown="if (event.keyCode == 13) form.fecha.select();">
		</td>
	    <th class="vtabla">Fecha</th>
	    <td class="vtabla"><input class="insert" name="fecha" type="text" id="fecha" size="10" maxlength="10" onChange="actualiza_fecha()" onKeyDown="if (event.keyCode == 13) form.enviar.focus();"></td>
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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Hoja diaria para la compañía <br>{num_cia}&nbsp;{nom_cia} del {dia} de {mes} de {anio}</p>
<table width="100%">
<tr>
	<th class="print" >Producto</th>
	<th class="print" >Existencia</th>
	<th class="print" >Mercancia<br>recibida</th>
	<th class="print" >Total</th>
    <th class="print" >Venta<br>total </th>
    <th class="print" >Para<br>mañana</th>
    <th class="print" >Precio de<br> venta</th>
    <th class="print" >Total <br>vendido </th>
    <th class="print" >Importe de <br>venta </th>
</tr>
<!-- START BLOCK : fila -->
<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	<td class="vprint" >{codmp}&#8212;{nom_mp}</td>
	<td class="print" >{existencia}</td>
	<td class="print" >{compra}</td>
	<td class="print" >{total}</td>
    <td class="print" >{venta}</td>
    <td class="print" >{sobrante}</td>
    <td class="rprint" >{precio_venta}</td>
    <td class="print" >{total_vendido}</td>
    <td class="rprint" >{importe_venta}</td>
</tr>
<!-- END BLOCK : fila -->
<!-- START BLOCK : otros -->
<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	<td class="vprint">OTROS</td>
	<td class="print" colspan="7"></td>
    <td class="rprint">{otros}</td>
</tr>
<!-- END BLOCK : otros -->

<tr >
  <td rowspan="3" colspan="7">&nbsp;</td>
  <th class="print">Total de Venta </th>
  <td onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="rprint"><font size="2"><strong>{total_venta}</strong></font></td>
</tr>
<tr >
  <th class="print" >Total de Gastos </th>
  <td onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');"class="rprint" ><font size="2"><strong>{total_gastos}</strong></font></td>
</tr>
<tr >
  <th class="print" >Efectivo</th>
  <td onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="rprint" ><font size="2"><strong>{efectivo}</strong></font></td>
</tr>
</table>
<br>


<table width="100%" >
  <tr>
    <td width="50%" align="center">
			<!-- START BLOCK : gastos -->
		<p class="title">Detalle de Gastos</p>
		<table class="print">
		  <tr class="print">
			<th class="print" colspan="2">Gasto</th>
			<th class="print">Concepto</th>
			<th class="print">Importe</th>
		  </tr>
		<!-- START BLOCK : rows -->
		  <tr class="print">
			<th class="print">{codgastos}</th>
			<td class="vprint">{nom_gasto}</td>
			<td class="print">{concepto}</td>
			<td class="rprint">{importe}</td>
		  </tr>
		 <!-- END BLOCK : rows -->
		  <tr class="print">
			<td class="print" colspan="3">Total</td>
			<th class="rprint"><font size="2"><strong>{total_gastos}</strong></font></th>
		  </tr>
		</table>
		<!-- END BLOCK : gastos -->

	</td>
    <td width="50%" align="center">
		<!-- START BLOCK : prestamos -->
		<p class="title">Detalle de Préstamos</p>
		<table class="print">
		  <tr class="print">
			<th class="print">Nombre</th>
		
			<th class="print">Cantidad</th>
			<th class="print">Abona</th>
			<th class="print">Resta</th>
		  </tr>
		<!-- START BLOCK : renglones -->
		  <tr class="print">
			<th class="print">{nombre}</th>
			<td class="vprint">{cantidad}</td>
			<td class="vprint">{abono}</td>
			<td class="rprint">{resta}</td>
		  </tr>
		 <!-- END BLOCK : renglones -->
		</table>
		<!-- END BLOCK : prestamos -->
	</td>
  </tr>
</table>



</td>
</tr>
</table>
<!-- END BLOCK : listado_dia -->
