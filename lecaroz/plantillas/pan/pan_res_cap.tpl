<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA INICIAL DE REZAGOS </P>
<!-- START BLOCK : obtener_dato -->

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
	//}
//			else {

//				document.form.fecha1.value = "";
//				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
//				document.form.fecha1.focus();
//				return;
//			}
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
//			}
//			else {
//				document.form.fecha1.value = "";
//				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
//				document.form1.fecha.focus();
//				return;
//			}
		}
		else {
			campo_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			campo_fecha.select();
			return;
		}
	}

	function valida(){
	if (document.form.num_cia.value > 0 && document.form.fecha.value != "")
		document.form.submit();
	else
		alert("Revise los datos por favor");
	}
	
</script>

<form action="./pan_res_cap.php" method="get" name="form">
  <table border="1" class="tabla">
    <tr>
      <th scope="col" class="tabla">Compañía</th>
      <th scope="col" class="tabla">Fecha</th>
    </tr>
    <tr>
      <td class="tabla">
	  <input name="num_cia" type="text" size="5" class="insert" onKeyDown="if(event.keyCode==13)document.form.fecha.select();">
	  </td>
      <td class="tabla"><input name="fecha" type="text" size="6" class="insert" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) document.form.enviar2.focus();"></td>
    </tr>
  </table>

<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>


  <p>
    <input type="button" name="enviar2" class="boton" value="Continuar" onclick='valida();'>
  </p>
</form>
<!-- END BLOCK : obtener_dato -->
<!-- START BLOCK : resagos -->
<p class="title">{num_cia}&#8212;{nom_cia}<br>{fecha}</P>
<script type="text/javascript" language="JavaScript">
	
function valida_registro() {



	document.form.submit();
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
	
	

<form name="form" method="post" action="./pan_res_cap1.php?tabla={tabla}">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" size="5">
<input name="fecha" type="hidden" id="fecha" value="{fecha}" size="5">
<input name="cont" type="hidden" id="cont" value="{cont}" size="5">  
<table class="tabla">
    <tr>
      <th class="tabla" colspan="2">Expendio</th>
      <th class="tabla">Importe</th>
      
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="tabla" align="center">
        <input name="num_exp{i}" type="hidden" class="insert" id="num_exp{i}" value="{num_exp}" size="5">{num_exp}
      </th>
	  <td class="vtabla" align="center">
	  {nombre_exp}
	  </td>
      <td class="tabla" align="center">
        <input name="importe{i}" type="text" class="insert" id="importe{i}" value="0" size="15" onKeyDown="if (event.keyCode == 13) document.form.importe{next}.select();" onChange="if (document.form.importe{i}.value < 0) alert('entre');">
   </td>
      
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Rezagos" onclick='valida_registro()'>&nbsp;&nbsp;
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
    <input name="button" type="button" class="boton" onclick='parent.history.back()' value="Regresar">
</form>
<!-- END BLOCK : resagos -->
</td>
</tr>
</table>