<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de Movimiento de Gastos por dia</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<=0)
		alert("Compañía erronea");
	else if (document.form.fecha.value=="")
		alert("Especifique una fecha de consulta");
	else
	document.form.submit();
}

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



</script>

<form name="form" method="get" action="./pan_mga_con.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="vtabla">
        <label>
      Fecha</label>
        <input name="fecha" type="text" class="insert" onChange="actualiza_fecha(this);" size="10" onKeyDown="if (event.keyCode == 13) document.form.cia.select();">
      </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2">
        <label> Compa&ntilde;&iacute;a&nbsp;</label>
        <input name="cia" type="text" class="insert" size="5" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();" {disabled}>
      </td>
    </tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : gastos -->
<script language="JavaScript" type="text/JavaScript">

	function modificar(id,codmp) {
		if  (codmp==114){
			alert("No puedes modificar la devolucion de base");
			return;
		}
		else
			var mod = window.open("./ros_gas_minimod.php?id="+id,null,"width=300,height=215,location=no,menubar=no,resizable=no,scrollbars=no,status=0,titlebar=0,toolbar=0,top=100,left=400");
	}
	function borrar(id,codgastos,fecha,importe,num_cia){
		if  (codgastos==114){
			alert("No puedes borrar la devolucion de base");
			return;
		}
		else
			var mod1 = window.open("./pan_gas_minidel.php?id="+id+"&codgastos="+codgastos+"&fecha="+fecha+"&importe="+importe+"&num_cia="+num_cia,null,"width=350,height=215,location=no,menubar=no,resizable=no,scrollbars=no,status=no,titlebar=no,toolbar=no,top=100,left=400");
	}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">GASTOS DE LA COMPAÑÍA  <br>
  {num_cia}&#8212;{nom_cia}<br>
   del {fecha}</p>
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th class="tabla" colspan="2">Gasto</th>
		<th class="tabla" >Concepto</th>
		<th class="tabla" >Importe</th>

	    <th class="tabla" >Modificar</th>
	    <th class="tabla" >Borrar</th>
	  </tr>
	
	<!-- START BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<th class="tabla">{codgasto}</th>
		<td class="vtabla">{nom_gasto}</td>	
		<td class="vtabla">{concepto}</td>
		<td class="rtabla">{importe}</td>
	    <td class="tabla"><input type="button" name="mod{i}" value="M" class="boton" onClick="modificar({id},{codgasto});"></td>
	    <td class="tabla"><input name="borrar{i}" type="button" class="boton" id="borrar{i}" onClick="borrar({id},{codgasto},'{fecha}',{importe1},{num_cia});" value="B" {disabled}></td>
	  </tr>
	  <!-- END BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <th class="tabla" colspan="3">TOTAL</th>
	    <th class="rtabla"><font size="+1"><strong>{total}</strong></font></th>
	    <th class="rtabla">&nbsp;</th>
	    <th class="rtabla">&nbsp;</th>
	  </tr>

	</table>
<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : gastos -->

