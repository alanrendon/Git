<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}

function valida_hora(campo_hora){
	var hora = campo_hora.value;
//	alert("longitud "+hora.length);
	if(hora.length == 4){
		if(parseInt(hora.charAt(0))==0)
			horas=parseInt(hora.charAt(1));
		else
			horas=parseInt(hora.substring(0,2));
		if(parseInt(hora.charAt(2))==0)
			minutos=parseInt(hora.charAt(3));
		else
			minutos=parseInt(hora.substring(2,4));
			
		if(horas < 0 || horas >23){
			alert("El intervalo de horas debe ser de 0 a 23");
			campo_hora.value="";
			campo_hora.select();
			return;
		}
		if(minutos < 0 || minutos > 59){
			alert("El intervalo de minutos debe ser de 0 a 59");
			campo_hora.value="";
			campo_hora.select();
			return;
		}
		
		if(parseInt(hora.charAt(0))==0){
			if(parseInt(hora.charAt(2))==0){
				campo_hora.value="0"+horas+":0"+minutos;
				return;
			}
			else{
				campo_hora.value="0"+horas+":"+minutos;
				return;
			}
		}

		if(parseInt(hora.charAt(2))==0){
			if(parseInt(hora.charAt(0))==0){
				campo_hora.value="0"+horas+":0"+minutos;
				return;
			}
			else{
				campo_hora.value=horas+":0"+minutos;
				return;
			}
		}
		campo_hora.value=horas+":"+minutos;
//		alert("horas: "+horas);
//		alert("minutos: "+minutos);
		return;
	}
	else{
		alert("Formato de tiempo no válido");
		campo_hora.value="";
		campo_hora.select();
	}
}

	
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
	
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" Erronea");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}




	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA DE MEDIDORES DE AGUA</p>
<form name="form" action="./pan_agu_cap.php" method="get">
<input name="temp" type="hidden">
  <table border="1" class="tabla">
    <tr>
      <th class="tabla" scope="col">N&uacute;mero</th>
      <th class="tabla" scope="col">Nombre</th>
	  <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Hora</th>
      <th class="tabla" scope="col">Medidor 1</th>
      <th class="tabla" scope="col">Medidor 2</th>
      <th class="tabla" scope="col">Medidor 3</th>
      <th class="tabla" scope="col">Medidor 4</th>
      
    </tr>
	<!-- START BLOCK : rows -->    
    <tr>
      <td class="tabla">
	  	<input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" value="{cia}" size="10" maxlength="5" onchange="valor=isInt(this,form.temp); if (valor==false) this.select();actualiza_compania(this,form.nombre_cia{i});"  onkeydown="if (event.keyCode == 13) document.form.fecha{i}.select();" onfocus="form.temp.value=this.value"></td>
	  <td class="tabla">
	    <input name="nombre_cia{i}" type="text" class="vnombre" value="{nombre_cia}" size="50" readonly></td>
      <td class="tabla">
	  	<input name="fecha{i}" type="text" class="insert" id="fecha{i}" value="{fecha}" size="9" maxlength="10" onkeydown="if (event.keyCode == 13) document.form.hora{i}.select();" onchange="valida_fecha(this);"></td>
      <td class="tabla">
	  	<input name="hora{i}" type="text" class="insert" id="hora{i}" value="{hora}" size="8" maxlength="5" onchange="valida_hora(this);" onkeydown="if (event.keyCode == 13) document.form.medida1{i}.select();" ></td>
      <td class="tabla">
	  	<input name="medida1{i}" type="text" class="insert" id="medida1{i}" value="{medidor1}" size="10" maxlength="10"  onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"onkeydown="if (event.keyCode == 13) document.form.medida2{i}.select();" onfocus="form.temp.value=this.value"></td>
      <td class="tabla">
	  	<input name="medida2{i}" type="text" class="insert" id="medida2{i}" value="{medidor2}" size="10" maxlength="10" onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.medida3{i}.select();" onfocus="form.temp.value=this.value"></td>
      <td class="tabla">
	  	<input name="medida3{i}" type="text" class="insert" id="medida3{i}" value="{medidor3}" size="10" maxlength="10" onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.medida4{i}.select();" onfocus="form.temp.value=this.value"></td>
      <td class="tabla">
	  	<input name="medida4{i}" type="text" class="insert" id="medida4{i}" value="{medidor4}" size="10" maxlength="10" onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.num_cia{next}.select();" onfocus="form.temp.value=this.value"></td>
      
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia0.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA DE MEDIDORES DE AGUA</p>

