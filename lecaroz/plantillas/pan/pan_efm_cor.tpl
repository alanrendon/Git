
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
		else {
			if (confirm("¿Son correctos los datos del formulario?")) {

				document.form.submit();
			}
			else
				document.form.fecha.select();
		}
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.fecha.select();
		}
		else
			document.form.fecha.select();
	}

//VALIDACION DE FECHAS

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
	
function venta_puerta(am_total,am_error,pm_total,pm_error,pastel,venta_puerta){

	if(am_total.value=="")
		var am_t=0;
	else 
		var am_t=parseFloat(am_total.value);
	if(am_error.value=="")
		var am_e=0;
	else 
		var am_e=parseFloat(am_error.value);

	if(pm_total.value=="")
		var pm_t=0;
	else 
		var pm_t=parseFloat(pm_total.value);
	if(pm_error.value=="")
		var pm_e=0;
	else 
		var pm_e=parseFloat(pm_error.value);
		
	if(pastel.value=="")
		var pas=0;
	else 
		var pas=parseFloat(pastel.value);
	var total=0;
	total = am_t - am_e + pm_t - pm_e + pas;
	
	venta_puerta.value=total.toFixed(2);
}

</script>

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p><p class="title">CAPTURA DE MOVIMIENTO DE EFECTIVOS</p>

<form name="form" method="get" action="./pan_efm_cap.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla"><strong>Fecha</strong></th>
      <td class="vtabla">
	  	<input name="fecha" type="text" class="insert" id="fecha" onChange="valida_fecha(this);" onKeyDown="if (event.keyCode == 13) form.num_cia0.select();" value="{fecha}" size="11">
	  </td>
    </tr>
  </table>
  
  <table class="tabla">
    <tr>
      <th class="tabla" rowspan="2">Compa&ntilde;&iacute;a</th>
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
	<!-- START BLOCK : rows -->
    <tr>
      <td class="tabla" align="center"><input name="num_cia{i}" type="text"  class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.am{i}.select();" value="{num_cia}" size="3"></td>
		<td class="tabla"><input name="am{i}" type="text" class="insert" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" value="{am}" size="5" onKeyDown="if (event.keyCode == 13) form.am_error{i}.select();" onFocus="form.temp.value=this.value"></td>
		<td class="tabla"><input name="am_error{i}" type="text" class="insert" value="{am_error}" size="5" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) form.pm{i}.select();"></td>
		<td class="tabla"><input name="pm{i}" type="text" class="insert" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" value="{pm}" size="5" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) form.pm_error{i}.select();"></td>
	  	<td class="tabla"><input name="pm_error{i}" type="text" class="insert" value="{pm_error}" size="5" onChange=" valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) form.pastel{i}.select();"></td>
		<td class="tabla"><input name="pastel{i}" type="text" class="insert" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" value="{pastel}" size="5" onKeyDown="if (event.keyCode == 13) form.pastillaje{i}.select();" onFocus="form.temp.value=this.value"></td>
		<td class="tabla"><input name="venta_pta{i}" type="text" class="insert" id="venta_pta{i}" value="{venta_pta}" size="7" readonly></td>
		<td class="tabla"><input name="pastillaje{i}" type="text" class="insert" id="pastillaje{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.otros{i}.select();" value="{pastillaje}" size="5"></td>
		<td class="tabla"><input name="otros{i}" type="text" class="insert" id="otros{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.ctes{i}.select();" value="{otros}" size="5"></td>
		<td class="tabla"><input name="ctes{i}" type="text" class="insert" id="ctes{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte1{i}.select();" value="{ctes}" size="5"></td>
		<td class="tabla"><input name="corte1{i}" type="text" class="insert" id="corte1{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte2{i}.select();" value="{corte1}" size="5"></td>
		<td class="tabla"><input name="corte2{i}" type="text" class="insert" id="corte2{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.desc_pastel{i}.select();" value="{corte2}" size="5"></td>
		<td class="tabla"><input name="desc_pastel{i}" type="text" class="insert" id="desc_pastel{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.num_cia{next}.select();" value="{desc_pastel}" size="5"></td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar movimientos" onclick='valida_registro()'>
    <br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</form>

