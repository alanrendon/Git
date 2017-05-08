<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
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
				fecha_mov.focus();
				return;
			}
		}
		else {
			document.form.fecha_mov.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			fecha_mov.select();
			return;
		}
	}

function valida(){
	if (document.form.tipo_cia.value == 0 && document.form.num_cia.value == ''){
		alert("Falta la compañía");
		document.form.num_cia.select();
		return;
		}
	else if (document.form.fecha_mov.value=="" || document.form.fecha_mov1.value==""){
		alert("Revise las fechas");
		document.form.fecha_mov.select();
		return;
		}
	else document.form.submit();
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Reporte de Pasivo a Proveedores </P>
<form name="form" action="./fac_pasivo_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">Fecha inicial
        <input class="insert" name="fecha_mov" type="text" id="fecha_mov" size="10" maxlength="10" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov1.select();">
	  </th>
	  <th class="tabla">
	  Fecha final
	  <input class="insert" name="fecha_mov1" type="text" id="fecha_mov1" size="10" maxlength="10" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();">
	  </th>
	  
    </tr>
    <tr>
      <td class="vtabla" colspan="2"><p>
        <label><input type="radio" name="consulta" value="0" checked onChange="form.tipo_cia.value=0">Compañía</label> 
		<input class="insert" name="num_cia" type="text" id="num_cia" size="5" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
        <input name="tipo_cia" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_total" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <br>
        <label><input type="radio" name="consulta" value="1" onChange="form.tipo_cia.value=1">Todas</label></p>
	  </td>
    </tr>
    <tr>
      <td class="vtabla" colspan="2"><p>
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

<!-- START BLOCK : pasivo -->
<!-- START BLOCK : compania -->
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

  <table class="listado">
  <tr class="listado">
  	<th class="listado" colspan="4"><font size="-2"><strong>Reporte Pasivo Proveedores del {dia} de {mes} de {anio} al {dia2} de {mes2} de {anio2}</strong></font></th>
  </tr>

  <tr class="listado">
  	<th class="listado" colspan="4">{num_cia}-{nom_cia}</th>
  </tr>
  
  <tr class="listado">
    <th scope="col" class="listado">Factura No.</th>
    <th scope="col" class="listado" colspan="2">Proveedor</th>

    <th scope="col" class="listado">Total factura</th>
    </tr>
<!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">
    <th scope="row" class="listado">{num_fact}</th>
    <td class="rlistado">{num_proveedor}</td>
    <td class="vlistado">{nombre_proveedor}</td>
    <td class="rlistado">{total_fac}</td>
    </tr>
 <!-- END BLOCK : rows -->
  <tr>
    <th scope="row" class="listado_total">&nbsp;</th>
    <th class="rlistado" colspan="2">Total a Pagar </th>

	<th class="rlistado">{total_pagar}</th>
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
  	<th class="listado" colspan="2">Proveedor</th>
	<th class="listado">Total</th>
  </tr>
  <!-- START BLOCK : renglones -->
  <tr class="listado">
    <td scope="col" class="listado">{num_proveedor}</td>
    <td scope="col" class="listado">{nom_proveedor}</td>
    <td scope="col" class="listado">{importe}</td>
    </tr>
   <!-- END BLOCK : renglones -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="listado">

    <th class="rlistado" colspan="2">{total_produccion}</th>

    <th class="rlistado">{total_pago}</th>
  </tr>

</table>
</td>
</tr>
</table>
<!-- END BLOCK : totales -->
<!-- END BLOCK : prueba_pan -->