<script type="text/javascript" language="JavaScript">


function valida(){
if (!(parseInt(document.formi.compania.value) >= 301 && parseInt(document.formi.compania.value) <= 599 || parseInt(document.formi.compania.value) == 702 || parseInt(document.formi.compania.value) == 704))
	{
	alert("No es una rosticeria");
	document.formi.compania.value="";
	}
else document.formi.submit();
}

	function borrar() {
		if (confirm("¿Desea borrar la pantalla?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de Facturas de Rosticerias</p>
<!-- START BLOCK : obtener_dato -->
<form name="formi" method="get" action="ros_fac_cap.php" onkeydown="if (event.keyCode == 13) document.formi.enviar.focus();">

  <table class="tabla">
    <tr>
      <th class="vtabla"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" size="5"></td>
    </tr>

    <tr>
      <th class="tabla" align="center"><font size="+1">Proveedor</font>
      </th>
      <td class="tabla" align="center"><select name="num_pro" class="insert" id="num_pro">
        <!-- <option value="13" selected="selected">13 POLLOS GUERRA</option>
		<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
		<option value="1386">1386 EL RANCHERITO S.A. DE C.V.</option> -->
		<!-- START BLOCK : pro -->
		<option value="{value}">{value} {text}</option>
		<!-- END BLOCK : pro -->
</select></td>
    </tr>

  </table>
  <br>

  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Continuar" onclick='valida()'>

</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.formi.compania.select();</script>

<!-- END BLOCK : obtener_dato -->



<!-- START BLOCK : factura -->
<script language="JavaScript" type="text/JavaScript">
//---------------------------------------------------VALIDA FECHA-------------------------------------------------------------

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
	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4 || dia_m==5 || dia_m==6 || dia_m==7 || dia_m==8)
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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de Facturas de Rosticerias</p>
<form name="form" method="post" action="ros_fac_cap1.php?tabla={tabla}" >
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" align="center"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <th class="tabla" align="center"><font size="+1">Proveedor</font></th>
      <th class="tabla" align="center"><font size="+1">N&uacute;mero Factura</font></th>
      <th class="tabla" align="center"><font size="+1">Fecha movimiento</font></th>
    </tr>

    <tr>
      <td class="tabla" align="center">
          <input name="num_cia" type="hidden" class="insert" id="num_cia" value="{num_cia}" size="5"><font size="+1">
{num_cia}&#8212;{nom_cia} </font></td>
      <td class="tabla" align="center">
          <input name="num_proveedor" type="text" class="insert" id="num_proveedor" value="{num_pro}" size="5" onfocus="form.temp.value=this.value" onchange="valor=isInt(this,form.temp); if (valor==false) this.select();">
      </td>
      <td class="tabla" align="center">
          <input name="num_fac" type="text" class="insert" id="num_fac" onfocus="form.temp.value=this.value" onchange="this.value=this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g,'');this.value=this.value.toUpperCase();" onkeydown="if (event.keyCode == 13) document.form.cantidad0.focus();" value="{num_fac}" size="5">

      </td>
      <td class="tabla" align="center">
        <input name="fecha_mov" type="text" class="insert" id="fecha_mov" onchange="valida_fecha(this);" value="{fecha}" size="9">
        <input name="contador" type="hidden" class="insert" id="contador" value="{contador}" size="9">
   </td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center" colspan="2">C&oacute;digo de Materia Primas</th>
      <th class="tabla" align="center">Cantidad</th>
      <th class="tabla" align="center">Kilos</th>
      <th class="tabla" align="center">Precio unitario </th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr>
      <th class="vtabla">
          <input name="codmp{i}" type="hidden" class="insert" id="codmp{i}" value="{codmp}" size="5">
      {codmp}     </th>
      <td class="vtabla">

      {nom_codmp}     </td>

      <td class="tabla" align="center">
          <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" onfocus="form.temp.value=this.value" onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.kilos{i}.focus();" value="{cantidad}" size="5">
      </td>
      <td class="tabla" align="center">
          <input name="kilos{i}" type="text" class="insert" id="kilos{i}" onfocus="form.temp.value=this.value" onchange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.cantidad{next}.focus();" value="{kilos}" size="5">
      </td>
      <td class="tabla" align="center">
        <input name="precio{i}" type="hidden" class="insert" id="precio{i}" value="{precio}" size="5">
{precio1}      </td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Movimientos" onclick='document.form.submit();'>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
	<br>
	<input type="button" class="boton" value="Regresar" onclick="document.location='./ros_fac_cap.php'">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_fac.select();</script>
<!-- END BLOCK : factura -->
