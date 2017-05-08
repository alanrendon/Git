<link href="/styles/prints.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/prints.css" rel="stylesheet" type="text/css">
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
else if(document.form.temp.value == 0 && (document.form.cia.value == "" || parseFloat(document.form.cia.value<=0))){
	alert("Ingrese una compañía");
	document.form.cia.select();
	return;
	}
else if(document.form.temp.value == 2 && (document.form.proveedor.value == "" || parseFloat(document.form.proveedor.value<=0))){
	alert("Ingrese un proveedor");
	document.form.proveedor.select();
	return;
	}
else if(document.form.temp.value == 3 && (document.form.gasto.value == "" || parseFloat(document.form.gasto.value<=0))){
	alert("Ingrese un gasto");
	document.form.gasto.select();
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

<p class="title">LISTADO DE CHEQUES</p>
<form action="./ban_cheques_list1.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Fecha inicial      
      <input name="fecha_inicial" type="text" class="insert" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.fecha_final.select();"></th>
    <th class="tabla">Fecha final   
      <input name="fecha_final" type="text" class="insert" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.cia.select();"></th>
  </tr>
  <tr class="tabla">
    <td class="Vtabla">
      <label><input name="tipo_con" type="radio" value="0" onChange="document.form.temp.value=0">Por compañía&nbsp;</label><input name="cia" type="text" class="insert" size="5" onKeyDown="if(event.keyCode==13) form.enviar.focus();">      <br>
	  <label><input name="tipo_con" type="radio" value="1" onChange="document.form.temp.value=1" checked>Todas las compañías</label>
    </td>
    <td class="vtabla">
	      <label><input type="radio" name="tipo_con" value="2" onChange="document.form.temp.value=2">Por proveedor</label> 
          <input name="proveedor" type="text" class="insert" id="proveedor" size="5" onKeyDown="if(event.keyCode==13) form.enviar.focus();"><br>
          <label><input type="radio" name="tipo_con" value="3" onChange="document.form.temp.value=3">Por gasto</label> 
		  <input name="gasto" type="text" class="insert" id="gasto" size="5" onKeyDown="if(event.keyCode==13) form.enviar.focus();">
		  <input name="temp" type="hidden" class="insert" id="temp" value="1" size="5">	</td>
  </tr>
  <tr class="tabla">
  <td class="vtabla">
    <p>
      <label>
      <input type="radio" name="emitidos" value="0">
  Emitidos</label>
      <br>
      <label>
      <input type="radio" name="emitidos" value="1">
  No emitidos</label>
      <br>
      <label>
      <input type="radio" name="emitidos" value="2" checked>
  Ambos</label>

    </p>
  </td>
	<td class="vtabla">
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

<!-- LISTADO PARA TODAS LAS COMPAÑÍAS CANCELADOS Y NO CANCELADOS PARA TODOS LOS PROVEEDORES-->

<!-- START BLOCK : listado_por_companias -->
<!-- START BLOCK : compania -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">LISTADO DE CHEQUES {emitidos} </p>
<p class="print_encabezado">DEL {fecha}  AL {fecha1} <br>
</p>

<table class="print" width="80%">

  <tr class="print">
    <th width="26%" class="print"><font size="2"><strong>{num_cuenta} </strong></font></th>
    <th colspan="2" class="vprint"><font size="2"><strong>{num_cia}&nbsp;{nombre_cia}</strong></font></th>
  </tr>
  <tr class="print">
    <th scope="col" class="print">Folio</th>
    <th width="52%" class="print" scope="col">Fecha</th>
    <th width="22%" class="print" scope="col">Importe</th>
  
  </tr>
<!-- START BLOCK : proveedor -->
  <tr class="print">
    <th class="vprint" colspan="3">{num_proveedor}&nbsp;{nombre_proveedor}</th>
  </tr>
<!-- START BLOCK : folios -->
  <tr class="print">
    <td class="print">{folio}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">
		<!-- START BLOCK : cheque_ok -->
		{importe}
		<!-- END BLOCK : cheque_ok -->
		<!-- START BLOCK : cheque_error -->
		<font color="#0033CC">{importe}</font>
		<!-- END BLOCK : cheque_error -->
	</td>
  </tr>
<!-- END BLOCK : folios -->
<!-- END BLOCK : proveedor -->
  <tr class="print">
    <th colspan="2" class="rprint"><font size="2"><strong>TOTAL COMPAÑÍA</strong></font></th>

    <th class="rprint"><font size="2"><strong>{total_cia}</strong></font></th>
  </tr>


</table>

</td>
</tr>
</table>

<br style="page-break-after:always;">
<!-- END BLOCK : compania -->
<!-- END BLOCK : listado_por_companias -->

<!-- POR PROVEEDORES -->
<!-- START BLOCK : listado_por_proveedores -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">LISTADO DE CHEQUES POR PROVEEDOR {emitidos} </p>
<p class="print_encabezado">DEL {fecha}  AL {fecha1} <br>
  </p>
<table class="print" width="80%">
  <tr class="print">
    <th class="print" colspan="3"><font size="2"><strong>{num_proveedor}&nbsp;{nombre_proveedor}</strong></font></th>
  </tr>
  <tr class="print">
    <th scope="col" class="print">Folio</th>
    <th width="38%" class="print" scope="col">Fecha</th>
    <th width="36%" class="print" scope="col">Importe</th>
  </tr>
  <tr class="print">
<!-- START BLOCK : cia -->
    <th width="26%" class="print">{num_cuenta} </th>
    <th colspan="2" class="vprint">{num_cia}&nbsp;{nombre_cia}</th>
  </tr>
<!-- START BLOCK : reg -->
  <tr class="print">
    <td height="18" class="print">{folio}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">
	<!-- START BLOCK : cheque_prov_ok -->
	{importe}
	<!-- END BLOCK : cheque_prov_ok -->
	<!-- START BLOCK : cheque_prov_error -->
	<font color="#0033CC">{importe}</font>
	<!-- END BLOCK : cheque_prov_error -->
	</td>
  </tr>
<!-- END BLOCK : reg -->
<!-- END BLOCK : cia -->
  <tr class="print">
    <th height="18" class="rprint" colspan="2"><font size="2"><strong>TOTAL PROVEEDOR</strong></font></th>
    <th class="rprint"><font size="2"><strong>{total_proveedor}</strong></font></th>
  </tr>

</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado_por_proveedores -->


<!-- START BLOCK : listado_cancelado -->
<!-- START BLOCK : companias_cancelado -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">LISTADO DE CHEQUES CANCELADOS </p>
<p class="print_encabezado">DEL {fecha}  AL {fecha1} </p>


<table class="print" width="80%">
  <tr class="print">
    <th width="26%" class="print"><font size="2"><strong>{num_cuenta} </strong></font></th>
    <th colspan="2" class="vprint"><font size="2"><strong>{num_cia}&nbsp;{nombre_cia}</strong></font></th>
  </tr>
  <tr class="print">
    <th scope="col" class="print">Folio</th>
    <th width="52%" class="print" scope="col">Fecha</th>
    <th width="22%" class="print" scope="col">Fecha Cancelaci&oacute;n </th>
  
  </tr>
<!-- START BLOCK : proveedor_cancelado -->
  <tr class="print">
    <th class="vprint" colspan="3">{num_proveedor}&nbsp;{nombre_proveedor}</th>
  </tr>
<!-- START BLOCK : folios_cancelados -->
  <tr class="print">
    <td class="print">{folio}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{fecha1}</td>
  </tr>
<!-- END BLOCK : folios_cancelados -->
<!-- END BLOCK : proveedor_cancelado -->
</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : companias_cancelado -->
<!-- END BLOCK : listado_cancelado -->



<!-- START BLOCK : listado_por_proveedores_cancelados -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">LISTADO DE CHEQUES CANCELADOS <br>
  POR PROVEEDOR {emitidos} </p>
<p class="print_encabezado">DEL {fecha}  AL {fecha1} <br>
  </p>
<table class="print" width="80%">
  <tr class="print">
    <th class="print" colspan="3"><font size="2"><strong>{num_proveedor}&nbsp;{nombre_proveedor}</strong></font></th>
  </tr>
  <tr class="print">
    <th scope="col" class="print">Folio</th>
    <th width="38%" class="print" scope="col">Fecha</th>
    <th width="36%" class="print" scope="col">Fecha Cancelaci&oacute;n</th>
  </tr>
  <tr class="print">
<!-- START BLOCK : cia_cancel -->
    <th width="26%" class="print">{num_cuenta} </th>
    <th colspan="2" class="vprint">{num_cia}&nbsp;{nombre_cia}</th>
  </tr>
<!-- START BLOCK : reg_cancel -->
  <tr class="print">
    <td height="18" class="print">{folio}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{fecha1}</td>
  </tr>
<!-- END BLOCK : reg_cancel -->
<!-- END BLOCK : cia_cancel -->
</table></td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado_por_proveedores_cancelados -->


<!-- START BLOCK : por_gasto -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado"><font size="2"><strong>LISTADO DE CHEQUES EMITIDOS 
DEL {fecha} AL {fecha1}</strong></font>
<!-- START BLOCK : gas_can -->
<br> CANCELADOS
<!-- END BLOCK : gas_can -->
</p>

<table cellpadding="0" cellspacing="0" class="listado">
		  <tr class="listado">
			<th class="listado" colspan="2"><strong>COMPAÑÍA </strong></th>
			<th class="listado"><strong>&nbsp;&nbsp;&nbsp;FOLIO&nbsp;&nbsp;&nbsp;</strong></th>
			<th class="listado"><strong>&nbsp;&nbsp;&nbsp;IMPORTE&nbsp;&nbsp;&nbsp;</strong></th>
		    <th class="listado">&nbsp;</th>
		    <th class="listado" colspan="2"><strong>COMPA&Ntilde;&Iacute;A</strong></th>
		    <th class="listado"><strong>&nbsp;&nbsp;&nbsp;FOLIO&nbsp;&nbsp;&nbsp;</strong></th>
		    <th class="listado"><strong>&nbsp;&nbsp;&nbsp;IMPORTE&nbsp;&nbsp;&nbsp;</strong></th>
      </tr>
		  <!-- START BLOCK : row_gasto -->
		  <tr class="listado">
			<th class="rlistado">{num_cia}&nbsp;&nbsp;&nbsp;</th>
			<th class="vlistado">{nombre_cia}</th>
			<td class="listado">{folio}</td>
			<td class="rlistado">{cantidad}</td>
		    <td class="rlistado">&nbsp;</td>
		    <th class="rlistado">{num_cia1}&nbsp;&nbsp;&nbsp;</th>
		    <th class="vlistado">{nombre_cia1}</th>
		    <td class="listado">{folio1}</td>
		    <td class="rlistado">{cantidad1}</td>
      </tr>
		  <!-- END BLOCK : row_gasto -->
		  <tr class="listado">
			<th class="rlistado" colspan="4"><strong>TOTAL&nbsp;&nbsp;</strong></th>
			<td class="rlistado"><font size="2"><strong>{total}</strong></font></td>
		    <th class="listado" colspan="4">&nbsp;</th>
	    </tr>
	  </table>
</td>
</tr>
</table>
<!-- END BLOCK : por_gasto -->
