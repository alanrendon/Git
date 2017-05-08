<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
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
	if (document.form.tipo_cia.value == 0 && document.form.num_cia.value == ''){
		alert("Falta la compañía");
		document.form.num_cia.select();
		return;
		}
	else if (document.form.fecha_mov.value==""){
		alert("Necesita insertar una fecha");
		document.form.fecha_mov.select();
		return;
		}
	else document.form.submit();
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Porcentajes de Materia Prima con respecto a la Harina </P>
<form name="form" action="./pan_porc_con.php" method="get">
<input name="temp" type="hidden" value="">
  <table class="tabla">
    <tr>
      <th class="tabla">FECHA <input name="fecha_mov" type="text" class="insert" id="fecha_mov" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) document.form.num_cia.select();" value="{fecha_anterior}" size="10" maxlength="10"></th>
    </tr>
    <tr>
      <td class="vtabla"><p>
        <label><input type="radio" name="consulta" value="0" checked onChange="form.tipo_cia.value=0">Compañía</label> 
		<input class="insert" name="num_cia" type="text" id="num_cia" size="5" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13) document.form.fecha_mov.select();">
        <input name="tipo_cia" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_total" type="hidden" class="insert" value="0"  size="5" maxlength="10">
        <input name="tipo_turno" type="hidden" class="insert" id="tipo_turno" value="0"  >
        <br>
        <label><input type="radio" name="consulta" value="1" onChange="form.tipo_cia.value=1">Todas</label></p>
	  </td>
    </tr>
    <tr>
      <td class="vtabla">
	    <p>
	      <label>
	      <input name="turno" type="radio" value="0" checked onChange="form.tipo_turno.value=0;">
  Francesero Dia &#8212; Francesero Noche</label>
	      <br>
	      <label>
	      <input name="turno" type="radio" value="1" onChange="form.tipo_turno.value=1;">
  Bizcochero &#8212; Repostero</label>
	      </p>
	  </td>
    </tr>

  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onClick='valida();'>
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha_mov.select();</script>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : franceseros -->
<!-- START BLOCK : compania -->
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">  
<table width="100%" cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
    <th colspan="19" class="listado">Porcentajes de Materia Prima con respecto a la Harina al {dia} de {mes} del {anio}</th>
  </tr>
  <tr class="listado">
    <th class="listado" colspan="19">{num_cia}&nbsp;{nom_cia}</th>
  </tr>
  <tr>
    <th class="listado" colspan="10">F&nbsp; R&nbsp; A&nbsp; N&nbsp; C&nbsp; E&nbsp; S&nbsp; E&nbsp; R&nbsp; O&nbsp;&nbsp;&nbsp; D&nbsp; E&nbsp;&nbsp;&nbsp; N&nbsp; O&nbsp; C&nbsp; H&nbsp; E </th>
    <th class="listado" colspan="9">F&nbsp; R&nbsp; A&nbsp; N&nbsp; C&nbsp; E&nbsp; S&nbsp; E&nbsp; R&nbsp; O&nbsp;&nbsp;&nbsp; D&nbsp; E&nbsp;&nbsp; D&nbsp; I&nbsp; A</th>
  </tr>
  <tr class="listado">
    <th class="listado">Dia</th>
    <th class="listado">Bultos de Harina </th>
    <th class="rlistado">Azucar</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Ultrapan</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Levadura</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Grasa</th>
    <th class="rlistado">Aceite</th>
    <th class="listado">Bultos de Harina </th>
    <th class="rlistado">Az&uacute;car</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Ultrapan</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Levadura</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Grasa</th>
    <th class="rlistado">Aceite</th>
  </tr>
<!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="listado">{dia}</td>
    <td class="rlistado">{harina1}</td>
    <td class="rlistado">{azucar1}</td>
    <td class="rlistado">{porc_azucar1}</td>
    <td class="rlistado">{ultrapan1}</td>
    <td class="rlistado">{porc_ultrapan1}</td>
    <td class="rlistado">{levadura1}</td>
    <td class="rlistado">{porc_levadura1}</td>
    <td class="rlistado">{grasa1}</td>
    <td class="rlistado">{aceite1}</td>
    <td class="rlistado">{harina2}</td>
    <td class="rlistado">{azucar2}</td>
    <td class="rlistado">{porc_azucar2}</td>
    <td class="rlistado">{ultrapan2}</td>
    <td class="rlistado">{porc_ultrapan2}</td>
    <td class="listado">{levadura2}</td>
    <td class="rlistado">{porc_levadura2}</td>
    <td class="rlistado">{grasa2}</td>
    <td class="rlistado">{aceite2}</td>
  </tr>
