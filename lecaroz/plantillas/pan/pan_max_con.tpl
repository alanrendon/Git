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
	else if (document.form.fecha.value==""){
		alert("Necesita insertar una fecha");
		document.form.fecha_mov.select();
		return;
		}
	else document.form.submit();
}
</script>
<table width="102%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">LISTADO DE CONSUMOS DE MAS </P>
<form name="form" action="./pan_max_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">FECHA <input class="insert" name="fecha" type="text" id="fecha" size="10" maxlength="10" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();"></th>
    </tr>
    <tr>
      <td class="vtabla"><p>
        <label><input type="radio" name="consulta" value="0" checked onChange="form.tipo_cia.value=0">Compañía</label> 
		<input class="insert" name="num_cia" type="text" id="num_cia" size="5" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
        <br>
        <label><input type="radio" name="consulta" value="1" onChange="form.tipo_cia.value=1">Todas</label>
		<input name="tipo_cia" type="hidden" id="tipo_cia" value="0" size="3">
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
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : maximos -->
<table width="100%" height="48%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">CONSUMOS DE "MAS" DE MATERIA PRIMA, POR BULTO DE HARINA<br>
ACUMULADO AL {dia} DE {mes} DEL A&Ntilde;O {anio} PARA LA COMPA&Ntilde;&Iacute;A<br>{num_cia}&nbsp;&nbsp;{nombre_cia}</p>
<table width="100%" class="print">
  <tr>
    <th scope="col" class="print" colspan="2" rowspan="2">Materia Prima</th>
    <th scope="col" class="print" colspan="3">Frances noche </th>
    <th scope="col" class="print" colspan="3">Frances dia</th>
    <th scope="col" class="print" colspan="3">Bizcochero</th>
    <th scope="col" class="print" colspan="3">Repostero</th>
    <th scope="col" class="print" colspan="3">Piconero</th>
  </tr>
  <tr class="print">
    <td class="print">Avío<br>Aut</td>
    <td class="print">Avío<br>Con</td>
    <td class="print">Consumo<br> de Mas</td>
    <td class="print">Avío<br>Aut</td>
    <td class="print">Avío<br>Con</td>
    <td class="print">Consumo<br> de Mas</td>
    <td class="print">Avío<br>Aut</td>
    <td class="print">Avío<br>Con</td>
    <td class="print">Consumo<br> de Mas</td>
    <td class="print">Avío<br>Aut</td>
    <td class="print">Avío<br>Con</td>
    <td class="print">Consumo<br> de Mas</td>
    <td class="print">Avío<br>Aut</td>
    <td class="print">Avío<br>Con</td>
    <td class="print">Consumo<br> de Mas</td>
  </tr>
  <!-- START BLOCK : rows -->
  <tr class="print">
    <th class="rprint">{codmp}</th>
    <th class="vprint">{nombre}</th>
    <td class="print">{fn_aut}</td>
    <td class="print">{fn_con}</td>
    <td class="print">{fn_mas}</td>
    <td class="print">{fd_aut}</td>
    <td class="print">{fd_con}</td>
    <td class="print">{fd_mas}</td>
    <td class="print">{biz_aut}</td>
    <td class="print">{biz_con}</td>
    <td class="print">{biz_mas}</td>
    <td class="print">{rep_aut}</td>
    <td class="print">{rep_con}</td>
    <td class="print">{rep_mas}</td>
    <td class="print">{pic_aut}</td>
    <td class="print">{pic_con}</td>
    <td class="print">{pic_mas}</td>
  </tr>
<!-- END BLOCK : rows -->  
</table>

</td>
</tr>
</table>
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->

<!-- END BLOCK : maximos -->
