<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Relaci&oacute;n de pasteles entregados </p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<0 || document.form.cia.value=="")
		alert("Compañía erronea");
	else if (document.form.bandera.value==0 && document.form.fecha.value=="")
		alert("Especifique una fecha de consulta");
//	else if (document.form.bandera.value==1 && document.form.num_fac.value=="")
//		alert("Especifique una fecha de consulta");

	else
	document.form.submit();
}

function actualiza_fecha() {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = document.form.fecha.value;
		var anio_actual = {anio_actual};
//		var anio_actual = 2004;		
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
//			if (anio == anio_actual || anio== anio_actual - 1) {
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
/*			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
*/		}
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
//			if (anio == (anio_actual) || anio== anio_actual -1) {
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
/*			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
*/		}
		else {
			document.form.fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha.focus();
			return;
		}
	}



</script>

<form name="form" method="get" action="./pan_rel_con.php">
  <table class="">
    <tr class="tabla">
      <th class="vtabla">
        <label>
        <input name="consulta" type="radio" value="fecha" checked onchange="document.form.bandera.value=0;">
      Fecha</label>
        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha();" size="10" onKeyDown="if (event.keyCode == 13) document.form.cia.select();">
      </th>
      <th class="vtabla">
	  <label>
        <input name="consulta" type="radio" value="factura" onchange="document.form.bandera.value=1;">
        Mes</label>
		<select name="mes" class="insert">
		<!-- START BLOCK : mes -->
          <option value="{mes}" {selected}>	{nombre_mes}</option>
		  <!-- END BLOCK : mes -->
        </select>
		&nbsp;Año
		<input name="anio" type="text" class="insert" id="anio" onKeyDown="if (event.keyCode == 13) document.form.cia.select();" value="{anio_actual}" size="5">
      </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5" onKeyDown="if(event.keyCode == 13) document.form.enviar.focus();">
        <input name="bandera" type="hidden" class="insert" id="bandera" value="0" size="5">
      </td>
    </tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
</td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha.select();
</script>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : por_mes -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">RELACIÓN DE KILOS ENTREGADOS PARA LA COMPAÑÍA <br>{num_cia} {nombre_cia} <br> 
de {mes} del {anio}</p>

<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Dia</th>
    <th scope="col" class="tabla">Kilos entregados</th>
	<th scope="col" class="tabla">Kilos producción</th>
	<th scope="col" class="tabla">Facturas liquidadas</th>
    <th scope="col" class="tabla">Importe de las facturas</th> 
  </tr>
  <!-- START BLOCK : facturas_mes -->
  <tr class="tabla">
    <td class="tabla">{dia}</td>
    <td class="tabla">{kilos}</td>
	<td class="tabla">{kilos_produccion}</td>
	<td class="tabla">{facturas}</td>
	<td class="tabla">{importe}</td> 
    
  </tr>
  <!-- END BLOCK : facturas_mes -->

  <tr class="tabla">
    <th class="tabla">Totales</th>
    <td class="tabla"><strong>{total_kilos}</strong></td>
    <td class="tabla"><strong>{total_kilos_produccion}</strong></td>
    <td class="tabla"><strong>{total_facturas}</strong></td>
    <td class="tabla"><strong>{total_importe}</strong></td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : por_mes -->

<!-- START BLOCK : por_dia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">RELACIÓN DE KILOS ENTREGADOS PARA LA COMPAÑÍA <br>{num_cia} {nombre_cia} <br> del {dia} de {mes} del {anio}</p>
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Factura</th>
    <th scope="col" class="tabla">Kilos</th>
    <th scope="col" class="tabla">Importe total </th>
  </tr>
  <!-- START BLOCK : facturas_dia -->
  <tr class="tabla">
    <td class="tabla">{let_folio}&nbsp;{num_remi}</td>
    <td class="tabla">{kilos}</td>
    <td class="tabla">{importe}</td>
  </tr>
  <!-- END BLOCK : facturas_dia -->
  <tr class="tabla">
    <th class="tabla">Totales</th>
    <td class="tabla"><strong>{total_kilos}</strong></td>
    <td class="tabla"><strong>{total_importe}</strong></td>
  </tr>
  <tr class="tabla">
    <th class="tabla" colspan="">Kilos producci&oacute;n</th>
    <td class="tabla"><strong>{total_produccion}</strong></td>
	<td class="tabla">&nbsp;</td>
  </tr>
</table>
</td>
</tr>
</table>

<!-- END BLOCK : por_dia -->



