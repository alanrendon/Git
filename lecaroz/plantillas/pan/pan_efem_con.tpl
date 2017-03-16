
<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
var tam;
var tpm;
var tpastel;

	function valida_registro() {
		if(document.form.fecha.value == "") {
			alert('Debe especificar una fecha');
			document.form.fecha.select();
		}
		else 
			document.form.submit();
	}

//VALIDACION DE FECHAS
function valida_fecha(campo_fecha)
{
	var fecha = campo_fecha.value;
	var dia_m= {dia};
	var mes_m= {mes};
	var anio_m={anio_actual};
	var bandera=false;
	if (fecha.length == 8) 
	{
		// Descomponer fecha en dia, mes y año en caso de que el año se presente en cuatro digitos
		if (parseInt(fecha.charAt(0)) == 0)
			dia = parseInt(fecha.charAt(1));
		else
			dia = parseInt(fecha.substring(0,2));
		if (parseInt(fecha.charAt(2)) == 0)
			mes = parseInt(fecha.charAt(3));
		else
			mes = parseInt(fecha.substring(2,4));
		anio = parseInt(fecha.substring(4));
	}
	else if (fecha.length == 6) 
	{
		// Descomponer fecha en dia, mes y año en caso de que el año se presente en dos digitos
		if (parseInt(fecha.charAt(0)) == 0)
			dia = parseInt(fecha.charAt(1));
		else
			dia = parseInt(fecha.substring(0,2));
		if (parseInt(fecha.charAt(2)) == 0)
			mes = parseInt(fecha.charAt(3));
		else
			mes = parseInt(fecha.substring(2,4));
		anio = parseInt(fecha.substring(4)) + 2000;
	}
//---------revision de dias
//VALIDACION PARA LOS 4 PRIMEROS DIAS DEL MES EN CURSO PARA CAPTURAR EL MES ANTERIOR
	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4)
	{
		if (anio > anio_m)
		{//año mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revisar el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m)
		{//mes mayor
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		//CASO ESPECIAL EN QUE SEA INICIO DE AÑO, TOMA EN CUENTA CAPTURAS DEL AÑO ANTERIOR
		else if(anio == (anio_m -1) && mes==12 && mes_m==1) 
			actualiza_fecha(campo_fecha);
		//CAPTURA DEL MES ANTERIOR EN EL AÑO EN CURSO
		else if (mes == (mes_m -1)&& anio==anio_m)
			actualiza_fecha(campo_fecha);
		//CAPTURA DEL MES Y AÑO EN CURSO
		else if (mes==mes_m && anio==anio_m)
			actualiza_fecha(campo_fecha);
			
		else if (mes < mes_m-1){
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if(anio < anio_m){
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}
	else 
	{//bloqueo de fechas mayores
		if (anio > anio_m || anio < anio_m)
		{//año mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m || mes < mes_m)
		{//mes mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el mes");
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el dia");
			return;
		}
		//caso en que la fecha es igual al dia corriente no se permite entrada
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el dia");
			return;
		}
		else if ((dia < dia_m) && (mes==mes_m) && (anio==anio_m))
			actualiza_fecha(campo_fecha);
//		document.form.fecha_mov.value="";
	}
}
//---------------------------------------------------------------------------------------------------------------------------
	function actualiza_fecha(campo_fecha) {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = campo_fecha.value;
		var anio_actual = {anio_actual};
		var mes_m={mes};
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
			else if(anio == (anio_actual -1) && mes==12 && mes_m==1){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
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
			else if(anio == (anio_actual - 1) && mes==12 && mes_m==1){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
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

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>

<p class="title">CONSULTA DE EFECTIVOS DESGLOSADOS</p>

<form name="form" method="get" action="./pan_efem_con.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla"><strong>Fecha</strong></th>
      <td class="vtabla">
	  	<input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this);" onKeyDown="if (event.keyCode == 13) form.num_cia0.select();" value="{fecha}" size="11">
	  </td>
    </tr>
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
	<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>
    </form>

</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : movimientos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">CONSULTA DE EFECTIVOS DESGLOSADOS</p>
<p class="title">{fecha}</p>
<table class="tabla">
  <tr>
    <th class="tabla" rowspan="2" colspan="2">Compa&ntilde;&iacute;a</th>
    <th class="tabla" colspan="2">AM</th>
    <th class="tabla" colspan="2">PM</th>
    <th class="tabla" rowspan="2">Pastel</th>
    <th class="tabla" rowspan="2">Venta<br>Puerta</th>
    <th class="tabla" rowspan="2">Pastillaje</th>
    <th class="tabla" rowspan="2">Otros</th>
    <th class="tabla" rowspan="2">Clientes</th>
    <th class="tabla" rowspan="2">Corte<br>Pan </th>
    <th class="tabla" rowspan="2">Corte<br>Pastel</th>
    <th class="tabla" rowspan="2">Descuento<br>Pastel</th>
  </tr>
  <tr>
    <th class="tabla">total</th>
    <th class="tabla">Error</th>
    <th class="tabla">total</th>
    <th class="tabla">Error</th>
  </tr>
  <!-- START BLOCK : rows1 -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla" align="center">{num_cia}</th>
	<th class="vtabla">{nombre_cia}</th>
    <td class="rtabla" align="center">{am1}</td>
    <td class="rtabla">{am_error1}</td>
    <td class="rtabla">{pm1}</td>
    <td class="rtabla">{pm_error1}</td>
    <td class="rtabla">{pastel1}</td>
    <td class="rtabla">{venta_pta1}</td>
	<td class="rtabla">{pastillaje1}</td>
    <td class="rtabla">{otros1}</td>
    <td class="rtabla">{ctes1}</td>
    <td class="rtabla">{corte11}</td>
    <td class="rtabla">{corte21}</td>
    <td class="rtabla">{desc_pastel1}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  <tr>
  </tr>

</table>
<p>
<input name="regresar" type="button" onClick="document.location='./pan_efem_con.php'" value="Regresar" class="boton"></p>
</td>
</tr>
</table>

<!-- END BLOCK : movimientos -->
