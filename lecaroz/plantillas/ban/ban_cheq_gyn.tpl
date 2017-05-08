<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/print.css" rel="stylesheet" type="text/css">
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
	
function valida()
{
if (document.form.fecha_inicial.value=="" || document.form.fecha_final.value==""){
	alert("Revise las fechas");
	document.form.fecha_inicial.select();
	return;
	}
else
	document.form.submit();
}
</script>



<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">LISTADO DE CHEQUES <BR>
GASTOS Y N&Oacute;MINAS</p>
<form action="./ban_cheq_gyn.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Fecha inicial      
      <input name="fecha_inicial" type="text" class="insert" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.fecha_final.select();"></th>
    <th class="tabla">Fecha final   
      <input name="fecha_final" type="text" class="insert" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.fecha_inicial.select();"></th>
  </tr>
  <tr class="tabla">
    <th class="tabla">Cuenta</th>
    <th class="tabla"><select name="cuenta" class="insert" id="cuenta">
      <option value="0" selected>-</option>
      <option value="1">BANORTE</option>
      <option value="2">SANTANDER</option>
    </select></th>
  </tr>
  <tr class="tabla">
	<td class="vtabla" colspan="2">
	  <p>
	    <label>
	    <input name="cancelado" type="radio" value="0">
  No cancelados</label>
	    <br>
	    <label>
	    <input type="radio" name="cancelado" value="1">
  Cancelados</label>
	    <br>
	    <label>
	    <input name="cancelado" type="radio" value="2" checked>
  Ambos</label>

	    </p>
	</td>
  </tr>
</table>
<p>
  <input type="button" name="enviar" value="Listado" onClick="valida();" class="boton">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha_inicial.select();
</script>


</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : por_gasto -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado"><font size="2"><strong>LISTADO DE CHEQUES EMITIDOS 
DEL {fecha} AL {fecha1} <br> GASTOS Y NÓMINAS</strong></font>
<!-- START BLOCK : gas_can -->
<br> CANCELADOS
<!-- END BLOCK : gas_can -->
</p>

<table cellpadding="0" cellspacing="0" class="listado">
		  <tr class="listado">
			<th class="listado" colspan="2"><font size="2"><strong>COMPAÑÍA </strong></font></th>
			<th class="listado"><font size="2"><strong>&nbsp;&nbsp;&nbsp;FOLIO&nbsp;&nbsp;&nbsp;</strong></font></th>
			<th class="listado"><font size="2"><strong>&nbsp;&nbsp;&nbsp;IMPORTE&nbsp;&nbsp;&nbsp;</strong></font></th>
		    <th class="listado">&nbsp;</th>
		    <th class="listado" colspan="2"><font size="2"><strong>COMPA&Ntilde;&Iacute;A</strong></font></th>
		    <th class="listado"><font size="2"><strong>&nbsp;&nbsp;&nbsp;FOLIO&nbsp;&nbsp;&nbsp;</strong></font></th>
		    <th class="listado"><font size="2"><strong>&nbsp;&nbsp;&nbsp;IMPORTE&nbsp;&nbsp;&nbsp;</strong></font></th>
      </tr>
		  <!-- START BLOCK : row_gasto -->
		  <tr class="listado">
			<td class="rlistado"><font size="2">{num_cia}&nbsp;&nbsp;&nbsp;</font></td>
			<td class="vlistado"><font size="2">{nombre_cia}</font></td>
			<td class="listado"><font size="2">{folio}</font></td>
			<td class="rlistado"><font size="2">{cantidad}</font></td>
		    <td class="rlistado">&nbsp;</td>
		    <td class="rlistado"><font size="2">{num_cia1}&nbsp;&nbsp;&nbsp;</font></td>
		    <td class="vlistado"><font size="2">{nombre_cia1}</font></td>
		    <td class="listado"><font size="2">{folio1}</font></td>
		    <td class="rlistado"><font size="2">{cantidad1}</font></td>
      </tr>
		  <!-- END BLOCK : row_gasto -->
		  <tr class="listado">
			<th class="rlistado" colspan="4"><strong>TOTAL&nbsp;&nbsp;</strong></th>
			<td class="rlistado"><font size="2"><strong>{total}</strong></font></td>
		    <th class="listado" colspan="4">&nbsp;</th>
	    </tr>
		  <tr class="listado">
		    <th class="rlistado" colspan="4">No. Cheques </th>
		    <td class="rlistado"><font size="3"><strong>{count}</strong></font></td>
		    <th class="listado" colspan="4">&nbsp;</th>
	    </tr>
	  </table>
</td>
</tr>
</table>
<!-- END BLOCK : por_gasto -->
