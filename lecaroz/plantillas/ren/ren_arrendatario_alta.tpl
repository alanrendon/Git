<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

<script language="JavaScript" type="text/JavaScript">
function actualiza_local(local, nombre,direccion,arrendador,predial,bloque,contrato) {
	// Arreglo con los nombres de las materias primas
	nom_local = new Array();
	dir_local = new Array();
	nom_arren = new Array();
	predial_local = new Array();
	bloque_local = new Array();
	tipo_contrato = new Array();
	 
	<!-- START BLOCK : nombre_local -->
	nom_local[{num_local}] = '{nombre_local}';
	dir_local[{num_local}] = '{direccion}';
	nom_arren[{num_local}] = '{nombre_arrendador}';
	predial_local[{num_local}] = '{predial}';
	bloque_local[{num_local}] = '{bloque}';
	tipo_contrato[{num_local}] = '{contrato}'
	<!-- END BLOCK : nombre_local -->
			
	if (local.value > 0) {
		if (nom_local[local.value] == null) {
			alert("Local "+local.value+" no esta en el catálogo de locales o esta ocupado");
			local.value = "";
			nombre.value  = "";
			direccion.value  = "";
			arrendador.value  = "";
			predial.value  = "";
			bloque.value = "";
			contrato.value = "";
			local.select();
		}
		else {
			nombre.value = nom_local[local.value];
			direccion.value = dir_local[local.value];
			arrendador.value = nom_arren[local.value];
			predial.value = predial_local[local.value];
			bloque.value = bloque_local[local.value];
			contrato.value = tipo_contrato[local.value];
		}
	}
	else if (local.value == "") {
		nombre.value = "";
		direccion.value  = "";
		arrendador.value = "";
		predial.value = "";
		bloque.value = "";
		contrato.value = "";
	}
}

function actualiza_fecha_v2(campo_fecha) {
	var fecha = campo_fecha.value;
	var anio_actual = {anio_actual};

	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8){
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2));
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10);
	
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
				return;
			}
			else if (dia == diasxmes[mes] && mes == 12) {
				campo_fecha.value = dia+"/"+mes+"/"+anio;
				return;
			}
			else {
				campo_fecha.value = dia+"/"+mes+"/"+anio;
				return;
			}
		}
		else {
			campo_fecha.value = "";
			alert("Rango de fecha no valido");
			campo_fecha.select();
			return;
		}
		return;
	}
	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1));
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10) + 2000;
		
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
				return;
			}
			else if (dia == diasxmes[mes] && mes == 12) {
				campo_fecha.value = dia+"/"+mes+"/"+anio;
				return;
			}
			else {
				campo_fecha.value = dia+"/"+mes+"/"+anio;
				return;
			}
		}
		else {
			campo_fecha.value = "";
			alert("Rango de fecha no valido");
			campo_fecha.select();
			return;
		}
		return;
	}
	else {
		campo_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		campo_fecha.select();
		return;
	}
}

//--------------------------------------------------------------------------------------------------------------------------------------
function actualiza_fecha1(campo_fecha,fecha_factura) {
	var fecha = campo_fecha.value;
	var fecha2 = fecha_factura.value;
	var anio_actual = {anio_actual};
	var partir = fecha2.split('/');
	
	if(fecha_factura.value==""){
		alert("Ingrese la fecha de inicio del contrato");
		fecha_factura.value="";
		campo_fecha.select();
		return;
	}
	
	dia2=partir[0];
	mes2=partir[1];
	anio2=partir[2];

	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8){
		//Descomponer la fecha que viene 
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0){
			dia = parseInt(fecha.charAt(1),10);
		}
		else{
			dia = parseInt(fecha.substring(0,2));
		}
		if (parseInt(fecha.charAt(2),10) == 0){
			mes = parseInt(fecha.charAt(3),10);
		}
		else{
			mes = parseInt(fecha.substring(2,4),10);
		}
		anio = parseInt(fecha.substring(4),10);