</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : movimientos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p><p class="title">CAPTURA DE MOVIMIENTO DE EFECTIVOS</p>
<p class="title">{fecha}</p>
<form name="form" method="post" action="./insert_pan_efm_cap.php">
<input type="hidden" value="{fecha}" name="fecha">
<input name="contador" type="hidden" id="contador" value="{cont}">
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
  <tr>
    <th class="tabla" align="center">
      <input name="num_cia{i}" type="hidden"  class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.am{i}.select();" value="{num_cia}" size="3">
	  <!-- START BLOCK : cia_ok -->
	  {num_cia1}
	  <!-- END BLOCK : cia_ok -->
	  <!-- START BLOCK : cia_error -->
	  <font color="#{color}">{num_cia1}</font>
	  <!-- END BLOCK : cia_error -->
   </th>
	<th class="vtabla">{nombre_cia}</th>
    <td class="rtabla" align="center"><input name="am{i}" type="hidden" class="insert" id="am{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onKeyDown="if (event.keyCode == 13) form.am_error{i}.select();" value="{am}" size="5">{am1}</td>
    <td class="rtabla"><input name="am_error{i}" type="hidden" class="insert" id="am_error{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onKeyDown="if (event.keyCode == 13) form.pm{i}.select();" value="{am_error}" size="5">{am_error1}</td>
    <td class="rtabla"><input name="pm{i}" type="hidden" class="insert" id="pm{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onKeyDown="if (event.keyCode == 13) form.pm_error{i}.select();" value="{pm}" size="5">{pm1}</td>
    <td class="rtabla"><input name="pm_error{i}" type="hidden" class="insert" id="pm_error{i}" onFocus="form.temp.value=this.value" onChange=" valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onKeyDown="if (event.keyCode == 13) form.pastel{i}.select();" value="{pm_error}" size="5">{pm_error1}</td>
    <td class="rtabla"><input name="pastel{i}" type="hidden" class="insert" id="pastel{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am{i},form.am_error{i},form.pm{i},form.pm_error{i},form.pastel{i},form.venta_pta{i});" onKeyDown="if (event.keyCode == 13) form.pastillaje{i}.select();" value="{pastel}" size="5">{pastel1}</td>
    <td class="rtabla"><input name="venta_pta{i}" type="hidden" class="insert" id="venta_pta{i}" value="{venta_pta}" size="7" readonly>{venta_pta1}</td>
	<td class="rtabla"><input name="pastillaje{i}" type="hidden" class="insert" id="pastillaje{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.otros{i}.select();" value="{pastillaje}" size="5">{pastillaje1}</td>
    <td class="rtabla"><input name="otros{i}" type="hidden" class="insert" id="otros{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.ctes{i}.select();" value="{otros}" size="5">{otros1}</td>
    <td class="rtabla"><input name="ctes{i}" type="hidden" class="insert" id="ctes{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte1{i}.select();" value="{ctes}" size="5">{ctes1}</td>
    <td class="rtabla"><input name="corte1{i}" type="hidden" class="insert" id="corte1{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte2{i}.select();" value="{corte1}" size="5">{corte11}</td>
    <td class="rtabla"><input name="corte2{i}" type="hidden" class="insert" id="corte2{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.desc_pastel{i}.select();" value="{corte2}" size="5">{corte21}</td>
    <td class="rtabla"><input name="desc_pastel{i}" type="hidden" class="insert" id="desc_pastel{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.num_cia{next}.select();" value="{desc_pastel}" size="5">{desc_pastel1}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  <tr>
  </tr>

</table>

<table class="tabla">
  <tr class="tabla">
    <td class="tabla" bgcolor="#FF99CC">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vtabla" >Ya existe registro</td>
  </tr>
  <tr class="tabla">
    <td class="tabla" bgcolor="#FFFF00"></td>
    <td class="vtabla">No te corresponde panadería</td>
  </tr>
</table>


<p>
<input name="regresar" type="button" onClick="document.location='./pan_efm_cap.php'" value="Regresar" class="boton">&nbsp;&nbsp;
<input name="enviar" type="button" id="enviar" onClick="document.form.submit();" value="Continuar" {disabled} class="boton">

</p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : movimientos -->


<!-- START BLOCK : inicio -->



<!-- END BLOCK : inicio -->