<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
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
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consultar notas de pastel pendientes de pago </p>
<form name="form" method="get" action="./admin_fac_con.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">
        <label> Hasta el dia: </label></th>
    </tr>

	<tr class="tabla">
      <td class="tabla" colspan="2">
	  <input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==9 || event.keyCode==13) document.form.enviar.focus();"value="{fecha}" size="10" maxlength="10">
	  </td>
	</tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar" {disabled}>
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
</td>
</tr>
<tr>
  <td align="center" valign="middle">&nbsp;</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : facturas -->
<script language="JavaScript" type="text/JavaScript">

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Facturas pendientes de pago <br>hasta el dia {fecha}</p>

<table class="tabla">
<!-- START BLOCK : rows -->
<!-- START BLOCK : cias -->

  <tr class="tabla">
    <th class="vtabla" colspan="3">
	{num_cia} {nombre_cia}
	</th>
    <th class="rtabla" colspan="2">
	{operadora}
	</th>
  </tr>
  <tr class="tabla">
    <th class="tabla" colspan="2">Folio</th>
    <th class="tabla">Total factura </th>
    <th class="tabla">Pendiente</th>
    <th class="tabla">Fecha de pago </th>
  </tr>
<!-- END BLOCK : cias -->  
  <tr class="tabla">
    <td class="tabla">{let_folio}</td>
	<td class="tabla">{num_fact}</td>
    <td class="tabla">{total}</td>
    <td class="tabla">{resta}</td>
    <td class="tabla">{fecha_entrega}</td>
  </tr>
 <!-- END BLOCK : rows -->
</table>

<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : facturas -->