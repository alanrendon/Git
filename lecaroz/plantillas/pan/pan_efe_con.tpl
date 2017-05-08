<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
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
		}
		else {
			campo_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			campo_fecha.select();
			return;
		}
	}

function valida(){
	if (document.form.tipo_cia.value == 0 && document.form.num_cia.value == ''){
		alert("Falta la compañía");
		document.form.num_cia.select();
		return;
		}
	else if (document.form.fecha_mov.value==""){
		alert("Necesita insertar una fecha");
		document.form.fecha_mov.select();
		return;
		}
	else document.form.submit();
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Reporte de la Producción, Ingresos y Gastos</P>
<form name="form" action="./pan_efe_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">FECHA <input class="insert" name="fecha_mov" type="text" id="fecha_mov" size="10" maxlength="10" onChange="actualiza_fecha(this);" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();"></th>
    </tr>
    <tr>
      <td class="vtabla"><p>
        <label><input type="radio" name="consulta" value="0" checked onChange="form.tipo_cia.value=0">Compañía</label> 
		<input class="insert" name="num_cia" type="text" id="num_cia" size="5" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
        <input name="tipo_cia" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_total" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <br>
        <label><input type="radio" name="consulta" value="1" onChange="form.tipo_cia.value=1">Todas</label></p>
	  </td>
    </tr>
    <!--
	<tr>
	
      <td class="vtabla"><p>
        <label>
        <input type="radio" name="desgloce" value="0" checked onChange="form.tipo_total.value=0">
  Desglozado</label>

        <label>
        <input type="radio" name="desgloce" value="1" onChange="form.tipo_total.value=1">
  Totales</label>
        <br>
      </p></td>
    </tr>
	-->
  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onClick='valida();'>
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha_mov.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : prueba_pan -->


<!-- START BLOCK : compania -->

<table width="100%"  height="49%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%" cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
    <td class="listado" colspan="14"><font size="-3"><strong>Reporte de la Producción, Ingresos y Gastos al {dia} de {mes} de {anio} {hora}</strong></font></td>
  </tr>
  <tr class="listado">
    <th class="listado" colspan="14">{num_cia}-{nom_cia}</th>
  </tr>
  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="listado">Produccion</th>
    <th scope="col" class="listado">Venta Puerta</th>
    <th scope="col" class="listado">Abonos</th>
    <th scope="col" class="listado">Otros</th>
    <th scope="col" class="listado">Total Ingresos</th>
    <th scope="col" class="listado">Raya</th>
    <th scope="col" class="listado">S. Empleados</th>
    <th scope="col" class="listado">S. Encargado</th>
    <th scope="col" class="listado">Panaderos</th>
    <th scope="col" class="listado">Otros</th>
    <th scope="col" class="listado">Total Gastos</th>
    <th scope="col" class="listado">Efectivo</th>
    <th scope="col" class="listado">Clientes</th>
  </tr>
  <!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{dia}</th>
    <td class="rlistado">{produccion}</td>
    <td class="rlistado">{venta_puerta}</td>
    <td class="rlistado">{abonos}</td>
    <td class="rlistado">{otros}</td>
    <td class="rlistado">{ingresos}</td>
    <td class="rlistado">{raya}</td>
    <td class="rlistado">{sueldo_emp}</td>
    <td class="rlistado">{sueldo_enc}</td>
    <td class="rlistado">{panadero}</td>
    <td class="rlistado">{otros2}</td>
    <td class="rlistado">{gastos}</td>
    <td class="rlistado">{efectivos}</td>
    <td class="rlistado">{clientes}</td>
  </tr>
  <!-- END BLOCK : rows -->
  <tr>
    <th scope="row" class="listado_total">&nbsp;</th>
    <th class="rlistado">{total_produccion}</th>
    <th class="rlistado">{total_venta_pta}</th>
    <th class="listado">&nbsp;&nbsp;{total_abonos}</th>
    <th class="listado">&nbsp;&nbsp;{total_otros}</th>
    <th class="rlistado">{total_ingresos}</th>
    <th class="rlistado">{total_raya}</th>
    <th class="rlistado">{total_empleados}</th>
    <th class="rlistado">{total_encargados}</th>
    <th class="rlistado">{total_panaderos}</th>
    <th class="listado">&nbsp;&nbsp;{total_otros2}</th>
    <th class="rlistado">{total_gastos}</th>
    <th class="listado">&nbsp;&nbsp;{total_efectivo}</th>
    <th class="listado">&nbsp;&nbsp;{total_clientes}</th>
  </tr>
  <tr>
    <th scope="row" class="listado_total">&nbsp;</th>
    <th class="listado">{promedio_produccion}</th>
    <th class="listado">{promedio_venta}</th>
    <th class="rlistado">{promedio_abonos}</th>
    <th class="listado">{promedio_otros}</th>
    <th class="listado">{promedio_ingresos}</th>
    <th class="listado">{promedio_raya}</th>
    <th class="rlistado">{promedio_emp}</th>
    <th class="listado">{promedio_enc}</th>
    <th class="listado">{promedio_pan}</th>
    <th class="listado">{promedio_otros2}</th>
    <th class="listado">{promedio_gastos}</th>
    <th class="listado">{promedio_efectivo}</th>
    <th class="listado">{promedio_clientes}</th>
  </tr>
</table></td>
</tr>
</table>
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->
<!-- END BLOCK : compania -->


<!-- START BLOCK : totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="listado">
  <tr class="listado">
  	<th class="listado" colspan="14">{num_cia}-{nom_cia}</th>
  </tr>
  
  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="listado">Produccion</th>
    <th scope="col" class="listado">Venta en puerta</th>
    <th scope="col" class="listado">Abonos</th>
    <th scope="col" class="listado">Otros</th>
    <th scope="col" class="listado">Total Ingresos</th>
    <th scope="col" class="listado">Raya</th>
    <th scope="col" class="listado">Sueldo Empleados</th>
    <th scope="col" class="listado">Sueldo Encargado</th>
    <th scope="col" class="listado">Panaderias</th>
    <th scope="col" class="listado">Otros</th>
    <th scope="col" class="listado">Total Gastos</th>
    <th scope="col" class="listado">Efectivo</th>
    <th scope="col" class="listado">Clientes</th>	
    </tr>
  <tr>
    <th scope="row" class="listado_total">{dia}</th>
    <td class="rlistado">{total_produccion}</td>
    <td class="rlistado">{total_venta_pta}</td>
    <td class="listado">{total_abonos}</td>
    <td class="listado">{total_otros}</td>
    <td class="rlistado">{total_ingresos}</td>
    <td class="rlistado">{total_raya}</td>
    <td class="rlistado">{total_empleados}</td>
    <td class="rlistado">{total_encargados}</td>
    <td class="rlistado">{total_panaderos}</td>
    <td class="listado">{total_otros}</td>
    <td class="rlistado">{total_gastos}</td>
    <td class="listado">{total_efectivo}</td>
	<td class="listado">{total_clientes}</td>
    </tr>
</table>
</td>
</tr>
</table>

  <!-- END BLOCK : totales -->
  <!-- END BLOCK : prueba_pan -->
