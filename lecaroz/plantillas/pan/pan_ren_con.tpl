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
<form name="form" action="./pan_ren_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">FECHA <input class="insert" name="fecha_mov" type="text" id="fecha_mov" size="10" maxlength="10" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();"></th>
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
	      <input name="turno" type="radio" value="1" onChange="form.tipo_turno.value=1;">
  Bizcochero &#8212; Repostero</label>
	      <br>
	      <label>
	      <input name="turno" type="radio" value="2" onChange="form.tipo_turno.value=2;">
  RENDIMIENTOS Y EFECTIVOS</label>

	      </p>
	  </td>
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
<table width="100%"  height="48%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
  	<th class="listado" colspan="9"><font size="-2"><strong>Reporte de rendimiento de Harina al {dia} de {mes} de {anio} {hora}</strong></font></th>
  </tr>

  <tr class="listado">
  	<th class="listado" colspan="9">{num_cia}-{nom_cia}</th>
  </tr>
  <tr class="listado">
  	<th class="listado" colspan="5">{nom_turno}</th>
	<th class="listado" colspan="4">{nom_turno1}</th>
  </tr>

  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Producci&oacute;n</th>
    <th scope="col" class="rlistado">Raya</th>
    <th scope="col" class="rlistado">Rendimiento</th>
    <th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Producci&oacute;n</th>

    <th scope="col" class="listado">Raya</th>
    <th scope="col" class="listado">Rendimiento</th>
    </tr>
<!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{dia}</th>
    <td class="rlistado">{consumo}</td>
    <td class="rlistado">{produccion}</td>
    <td class="rlistado">{raya}</td>
    <td class="rlistado">{rendimiento}</td>
    <td class="rlistado">{consumo1}</td>
    <td class="rlistado">{produccion1}</td>
    <td class="rlistado">{raya1}</td>
    <td class="rlistado">{rendimiento1}</td>	
    </tr>
 <!-- END BLOCK : rows -->
  <tr>
    <th class="listado_total">&nbsp;</th>
    <th class="rlistado">{total_consumo}</th>
    <th class="rlistado">{total_produccion}</th>
    <th class="rlistado">{total_raya}</th>
    <th class="rlistado">{total_rendimiento}</th>

    <th class="rlistado">{total_consumo1}</th>
    <th class="rlistado">{total_produccion1}</th>
    <th class="rlistado">{total_raya1}</th>
    <th class="rlistado">{total_rendimiento1}</th>
    </tr>
  <tr>
    <th class="listado" colspan="2">Consumo total</th>
    <th class="rlistado">{consumo_total}</th>
    <th class="listado" colspan="2">Produccion total </th>
    <th class="rlistado">{produccion_total}</th>
    <th class="listado" colspan="2">Rendimiento total</th>
    <th class="rlistado">{rendimiento_total}</th>

  </tr>
</table>
</td>
</tr>
</table>
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->

<!-- END BLOCK : compania -->

<!-- ********************************************************************************************************************* -->
<!-- START BLOCK : turnos -->
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
  	<th class="listado" colspan="10"><font size="-2"><strong>Reporte de rendimiento de Harina al {dia} de {mes} de {anio} {hora}</strong></font></th>
  </tr>

  <tr class="listado">
  	<th class="listado" colspan="10">{num_cia}-{nom_cia}</th>
  </tr>
  <tr class="listado">
  	<th class="listado"></th>
  	<th class="listado" colspan="2">F R A N C E S &nbsp; D E&nbsp;  N O C H E</th>
	<th class="listado" colspan="2">F R A N C E S&nbsp;  D E&nbsp;  D I A</th>
  	<th class="listado" colspan="2">B I Z C O C H E R O</th>
	<th class="listado" colspan="2">R E P O S T E R O</th>
	<th class="listado"></th>

  </tr>

  <tr class="listado">
    <th scope="col" class="listado">Dia</th>
    <th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Rendimiento</th>
    <th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Rendimiento</th>
    <th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Rendimiento</th>
	<th scope="col" class="rlistado">Consumo</th>
    <th scope="col" class="rlistado">Rendimiento</th>
    <th scope="col" class="rlistado">Efectivo</th>
  </tr>
<!-- START BLOCK : renglones -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{dia}</th>
    <td class="rlistado">{consumo1}</td>
    <td class="rlistado">{rendimiento1}</td>
    <td class="rlistado">{consumo2}</td>
    <td class="rlistado">{rendimiento2}</td>
    <td class="rlistado">{consumo3}</td>
    <td class="rlistado">{rendimiento3}</td>
    <td class="rlistado">{consumo4}</td>
    <td class="rlistado">{rendimiento4}</td>	
    <td class="rlistado">{efectivo}</td>
  </tr>
 <!-- END BLOCK : renglones -->
  <tr>
    <th class="listado_total">&nbsp;</th>
    <th class="rlistado">{total_consumo1}</th>
    <th class="rlistado">{total_rendimiento1}</th>
    <th class="rlistado">{total_consumo2}</th>
    <th class="rlistado">{total_rendimiento2}</th>
    <th class="rlistado">{total_consumo3}</th>
    <th class="rlistado">{total_rendimiento3}</th>
    <th class="rlistado">{total_consumo4}</th>
    <th class="rlistado">{total_rendimiento4}</th>
    <th class="rlistado">{total_efectivo}</th>
  </tr>
</table>
</td>
</tr>
</table>
<!-- START BLOCK : salto1 -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto1 -->

<!-- END BLOCK : turnos -->
<!-- END BLOCK : prueba_pan -->
