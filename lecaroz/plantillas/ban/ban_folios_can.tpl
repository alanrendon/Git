<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function actualiza_fecha(fecha_mov) {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = fecha_mov.value;
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
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else {
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					fecha_mov.value = "";
					alert("Rango de fecha no valido");
					fecha_mov.select();
					return;
				}
			}
			else {
				document.form.fecha_mov.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				fecha_mov.select();
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
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
					else {
						fecha_mov.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					fecha_mov.value = "";
					alert("Rango de fecha no valido");
					fecha_mov.select();
					return;
				}
			}
			else {
				fecha_mov.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				fecha_mov.select();
				return;
			}
		}
		else {
			fecha_mov.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			fecha_mov.select();
			return;
		}
	}
function valida()
{
	if(document.form.fecha_inicial.value=="" || document.form.fecha_final.value=="")
		alert("Revise las fechas por favor");
	if(document.form.consulta.value==0 && (document.form.cia.value=="" || document.form.cia.value <= 0 ))
	{	
		alert("Revise el número de la compañía por favor");
		document.form.num_cia.select();
	}	
	else 
		document.form.submit();
}

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<form name="form" action="./ban_folios_can.php" method="get">
<p class="title">CONSULTA DE CHEQUES CANCELADOS</p>
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">
	Fecha inicio
      <input name="fecha_inicial" type="text" class="insert" id="fecha_inicial" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.fecha_final.select();" value="{f1}" size="12" maxlength="10">
	 </th>
     <th class="tabla">
     Fecha final
      <input name="fecha_final" type="text" class="insert" id="fecha_final" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.num_cia.select();" value="{f2}" size="12" maxlength="10"> 	</th>
  </tr>
  <tr class="tabla">
    <td scope="col" class="tabla" colspan="2"><p>
      <label>
      <input name="consulta" type="radio" value="0" checked>
  Compañía
        </label>
  <input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="3" onKeyDown="if(event.keyCode==13) form.enviar.focus();">


      <label>
      &nbsp;&nbsp;&nbsp;
      <input type="radio" name="consulta" value="1">
  Todas</label>

    </p>
	</td>
  </tr>

</table>
<p>
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="Consultar">
</p>
</form>
</td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha_inicial.select();
</script>


<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">CHEQUES CANCELADOS </p>

<table border="1" class="tabla">
<!-- START BLOCK : compania -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <th class="vtabla" colspan="4"><span class="vtabla">{num_cia}&#8212;{nombre_corto}</span></th>
	  </tr>
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<td class="tabla">Proveedor</td>
		<td class="tabla">N&uacute;mero de folio </td>
		<td class="tabla">Fecha movimiento</td>
		<td class="tabla">Fecha cancelacion</td>

	  </tr>
	<!-- START BLOCK : rows -->	  
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <td class="vtabla">{num_proveedor}&#8212;{nombre_proveedor}</td>
	    <td class="tabla">{folio}</td>
	    <td class="tabla">{fecha}</td>
	    <td class="tabla">{fecha_cancelacion}</td>

	  </tr>
	<!-- END BLOCK : rows -->
<!-- END BLOCK : compania -->
	
	</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->