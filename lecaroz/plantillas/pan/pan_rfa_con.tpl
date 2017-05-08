<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de Facturas de Venta de pastel</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<0 || document.form.cia.value=="")
		alert("Compañía erronea");
	else if (document.form.bandera.value==0 && document.form.fecha.value=="")
		alert("Especifique una fecha de consulta");
	else if (document.form.bandera.value==1 && document.form.num_fac.value=="")
		alert("Especifique una fecha de consulta");

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

<form name="form" method="get" action="./pan_rfa_con.php">
  <table class="">
    <tr class="tabla">
      <th class="vtabla">
        <label>
        <input name="consulta" type="radio" value="fecha" checked onchange="document.form.bandera.value=0;">
      Fecha</label>
        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha();" size="10" onKeyDown="if (event.keyCode == 13) document.form.cia.select();">
      </th>
      <th class="vtabla"><label>
        <input name="consulta" type="radio" value="factura" onchange="document.form.bandera.value=1;">
      Num. factura
	  </label>
      <input name="num_fac" type="text" class="insert" id="num_fac" size="10" onKeyDown="if (event.keyCode == 13) document.form.cia.select();"> 
      </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5" onKeyDown="if(event.keyCode == 13) document.form.enviar.focus();">
        <input name="bandera" type="hidden" class="insert" id="bandera" size="5">
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

<!-- START BLOCK : fecha -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Facturas capturadas para la compa&ntilde;&iacute;a  {num_cia}&#8212;{nom_cia} del {fecha}</p>
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th class="tabla" colspan="2">No. de factura</th>
		<th class="tabla" >Expendio</th>
		<th class="tabla" >Kilos</th>
		<th class="tabla" >Precio unidad </th>
		<th class="tabla" >Pan</th>
		<th class="tabla" >Base</th>
		<th class="tabla" >A cuenta </th>
		<th class="tabla" >Devoluci&oacute;n de base </th>
		<th class="tabla" >Resta</th>
	    <th class="tabla" >Pastillaje</th>
	    <th class="tabla" >Otros</th>
		<th class="tabla" >Fecha de entrega </th>
	  </tr>
	
	<!-- START BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<th class="tabla">{letra_folio}</th>
		<th class="tabla">{num_remi}</th>	
		<td class="tabla">{idexpendio}</td>
		<td class="tabla">{kilos}</td>
		<td class="tabla">{precio_unidad}</td>
		<td class="tabla">{otros}</td>
		<td class="tabla">{base}</td>
		<td class="tabla">{cuenta}</td>	
		<td class="rtabla">{dev_base}</td>
		<td class="tabla">{resta}</td>
		<td class="rtabla">{pastillaje}</td>
	    <td class="rtabla">{otros_efe}</td>
	    <td class="rtabla">{fecha_entrega}</td>
	  </tr>
	  <!-- END BLOCK : rows -->	  
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <td colspan="6" class="rtabla"><strong>TOTAL</strong></td>
	    <td class="tabla"><strong>{base}</strong></td>
	    <td class="tabla"><strong>{total_cuenta}</strong></td>
	    <td class="rtabla"><strong>{dev_base}</strong></td>
	    <td class="tabla"><strong>{total_resta}</strong></td>
	    <td class="rtabla"></td>
	    <td class="rtabla"></td>
	    <td class="rtabla"></td>
	    </tr>
	</table>
	<br>
	
	<table class="tabla">
		<tr class="tabla">
			<th class="tabla"><font size="+1">VENTA EN PUERTA</font></th>
			<th class="tabla"><font size="+1">ABONO EXPENDIOS</font></th>
			<th class="tabla"><font size="+1">BASES</font></th>
			<th class="tabla"><font size="+1">DEVOLUCIÓN DE BASE</font></th>
			<th class="tabla"><font size="+1">PASTILLAJE</font></th>
			<th class="tabla"><font size="+1">OTROS</font></th>
		</tr>
		<tr class="tabla">
			<td class="tabla"><font size="+1">{venta_pta}</font></td>
			<td class="tabla"><font size="+1">{ab_expendios}</font></td>
			<td class="tabla"><font size="+1">{base}</font></td>
			<td class="tabla"><font size="+1">{dev_base}</font></td>
			<td class="tabla"><font size="+1">{pastillaje}</font></td>
			<td class="tabla"><font size="+1">{otros_efec}</font></td>								
		</tr>
	</table>
	
	
	
	
<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : fecha -->

<!-- START BLOCK : factura -->
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Factura No. {num_fac} de la compañía {num_cia}&#8212;{nom_cia}</p>

<table border="1" class="tabla">
  <tr class="tabla">
    <th class="tabla" colspan="2">No. de factura</th>
    <th class="tabla" >Expendio</th>
    <th class="tabla" >Kilos</th>
    <th class="tabla" >Precio unidad </th>
    <th class="tabla" >Otros</th>
    <th class="tabla" >Base</th>
    <th class="tabla" >A cuenta </th>
    <th class="tabla" >Devoluci&oacute;n de base </th>
    <th class="tabla" >Resta</th>
    <th class="tabla" >Fecha de entrega </th>
    <th class="tabla" >Faltante</th>
    <th class="tabla" >Fecha de entrada </th>
  </tr>
  <!-- START BLOCK : facturas -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
    <th class="tabla">{letra_folio}</th>
    <th class="tabla">{num_remi}</th>
    <td class="tabla">{idexpendio}</td>
    <td class="tabla">{kilos}</td>
    <td class="tabla">{precio_unidad}</td>
    <td class="tabla">{otros}</td>
    <td class="tabla">{base}</td>
    <td class="tabla">{cuenta}</td>
    <td class="rtabla">{dev_base}</td>
    <td class="tabla">{resta}</td>
    <td class="rtabla">{fecha_entrega}</td>
    <td class="rtabla">{faltante}</td>
    <td class="rtabla">{fecha_entrada}</td>
  </tr>
  <!-- END BLOCK : facturas -->	
</table>
<p>
<input name="regresar" type="button" value="{name}" onClick="{onclick}" class="boton">
</p>
</td>
</tr>
</table>

<!-- END BLOCK : factura


