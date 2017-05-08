<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Inventario inicial por código de materia prima </p>
<script language="JavaScript" type="text/JavaScript">
function actualiza_mp(cod_mp, nombre) {
	mp = new Array();// mp
	<!-- START BLOCK : nom_mp -->
	mp[{cod_mp}] = '{nombre_mp}';
	<!-- END BLOCK : nom_mp -->
			
	if (cod_mp.value > 0) {
		if (mp[cod_mp.value] == null) {
			alert("Gasto "+cod_mp.value+" no esta en el catálogo de mps");
			cod_mp.value = "";
			nombre.value  = "";
			cod_mp.focus();
		}
		else {
			nombre.value   = mp[cod_mp.value];
		}
	}
	else if (cod_mp.value == "") {
		cod_mp.value = "";
		nombre.value  = "";
	}
}

function valida_registro()
{
	if(document.form.cod_mp.value <=0)
	{
		alert("Ingrese un número de Materia Prima");
		document.form.cod_mp.focus();
	}
	else
		document.form.submit();
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

	
</script>
<form name="form" method="get" action="./hist_inventario.php" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="vtabla">Código Materia Prima</th>
    <td class="tabla">
      <input name="cod_mp" type="text" class="insert" id="cod_mp" size="5" maxlength="5" onChange="actualiza_mp(this, form.nombre_mp)">	
      <input name="nombre_mp" type="text" id="nombre_mp" size="50" disabled class="vnombre">
	
	</td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" value="{fecha}" size="12" maxlength="12" onChange="actualiza_fecha();"></td>
  </tr>
</table><br>
<input name="enviar" type="button" class="boton" id="enviar" value="Continuar" onClick="valida_registro();">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Inventario inicial por código de materia prima </p>
<script language="javascript" type="text/javascript">

function actualiza_cia(num_cia, nombre) {
	cia = new Array();
	<!-- START BLOCK : nombre_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nombre_cia -->
			
	if (num_cia.value > 0) {
		if (cia[num_cia.value] == null) {
			alert("Compañía "+num_cia.value+" no esta en el catálogo de Compañías");
			num_cia.value = "";
			nombre.value  = "";
			num_cia.focus();
		}
		else {
			nombre.value   = cia[num_cia.value];
		}
	}
	else if (num_cia.value == "") {
		num_cia.value = "";
		nombre.value  = "";
	}
}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			document.form.num_cia0.select();
	}
	
	function borrar() {
		if (confirm("Se borraran todos los datos del formulario capturado. ¿Desea continuar?"))
			document.form.reset();
		else
			document.form.num_cia0.select();
	}
	
	function suma(valores, unidades, costo){
	var valores=parseFloat(valores.value);
	var unidades=parseFloat(unidades.value);
	costo.value=valores/unidades;
	}
</script>
<form name="form" method="post" action="./insert_hist_inventario.php?tabla={tabla}">
<table class="tabla">
  <tr>
    <th class="vtabla"><font size="+1">Materia Prima </font></th>
    <td class="vtabla"> <font size="+1">{codmp} - {cod_nombre}</font></td>
    <th class="vtabla"><font size="+1">Fecha</font></th>
    <td class="vtabla"> <font size="+1">{fecha}</font></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr class="tabla">
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Nombre de la Compa&ntilde;&iacute;a </th>
    <th class="tabla" scope="col">Unidades</th>
    <th class="tabla" scope="col">Valores</th>
    <th class="tabla" scope="col">Costo</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr class="tabla">
    <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" size="5" maxlength="5" onChange="actualiza_cia(this, form.nombre_cia{i})" onKeyDown="if (event.keyCode == 13) form.unidades{i}.select();">
      <span class="vtabla">
      <input name="fecha{i}" type="hidden" id="fecha{i}" value="{fecha}">
      <input name="codmp{i}" type="hidden" id="compania2" value="{codmp}">
</span></td>
    <td class="tabla"><input name="nombre_cia{i}" type="text" class="vnombre" id="nombre_cia{i}" size="30" disabled></td>
    <td class="tabla"><input name="unidades{i}" type="text" class="insert" id="unidades{i}" onKeyDown="if (event.keyCode == 13) form.valores{i}.select();" value="0" size="12" maxlength="12">
      </td>
    <td class="tabla"><input name="valores{i}" type="text" class="insert" id="valores{i}" onKeyDown="if (event.keyCode == 13) form.num_cia{next}.select();" value="0" size="12" maxlength="12" onChange="suma(this,form.unidades{i},form.costo{i});"></td>
    <td class="tabla"><input name="costo{i}" type="text" class="nombre" id="costo{i}" size="12" maxlength="12" readonly></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>

<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input type="button" class="boton" value="Borrar" onClick="borrar();">
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="./menus/insert.gif" width="16" height="16">
<input name="capturar" type="button" class="boton" id="enviar" value="Capturar inventario" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : captura -->
