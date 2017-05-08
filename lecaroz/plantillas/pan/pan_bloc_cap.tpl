<!-- tabla control_produccion -->
<script type="text/javascript" language="JavaScript">
	
	miFecha = new Date()
	
	function valida_registro() {
		var bandera;
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañia');
			document.form.num_cia.select();
		}
		
		else if (document.form.folios_por_block.value.indexOf('.') > 0){
			alert('El numero de folios por block debe ser exacto');
			document.form.folios_por_block.select();
			}
		else if(parseInt(document.form.folio_final.value) < parseInt(document.form.folio_inicio.value)){
			alert('El folio final no puede ser menor o igual al folio inicial');
			document.form.folio_final.select();
			}
			
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}
	
	function calcula_bloc(){
	if(document.form.enviados.value=="" || parseInt(document.form.enviados.value)<=0)
		var fe=1;
	else
		var fe=parseInt(document.form.enviados.value);
	if(document.form.folio_inicio.value=="" || parseInt(document.form.folio_inicio.value)<=0)
		var f1=0;
	else
		var f1=parseInt(document.form.folio_inicio.value);
	if(document.form.folio_final.value=="" || parseInt(document.form.folio_final.value)<=0)
		var f2=0;
	else
		var f2=parseInt(document.form.folio_final.value);

	document.form.folios_por_block.value = ((f2 - f1) + 1)/ fe;
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
	
function valida_fecha()
{
	var fecha = document.form.fecha.value;
	var dia_m= {dia};

	var mes_m= {mes};
	var anio_m={anio_actual};
	var bandera=false;
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
	}
	else if (fecha.length == 6) 
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
		anio = parseInt(fecha.substring(4)) + 2000;
	}
//---------revision de dias

	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4)
	{

		if (anio > anio_m || anio < anio_m)
		{//año mayor
			document.form.fecha.value="";
			document.form.fecha.select();

			return;
		}
		else if (mes > mes_m && anio==anio_m)
		{//mes mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (mes == (mes_m -1)&& anio==anio_m)
			actualiza_fecha();
		else if (mes==mes_m && anio==anio_m)
			actualiza_fecha();
		else if (mes < mes_m-1)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
	}
	else 
	{//bloqueo de fechas mayores
		if (anio > anio_m || anio < anio_m)
		{//año mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m || mes < mes_m)
		{//mes mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el mes");
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el dia");
			return;
		}
		//caso en que la fecha es igual al dia corriente no se permite entrada
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el dia");
			return;
		}
		else if ((dia < dia_m) && (mes==mes_m) && (anio==anio_m))
			actualiza_fecha();
//		document.form.fecha.value="";
	}
}


function actualiza_fecha() {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = document.form.fecha.value;
		var anio_actual = {anio_actual};
//		var anio_actual = 2004;		
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
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha.focus();
					return;
				}
			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
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
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
					else {
						document.form.fecha.value = dia+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha.focus();
					return;
				}
			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
		}
		else {
			document.form.fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha.focus();
			return;
		}
	}
	
function actualiza_cia(compania, nombre) {
	cia = new Array();// companias

	<!-- START BLOCK : nom_cia -->
	cia[{num_cia}] = '{nombre_cia}';
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
<tr align="center" valign="middle">
<td>
<p class="title">Envío de blocs a panaderias</p>

<form name="form" action="./insert_pan_bloc_cap.php?tabla={tabla}" method="post">
<table class="tabla">
    <tr>
      <th  class="vtabla">N&uacute;mero de Compa&ntilde;&iacute;a</th>
      <td  class="vtabla">
	  <input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5" onKeyDown="if (event.keyCode == 13) document.form.enviados.select();" onChange="actualiza_cia(this,nombrecia);">
      <input name="nombrecia" type="text" id="nombrecia" size="50" disabled class="vnombre">
      <input name="estado" type="hidden" id="estado" value="false">
      <input name="folios_usados" type="hidden" id="folios_usados" value="0">

	  </td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de Blocs enviados 
      <td class="vtabla"><input name="enviados" type="text" id="enviados" size="5" class="insert" value=1 onKeyDown="if (event.keyCode == 13) document.form.letra_folio.select();"></td>
    </tr>
    <tr>
      <th class="vtabla">Folio inicial del primer bloc</th>
      <td class="vtabla"><input name="letra_folio" type="text" class="insert" id="letra_folio" size="2" maxlength="2" onChange="document.form.lf.value=document.form.letra_folio.value" onKeyDown="if (event.keyCode == 13) document.form.folio_inicio.select();">
      <input name="folio_inicio" type="text" class="insert" id="folio_inicio" size="15" maxlength="15" onchange="calcula_bloc()" onKeyDown="if (event.keyCode == 13) document.form.lf.select();"></td>
    </tr>
    <tr>
      <th class="vtabla">Folio final del &uacute;ltimo bloc</th>
      <td class="vtabla"><input name="lf" type="text" class="insert" id="lf" size="2" maxlength="2" onKeyDown="if (event.keyCode == 13) document.form.folio_final.select();">
	  <input name="folio_final" type="text" class="insert" id="folio_final" size="15" maxlength="15" onchange="calcula_bloc()" onKeyDown="if (event.keyCode == 13) document.form.fecha.select();"></td>
    </tr>
    <tr>
      <th class="vtabla">Cantidad de folios por bloc</th>
      <td class="vtabla"><input name="folios_por_block" type="text" class="insert" id="folios_por_block" size="10" maxlength="5" readonly></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de envio </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" value="{fecha}" size="10" maxlength="11" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" onChange="valida_fecha();"></td>
    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <br><br>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>

<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>

</td>
</tr>
</table>