<!-- END BLOCK : rows -->
  <tr>
    <th class="listado">&nbsp;</th>
    <th class="listado">{total_harina1}</th>
    <th class="rlistado">{total_azucar1}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_ultrapan1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_levadura1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_grasa1}</th>
    <th class="rlistado">{total_aceite1}</th>
    <th class="listado">{total_harina2}</th>
    <th class="rlistado">{total_azucar2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_ultrapan2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_levadura2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_grasa2}</th>
    <th class="rlistado">{total_aceite2}</th>
  </tr>
  <tr>
    <th class="listado">&nbsp;</th>
    <th class="listado" colspan="2">Por bulto:</th>
    <th class="rlistado">{total_porc_azucar1}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_porc_ultrapan1}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_porc_levadura1}</th>
    <th class="rlistado">{porc_grasa1}</th>
    <th class="rlistado">{porc_aceite1}</th>
    <th class="listado">&nbsp;</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_porc_azucar2}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_porc_ultrapan2}</th>
    <th class="listado">&nbsp;</th>
    <th class="rlistado">{total_porc_levadura2}</th>
    <th class="rlistado">{porc_grasa2}</th>
    <th class="rlistado">{porc_aceite2}</th>
  </tr>
</table>

</td>
</tr>
</table>
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->

<!-- END BLOCK : compania -->
<!-- END BLOCK : franceseros -->




<!-- START BLOCK : bizcochero -->
<!-- START BLOCK : compania2 -->
<table width="100%"  height="47%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">  
<table width="100%" cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
    <th colspan="15" class="listado">Porcentajes de Materia Prima con respecto a la Harina al {dia} de {mes} del {anio}</th>
  </tr>
  <tr class="listado">
    <th class="listado" colspan="15">{num_cia}&nbsp;{nombre_cia}</th>
  </tr>
  <tr>
    <th class="listado" colspan="8">B&nbsp; I&nbsp; Z&nbsp; C&nbsp;O &nbsp;C &nbsp;H &nbsp;E&nbsp; R&nbsp; O</th>
    <th class="listado" colspan="7">R&nbsp; E&nbsp; P&nbsp; O&nbsp; S&nbsp; T&nbsp; E&nbsp; R&nbsp; O</th>
  </tr>
  <tr class="listado">
    <th class="listado">Dia</th>
    <th class="listado">B. Harina </th>
    <th class="rlistado">Azucar</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Grasas</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Huevos</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="listado">B. Harina</th>
    <th class="rlistado">Az&uacute;car</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Grasas</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    <th class="rlistado">Huevos</th>
    <th class="rlistado">%&nbsp;&nbsp;&nbsp;&nbsp;</th>
    </tr>
<!-- START BLOCK : rows1 -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rlistado">{dia}</td>
    <td class="listado">{harina1}</td>
    <td class="rlistado">{azucar1}</td>
    <td class="rlistado">{porc_azucar1}</td>
    <td class="rlistado">{grasas1}</td>
    <td class="rlistado">{porc_grasas1}</td>
    <td class="rlistado">{huevo1}</td>
    <td class="rlistado">{porc_huevo1}</td>
    <td class="listado">{harina2}</td>
    <td class="rlistado">{azucar2}</td>
    <td class="rlistado">{porc_azucar2}</td>
    <td class="rlistado">{grasas2}</td>
    <td class="rlistado">{porc_grasas2}</td>
    <td class="rlistado">{huevo2}</td>
    <td class="rlistado">{porc_huevo2}</td>
    </tr>
<!-- END BLOCK : rows1 -->	
  <tr>
    <th class="rlistado">&nbsp;</th>
    <th class=" listado">{total_harina1}</th>
    <th class="rlistado">{total_azucar1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_grasas1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_huevo1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="listado">{total_harina2}</th>
    <th class="rlistado">{total_azucar2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_grasas2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_huevo2}</th>
    <th class="rlistado">&nbsp;</th>
    </tr>
  <tr>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_azucar1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_grasas1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_huevo1}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_azucar2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_grasas2}</th>
    <th class="rlistado">&nbsp;</th>
    <th class="rlistado">{total_porc_huevo2}</th>
    </tr>
</table>
</td>
</tr>
</table>
<!-- START BLOCK : salto1 -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto1 -->
<!-- END BLOCK : compania2 -->
<!-- END BLOCK : bizcochero -->