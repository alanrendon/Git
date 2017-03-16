<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
/*		window.opener.document.form.method = "post";
		window.opener.document.form.target = "_self";
		window.opener.document.form.action = "./hojadiaria.php?tabla=produccion";
*/		self.close();
	}
	
//	window.onload = cerrar();
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">FACTURA MODIFICADA</p>
	<table class="tabla">
	  <tr class="tabla">
		<th scope="row" class="vtabla">Compañía</th>
		<td class="tabla">{num_cia}&nbsp;&nbsp;{nombre_cia}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Nota</th>
		<td class="tabla">{let_folio}&nbsp;{num_remi}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Dia Afectación</th>
		<td class="tabla">{fecha}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Venta en puerta</th>
		<td class="rtabla">{venta_puerta}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Abono expendios</th>
		<td class="rtabla">{abono_exp}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Gastos</th>
		<td class="rtabla">{gastos}</td>
	  </tr>
	  <tr class="tabla">
	    <td scope="row" class="tabla" colspan="2">
		<!-- START BLOCK : cambio_fecha1 -->
		<font color="#3366FF"><strong>CAMBIO DE FECHA DE ENTREGA</strong></font>
		<!-- END BLOCK : cambio_fecha1 -->
		
		<!-- START BLOCK : perdida_control -->
		<font color="#3366FF"><strong>PERDIDA DE CONTROL</strong></font>
		<!-- END BLOCK : perdida_control -->
		</td>
	    </tr>
	</table>
    <p>
      <input type="button" name="cierra" value="Cerrar" onClick="cerrar();" class="boton">
    </p></td>
</tr>
</table>


<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if(document.form.fecha.value=="" && document.form.tipo_mov.value==0){
			alert("DEBE INGRESAR UNA FECHA DE AFECTACION");
			document.form.fecha.select();
		}
		else if(document.form.tipo_mov.value==2 && document.form.fecha_entrega.value==""){
			alert("DEBE METER UNA NUEVA FECHA DE ENTREGA");
			document.form.fecha_entrega.select();
		}
		else if(document.form.tipo_mov.value==3 && document.form.fecha_nueva.value==""){
			alert("DEBE METER UNA NUEVA FECHA PARA LA NOTA");
			document.form.fecha_nueva.select();
		}
		
		else if(document.form.tipo_mov.value==1)
			document.form.submit();
		else
			document.form.submit();
	}
	
function modificar()
{
var kilos=0;
var precio=0;
var otros=0;
var base=0;
var total_factura=0;
var resta_pagar=0;
var total=0;
var kilos_mas=0;
var kilos_menos=0;
var resta=0;

kilos=parseFloat(document.form.kilos.value);
precio=parseFloat(document.form.precio_unidad.value);
otros=parseFloat(document.form.otros.value);
base=parseFloat(document.form.base.value);
total_factura= parseFloat(document.form.total_factura.value);
resta_pagar=parseFloat(document.form.resta_pagar.value);
kilos_mas=parseFloat(document.form.kilos_mas.value);
kilos_menos=parseFloat(document.form.kilos_menos.value);
cuenta=parseFloat(document.form.cuenta.value);

total=(kilos + kilos_mas - kilos_menos) * precio + base + otros;
resta= total - cuenta;
//alert(total);
//alert(resta);
document.form.total_factura.value=total;
document.form.resta_pagar.value=resta;


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
}

