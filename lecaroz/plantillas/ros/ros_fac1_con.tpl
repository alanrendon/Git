<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Facturas de Rosticerias</P>
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function actualiza_fecha(campo_fecha) {
	var fecha = campo_fecha.value;
	var anio_actual = {anio_actual};

	// Si la fecha tiene el formato ddmmaaaa
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

		// El año de captura de ser el año en curso
	//			if (anio == anio_actual) {
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
//			if (anio == (anio_actual)) {
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
		else {
			campo_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			campo_fecha.select();
			return;
		}
	}

function valida(){
	if (document.form.num_cia.value<=0 || document.form.num_cia.value=="" || document.form.fecha_mov.value=="")
	{
		alert("Verifique los campos por favor");
		document.form.num_cia.select();
	}
	else document.form.submit();
}
</script>
<form name="form" action="./ros_fac1_con.php" method="get">
<input name="temp" type="hidden">

  <table class="tabla">
    <tr>
      <th class="vtabla">Compañía</th>
      <td class="vtabla">
        <input class="insert" name="num_cia" type="text" id="num_cia" size="10" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
      </td>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
        <!-- <option value="13" selected>13 POLLOS GUERRA</option>
		<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
		<option value="1225">1225 EL RANCHERITO S.A. DE C.V.</option>
        <option value="204">204 GONZALEZ AYALA JOSE REGINO</option> -->
        <!-- START BLOCK : pro -->
        <option value="{value}">{value} {text}</option>
        <!-- END BLOCK : pro -->
      </select></td>
      <th class="vtabla">Fecha del movimiento</th>
      <td class="vtabla"><input class="insert" name="fecha_mov" type="text" id="fecha_mov" size="10" maxlength="10" onChange="actualiza_fecha(this)"></td>
    </tr>
  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onClick='valida();'>
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : factura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Fecha de pago </th>
    </tr>
    <tr>
      <td class="tabla" align="center">

<font size="+1">
<strong>{numero_cia}&#8212;{nombre_cia}</strong>
</font>
</td>
      <td class="tabla" align="center">

<font size="+1">
{num_proveedor}&#8212;{nom_proveedor}
</font>

</td>
      <td class="tabla" align="center">

	<font size="+1">
	{num_factura}
	</font>
</td>
      <td class="tabla" align="center">
<font size="+1">{fecha_mov}</font>      </td>
      <td class="tabla" align="center">
<font size="+1">
      {fecha_pago}
</font>	  </td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th width="306" align="center" class="tabla">C&oacute;digo de Materia Primas</th>
      <th width="112" align="center" class="tabla">Cantidad</th>
      <th width="89" align="center" class="tabla">Kilos</th>
      <th width="101" align="center" class="tabla">Precio unitario </th>
      <th width="88" align="center" class="tabla">Total</th>
    </tr>
    <!-- START BLOCK : rows -->
     <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		  <td class="vtabla" align="left">
			<strong>{codmp}&#8212;{nom_mp}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong> {cantidad}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong>{kilos}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong> {precio}</strong>
		  </td>
		  <th class="tabla" align="center">
			<strong>{total1}</strong>
		  </th>
    </tr>
    <!-- END BLOCK : rows -->

<!-- START BLOCK : totales -->

  <th class="tabla" colspan="4" align="center"><b>Total</b></th>
      <th class="tabla" align="center">
        <font size="+2">{total}</font></th>
    <!-- END BLOCK : totales -->
  </table>

  <p>
    <input name="button" type="button" class="boton" onclick='parent.history.back()' value="Regresar">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : factura -->
