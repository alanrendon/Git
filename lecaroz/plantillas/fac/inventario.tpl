<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_compania -->
<script language="JavaScript" type="text/JavaScript">
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
			if (anio == anio_actual || anio == anio_actual-1) {
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
			if (anio == (anio_actual) || anio == anio_actual-1) {
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
			document.form.fecha.select();
			return;
		}
	}
	
	
function valida(){
	if(document.form.compania.value==""){
		alert("Debes ingresar una compañía");
		return;
	}
	else if(document.form.fecha.value==""){
		alert("Debes ingresar una fecha");
		return;
	}
	else{
		document.form.submit();
	}
}	
	
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title" align="center">Captura de av&iacute;o nuevo </p>
<form name="form" method="get" action="./inventario.php">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" size="5" maxlength="5" onKeyDown="if(event.keyCode==13) form.fecha.select();"></td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" size="12" maxlength="12" onChange="actualiza_fecha();" onKeyDown="if(event.keyCode==13) form.continuar.focus();"></td>
  </tr>
</table><br>
<input name="continuar" type="button" value="continuar" onClick="valida();" class="boton">
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.compania.select();</script>
<!-- END BLOCK : obtener_compania -->

<!-- START BLOCK : captura -->


<script language="javascript" type="text/javascript">
	function actualiza_campos(codmp, nombre, unidad, existencia, fecha, fechain, fechaout, numcia) {
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		unidad_consumo = new Array();	// Unidad de consumo
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}] = '{nombre_mp}';
		unidad_consumo[{codmp}] = '{unidad}';
		<!-- END BLOCK : nombre_mp -->
				
		if (codmp.value > 0) {
			if (mp[codmp.value] == null) {
				alert("Código "+codmp.value+" no esta en el catálogo de materias primas");
				codmp.value      = "";
				nombre.value     = "";
				unidad.value     = "";
				existencia.value = "";
				fecha.value      = "";
				fechain.value    = "";
				fechaout.value   = "";
				numcia.value     = "";
				codmp.focus();
			}
			else {
				nombre.value   = mp[codmp.value];
				unidad.value   = unidad_consumo[codmp.value];
				fecha.value    = document.form.fecha.value;
				fechain.value  = document.form.fecha.value;
				fechaout.value = document.form.fecha.value;
				numcia.value   = document.form.compania.value;
			}
		}
		else if (codmp.value == "") {
			codmp.value      = "";
			nombre.value     = "";
			unidad.value     = "";
			existencia.value = "";
			fecha.value      = "";
			fechain.value    = "";
			fechaout.value   = "";
			numcia.value     = "";
		}
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			document.form.codmp0.select();
	}
	
	function borrar() {
		if (confirm("Se borraran todos los datos del formulario capturado. ¿Desea continuar?"))
			document.form.reset();
		else
			document.form.codmp0.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./inventario.php?ok=1">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. Y C.V. <br>
Captura de av&iacute;o nuevo </p>
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="compania" type="hidden" id="compania" value="{num_cia}">
	<input name="temp" type="hidden">
      {num_cia} - {nombre_cia} </td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
      {fecha}</td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Nombre de Materia Prima y Unidad </th>
    <th class="tabla" scope="col">Existencia</th>
    <th class="tabla" scope="col">Costo Unitario</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="5" maxlength="5" onChange="actualiza_campos(form.codmp{i},form.nombre{i},form.unidad{i},form.existencia{i},form.fecha{i},form.fecha_entrada{i},form.fecha_salida{i},form.num_cia{i})" onKeyDown="if (event.keyCode == 13) form.existencia{i}.select();"></td>
    <td class="tabla"><input name="nombre{i}" type="text" class="vnombre" id="nombre{i}" size="30" readonly><input name="unidad{i}" type="text" class="vnombre" id="unidad{i}" size="10" readonly></td>
    <td class="tabla"><input name="existencia{i}" type="text" class="insert" id="existencia{i}" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onFocus="form.temp.value=this.value" onKeyDown="if (event.keyCode == 13) form.precio_unidad{i}.select();" value="0" size="12" maxlength="12">      
      <input name="num_cia{i}" type="hidden" id="num_cia{i}">
      <input name="fecha{i}" type="hidden" id="fecha{i}">
      <input name="fecha_entrada{i}" type="hidden" id="fecha_entrada{i}">
      <input name="fecha_salida{i}" type="hidden" id="fecha_salida{i}">
	  <input name="num_orden{i}" type="hidden">
	  </td>
    <td class="tabla"><input name="precio_unidad{i}" type="text" class="insert" id="precio_unidad{i}" size="12" maxlength="12" onChange="var value=parseFloat(this.value); this.value=value.toFixed(3);" onKeyDown="if (event.keyCode == 13) {form.codmp{next}.select(); scrollByLines(1)}"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>

<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input type="button" class="boton" value="Borrar" onClick="borrar();">
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="./menus/insert.gif" width="16" height="16">
<input name="enviar" type="button" class="boton" id="enviar" value="Capturar inventario" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.codmp0.select();</script>
<!-- END BLOCK : captura -->
