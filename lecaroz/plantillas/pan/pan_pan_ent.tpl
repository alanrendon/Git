<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de Pan entregado </p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<=0){
		alert("Compañía erronea");
		document.form.cia.select();
	}
	else if (document.form.fecha.value=="" && document.form.anio.value==""){
		alert("Especifique un año de consulta");
		document.form.anio.select();
	}
	else
	document.form.submit();
}

function actualiza_fecha(campo_fecha) {
	if(campo_fecha.value=="")
		return;

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

<form name="form" method="get" action="./pan_pan_ent.php">
<input type="hidden" size="5" name="tipo_con1" value="1">
  <table class="tabla">
    <tr class="tabla">
      <th class="vtabla">
        <label>      </label>

          <label>
          <input type="hidden" name="tipo_con" value="0" onChange="document.form.tipo_con1.value=0">
  Fecha</label>

        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha(this);" onKeyDown="if (event.keyCode == 13) document.form.cia.select();" value="{fecha_anterior}" size="10">      </th>
	  <th class="tabla">
          <label>
          <input name="tipo_con" type="hidden" value="1" checked onChange="document.form.tipo_con1.value=1">
  Mes</label>

	  <select name="mes" id="mes" class="insert">
	  <!-- START BLOCK : mes -->
	    <option value="{mes}" {selected}>{nombre_mes}</option>
		<!-- END BLOCK : mes -->
	    </select>
	Año
	<input type="text" class="insert" size="5" name="anio" value="{anio_actual}">
	  </th>
	   	
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();" {disabled}>
      </td>
    </tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : resultado_dia -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">{num_cia}-{nombre_cia}
<br>RELACION DE PASTEL ENTREGADO EL <BR>{dia} DE {mes} DEL {anio}</p>
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Letra</th>
    <th scope="col" class="tabla">Número</th>
    <th scope="col" class="tabla">Anticipado</th>
    <th scope="col" class="tabla">Pagado</th>
    <th scope="col" class="tabla">Posterior</th>
    <th scope="col" class="tabla">Fecha pago </th>

  </tr>
  <!-- START BLOCK : facturas -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{letra_folio}</td>
    <td class="tabla">{num_remi}</td>
    <td class="tabla"><font color="#FF0099">{anticipado}</font></td>
    <td class="tabla"><font color="#CC6600">{cuenta}</font></td>
    <td class="tabla"><font color="#339999">{posterior}</font></td>
    <td class="tabla">{fecha_pago}</td>
  </tr>
  <!-- END BLOCK : facturas -->
  <tr class="tabla">
    <th class="tabla" colspan="2">TOTALES</th>
    <td class="tabla"><font color="#FF0099"><strong>{total_anticipadas}</strong></font></td>
    <td class="tabla"><font color="#CC6600"><strong>{total_facturas}</strong></font></td>
    <td class="tabla"><font color="#339999"><strong>{total_posteriores}</strong></font></td>
    <td class="tabla">&nbsp;</td>
  </tr>
  <tr>
 	<th colspan="5" class="rtabla"><font size="2"><strong>Total</strong></font></th>
	<td class="rtabla"><font color="#{color}"><strong>{total_general}</strong></font></td>
  </tr>
  <tr>
	<th colspan="5" class="rtabla">Pastel registrado</th>
	<td class="rtabla"><font color="#0000FF"><strong>{pastel}</strong></font></td>
  </tr>
  <tr>
	<th colspan="5" class="rtabla">Diferencia</th>
	<td class="rtabla"><font color="{color1}"><strong>{diferencia}</strong></font></td>
  </tr>
  
</table>
</td>
</tr>
</table>
<!-- END BLOCK : resultado_dia -->

<!-- START BLOCK : resultado_mes -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">{num_cia}-{nombre_cia}<br>
RELACION DE PASTEL ENTREGADO EN <br> {mes} DEL {anio}</p>

<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Dia</th>
    <th scope="col" class="tabla">Pago anticipado </th>
    <th scope="col" class="tabla">Pastel pagado </th>
    <th scope="col" class="tabla">Pago  posterior</th>
    <th scope="col" class="tabla">Total</th>
    <th scope="col" class="tabla">Pastel registrado </th>
    <th scope="col" class="tabla">Diferencia</th>
  </tr>
  <!-- START BLOCK : dias -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{dia}</td>
    <td class="tabla"><font color="#FF0099">{anticipado}</font></td>
    <td class="tabla"><font color="#CC6600">{cuenta}</font></td>
    <td class="tabla"><font color="#339999">{posterior}</font></td>
    <td class="tabla"><font color="#{color}">{total}</font></td>
    <td class="tabla"><font color="#CC3300">{pastel}</font></td>
    <td class="tabla"><font color="#{color1}">{diferencia}</font></td>
  </tr>
  <!-- END BLOCK : dias -->
  <tr class="tabla">
    <th class="tabla">TOTALES</th>
    <td class="tabla"><font color="#FF0099"><strong>{total_anticipado}</strong></font></td>
    <td class="tabla"><font color="#CC6600"><strong>{total_cuenta}</strong></font></td>
    <td class="tabla"><font color="#339999"><strong>{total_posterior}</strong></font></td>
    <td class="tabla"><font color="#{color}"><strong>{total_general}</strong></font></td>
	<td class="tabla"><font color="#CC3300"><strong>{total_pas}</strong></font></td>
	<td class="tabla"><font color="#{color1}"><strong>{total_dif}</strong></font></td>  
  </tr>
</table>

</td>
</tr>
</table>
<!-- END BLOCK : resultado_mes -->
