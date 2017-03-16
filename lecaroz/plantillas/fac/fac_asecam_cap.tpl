<!-- tabla facturas -->
<script type="text/javascript" language="JavaScript">

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
	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4 || dia_m==5)
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
			actualiza_fecha(campo_fecha);
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
			actualiza_fecha(campo_fecha);
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

function valida_registro() {
		if(document.form.num_cia.value <= 0 || document.form.num_cia.value=="") {
			alert('Debe especificar una compañia');
			document.form.num_cia.select();
		}
		else if(document.form.fecha_mov.value <= 0 || document.form.fecha_mov.value=="") {
			alert('Debe especificar una fecha');
			document.form.fecha_mov.select();
		}
		else if(document.form.num_fact.value <= 0 || document.form.num_fact.value=="") {
			alert('Debe especificar un número de factura');
			document.form.num_fact.select();
		}
		else if(document.form.codgastos.value <= 0 || document.form.codgastos.value=="") {
			alert('Debe especificar un código de gasto');
			document.form.codgastos;
		}
		else if(document.form.concepto.value=="") {
				alert('Debe especificar el concepto');
				document.form.concepto;
			}
		else if(document.form.importe_total.value <= 0 || document.form.importe_total.value=="") {
			alert('El importe de la factura es erroneo');
			document.form.importe_total;
		}
		else 		
		{
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}
	
	function borrar() {
			document.form.reset();
			document.form.num_cia.select();
	}


	function actualiza_compania(compania, nombre) {
	cia = new Array();// Companias
	<!-- START BLOCK : nom_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nom_cia -->
	if (compania.value > 0) {
		if (cia[compania.value] == null) {
			alert("Compania "+compania.value+" no esta en el catálogo de Compañías");
			compania.value = "";
			nombre.value  = "";
			compania.select();
		}
		else {
			if(cia[compania.value]=='') nombre.value = ""; else nombre.value = cia[compania.value];
		}
	}
	else if (compania.value == "") {
		compania.value = "";
		nombre.value  = "";
	}
}

function suma(importe,iva_renta,iva_capital,iva_interes)
{
	if(importe.value=="") var imp=0;
	else var imp=parseFloat(importe.value);

	if(iva_renta.value=="") var renta=0;
	else var renta=parseFloat(iva_renta.value);

	if(iva_capital.value=="") var capital=0;
	else var capital=parseFloat(iva_capital.value);

	if(iva_interes.value=="") var interes=0;
	else var interes=parseFloat(iva_interes.value);
	
	medio=imp/2;
	if(interes >= medio){
		alert("El I.V.A. de INTERES es demasiado alto");
		iva_interes.select();
		return;
		}
	else if(renta >= medio){
		alert("El I.V.A. de RENTA es demasiado alto");
		iva_renta.select();
		return;
		}
	else if(capital >= medio){
		alert("El I.V.A. de CAPITAL es demasiado alto");
		iva_capital.select();
		return;
		}
	else{
		var total = imp + renta + capital + interes;
		document.form.importe_total.value=total.toFixed(2);
		}
}

</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">FACTURAS DE ARRENDADORA ASECAM, S.A. DE C.V.</p>
<form name="form" method="post" action="./insert_fac_asecam.php?tabla={tabla}">

<table class="tabla">
      <tr>
        <th class="vtabla">N&uacute;mero compa&ntilde;&iacute;a</th>
        <td class="vtabla">
          <input name="num_cia" type="text" class="insert" id="num_cia" size="4" onChange="actualiza_compania(this,form.nombre_cia)" onKeyDown="if (event.keyCode == 13) document.form.num_fact.select();">
          <input name="num_proveedor" type="hidden" class="insert" id="num_proveedor" value="168" size="4">
          <input name="temp" type="hidden" class="insert" id="temp" size="4">
          <input name="nombre_cia" type="text" disabled class="vnombre" id="nombre_cia" size="50">
        </td>
      </tr>
      <tr>
        <th class="vtabla">N&uacute;m. factura</th>
        <td class="vtabla">
          <input name="num_fact" type="text" class="insert" id="num_fact" size="12" maxlength="12" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();" onFocus="form.temp.value=this.value">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Fecha movimiento</th>
        <td class="vtabla">
          <input name="fecha_mov" type="text" class="insert" id="fecha_mov" onChange="valida_fecha(this);" size="11" maxlength="11" onKeyDown="if (event.keyCode == 13) document.form.concepto.select();">
		  </td>
      </tr>
	  <tr>
        <th class="vtabla">Concepto</th>
        <td class="vtabla">
          <input name="concepto" type="text" class="vinsert" id="concepto" size="45" maxlength="30" onKeyDown="if (event.keyCode == 13) document.form.importe.select();" >
        </td>
      </tr>

      <tr>
        <th class="vtabla">Importe sin I.V.A.</th>
        <td class="vtabla">
          <input name="importe" type="text" class="rinsert" id="importe"  size="15" onKeyDown="if (event.keyCode == 13) document.form.iva_renta.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); suma(this,form.iva_renta,form.iva_capital,form.iva_interes);" onFocus="form.temp.value=this.value">
        </td>
      </tr>


      <tr>
        <th class="vtabla">Importe I.V.A. Renta </th>
        <td class="vtabla">
          <input name="iva_renta" type="text" class="rinsert" id="iva_renta"  size="15" onKeyDown="if (event.keyCode == 13) document.form.iva_capital.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); suma(form.importe,this,form.iva_capital,form.iva_interes);" onFocus="form.temp.value=this.value">
        </td>
      </tr>


	  <tr>
        <th class="vtabla">Importe I.V.A. Capital </th>
        <td class="vtabla">
          <input name="iva_capital" type="text" class="rinsert" id="iva_capital" size="15" onChange="suma(form.importe,form.iva_renta,this,form.iva_interes); valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) document.form.iva_interes.select();">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Importe I.V.A. Inter&eacute;s </th>
        <td class="vtabla">
          <input name="iva_interes" type="text" class="rinsert" id="iva_interes" size="15" onChange="suma(form.importe,form.iva_renta,form.iva_capital,this); valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) document.form.codgastos.select();">
		  </td>
      </tr>
      <tr>
        <th class="vtabla">Importe Total</th>
        <td class="vtabla">
          <input name="importe_total" type="text" class="rinsert" id="importe_total" size="15" onKeyDown="if (event.keyCode == 13) document.form.codgastos.select();" readonly>
        </td>
      </tr>

	  <tr>
        <th class="vtabla">C&oacute;digo gasto</th>
        <td class="vtabla">
          <input name="codgastos" type="text" class="insert" id="codgastos" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" value="104" size="5">
        </td>
      </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <br><br>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p> 
</form>

<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>

</td>
</tr>
</table>