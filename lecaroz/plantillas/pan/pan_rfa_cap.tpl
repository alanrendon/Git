<script type="text/javascript" language="JavaScript">

function valida_registro() {
if(document.form.num_cia.value <= 0) {
	alert('Revise el numero de compañia por favor');
	document.form.num_cia.select();
	}
else if(document.form.num_cia.value == "") {
	alert('Debe especificar un número de compañía');	
	document.form.num_cia.select();
	}
		else {
			if (confirm("¿Son correctos los datos de la pantalla?"))
				
				document.form.submit();
			else
				document.form.num_cia.select();
		}
}
	
function borrar() {
	if (confirm("¿Desea borrar la pantalla?")) {
	
		document.form.reset();
		document.form.num_cia.select();
	}
	else
		document.form.num_cia.select();
}

function buscar(num_cia,num_remi,let_folio){
	if(num_cia != "" && num_remi !=""){
		var url = "pan_rfa_con.php?fecha=&consulta=factura&num_fac=" + num_remi + "&cia=" + num_cia + "&bandera=1&close=1";
		var ven = window.open(url,"detalle_nota","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=500");
		ven.focus();
	}
}

function buscarPendientes(num_cia,fecha){
	if(num_cia !="" && fecha != ""){
//		alert(num_cia);
//		alert(fecha);
		var url = "pan_fpan_pend.php?cias=" + num_cia + "&fecha=" + fecha;
		var ven = window.open(url,"detalle_nota","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=800");
		ven.focus();
	}
}


//VALIDACION DE FECHAS

function valida_fecha(campo_fecha)
{
	var fecha = campo_fecha.value;
	var dia_m= {dia};
	var mes_m= {mes};
	var anio_m={anio_actual};
	var bandera=false;
	document.form.fecha_oculta.value=campo_fecha.value;
	
	//alert(dia_m + "/" + mes_m + "/" + anio_m);
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
		//alert("6 digitos");
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
		else if (mes == (mes_m -1)&& anio==anio_m){
//			alert("Notas al mes anterior al mes corriente");
			actualiza_fecha(campo_fecha);
			}
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


function actualiza_fecha(campo_fecha) {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = campo_fecha.value;
		var anio_actual = {anio_actual};
		var mes_actual = {mes_actual}
		var anio_sig= parseInt(anio_actual) + 1;
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
			if (anio == anio_actual  || (anio == anio_sig && mes_actual == 12)) {
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
				
				if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]/*31*/) {
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
					campo_fecha.focus();
					return;
				}
			}
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				campo_fecha.focus();
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
			if (anio == (anio_actual) || (anio==anio_sig && mes_actual == 12) || (anio == (anio_actual -1) && mes==12 && mes_actual==1)) {
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
				
				if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]/*31*/) {
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
					campo_fecha.focus();
					return;
				}
			}
			
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				campo_fecha.focus();
				return;
			}
		}
		else {
			campo_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			campo_fecha.focus();
			return;
		}
	}



