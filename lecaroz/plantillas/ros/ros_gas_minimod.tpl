<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->


<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function actualizar() {
		document.form.submit();
	}
	
//------------
function valida_fecha(campo_fecha)
{
	var fecha = campo_fecha.value;
	var dia_m= {dia_actual};
	var mes_m= {mes_actual};
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
		var mes_m={mes_actual};
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
<tr>
<td align="center" valign="middle">

<form name="form" method="get" action="./ros_gas_minimod.php" >
<input name="idmovimiento_gastos" type="hidden" value="{id}">
<input name="fecha_oculta" type="hidden" value="{fecha}">  
<table>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla">{num_cia}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Codigo de Gasto </th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" onKeyDown="if(event.keyCode==13)document.form.fecha.select();" value="{codgastos}" size="5" maxlength="5"{readoly}>
        <input name="num_cia" type="hidden" class="insert" value="{num_cia}">
        </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla">
	  <input name="fecha" type="text" class="insert" id="fecha" onChange="valida_fecha(this);" value="{fecha}" size="10" {estado} onKeyDown="if(event.keyCode==13)document.form.codgastos.select();"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla">{concepto}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"> {importe} 
        <input name="importe" type="hidden" class="insert" id="importe" value="{importe1}"></td>
    </tr>
  </table>
  <p>
    <input name="modificar" type="button" class="boton" id="modificar" value="Modificar" onClick="document.form.submit();">
  </p>
</form>
</td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.codgastos.select();
</script>

<!-- END BLOCK : modificar -->

