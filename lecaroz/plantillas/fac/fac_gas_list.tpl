<link href="/styles/prints.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Movimientos operados a Gas </p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<0)
		alert("Compañía erronea");
	else if (document.form.fecha1.value == "" || document.form.fecha2.value == "")
		alert("Especifique una fecha");
	else
	document.form.submit();
}

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



</script>

<form name="form" method="get" action="./fac_gas_list.php">
  <table class="tabla">
    <!--<tr class="tabla">
      <th class="vtabla">
        <label>
        <input name="consulta" type="radio" value="fecha" onchange="document.form.bandera.value=0;">
      Fecha</label>
        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha(this);" value="{fecha}" size="10">
      </th>
      <th class="vtabla">
        <input name="consulta" type="radio" onchange="document.form.bandera.value=1;" value="mes" checked>
      Mes
	  
	  
		<select name="mes" size="1" class="insert">-->
    <!-- START BLOCK : mes -->
    <!--<option value="{num_mes}" {checked}>{nom_mes}</option>-->
    <!-- END BLOCK : mes -->
    <!--</select>
		Año
		<input name="anio" type="text" class="insert" value="{anio_actual}" size="4">
      </th>
    </tr>-->
    <tr>
      <th class="vtabla">Fecha inicial </th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha13" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10"></td>
      <th class="vtabla">Fecha final </th>
      <td class="vtabla"><input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) cia.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr class="tabla">
      <td colspan="2" class="tabla">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" onKeyDown="if (event.keyCode == 13) num_pro.select()" size="5">
        <input name="bandera" type="hidden" class="insert" id="bandera2" size="5" value="1">
      </td>
      <td class="tabla">Proveedor</td>
      <td class="tabla"><input name="num_pro" type="text" class="insert" id="num_pro" size="4" maxlength="4"></td>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="4">
	  <label>
	  <input name="total" type="checkbox" id="total" value="1" onChange="if(form.tipo_total.value==1) form.tipo_total.value=0; else form.tipo_total.value=1;">
        Solo totales
		</label>
		<input name="tipo_total" type="hidden" value="0">		 </td>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="4">
	  <label>
	  <input name="user" type="checkbox" id="user" value="1" checked onChange="if(form.tipo_user.value==1) form.tipo_user.value=0; else form.tipo_user.value=1;">
        Consultar por usuario 
		</label>          <input name="tipo_user" type="hidden" id="tipo_user" value="1"></td>
    </tr>
	
  </table>
<p><img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">



<p class="title">MOVIMIENTOS OPERADOS A GAS <br>
   DEL {fecha1} AL {fecha2}</p>
<table border="1" class="print">
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Num.<br>docto</th>
    <th class="print">Litros</th>
    <th class="print">Costo<br>bruto</th>
    <th class="print">Descuento<br>norm </th>
    <th class="print">Descuento<br>adicional</th>
    <th class="print">I.V.A.</th>
    <th class="print">Costo<br>unitario</th>
    <th class="print">Valores</th>
  </tr>

<!-- START BLOCK : companias -->
<!-- START BLOCK : totalprov1 -->
  <tr class="print">
  	<td class="print" colspan="2">Total Proveedor</td>
	<td class="print"><strong>{litr_cia}</strong></td>
	<td class="print" colspan="5">&nbsp;</td>
	<td class="rprint"><strong>{total_proveedor1}</strong></td>
  </tr>
<!-- END BLOCK : totalprov1 -->


  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint"></th>
	<th class="print" colspan="8">{num_cia}&#8212;{nombre_cia}</th>
  </tr>
<!-- START BLOCK : proveedor -->
<!-- START BLOCK : totalprov -->
  <tr class="print">
  	<td class="print" colspan="2">Total Proveedor</td>
	<td class="print"><strong>{litr_cia}</strong></td>
	<td class="print" colspan="5">&nbsp;</td>
	<td class="rprint"><strong>{total_proveedor}</strong></td>
  </tr>
<!-- END BLOCK : totalprov -->


  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint">{num_proveedor}</th>
	<th class="vprint" colspan="8">{nombre_proveedor}</th>	
  </tr>
<!-- START BLOCK : rows -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="print">{num_fac}</td>
    <td class="print">{litros}</td>
    <td class="print">{costo}</td>
    <td class="print">{descuento}</td>
    <td class="print">{descuento1}</td>
    <td class="print">{impuesto}</td>	
    <td class="print">{costo_unitario}</td>

    <td class="rprint">{valores}</td>
  </tr>
<!-- START BLOCK : totalfac -->
  <tr class="print">
  	<td class="print" colspan="8"></td>
	<td class="rprint">{fac}</td>
  </tr>
<!-- END BLOCK : totalfac -->

<!-- START BLOCK : totalprov3 -->
  <tr class="print">
  	<td class="print" colspan="2">Total Proveedor</td>
	<td class="print"><strong>{litr_cia}</strong></td>
	<td class="print" colspan="5">&nbsp;</td>
	<td class="rprint"><strong>{total_proveedor}</strong></td>
  </tr>
<!-- END BLOCK : totalprov3 -->

<!-- END BLOCK : rows -->
<!-- END BLOCK : proveedor -->
<!-- END BLOCK : companias -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="print">TOTAL</th>
    <th class="print">{num_fact}</th>
    <th class="print">{litros}</th>
    <th colspan="5" class="print">&nbsp;</th>
    <td class="rprint"><strong><font size="+1">{total}</font></strong></td>
  </tr>
</table>

<p>
  <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_gas_list.php'">
</p></td>
</tr>
</table>
<!-- END BLOCK : listado -->

<!-- END BLOCK : listado_total -->


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">MOVIMIENTOS OPERADOS A GAS <br>
   DEL {fecha1} AL {fecha2}</p>
<table border="1" class="print">
<!-- START BLOCK : cias -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vprint" colspan="4"><font size="2">{num_cia}&#8212;{nombre_cia}</font></th>
  </tr>
  <tr>
    <td colspan="2" class="print"><strong>PROVEEDOR</strong></td>
    <td class="print"><strong>Litros</strong></td>
    <td class="print"><strong>Valores</strong></td>
  </tr>
  <!-- START BLOCK : proveedor_tot -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_proveedor}</td>
    <td class="vprint">{nombre_proveedor}</td>
    <td class="print"><strong>{litr_cia}</strong></td>
    <td class="rprint"><strong>{total_proveedor}</strong></td>
  </tr>
  <!-- END BLOCK : proveedor_tot -->
<!-- END BLOCK : cias -->  
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="print" colspan="2"><font size="+1">TOTAL</font></th>
    <th class="print"><strong><font size="+1">{litros}</font></strong></th>
    <th class="rprint"><strong><font size="+1">{total}</font></strong></th>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_gas_list.php'">
</p>

</td>
</tr>
</table>

<!-- START BLOCK : listado_total -->