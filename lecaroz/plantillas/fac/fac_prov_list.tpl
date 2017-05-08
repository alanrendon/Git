
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Movimientos operados a Proveedores </p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<0)
		alert("Compañía erronea");
	else if (document.form.fecha=="")
		alert("Especifique una fecha");
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

<form name="form" method="get" action="./fac_prov_list.php">
  <table class="">
    <tr class="tabla">
      <th class="vtabla">
        <label>
        <input name="consulta" type="radio" value="fecha" checked onchange="document.form.bandera.value=0;">
      Fecha</label>
        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha();" value="{fecha}" size="10">
      </th>
      <th class="vtabla">
        <input name="consulta" type="radio" value="mes" onchange="document.form.bandera.value=1;">
      Mes
		<select name="mes" size="1" class="insert">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		Año
		<input name="anio" type="text" class="insert" value="{anio_actual}" size="5">
      </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5">
        <input name="bandera" type="hidden" class="insert" id="bandera" size="5">
      </td>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2"><input name="user" type="checkbox" id="user" value="1" checked onChange="if(form.tipo_user.value==1) form.tipo_user.value=0; else form.tipo_user.value=1;">
        Consultar por usuario 
          <input name="tipo_user" type="hidden" id="tipo_user" value="1"></td>
    </tr>
	
  </table>
  <br>
<p><img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">Movimientos operados a Proveedores {fecha} del {anio} </p>

<table border="1" class="print" width="100%">
  <tr>
    <th class="print" rowspan="2">Fecha</th>
    <th class="print" rowspan="2">Numero documento</th>
    <th class="print" rowspan="2">Concepto</th>
    <th class="print" rowspan="2">Total</th>

  </tr>
  <tr class="print">
  </tr>

<!-- START BLOCK : companias -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
 
	<th class="print_cia" colspan="4">{num_cia}&#8212;{nombre_cia}</th>
  </tr>
<!-- START BLOCK : proveedor -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint">{num_proveedor}</th>
	<th class="vprint" colspan="3">{nombre_proveedor}</th>	
  </tr>
<!-- START BLOCK : rows -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="print">{num_fac}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{valores}</td>
  </tr>
  <!-- END BLOCK : rows -->
<!-- START BLOCK : totalfac -->
  <tr class="print">
  	<td class="print" colspan="3"></td>
	<td class="rprint">{fac}</td>
  </tr>
<!-- END BLOCK : totalfac -->
<!-- END BLOCK : proveedor -->
<!-- END BLOCK : companias -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="print" colspan="3">TOTAL</th>
    <td class="rprint"><strong><font size="+1">{total1}</font></strong></td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