<form name="formi" action="./insert_pan_agu_cap.php" method="post">
<table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col" colspan="2">Compañía</th>
	  <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Hora</th>
      <th class="tabla" scope="col">Medidor 1</th>
      <th class="tabla" scope="col">Medidor 2</th>
      <th class="tabla" scope="col">Medidor 3</th>
      <th class="tabla" scope="col">Medidor 4</th>
    </tr>
	<!-- START BLOCK : rows1 -->
    <tr>
      <th class="tabla">
	  	<input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}" value="{num_cia}" size="10" maxlength="5">
		<!-- START BLOCK : cia_ok -->
	  	{num_cia}
		<!-- END BLOCK : cia_ok -->
		<!-- START BLOCK : cia_error -->
	  	<font color="#FFFF33">{num_cia}</font>
		<!-- END BLOCK : cia_error -->
		</th>
	  <th class="vtabla">
	    {nombre_cia}</th>
      <td class="tabla">
	  	<input name="fecha{i}" type="hidden" class="insert" id="fecha{i}" value="{fecha}" size="9" maxlength="10">
	  	{fecha}</td>
      <td class="tabla">
	  	<input name="hora{i}" type="hidden" class="insert" id="hora{i}" value="{hora}" size="8" maxlength="5">
	  	{hora}</td>
      <td class="tabla">
	  	<input name="medida1{i}" type="hidden" class="insert" id="medida1{i}" value="{medida1}" size="10" maxlength="10" >
		<!-- START BLOCK : med1_ok -->
	  	{medidor1}
		<!-- END BLOCK : med1_ok -->
		<!-- START BLOCK : med1_error -->
		<font color="#FF0000">{medidor1}</font>
		<!-- END BLOCK : med1_error -->
		</td>
      <td class="tabla">
	  	<input name="medida2{i}" type="hidden" class="insert" id="medida2{i}" value="{medida2}" size="10" maxlength="10">
		<!-- START BLOCK : med2_ok -->
	  	{medidor2}
		<!-- END BLOCK : med2_ok -->
		<!-- START BLOCK : med2_error -->
		<font color="#FF0000">{medidor2}</font>
		<!-- END BLOCK : med2_error -->
		</td>
      <td class="tabla">
	  	<input name="medida3{i}" type="hidden" class="insert" id="medida3{i}" value="{medida3}" size="10" maxlength="10">
		<!-- START BLOCK : med3_ok -->
	  	{medidor3}
		<!-- END BLOCK : med3_ok -->
		<!-- START BLOCK : med3_error -->
		<font color="#FF0000">{medidor3}</font>
		<!-- END BLOCK : med3_error -->
		</td>
      <td class="tabla">
	  	<input name="medida4{i}" type="hidden" class="insert" id="medida4{i}" value="{medida4}" size="10" maxlength="10">
		<!-- START BLOCK : med4_ok -->
	  	{medidor4}
		<!-- END BLOCK : med4_ok -->
		<!-- START BLOCK : med4_error -->
		<font color="#FF0000">{medidor4}</font>
		<!-- END BLOCK : med4_error -->
		</td>
    </tr>
	<!-- END BLOCK : rows1 -->
  </table>
  
 </form>
<p>
  <input type="button" name="regresar" value="Regresar" class="boton" onclick="document.location='./pan_agu_cap.php'">&nbsp;&nbsp;

  <input name="enviar" type="button" class="boton" id="enviar" onclick="document.formi.submit();" value="Capturar" {disabled}>
</p></td>
</tr>
</table>
<!-- END BLOCK : captura -->


