<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
		if(document.form.fecha_inicio.value==""){
			alert("Debe Ingresar una fecha de inicio de consulta");
			return;
		}
		else if(document.form.fecha_final.value==""){
			alert("debe ingresar una fecha de término de consulta");
			return;
		}
		else if(document.form.num_cia.value==""){
			alert("debe especificar una compañía para consultar");
			return;
		}
		else
			document.form.submit();

	}

	
function actualiza_fecha(campo_fecha) {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = campo_fecha.value;
		var anio_actual = {anio_actual};
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
//			if (anio == anio_actual || anio== anio_actual - 1) {
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
/*			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
*/		}
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
//			if (anio == (anio_actual) || anio== anio_actual -1) {
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
/*			}
			else {
				document.form.fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha.focus();
				return;
			}
*/		}
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
<p class="title">CONSULTA DE MEDIDORES DE AGUA</p>
<form name="form" action="./pan_agu_con.php" method="get">
  <p>
    <input name="temp" type="hidden">
</p>
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">Fecha inicio
        <input name="fecha_inicio" type="text" class="insert" id="fecha_inicio" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) document.form.fecha_final.select();"> </th>
      <th class="tabla">Fecha final
        <input name="fecha_final" type="text" class="insert" id="fecha_final" size="10" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) document.form.num_cia.select();"> </th>
    </tr>
    <tr>
      <td class="tabla" colspan="2">Compa&ntilde;&iacute;a 
        <input name="num_cia" type="text" class="insert" id="num_cia" size="4" maxlength="3" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();"></td>

    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  &nbsp;&nbsp;  </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha_inicio.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : consulta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">CONSULTA DE MEDIDORES DE AGUA</p>
<p class="title">{num_cia}&nbsp;{nombre_cia}<br>DEL {fecha1} AL {fecha2}</p>
<table border="1" class="tabla">
    <tr class="tabla">
	  <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Hora</th>
      <th class="tabla" scope="col">Medidor 1</th>
      <th class="tabla" scope="col">Medidor 2</th>
      <th class="tabla" scope="col">Medidor 3</th>
      <th class="tabla" scope="col">Medidor 4</th>
    </tr>
	<!-- START BLOCK : rows1 -->
    <tr>
      <td class="tabla">
	  	{fecha}</td>
      <td class="tabla">
	  	{hora}</td>
      <td class="rtabla">
	  	{medidor1}
		</td>
      <td class="rtabla">
	  	{medidor2}
		</td>
      <td class="rtabla">
	  	{medidor3}
		</td>
      <td class="rtabla">
	  	{medidor4}
		</td>
    </tr>
	<!-- END BLOCK : rows1 -->
	
    <tr>
      <th class="tabla" colspan="2">Total de M<sup>3</sup> consumidos:</th>
      <td class="rtabla">{total1}</td>
      <td class="rtabla">{total2}</td>
      <td class="rtabla">{total3}</td>
      <td class="rtabla">{total4}</td>
    </tr>
    <tr>
      <th class="tabla" colspan="2">Promedio de {dias} dias:</th>
      <td class="rtabla">{p1}</td>
      <td class="rtabla">{p2}</td>
      <td class="rtabla">{p3}</td>
      <td class="rtabla">{p4}</td>
    </tr>
  </table>

</td>
</tr>
</table>
<!-- END BLOCK : consulta -->


