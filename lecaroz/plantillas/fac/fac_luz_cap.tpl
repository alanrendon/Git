<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_dato -->
<script language="JavaScript" type="text/JavaScript">
function valida(){
 if(document.form.cia.value=="" || parseInt(document.form.cia.value)<=0){
    alert("Revise la Compañía");
    document.form.cia.select();
	}
 else if(document.form.proveedor.value=="" || parseInt(document.form.proveedor.value)<=0){
    alert("Revise el código de proveedor");
    document.form.proveedor.select();
	}

 else
    document.form.submit();
}
     
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA DE FACTURAS DE LUZ</p>
<form name="form" action="./fac_luz_cap.php" method="get">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Número de Compañía</th>
    <th scope="col" class="tabla">Número de Proveedor</th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><input name="cia" type="text" class="insert" size="5" maxlength="3" onKeyDown="if(event.keyCode==13)form.proveedor.select();"></td>
    <td class="tabla"><input name="proveedor" type="text" class="insert" id="proveedor" size="5" maxlength="3" onKeyDown="if(event.keyCode==13)form.enviar.focus();"></td>
  </tr>
</table>
<p>
  <input type="button" name="enviar" value="Siguiente" onClick="valida();" class="boton">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_dato -->


<!-- START BLOCK : factura -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_fact.value <= 0) {
			alert('Debe especificar un número de factura');
			document.form.num_fact.select();
		}
		else if(document.form.num_fact.value == "") {
			alert('Debe especificar un número de factura');
			document.form.num_fact.select();
		}
		else if(document.form.codgastos.value <= 0) {
			alert('Debe especificar un código de gasto');
			document.form.codgastos.select();
		}
		else if(document.form.codgastos.value == "") {
			alert('Debe especificar un código de gasto');
			document.form.codgastos.select();
			}
		else if(document.form.importe_total.value <= 0) {
			alert('Debe especificar importe de la factura');
			document.form.importe_total.select();
		}
		else if(document.form.importe_total.value == "") {
			alert('Debe especificar importe de la factura');
			document.form.importe_total.select();
		}
		else if(document.form.importe_iva.value <= 0) {
			alert('Debe especificar importe del iva');
			document.form.importe_iva.select();
		}
		else if(document.form.importe_iva.value == "") {
			alert('Debe especificar importe del iva');
			document.form.importe_iva.select();
		}
		else if(parseFloat(document.form.importe_iva.value) >= parseFloat(document.form.importe_total.value)){
			alert("el iva no puede ser mayor al total de la factura");
			document.form.importe_iva,select();
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
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
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
	
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">FACTURAS DE LUZ</p>
<p class="title">{num_cia}&#8212;{nombre_cia}</p>
<form name="form" method="post" action="./insert_fac_luz_cap.php?tabla={tabla}">

<table class="tabla">
<tr class="tabla">
<th colspan="2" class="tabla"> {num_proveedor}&nbsp;{nombre_proveedor}</th>
</tr>
      <tr>
        <th class="vtabla">N&uacute;m. factura</th>
        <td class="vtabla">
          <input name="num_fact" type="text" class="insert" id="num_fact" onKeyDown="if (event.keyCode == 13) document.form.fecha.select();" value="{num_fact}" size="12" maxlength="12">
          <input type="hidden" name="temp">
          <input type="hidden" name="num_cia" value="{num_cia}">
          <input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}"></td>
      </tr>
	  <tr>
	    <th class="vtabla">Fecha</th>
	    <td class="vtabla"><input name="fecha" type="text" class="insert" value="{fecha}" size="10" onKeyDown="if (event.keyCode == 13) document.form.concepto.select();" onChange="actualiza_fecha(this);"></td>
	    </tr>
	  
      <tr>
        <th class="vtabla">Concepto</th>
        <td class="vtabla">
          <input name="concepto" type="text" class="insert" id="concepto" onKeyDown="if (event.keyCode == 13) document.form.importe_total.select();" size="50">
        </td>
      </tr>

      <tr>
        <th class="vtabla">Importe total</th>
        <td class="vtabla">
          <input name="importe_total" type="text" class="rinsert" id="importe_total" onFocus="form.temp.value=this.value;" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) document.form.importe_iva.select();" size="15">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Importe del I.V.A.</th>
        <td class="vtabla">
          <input name="importe_iva" type="text" class="rinsert" id="importe_iva" size="15" onFocus="form.temp.value=this.value;" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if(event.keyCode==13)form.codgastos.select();">
        </td>
      </tr>
	  <tr>
        <th class="vtabla">C&oacute;digo gasto</th>
        <td class="vtabla">
          <input name="codgastos" type="text" class="rinsert" id="codgastos" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" value="12" size="5">
        </td>
      </tr>
  </table>
  <p>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;
  <input name="regresar" type="button" class="boton" onClick='parent.history.back();' value="Regresar">

  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
</p> 
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_fact.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : factura -->
