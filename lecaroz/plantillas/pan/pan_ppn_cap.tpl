
<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<script language="JavaScript" type="text/JavaScript">
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
	
	function valida_registro(){
	if(document.form.fecha.value==""){
		alert("Debe insertar fecha");
		document.form.fecha.select();
		}
	else document.form.submit();
	}
</script>	
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Prueba de Pan</p>
<form name="form" method="get" action="./pan_ppn_cap.php">
<input name="temp" type="hidden">
 <table class="tabla">
    <tr>
      <th class="tabla">Fecha</th>
      
      <td class="tabla" align="center">
        <input name="fecha" type="text" class="insert" id="fecha" onChange="/*valida_fecha(this)*/actualiza_fecha(this);" onKeyDown="if (event.keyCode == 13) document.form.num_cia0.select();" value="{fecha}" size="9">
   </td>
    </tr>
  </table>
  <table class="tabla">
    <tr>
      <th class="tabla">Num. Compa&ntilde;&iacute;a </th>
      <th class="tabla">Importe</th>
      
    </tr>
	<!-- START BLOCK : rows1 -->
    <tr>
      <td class="tabla" align="center">
        <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onfocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) document.form.importe{i}.select();" value="{num_cia}" size="5">
      </td>
      <td class="tabla" align="center">
        <input name="importe{i}" type="text" class="rinsert" id="importe{i}" onfocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13) document.form.num_cia{next}.select();" value="{importe}" size="9">
   </td>
      
    </tr>
	<!-- END BLOCK : rows1 -->
  </table>

  <p>&nbsp;  </p>
  <p><img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
    <input type="button" name="enviar" class="boton" value="Capturar Movimientos" onclick='valida_registro()'>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;
    <input name="button" type="button" class="boton" onclick='' value="Borrar formulario">
    <script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
    </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : prueba_pan -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Prueba de Pan</p>
<form name="form" method="post" action="./insert_pan_ppn_cap.php">


 <table class="tabla">
<input name="cont" type="hidden" value="{cont}">

    <tr>
      <th class="tabla">Fecha</th>
      <td class="tabla" align="center">
	  <input name="bandera" type="hidden" value="{bandera}">
        <input name="fecha_mov" type="hidden" class="insert" id="fecha_mov" value="{fecha}" size="9">
        <input name="contador" type="hidden" class="insert" id="contador" value="{cont}" size="9">        <font size="+1">
   {fecha}</font></td>
    </tr>
  </table>
  <table class="tabla">
    <tr>
      <th class="tabla" colspan="2">Num. Compa&ntilde;&iacute;a </th>
      <th class="tabla">Importe</th>
      <th class="tabla">Producción<br>promedio</th>
      <th class="tabla">Estado</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="rtabla" align="center">
        <input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}" value="{num_cia}" size="5">
	<!-- START BLOCK : cia_ok -->
	{num_cia1}      
	<!-- END BLOCK : cia_ok -->
	<!-- START BLOCK : cia_error -->
	<font color="#{color}">{num_cia1}</font>
	<!-- START BLOCK : cia_error -->
	</th>
	<td class="vtabla">{nombre_cia}</td>
      <td class="rtabla" align="center">{importe1}
        <input name="importe{i}" type="hidden" class="insert" id="importe{i}" value="{importe}" size="9">
   </td>
      <td class="rtabla" align="center"><span class="rtabla">
        <input name="produccion{i}" type="hidden" class="insert" id="produccion{i}" value="{produccion}" size="9">
      </span>{produccion1}</td>
      <td class="tabla" align="center">
	  <!-- START BLOCK : aviso_ok -->
	  <strong>{aviso}</strong>
	  <!-- END BLOCK : aviso_ok -->
	  <!-- START BLOCK : aviso_error -->
	  <font color="#FF0000" size="+3">{aviso}</font>
	  <!-- END BLOCK : aviso_error -->
	  </td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <table class="tabla">
    <tr class="tabla">
      <td class="tabla" bgcolor="#FF99CC">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td class="vtabla" >Ya existe registro</td>
    </tr>
    <tr class="tabla">
      <td class="tabla" bgcolor="#FFFF00"></td>
      <td class="vtabla">No te corresponde panader&iacute;a</td>
    </tr>
  </table>
  <script language="JavaScript" type="text/JavaScript">
function valida_registro(){ 
if(document.form.bandera.value==0)
	alert("RECUERDE QUE USTED ES LA RESPONSABLE SI DA POR BUENO ESTE IMPORTE, FAVOR DE VERIFICAR CON EL ADMINISTRADOR");
	
if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
}
</script>
  <p>&nbsp;&nbsp;
	<input name="button2" type="button" class="boton" onclick='parent.history.back()' value="Regresar">&nbsp;&nbsp;
    <input type="button" name="enviar" class="boton" value="Capturar Movimientos" onclick='valida_registro()' {disabled}>

    
  </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : prueba_pan -->