//		alert (anio);
	
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
		
		if(anio == anio2){
//		anio=anio+2000;
			if(mes == mes2){
				if(dia >= dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<dia2){
					alert("La fecha de final del contrato es menor a la fecha inicial");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2){
					alert("La fecha de final del contrato es menor a la fecha inicial");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
//		anio=anio+2000;
			if(dia <= diasxmes[mes] && mes <= 12)
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			else{
				alert("La fecha de entrega es erronea");
				campo_fecha.value="";
				campo_fecha.select();
				return;
			}
		}
		else if(anio < anio2){
			alert("La fecha de final del contrato es menor a la fecha inicial");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}

	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0){
			dia = parseInt(fecha.charAt(1),10);
		}
		else{
			dia = parseInt(fecha.substring(0,2),10);
		}
		if (parseInt(fecha.charAt(2),10) == 0){
			mes = parseInt(fecha.charAt(3),10);
		}
		else{
			mes = parseInt(fecha.substring(2,4),10);
		}
		
		if(parseInt(fecha.charAt(4),10) == 0){
			anio = parseInt(fecha.charAt(5),10)
		}
		else{
			anio = parseInt(fecha.substring(4,6),10);
		}
		anio=anio+2000;
	
//		anio=parseInt(fecha.substring(4)) + 2000;
//		alert(anio);
		
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
			
		if(anio == anio2){
//			anio=anio+2000;
			if(mes == mes2){
				if(dia >= dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<dia2){
					alert("La fecha de final del contrato es menor a la fecha inicial");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2){
					alert("La fecha de final del contrato es menor a la fecha inicial");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
//			anio=anio+2000;
			if(dia <= diasxmes[mes] && mes <= 12)
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			else{
				alert("La fecha final es erronea");
				campo_fecha.value="";
				campo_fecha.select();
				return;
			}
		}
		else if(anio < anio2){
			alert("La fecha de final del contrato es menor a la fecha inicial");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}
	else {
		campo_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		alert("entre al final");
		campo_fecha.value="";
		campo_fecha.select();
		return;
	}
}

function valida_registro(){
	if(document.form.nombre_arrendatario.value==""){
		alert("Revise el Nombre del Arrendatario");
		document.form.nombre_arrendatario.select();
	}
	else if(document.form.num_local.value==""){
		alert("Revise el Número de Local");
		document.form.num_local.select();
	}
	else if(document.form.giro.value==""){
		alert("Revise el Giro del arrendatario");
		document.form.giro.select();
	}
	else if(document.form.fecha_inicial.value=="" || document.form.fecha_final.value==""){
		alert("Revise las fechas del contrato");
		document.form.fecha_inicial.select();
	}
	else{
		document.form.submit();
//		window.open('./contrato_renta.php?local='+document.form.num_local.value+'&nombre='+document.form.nombre_arrendatario.value+'&representa='+document.form.representante.value+'&aval='+document.form.nombre_aval.value+'&bien_aval='+document.form.bien_avaluo.value+'&giro='+document.form.giro.value+'&fecha_inicial='+document.form.fecha_inicial.value+'&fecha_final='+document.form.fecha_final.value+'&renta='+document.form.con_recibo.value+'&danos='+document.form.daños.value+'&termino='+document.form.termino.value+'&mantenimiento='+document.form.mantenimiento.value+'&agua='+document.form.agua.value,'Contrato','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,top=100,left=400');

		window.open('','contrato','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=600,height=400,top=100,left=400');
		document.form.action=document.form.contrato.value;
		document.form.target='contrato';
		document.form.submit();
	}
}
function disponibilidad(indice){
	arreglo = new Array();
	var contador;
	<!-- START BLOCK : ocupados -->
	arreglo[{i}]='{cod_arrendatario}';
	<!-- END BLOCK : ocupados -->
	for(i=0;i<arreglo.length;i++){
		if(parseInt(indice.value)==arreglo[i]){
			alert("El código ya esta ocupado");
			indice.value=document.form.temp1.value;
			document.form.num_local.select();
			return;
		}
	}
	return;
}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Arrendatarios</p>
  <form name="form" action="./ren_arrendatario_alta.php" method="post">
    <input name="temp" type="hidden"> 
	   <input name="temp1" type="hidden"> 
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="row">C&oacute;digo arrendatario </th>
        <td colspan="3" class="vtabla"><input name="cod_arrendatario" type="text" class="insert" id="cod_arrendatario" onKeyPress="if(event.keyCode==13) document.form.num_local.select();" value="{id}" size="5" maxlength="5" onFocus="temp1.value=this.value;"onChange="disponibilidad(this);"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Local</th>
        <td colspan="3" class="vtabla"><input name="num_local" type="text" class="insert" id="num_local" size="4" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_local(this,form.nombre_local,form.direccion,form.arrendador,form.cta_predial,form.bloque,form.contrato);" onKeyPress="if(event.keyCode==13) form.descripcion_local.select();">
          <input name="nombre_local" type="text" class="vnombre" id="nombre_local" size="50" readonly></td>
      </tr>
	  <tr>
		<th class="vtabla" scope="row">Descripción local</th>
		<td class="vtabla" colspan="3"><input type="text" class="vinsert" id="descripcion_local" name="descripcion_local" size="50" onKeyPress="if(event.keyCode==13) form.nombre_arrendatario.select();"></td>
	  </tr>
      <tr>
        <th class="vtabla" scope="row">Tipo de Bloque </th>
        <td colspan="3" class="vtabla"><input name="bloque" type="text" class="vnombre" size="10" readonly="true"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Direcci&oacute;n</th>
        <td colspan="3" class="vtabla"><textarea name="direccion" cols="50" rows="2" readonly class="vnombre" id="direccion"></textarea></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Arrendador</th>
        <td colspan="3" class="vtabla"><input name="arrendador" type="text" class="vnombre" id="arrendador" size="50" readonly>
          <input name="contrato" type="hidden" id="contrato" size="15"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cuenta predial </th>
        <td colspan="3" class="vtabla"><input name="cta_predial" type="text" class="vnombre" id="cta_predial" size="30" readonly></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Nombre del arrendatario </th>
        <td colspan="3" class="vtabla"><input name="nombre_arrendatario" type="text" class="vinsert" id="nombre_arrendatario" size="50" onKeyPress="if(event.keyCode==13) form.dir_fiscal.select();"></td>
      </tr>
	  <tr>
		<th class="vtabla">Dirección fiscal</th>
		<td colspan="3" class="vtabla"><textarea name="dir_fiscal" cols="50" rows="2" class="insert" onKeyPress="if(event.keyCode==13) form.representante.select();"></textarea></td>
	  </tr>
      <tr>
        <th class="vtabla" scope="row">Representante</th>
        <td colspan="3" class="vtabla"><input name="representante" type="text" class="vinsert" id="representante" size="50" onKeyPress="if(event.keyCode==13) form.nombre_aval.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Nombre del aval </th>
        <td colspan="3" class="vtabla"><input name="nombre_aval" type="text" class="vinsert" id="nombre_aval" size="50" onKeyPress="if(event.keyCode==13) form.bien_avaluo.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Direcci&oacute;n del aval </th>
        <td colspan="3" class="vtabla"><textarea name="bien_avaluo" cols="50" class="insert" id="bien_avaluo" onKeyPress="if(event.keyCode==13) form.rfc.select();"></textarea></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">R.F.C.</th>
        <td colspan="3" class="vtabla"><input name="rfc" type="text" class="vinsert" id="rfc" size="30" onKeyPress="if(event.keyCode==13) form.giro.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Giro</th>
        <td colspan="3" class="vtabla"><input name="giro" type="text" class="vinsert" id="giro" size="50" onKeyPress="if(event.keyCode==13) form.fecha_inicial.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Fecha inicio de contrato </th>
        <td class="tabla"><input name="fecha_inicial" type="text" class="vinsert" id="fecha_inicial" size="10" onKeyDown="if(event.keyCode==13) form.fecha_final.select();" onChange="actualiza_fecha_v2(this);"></td>
        <th class="vtabla">Fecha final de contrato </th>
        <td class="tabla"><input name="fecha_final" type="text" class="vinsert" id="fecha_final" size="10" onKeyDown="if(event.keyCode==13) form.con_recibo.select();" onChange="actualiza_fecha1(this, form.fecha_inicial);"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Renta con recibo </th>
        <td class="tabla"><input name="con_recibo" type="text" class="rinsert" id="con_recibo" size="15" onKeyPress="if(event.keyCode==13) form.sin_recibo.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
        <th class="vtabla">Renta sin recibo </th>
        <td class="tabla"><input name="sin_recibo" type="text" class="rinsert" id="sin_recibo2" size="15" onKeyPress="if(event.keyCode==13) form.agua.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cuota de agua </th>
        <td class="tabla"><input name="agua" type="text" class="rinsert" id="agua" size="15" onKeyPress="if(event.keyCode==13) form.mantenimiento.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
        <th class="vtabla">Cuota de mantenimiento </th>
        <td class="tabla"><input name="mantenimiento" type="text" class="rinsert" id="mantenimiento2" size="15" onKeyPress="if(event.keyCode==13) form.rentas_deposito.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Rentas en dep&oacute;sito </th>
        <td class="tabla"><input name="rentas_deposito" type="text" class="rinsert" id="rentas_deposito" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyPress="if(event.keyCode==13) form.daños.select();" value="0" size="15"></td>
        <th class="vtabla">Cargo por da&ntilde;os </th>
        <td class="tabla"><input name="daños" type="text" class="rinsert" id="daños2" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyPress="if(event.keyCode==13) form.termino.select();" value="{danos}" size="15"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cargo contrato terminado </th>
        <td colspan="3" class="vtabla"><input name="termino" type="text" class="rinsert" id="termino" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyPress="if(event.keyCode==13) form.num_local.select();" value="{termino}" size="15"></td>
      </tr>
	  
      <tr>
        <th class="vtabla" scope="row">Incremento anual </th>
        <td class="vtabla"><p>
          <label>
          <input name="incremento" type="radio" value="false" checked>
  No</label>
          <label>
          <input type="radio" name="incremento" value="true">
  Si</label>
          <br>
        </p></td>
        <th class="vtabla">Retenci&oacute;n I.S.R. </th>
        <td class="vtabla"><label>
          <input name="retencion_isr" type="radio" value="false" checked>
No</label>
          <label>
          <input type="radio" name="retencion_isr" value="true">
Si</label></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Retenci&oacute;n I.V.A. </th>
        <td class="vtabla"><p>
          <label>
          <input type="radio" name="iva" value="false">
  No</label>
          <label>
          <input name="iva" type="radio" value="true" checked>
  Si</label>
          <br>
        </p></td>
        <th class="vtabla">Fianza</th>
        <td class="vtabla"><label>
          <input type="radio" name="fianza" value="false">
No</label>
          <label>
          <input name="fianza" type="radio" value="true" checked>
Si</label></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Tipo persona </th>
        <td class="vtabla"><p>
          <label>
          <input type="radio" name="tipo_persona" value="false">
  Física</label>
          <label>
          <input name="tipo_persona" type="radio" value="true" checked>
  Moral</label>
          <br>
        </p></td>
        <th class="vtabla">Imprime recibos mensualmente</th>
        <td class="vtabla"><label>
          <input type="radio" name="recibo_mensual" value="false">
No</label>
          <label>
          <input name="recibo_mensual" type="radio" value="true" checked>
Si</label></td>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Alta" onClick="valida_registro();">
  </p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_local.select();
</script>
  
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->