function actualiza_fecha1(campo_fecha,fecha_factura) {
	if(fecha_factura.value==""){
		campo_fecha.value="";
		return;
	}
	var fecha = campo_fecha.value;
	var anio_actual = {anio_actual};
	var fecha2 = fecha_factura.value;

//---------
	if (fecha2.length == 8) 
	{
		if (parseInt(fecha2.charAt(0),10) == 0){
			dia2 = parseInt(fecha2.charAt(1),10);
		}
		else{
			dia2 = parseInt(fecha2.substring(0,2),10);
		}
		if (parseInt(fecha2.charAt(2),10) == 0){
			mes2 = parseInt(fecha2.charAt(3),10);
		}
		else{
			mes2 = parseInt(fecha2.substring(2,4));
		}
		anio2 = parseInt(fecha2.substring(4),10);
	}
	
	else if (fecha2.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha2.charAt(0),10) == 0){
			dia2 = parseInt(fecha2.charAt(1),10);
		}
		else{
			dia2 = parseInt(fecha2.substring(0,2),10);
		}
		if (parseInt(fecha2.charAt(2),10) == 0){
			mes2 = parseInt(fecha2.charAt(3),10);
		}
		else{
			mes2 = parseInt(fecha2.substring(2,4),10);
		}
		
		anio2 = parseInt(fecha2.substring(4),10) + 2000;
	}
	

	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8) 
	{
		//Descomponer la fecha que viene 
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0){
			dia = parseInt(fecha.charAt(1),10);
		}
		else{
			dia = parseInt(fecha.substring(0,2),10);
		}
		if (parseInt(fecha.charAt(2),10) == 0){
			mes = parseInt(fecha.charAt(3),10);
		}
		else{
			mes = parseInt(fecha.substring(2,4));
		}
		anio = parseInt(fecha.substring(4),10);
	
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
		
		if(anio == anio2){
			if(mes == mes2){
				if(dia >= dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<dia2){
					alert("La fecha de entrega es menor a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2){
					alert("La fecha de entrega es menor a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
			if(dia <= diasxmes[mes] && mes <= 12)
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			else{
				alert("La fecha de entrega es erronea");
				campo_fecha.value="";
				campo_fecha.select();
				return;
			}
		}
		else if(anio < anio2){
			alert("La fecha de entrega es menor a la fecha que se capturó la factura");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}

	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0){
			dia = parseInt(fecha.charAt(1),10);
		}
		else{
			dia = parseInt(fecha.substring(0,2),10);
		}
		if (parseInt(fecha.charAt(2),10) == 0){
			mes = parseInt(fecha.charAt(3),10);
		}
		else{
			mes = parseInt(fecha.substring(2,4),10);
		}
		
		anio = parseInt(fecha.substring(4),10) + 2000;

	


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
			
		if(anio == anio2){
			if(mes == mes2){
				if(dia >= dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<dia2){
					alert("La fecha de entrega es menor a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2){
					alert("La fecha de entrega es menor a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
			if(dia <= diasxmes[mes] && mes <= 12)
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			else{
				alert("La fecha de entrega es erronea");
				campo_fecha.value="";
				campo_fecha.select();
				return;
			}
		}
		else if(anio < anio2){
			alert("La fecha de entrega es menor a la fecha que se capturó la factura");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}
	else {
		campo_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		campo_fecha.value="";
		campo_fecha.select();
		return;
	}
}

function actualiza_cia(compania, nombre) {
	cia = new Array();// companias
	<!-- START BLOCK : nom_cia -->
	cia[{num_cia1}] = '{nombre_cia}';
	<!-- END BLOCK : nom_cia -->
			
	if (compania.value > 0) {
		if (cia[compania.value] == null) {
			alert("Compañía "+compania.value+" no esta en el catálogo de compañías");
			compania.value = "";
			nombre.value  = "";
			compania.select();
		}
		else 
			nombre.value   = cia[compania.value];
	}
	else if (compania.value == "") {
		compania.value = "";
		nombre.value  = "";
	}
}

</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de facturas de pastel</P>

<form name="form" method="post" action="./pan_rfa_cap3.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="fecha_oculta" type="hidden" value="{fecha_oculta1}">
 <table class="tabla">
      <tr>
        <th class="tabla">Compa&ntilde;ia</th>
        <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) form.fecha.select();" value="{num_cia}" size="3" maxlength="3" onchange="actualiza_cia(this,nombrecia);"></td>
		<td class="vtabla"><input name="nombrecia" type="text" id="nombrecia" size="50" disabled class="vnombre"></td>
        <th class="vtabla">Fecha</th>
        <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="valida_fecha(this);" onKeyDown="if (event.keyCode == 13) form.let_remi0.select();" value="{fecha}" size="10" maxlength="10"></td>
      </tr>
	  <tr>
	  	<td class="tabla" colspan="5"><input name= "buscaFactura" type="button" id="buscaFactura" class="boton" value="Buscar pendientes" onclick="buscarPendientes(document.form.num_cia.value, document.form.fecha.value);"/></td>
	  </tr>
</table> <br>
<table class="tabla">
	<tr>
		<td class="vtabla">    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviarBis" class="boton" value="Capturar Facturas" onclick='valida_registro()'> </td>
		
	</tr>
</table>
<br >
<table class="tabla">
        <tr>
          <th class="tabla">No. factura</th>
          <th class="tabla">Exp.</th>
          <th class="tabla">Kgs</th>
          <th class="tabla">P/unidad</th>
          <th class="tabla" >Pan </th>
          <th class="tabla" >Base</th>
          <th class="tabla" >A cuenta</th>
          <th class="tabla" >Dev. Base </th>

          <th class="tabla" >Resta</th>
          <th class="tabla" >Fecha <br>
            entrega</th>
          <th class="tabla" >Pastillaje</th>
          <th class="tabla" >Otros</th>
		  <th class="tabla" >Consultar</th>
        </tr>
        <!-- START BLOCK : rows -->
		<tr>
          <td class="tabla" align="center">
		  <input name="let_remi{i}" type="text" class="insert" id="1et_remi{i}" value="{let_remi}"  size="1" maxlength="1"  onKeyDown="if (event.keyCode == 13) form.num_remi{i}.select(); if(event.keyCode==37) form.otros_efectivos{ant}.select(); if(event.keyCode==38) form.let_remi{ant}.select(); if(event.keyCode==39) form.num_remi{i}.select(); if(event.keyCode==40) form.let_remi{next}.select();">
		  <input name="num_remi{i}" type="text" class="insert" id="num_remi{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();"onKeyDown="if (event.keyCode == 13) form.idexpendio{i}.select(); if(event.keyCode==37) form.let_remi{i}.select(); if(event.keyCode==38) form.num_remi{ant}.select(); if(event.keyCode==39) form.idexpendio{i}.select(); if(event.keyCode==40) form.num_remi{next}.select();" value="{num_remi}"  size="5">
          </td>
          <td class="tabla" align="center"><input name="idexpendio{i}" type="text" class="insert" id="idexpendio{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.kilos{i}.select(); if(event.keyCode==37) form.num_remi{i}.select(); if(event.keyCode==38) form.idexpendio{ant}.select(); if(event.keyCode==39) form.kilos{i}.select(); if(event.keyCode==40) form.idexpendio{next}.select();" value="{idexpendio}" size="1"></td>
          <td class="tabla" align="center"><input name="kilos{i}" type="text" class="insert" id="kilos{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.precio_unidad{i}.select(); if(event.keyCode==37) form.idexpendio{i}.select(); if(event.keyCode==38) form.kilos{ant}.select(); if(event.keyCode==39) form.precio_unidad{i}.select(); if(event.keyCode==40) form.kilos{next}.select();" value="{kilos}" size="2"></td>
          <td class="tabla" align="center"><input name="precio_unidad{i}" type="text" class="insert" id="precio_unidad{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.otros{i}.select(); if(event.keyCode==37) form.kilos{i}.select(); if(event.keyCode==38) form.precio_unidad{ant}.select(); if(event.keyCode==39) form.otros{i}.select(); if(event.keyCode==40) form.precio_unidad{next}.select();" value="{precio_unidad}" size="5"></td>
          <td class="tabla" align="center"><input name="otros{i}" type="text" class="insert" id="otros{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.base{i}.select(); if(event.keyCode==37) form.precio_unidad{i}.select(); if(event.keyCode==38) form.otros{ant}.select(); if(event.keyCode==39) form.base{i}.select(); if(event.keyCode==40) form.otros{next}.select();" value="{otros}" size="5"></td>
          <td class="tabla" align="center"><input name="base{i}" type="text" class="insert" id="base{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.cuenta{i}.select(); if(event.keyCode==37) form.otros{i}.select(); if(event.keyCode==38) form.base{ant}.select(); if(event.keyCode==39) form.cuenta{i}.select(); if(event.keyCode==40) form.base{next}.select();" value="{base}" size="5"></td>
          <td class="tabla" align="center"><input name="cuenta{i}" type="text" class="insert" id="cuenta{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.dev_base{i}.select(); if(event.keyCode==37) form.base{i}.select(); if(event.keyCode==38) form.cuenta{ant}.select(); if(event.keyCode==39) form.dev_base{i}.select(); if(event.keyCode==40) form.cuenta{next}.select();" value="{cuenta}" size="5"></td>
          <td class="tabla" align="center"><input name="dev_base{i}" type="text" class="insert" id="dev_base{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.resta{i}.select(); if(event.keyCode==37) form.cuenta{i}.select(); if(event.keyCode==38) form.dev_base{ant}.select(); if(event.keyCode==39) form.resta{i}.select(); if(event.keyCode==40) form.dev_base{next}.select();" value="{dev_base}" size="7"></td>

          <td class="tabla" align="center"><input name="resta{i}" type="text" class="insert" id="resta{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.fecha_entrega{i}.select(); if(event.keyCode==37) form.dev_base{i}.select(); if(event.keyCode==38) form.resta{ant}.select(); if(event.keyCode==39) form.fecha_entrega{i}.select(); if(event.keyCode==40) form.resta{next}.select();" value="{resta}" size="5"></td>
          <td class="tabla" align="center"><input name="fecha_entrega{i}" type="text" class="insert" id="fecha_entrega{i}" onChange="actualiza_fecha1(this,document.form.fecha_oculta);" onKeyDown="if (event.keyCode == 13) form.pastillaje{i}.select(); if(event.keyCode==37) form.resta{i}.select(); if(event.keyCode==38) form.fecha_entrega{ant}.select(); if(event.keyCode==39) form.pastillaje{i}.select(); if(event.keyCode==40) form.fecha_entrega{next}.select();" value="{fecha_entrega}" size="7"></td>
		  <td class="tabla" align="center"><input name="pastillaje{i}" type="text" class="insert" id="pastillaje{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.otros_efectivos{i}.select(); if(event.keyCode==37) form.fecha_entrega{i}.select(); if(event.keyCode==38) form.pastillaje{ant}.select(); if(event.keyCode==39) form.otros_efectivos{i}.select(); if(event.keyCode==40) form.pastillaje{next}.select();" value="{pastillaje}" size="5"></td>
		  <td class="tabla" align="center"><input name="otros_efectivos{i}" type="text" class="insert" id="otros_efectivos{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.let_remi{next}.select(); if(event.keyCode==37) form.pastillaje{i}.select(); if(event.keyCode==38) form.otros_efectivos{ant}.select(); if(event.keyCode==39) form.let_remi{next}.select(); if(event.keyCode==40) form.otros_efectivos{next}.select();" value="{otros_efectivos}" size="5"></td>
		  <td class="tabla" align="center"><input type="button" class="boton" value="Buscar" onclick='buscar(form.num_cia.value,form.num_remi{i}.value,form.let_remi{i}.value)'>
</td>
		</tr>
		<!-- END BLOCK : rows -->
  </table>  
	  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Facturas" onclick='valida_registro()'>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar pantalla" onclick='borrar()'>
</p>  
</form>  
</td>
</tr>
</table>
      <script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>