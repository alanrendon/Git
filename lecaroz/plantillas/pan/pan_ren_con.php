<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function actualiza_fecha() {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = document.form.fecha_mov.value;
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
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha_mov.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha_mov.focus();
					return;
				}
			}
			else {
				document.form.fecha_mov.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha_mov.focus();
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
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha_mov.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha_mov.focus();
					return;
				}
			}
			else {
				document.form.fecha_mov.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha_mov.focus();
				return;
			}
		}
		else {
			document.form.fecha_mov.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha_mov.select();
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
<p class="title">Rendimientos de la Harina </P>
<form name="form" action="./pan_ppn_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">FECHA <input class="insert" name="fecha_mov" type="text" id="fecha_mov" size="10" maxlength="10" onChange="actualiza_fecha()" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();"></th>
    </tr>
    <tr>
      <td class="vtabla"><p>
        <label><input type="radio" name="consulta" value="0" checked onChange="form.tipo_cia.value=0">Compañía</label> 
		<input class="insert" name="num_cia" type="text" id="num_cia" size="5" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
        <input name="tipo_cia" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_total" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_turno" type="hidden" class="insert" id="tipo_turno" value="0"  >
        <br>
        <label><input type="radio" name="consulta" value="1" onChange="form.tipo_cia.value=1">Todas</label></p>
	  </td>
    </tr>
    <tr>
      <td class="vtabla">
	    <p>
	      <label>
	      <input name="turno" type="radio" value="0" checked onChange="form.tipo_turno.value=0;">
  Francesero Dia &#8212; Francesero Noche</label>
	      <br>
	      <label>
	      <input type="radio" name="turno" value="1" onChange="form.tipo_turno.value=1;">
  Bizcochero &#8212; Repostero</label>
	      <br>
	      </p>
	  </td>
    </tr>



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
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

  <table class="listado">
  <tr class="listado">
  	<th class="listado" colspan="13"><font size="-2"><strong>Reporte para la prueba de pan al {dia} de {mes} de {anio} {hora}</strong></font></th>
  </tr>

  <tr class="listado">
  	<th class="listado" colspan="13">{num_cia}-{nom_cia}</th>
  </tr>
  
  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="listado">Producción</th>
    <th scope="col" class="listado">Pan Comprado</th>
    <th scope="col" class="listado">Sobrante Ayer</th>
    <th scope="col" class="listado">Total de pan</th>
    <th scope="col" class="listado">Venta en puerta</th>
    <th scope="col" class="listado">&nbsp;&nbsp;&nbsp;Reparto&nbsp;&nbsp;&nbsp;</th>
    <th scope="col" class="listado">Pan Devuelto</th>
    <th scope="col" class="listado">Pan Quebrado</th>
    <th scope="col" class="listado">Descuento Pastel</th>
    <th scope="col" class="listado">Sobrante de Hoy</th>
    <th scope="col" class="listado">Existencia Física</th>
    <th scope="col" class="listado">Diferencia</th>
    </tr>
<!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{dia}</th>
    <td class="rlistado">{produccion}</td>
    <td class="rlistado">{pan_comprado}</td>
    <td class="rlistado">{sobrante_ayer}</td>
    <td class="rlistado">{total_pan}</td>
    <td class="rlistado">{venta_puerta}</td>
    <td class="rlistado">{reparto}</td>
    <td class="rlistado">{pan_devuelto}</td>
    <td class="rlistado">{pan_quebrado}</td>
    <td class="rlistado">{desc_pastel}</td>	
    <td class="rlistado">{sobrante_hoy}</td>
    <td class="rlistado">{existencia_fisica}</td>
    <td class="rlistado">{diferencia}</td>
    </tr>
 <!-- END BLOCK : rows -->
  <tr>
    <th scope="row" class="listado_total">&nbsp;</th>
    <th class="rlistado">{total_produccion}</th>
    <th class="rlistado">{total_comprado}</th>
    <th class="listado">&nbsp;</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_puerta}</th>
    <th class="rlistado">{total_reparto}</th>
    <th class="rlistado">{total_devuelto}</th>
    <th class="rlistado">{total_quebrado}</th>
    <th class="rlistado">{total_desc_pastel}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado"></th>
    <th class="listado">{total_diferencia}</th>
    </tr>
  <tr>
    <th scope="row" class="listado_total">&nbsp;</th>
    <th class="listado" colspan="2">Efectivo/ Produccion</th>

    <th class="rlistado">{ef_prod}</th>
    <th class="listado">&nbsp;</th>
    <th class="listado" colspan="2">Promedio Diferencia </th>
    <th class="rlistado">{promedio_dif}</th>
    <th class="listado">&nbsp;</th>
    <th class="listado">&nbsp;</th>
    <th class="listado" colspan="2">%Diferencia / Producto</th>
    <th class="listado">{porc_dif}</th>
    </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : compania -->
<br><br>

<!-- START BLOCK : totales -->
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">


  <table class="listado">
  <tr class="listado">
  	<th class="listado" colspan="12">{num_cia}-{nom_cia}</th>
  </tr>
  
  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="listado">Producción</th>
    <th scope="col" class="listado">Pan Comprado</th>
    <th scope="col" class="listado">Venta en puerta</th>
    <th scope="col" class="listado">Reparto</th>
    <th scope="col" class="listado">Pan devuelto</th>
    <th scope="col" class="listado">Pan quebrado</th>
    <th scope="col" class="listado">Descuento pastel</th>
    <th scope="col" class="listado">Diferencia</th>
    <th scope="col" class="listado">Efectivo / Produccion</th>
    <th scope="col" class="listado">Promedio Diferencia</th>
    <th scope="col" class="listado">%Diferencia / Producto</th>

    </tr>

  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{dia}</th>
    <td class="rlistado">{total_produccion}</td>
    <td class="rlistado">{total_pan_comprado}</td>
    <td class="rlistado">{total_puerta}</td>
    <td class="rlistado">{reparto}</td>
    <td class="rlistado">{total_devuelto}</td>
    <td class="rlistado">{total_quebrado}</td>
    <td class="rlistado">{total_desc_pastel}</td>
    <td class="rlistado">{total_diferencia}</td>
    <td class="rlistado">{ef_prod}</td>	
    <td class="rlistado">{promedio_dif}</td>
    <td class="rlistado">{porc_dif}</td>
  </tr>

</table>
</td>
</tr>
</table>
<!-- END BLOCK : totales -->
<!-- END BLOCK : prueba_pan -->