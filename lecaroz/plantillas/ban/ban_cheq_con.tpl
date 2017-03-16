<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<p class="title">Consulta de Cheques </p>
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if(document.form.bandera2.value==1)
	{
		if (document.form.folio.value=="" || document.form.folio.value<=0)
			{alert("Revise el folio");}
		else if(document.form.cia2.value=="" || document.form.cia2.value<=0)
			{alert("Revise la compañía");}
		else document.form.submit();
	}
	else if(document.form.bandera2.value==0)
	{
			if (document.form.bandera1.value==0 && (document.form.cia.value=="" || document.form.cia.value<=0))
				{alert("Revise la compañia");}
			else if (document.form.bandera1.value==1 && (document.form.proveedor.value=="" || document.form.prov.value<=0))
				{alert("Revise el proveedor");}
			else document.form.submit();
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



</script>

<form name="form" method="get" action="./ban_cheq_con.php">
  <table  class="tabla">
    <tr class="tabla">
      <th class="vtabla">
<label>
        <input name="consulta" type="radio" value="fecha" checked onchange="document.form.bandera2.value=0;">
        Fecha</label>
        <input name="fecha" type="text" class="insert" size="10" onChange="actualiza_fecha();">
      </th>
	<th class="vtabla"><label><input type="radio" name="consulta" value="folio" onchange="document.form.bandera2.value=1;">
Folio</label>
  <input name="folio" type="text" class="insert" id="folio2" size="10"></th>

    </tr>
    <tr class="tabla">
      <td class="vtabla">
        <label>
        <input name="tipo_con" type="radio" value="cia" checked onchange="document.form.bandera1.value=0;">
  Por compañía&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5">
        <br>
        <label>
        <input type="radio" name="tipo_con" value="prov" onchange="document.form.bandera1.value=1;">
  Por proveedor&nbsp;</label>
  <input name="proveedor" type="text" class="insert" id="proveedor2" size="5">
	  </td>
	  <td class="tabla">
		Compa&ntilde;&iacute;a&nbsp;<input name="cia2" type="text" class="insert" size="5"><br>
		<input name="bandera1" type="hidden" class="insert" id="bandera1" value="0" size="5">
		<input name="bandera2" type="hidden" class="insert" id="bandera2" value="0" size="5"></td>
    </tr>
  </table>
  <br>
<p><img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : cheque -->
<table border="1">
  <tr>
    <th class="tabla">Compa&ntilde;&iacute;a</th>
    <th class="tabla">Proveedor</th>
    <th class="tabla">Concepto</th>
    <th class="tabla">Fecha movimiento</th>
    <th class="tabla">Folio</th>
    <th class="tabla">Importe cheque </th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla"onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{cia}</td>
    <td class="tabla">{proveedor}</td>
    <td class="tabla">{concepto}</td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{folio}</td>
    <td class="tabla">{importe}</td>
  </tr>
 <!-- START BLOCK : rows -->
</table><br>
<input name="aceptar" type="button" class="boton" onClick="parent.location='./ban_cheq_con.php'" value="Aceptar">
<!-- END BLOCK : cheque -->
