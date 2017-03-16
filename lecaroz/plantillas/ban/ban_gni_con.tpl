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
	if(document.form.tipo.value==0){
		if (document.form.fecha_inicial.value=="" || document.form.fecha_final.value==""){
			alert("Revise las fechas");
			document.form.fecha_inicial.select();
			return;
		}
		else
			document.form.submit();
	}
	else{
		if(document.form.anio.value==""){
			alert("debe especificar una fecha");
			document.form.anio.select();
			return;
		}
		else
			document.form.submit();
	}
}

</script>



<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">LISTADO DE CHEQUES DE GASTOS NO INCLUIDOS </p>
<form action="./ban_gni_con.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Fecha inicial      
      <input name="fecha_inicial" type="text" class="insert" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.fecha_final.select();" value="{fecha1}" size="10"></th>
    <th class="tabla">Fecha final   
      <input name="fecha_final" type="text" class="insert" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) form.num_cia.select();" value="{fecha2}" size="10"></th>
  </tr>
  <tr class="tabla">
    <td class="tabla" colspan="2">Compa&ntilde;&iacute;a 
      <input name="num_cia" type="text" class="insert" id="num_cia" size="5"></td>
  </tr>
  <tr class="tabla">
    <td class="tabla" colspan="2">
	<label>
	<input name="anualizado" type="checkbox" value="0" onChange="if(document.form.tipo.value==0) document.form.tipo.value=1; else document.form.tipo.value=0;">
    Listado Anualizado para el a&ntilde;o 
	</label>
      <input name="anio" type="text" class="insert" value="{anio_actual}" size="5">
	  <input name="tipo" type="hidden" class="insert" value="0" size="5">
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
<p class="listado_encabezado"><font size="3"><strong>LISTADO DE CHEQUES 
PARA GASTOS NO INCLUIDOS <br>DEL {fecha} AL {fecha1}</strong></font>
</p>

<table class="print">
<!-- START BLOCK : compania2 -->
  <tr class="print">
    <th class="print"><font size="2"><strong>{num_cuenta} </strong></font></th>
    <th colspan="7" class="vprint"><font size="2"><strong>{num_cia}&nbsp;{nombre_cia}</strong></font></th>
  </tr>

  <tr class="print">
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Fecha</th>
    <th colspan="2" class="print" scope="col"><strong>Proveedor</strong></th>
	<th colspan="2" class="print" scope="col"><strong>Gasto</strong></th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Importe</th>
  </tr>

<!-- START BLOCK : row_gasto -->
  <tr class="print" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{folio}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{num_proveedor}</td>
    <td class="vprint">{nombre_proveedor}</td>
	<td class="rprint">{codgastos}</td>
	<td class="vprint">{descripcion}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{cantidad}
	</td>
  </tr>
<!-- END BLOCK : row_gasto -->
  <tr class="print">
    <td colspan="7" class="rprint"><strong>Total Compa&ntilde;&iacute;a </strong></td>
    <td class="rprint"><font size="2"><strong>{total_cia}</strong></font></td>
  </tr>
<!-- END BLOCK : compania2 -->
  <tr class="print">
    <td colspan="7" class="rprint"><strong>TOTAL GENERAL</strong></td>
    <td class="rprint"><font size="2"><strong>{total_general}</strong></font></td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : por_gasto -->

<!-- START BLOCK : anualizado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top"><table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Cheques de gastos no incluidos del {anio} <br>    </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="99%" align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th width="6%" class="print" scope="col">Enero</th>
    <th width="6%" class="print" scope="col">Febrero</th>
    <th width="6%" class="print" scope="col">Marzo</th>
    <th width="6%" class="print" scope="col">Abril</th>
    <th width="6%" class="print" scope="col">Mayo</th>
    <th width="6%" class="print" scope="col">Junio</th>
    <th width="6%" class="print" scope="col">Julio</th>
    <th width="6%" class="print" scope="col">Agosto</th>
    <th width="6%" class="print" scope="col">Septiembre</th>
    <th width="6%" class="print" scope="col">Octubre</th>
    <th width="6%" class="print" scope="col">Noviembre</th>
    <th width="6%" class="print" scope="col">Diciembre</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td width="3%" class="rprint">{num_cia}</td>
    <td width="25%" class="vprint">{nombre_cia}</td>
    <td class="print">{1}</td>
    <td class="print">{2}</td>
    <td class="print">{3}</td>
    <td class="print">{4}</td>
    <td class="print">{5}</td>
    <td class="print">{6}</td>
    <td class="print">{7}</td>
    <td class="print">{8}</td>
    <td class="print">{9}</td>
    <td class="print">{10}</td>
    <td class="print">{11}</td>
    <td class="print">{12}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <td colspan="2" class="rprint"> <strong> <font size="2">Totales</font></strong></td>
    <td class="print"><font size="2"><strong>{t1}</strong></font></td>
    <td class="print"><font size="2"><strong>{t2}</strong></font></td>
    <td class="print"><font size="2"><strong>{t3}</strong></font></td>
    <td class="print"><font size="2"><strong>{t4}</strong></font></td>
    <td class="print"><font size="2"><strong>{t5}</strong></font></td>
    <td class="print"><font size="2"><strong>{t6}</strong></font></td>
    <td class="print"><font size="2"><strong>{t7}</strong></font></td>
    <td class="print"><font size="2"><strong>{t8}</strong></font></td>
    <td class="print"><font size="2"><strong>{t9}</strong></font></td>
    <td class="print"><font size="2"><strong>{t10}</strong></font></td>
    <td class="print"><font size="2"><strong>{t11}</strong></font></td>
    <td class="print"><font size="2"><strong>{t12}</strong></font></td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : anualizado -->

