<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

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
	if(document.form.fecha.value==""){
		alert("Inserte una fecha de consulta");
		document.form.fecha.select();
	}
	else
		document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">MODIFICACIÓN DE MEDIDORES DE AGUA</p>
<form name="form" action="./pan_agu_mod.php" method="get">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Fecha</th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><input name="fecha" type="text" class="insert" onChange="actualiza_fecha(this)" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();" value="{fecha}" size="10"></td>
  </tr>
</table>
<p>
  <input type="button" name="enviar" value="Enviar" class="boton" onClick="valida();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha.select();
</script>

</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : cias -->
<script language="JavaScript" type="text/JavaScript">
function revisa()
{
if(parseFloat(document.form.temp.value) > 0) document.form.enviar2.disabled=false;
else 
document.form.enviar2.disabled=true; 
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">MODIFICACI&Oacute;N DE MEDIDORES DE AGUA <br>{dia} DE {mes} DEL {anio}</P>
<form name="form" method="post" action="./pan_agu_mod.php">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">Compa&ntilde;&iacute;a
        <input name="cont" type="hidden" id="cont" value="{count}">
        <input name="temp" type="hidden" id="temp" value="0">
		<input name="fecha2" type="hidden" id="fecha2" value="{fecha}">
		</th>
      <th class="tabla" align="center">Medidor 1 </th>
      <th class="tabla" align="center">Medidor 2 </th>
      <th class="tabla" align="center">Medidor 3 </th>
      <th class="tabla" align="center">Medidor 4 </th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{num_cia}
	    <input name="idagua{i}" type="hidden" id="idagua{i}" value="{idagua}"> 
	    <input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}">
		<input name="nombre_cia{i}" type="hidden" id="nombre_cia{i}" value="{nombre_cia}">
	  </th>
	  <td class="vtabla">{nombre_cia}</td>
	
      <td class="tabla">{medida1}
        <input name="medidor1{i}" type="hidden" id="medidor1{i}" value="{medidor1}">
      </td>
      <td class="tabla">{medida2}
        <input name="medidor2{i}" type="hidden" id="medidor2{i}" value="{medidor2}">
      </td>
      <td class="tabla">{medida3}
        <input name="medidor3{i}" type="hidden" id="medidor3{i}" value="{medidor3}">
      </td>
      <td class="tabla">{medida4}
        <input name="medidor4{i}" type="hidden" id="medidor4{i}" value="{medidor4}">
      </td>

      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true){ document.form.modificar{i}.value=1; document.form.temp.value=parseFloat(document.form.temp.value) + 1 } else if(this.checked==false){document.form.modificar{i}.value=0;document.form.temp.value=parseFloat(document.form.temp.value) - 1};}" onChange="revisa();"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
</table>
<!-- START BLOCK : aviso -->
<p class="title"><font color="#FF0000">NO HAY REGISTROS PARA ESTA FECHA</font></p>
<!-- END BLOCK : aviso -->

<p>
<input type="button" name="regresar" class="boton" value="Regresar" onclick="document.location='./pan_agu_mod.php'">
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();' disabled>
</p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : cias -->

<!-- START BLOCK : modifica -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="actualiza_pan_agua.php" method="post">
<p class="title">MODIFICACI&Oacute;N DE MEDIDORES DE AGUA <br>{dia} DE {mes} DEL {anio}</P>
<table class="tabla">
  <tr class="rtabla">
    <th class="tabla" align="center" colspan="2">Compa&ntilde;&iacute;a
      <input name="cont2" type="hidden" value="{cont}">
      <input name="temp" type="hidden"></th>
    <th class="tabla" align="center">Medidor 1 </th>
    <th class="tabla" align="center">Medidor 2 </th>
    <th class="tabla" align="center">Medidor 3 </th>
    <th class="tabla" align="center">Medidor 4 </th>

    <th class="tabla" align="center">Eliminar</th>
  </tr>
  <!-- START BLOCK : rows1 -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rtabla">{num_cia}<input name="idagua{i}" type="hidden" id="idagua{i}" value="{idagua}"></td>
	<td class="vtabla">{nombre_cia}</td>
    <td class="tabla">
      <input name="medida1{i}" type="text" class="insert" id="medida1{i}" value="{medida1}" size="10" onKeyDown="if(event.keyCode==13) document.form.medida2{i}.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();">
    </td>
    <td class="tabla">
      <input name="medida2{i}" type="text" class="insert" id="medida2{i}" value="{medida2}" size="10" onKeyDown="if(event.keyCode==13) document.form.medida3{i}.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();">
    </td>
    <td class="tabla">
      <input name="medida3{i}" type="text" class="insert" id="medida3{i}" value="{medida3}" size="10" onKeyDown="if(event.keyCode==13) document.form.medida4{i}.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();">
    </td>
    <td class="tabla">
      <input name="medida4{i}" type="text" class="insert" id="medida4{i}" value="{medida4}" size="10" onKeyDown="if(event.keyCode==13) document.form.medida1{next}.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();">
    </td>

    <td class="tabla"><input type="checkbox" name="mod{i}2" onClick="if (this.checked==true) document.form.eliminar{i}.value=1; else if(this.checked==false)document.form.eliminar{i}.value=0;">
        <input type="hidden" name="eliminar{i}" size="3" value="0"></td>
  </tr>
  <!-- END BLOCK : rows1 -->
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.medida10.select();
</script>


<p>
<input name="regresar" type="button" value="Regresar" class="boton" onClick="document.location='./pan_agu_mod.php?fecha={fecha_link}'">&nbsp;&nbsp;
<input name="enviar" type="button" value="Enviar" class="boton" onClick="document.form.submit();"></p>
</form>

</td>
</tr>
</table>

<!-- END BLOCK : modifica -->