function actualiza_fecha1(campo_fecha,fecha_factura) {
	var fecha = campo_fecha.value;
	var anio_actual = {anio_actual};
	var fecha2 = fecha_factura.value;
		
	if (parseInt(fecha2.charAt(0)) == 0){
		dia2 = parseInt(fecha2.charAt(1));
	}
	else{
		dia2= parseInt(fecha2.substring(0,2));
	}
	if (parseInt(fecha2.charAt(3)) == 0){
		mes2 = parseInt(fecha2.charAt(4));
	}
	else{
		mes2= parseInt(fecha2.substring(3,5));
	}
	anio2 = parseInt(fecha2.substring(6));
	

	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8) 
	{
		//Descomponer la fecha que viene 
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0)) == 0){
			dia = parseInt(fecha.charAt(1));
		}
		else{
			dia = parseInt(fecha.substring(0,2));
		}
		if (parseInt(fecha.charAt(2)) == 0){
			mes = parseInt(fecha.charAt(3));
		}
		else{
			mes = parseInt(fecha.substring(2,4));
		}
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
		
		if(anio == anio2){
			if(mes == mes2){
				if(dia > dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<=dia2){
					alert("La fecha de entrega es menor o igual a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2 && anio <= anio2){
					alert("La fecha de entrega es menor o igual a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
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
			alert("La fecha de entrega es menor a la fecha que se capturó la factura");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}

	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0)) == 0){
			dia = parseInt(fecha.charAt(1));
		}
		else{
			dia = parseInt(fecha.substring(0,2));
		}
		if (parseInt(fecha.charAt(2)) == 0){
			mes = parseInt(fecha.charAt(3));
		}
		else{
			mes = parseInt(fecha.substring(2,4));
		}
		
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
			
		if(anio == anio2){
			if(mes == mes2){
				if(dia > dia2 && dia <= diasxmes[mes]){
					campo_fecha.value = dia+"/"+mes+"/"+anio;
				}
				if(dia<=dia2){
					alert("La fecha de entrega es menor o igual a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
				}
			}
			else if(mes < mes2){
					alert("La fecha de entrega es menor o igual a la que se capturó la nota de pastel");
					campo_fecha.value="";
					campo_fecha.select();
					return;
			}
			else if(mes > mes2 && mes <= 12){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
		}
		else if(anio > anio2){
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
			alert("La fecha de entrega es menor a la fecha que se capturó la factura");
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}
	else {
		campo_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		campo_fecha.value="";
		campo_fecha.select();
		return;
	}
}


</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Modificar factura</p>
<!-- START BLOCK : cancelar -->
<p class="title"><font size="3" color="#CC3300"><strong>CANCELACI&Oacute;N</strong></font><br> <font size="2" color="#CC3300">Introduce la fecha de afectación<br>y presiona modificar</font></p>
<!-- END BLOCK : cancelar -->

<!-- START BLOCK : perdida -->
<p class="title"><font size="3" color="#339966"><strong>PÉRDIDA DE NOTA</strong></font><br> <font size="2" color="#339966">Solo presiona Modificar</font></p>
<!-- END BLOCK : perdida -->

<!-- START BLOCK : cambio_fecha -->
<p class="title"><font size="3" color="#0000FF"><strong>CAMBIO DE FECHA DE ENTREGA</strong></font><br> 
<font size="2" color="#0000FF">Escribe UNICAMENTE la nueva fecha <br>
de entrega
 y presiona Modificar</font></p>
<!-- END BLOCK : cambio_fecha -->

<!-- START BLOCK : fecha_nueva -->
<p class="title">
<font size="3" color="#996666"><strong>CAMBIO DE FECHA DE FACTURA<br></strong></font>
<font size="2" color="#996666">Escribe UNICAMENTE la nueva fecha de<br>entrada de la nota y presione Modificar</font>
</p>
<!-- END BLOCK : fecha_nueva -->

<!-- START BLOCK : factura_pagada -->
<p class="title">
<font size="3" color="#0000FF"><strong>La factura ya se encuentra pagada</strong></font>
</p>
<!-- END BLOCK : factura_pagada -->

<form name="form" method="post" action="./pan_pastel_minimod2.php">
<input name="temp" type="hidden">
<input name="idpastel" type="hidden" id="idpastel" value="{idpastel}">
<input name="idmodifica" type="hidden" id="idmodifica" value="{idmodifica}">
<table class="tabla">
   <tr>
     <th class="vtabla">N&uacute;mero de Factura</th>
     <th class="tabla">{let_folio} &nbsp; {num_remi} </th>
   </tr>
       <tr>
      <th class="vtabla">Fecha a la que<br> afecta el <br>movimiento</th>
      <td class="tabla"><input name="fecha" type="text" size="10" class="insert" onChange="actualiza_fecha(this);" onKeyDown="if(event.keyCode==13) document.form.kilos_mas.select();" {readonly}></td>
    </tr>

    <tr>
      <th class="vtabla">Dinero a cuenta</th>
      <td class="rtabla"> {cuenta}       <input name="cuenta" type="hidden" class="insert" id="cuenta" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" value="{cuenta}" size="8" {leerkilosmas}></td>
    </tr>


    <tr>
      <th class="vtabla">Kilos Capturados</th>
      <td class="rtabla">{kilos_capturados}<span class="vtabla">
        <input name="kilos" type="hidden" class="insert" id="kilos" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" value="{kilos_capturados}" size="8" {leerkilosmas}>

</span></td>
    </tr>


   <tr>
      <th class="vtabla">Kilos de mas </th>
      <td class="tabla" {color}>
	    <input name="kilos_mas" type="text" class="rinsert" id="kilos_mas" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp); modificar();" value="0" size="7" maxlength="5" {leerkilosmas}>
	    <span class="rtabla"><span class="vtabla">
	    </span></span></td>
    </tr>
    <tr>
      <th class="vtabla">Kilos de menos </th>
      <td class="tabla" {color1}>
	  <input name="kilos_menos" type="text" class="rinsert" id="precio_raya_unidad" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp); modificar();" value="0" size="7" {leerkilosmenos}>	  </td>
    </tr>
    <tr>
      <th class="vtabla"}>Precio por unidad </th>
      <td class="tabla" {color2}>
	  <input name="precio_unidad" type="text" class="rinsert" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp); modificar();" value="{precio_unidad}" size="7" maxlength="5" {leerprecio}>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Pan</th>
      <td class="tabla" {color3}>
	  <input name="otros" type="text" class="rinsert" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp); modificar();" value="{otros}" size="7" maxlength="5" {leerotros}>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Base</th>
      <td class="tabla" {color4}>
	  <input name="base" type="text" class="rinsert" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp); modificar();" value="{base}" size="7" maxlength="5" {leerbase}>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Total de la factura </th>
      <td class="tabla"><input name="total_factura" type="text" class="rinsert" onFocus="form.temp.value=this.value" value="{total_factura}" size="7" maxlength="5" readonly>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Resta pagar </th>
      <td class="tabla">	  <input name="resta_pagar" type="text" class="rinsert" value="{resta_pagar}" size="7" maxlength="5" readonly></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de entrega </th>
      <td class="tabla" {color5}><input name="fecha_entrega" type="text" class="insert" id="fecha_entrega" onChange="actualiza_fecha1(this,document.form.fecha_fac);" onKeyDown="if(event.keyCode==13) document.form.fecha.select();" size="8" {fechaentrega}>     <input name="fecha_fac" type="hidden" class="insert" id="fecha_fac2" value="{fecha_fac}"></td>
    </tr>
    <tr>
      <th class="vtabla">Nueva Fecha </th>
      <td class="tabla" {color6}>
	  <input name="fecha_nueva" type="text" class="insert" id="fecha_nueva" onChange="actualiza_fecha(this,document.form.fecha_fac);" onKeyDown="if(event.keyCode==13) document.form.fecha.select();" size="8" {nuevafecha}>
	  <input type="hidden" name="tipo_mov" size="5" value="{movimiento}">
	  </td>
    </tr>
  </table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
&nbsp;&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Modificar" onClick="valida_registro()">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.{seleccion}.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : modificar -->