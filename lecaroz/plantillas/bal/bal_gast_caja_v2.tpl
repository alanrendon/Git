<script type="text/javascript" language="JavaScript">


	function valida_registro() {
		/*if(document.form.num_gastos.value <= 0) {
			alert('Debe especificar un numero de gastos a insertar');
			document.form.idcia.focus();
		}
		else {*/
//if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
		//}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_gastos.focus();
	}
	

</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<!--START BLOCK : obtener_datos -->
<p class="title"><font size="+3">Gastos de Caja</font></p>
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

<form name="form" action="./bal_gast_caja_v2.php" method="get">
<input name="temp" type="hidden">
<table border="1" class="tabla">
<tr class="tabla">
      <th class="tabla" scope="col">Número de gastos a insertar</th>
      <th class="tabla" scope="col">Fecha</th>
</tr>
<tr class="tabla">
      <td class="tabla" align="center"><input name="num_gastos" type="text" class="insert" id="num_gastos" value="10" size="5" maxlength="3" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) document.form.fecha_i.select();"></td>
      <td class="tabla"><input name="fecha_i" type="text" class="insert" id="fecha_i" value="{fecha}" size="10" maxlength="10" onChange="actualiza_fecha(this);" onKeyDown="if (event.keyCode == 13) document.form.num_gastos.select();"></td>
</tr>
</table>
<p>
<table border="1" class="tabla">
<tr class="tabla">
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Clave</th>
      <th class="tabla" scope="col">Tipo</th>
</tr>
<tr>
      <td>
	  <select name="concepto" size="1" class="insert" id="select">
        <!-- START BLOCK : codigo1 -->
	    <option value="{num_gasto}">{descripcion}</option>
        <!-- END BLOCK : codigo1 -->
      </select></td>
      <td>
      <select name="balance" size="1" class="insert" id="balance">
        <option value="1" selected>Si balance</option>
        <option value="0">No balance</option>
      </select>
	  </td>
      <td><label>
        <input type="radio" name="tipo_mov" value="0" checked>
  Egreso</label>
          
          <label>
          <input name="tipo_mov" type="radio" value="1">
  Ingreso</label>
  </td>
</tr>


</table>



</p>
<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.num_gastos.select();</script>
</td>
</tr>
</table>
<!--END BLOCK : obtener_datos -->


<!--START BLOCK : gastos_caja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">GASTOS DE CAJA <br>{fecha}</p>
<script language="JavaScript" type="text/JavaScript">

//var importe = new Array();

function totales1(importe,operador){
	var numero=0;
	var total=0;
	var opera = operador.value;
	var total_ingreso=parseFloat(document.form.total_ingresos.value);
	var total_egreso=parseFloat(document.form.total_egresos.value);
	var gran_total=parseFloat(document.form.gran_total.value);
	
	if(opera==0){
		if(document.form.temp.value !=""){
			total_egreso -= parseFloat(document.form.temp.value);
		}
		
		if(importe.value=="")
			total_egreso += 0;
		else
			total_egreso += parseFloat(importe.value);
		document.form.total_egresos.value=total_egreso.toFixed(2);
		total=total_ingreso - total_egreso;
		document.form.gran_total.value = total.toFixed(2);
	}
	else if(opera==1)	{
		if(document.form.temp.value !=""){
			total_ingreso -= parseFloat(document.form.temp.value);
		}
		
		if(importe.value=="")
			total_ingreso += 0;
		else
			total_ingreso += parseFloat(importe.value);
			
		document.form.total_ingresos.value=total_ingreso.toFixed(2);
		total=total_ingreso - total_egreso;
		document.form.gran_total.value = total.toFixed(2);
	}
}

function totales2(importe,operador){
	var total_ingreso=parseFloat(document.form.total_ingresos.value);
	var total_egreso=parseFloat(document.form.total_egresos.value);
	var numero=0;
	var total=0;
	
	if(importe.value==""){
		numero=0;
	}
	else{
		numero=parseFloat(importe.value);
	}
	if(operador.value==0){
		total_egreso += numero;
		total_ingreso -= numero;
		document.form.total_egresos.value=total_egreso.toFixed(2);
		document.form.total_ingresos.value=total_ingreso.toFixed(2);
		total=total_ingreso - total_egreso;
		document.form.gran_total.value = total.toFixed(2);
	}
	else if(operador.value==1){
		total_egreso -= numero;
		total_ingreso += numero;
		document.form.total_egresos.value=total_egreso.toFixed(2);
		document.form.total_ingresos.value=total_ingreso.toFixed(2);
		total=total_ingreso - total_egreso;
		document.form.gran_total.value = total.toFixed(2);
	}

}

function totales3(){
	form.total_ingresos.value='';
	form.total_egresos.value='';
	form.gran_total.value='';
	var total=0;
	var ingresos=0;
	var egresos=0;
	var importe=0;
//	alert(form.num_cia.length);
	
	for(i=0;i < form.num_cia.length;i++){
		if(form.importe[i].value !=""){
			importe=parseFloat(form.importe[i].value);
			if(parseInt(form.operador[i].value,10)==0)
				egresos += importe;
			else
				ingresos += importe;
		}
	}
	
	form.total_ingresos.value= ingresos.toFixed(2);
	form.total_egresos.value = egresos.toFixed(2);
	total=ingresos - egresos;
	form.gran_total.value=total.toFixed(2);
	
}


function actualiza_compania(num_cia, nombre, i) {
	// Arreglo con los nombres de las materias primas
	cia = new Array();				// Materias primas
	<!-- START BLOCK : nombre_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nombre_cia -->
			
	if (num_cia.value > 0) {
		if (cia[num_cia.value] == null) {
			alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
			num_cia.value = "";
			nombre.value  = "";
			num_cia.focus();
		}
		else {
			nombre.value = cia[num_cia.value];
			
			var myConn = new XHConn();
	
			if (!myConn)
				alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
			
			// Pedir datos
			myConn.connect("./bal_gast_caja_v2.php", "GET", 'c=' + num_cia.value + '&f=' + document.form.fecha[0].value + '&i=' + i, validaEfe);
		}
	}
	else if (num_cia.value == "") {
		num_cia.value = "";
		nombre.value  = "";
	}
}

var validaEfe = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (result[1] == '0') {
		alert('La compañía en el día especificado no tiene efectivo');
		document.form.num_cia[get_val2(result[0])].value = '';
		document.form.nombre_cia[get_val2(result[0])].value = '';
		return false;
	}
}

</script>


<!-- <form name="form" action="./insert_bal_gast_caja.php?tabla={tabla}" method="post"  > -->
<form name="form" action="./bal_gast_caja_v2.php" method="post">
<input name="temp" type="hidden">
<input name="temporal" type="hidden" id="temporal"> 
<input name="contador" type="hidden" id="contador" value="{contador}"> 
<input name="temporal2" type="hidden" id="temporal2">
<table border="1" class="tabla">
    <tr class="vtabla">
      <th class="tabla" scope="col">Compañia</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
	  <th class="tabla" scope="col">Comentario</th>
      <th class="tabla" scope="col">Clave</th>
      <th class="tabla" scope="col">Tipo</th>
    </tr>
	<!-- START BLOCK : rows -->
<input name="contador" type="hidden" class="insert" id="contador" size="13" maxlength="5" value="{contador}">
    <tr class="tabla" align="center">
      <td>
	  	<p>
	  	  <input name="num_cia[]" type="text" class="insert" id="num_cia" size="3" maxlength="3" onKeyDown="if(event.keyCode == 13){document.form.importe[{i}].select();}" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_compania(this,form.nombre_cia[{i}], {i})" onFocus="form.temporal2.value=this.value">
          <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" size="28" readonly>
		</p>
  	  </td>
      <td>
        <input name="importe[]" type="text" class="insert" id="importe" size="8" maxlength="15" onChange="valor=isFloat(this,2,form.temporal); if (valor==false) this.select(); totales3();" onKeyDown="if(event.keyCode == 13){document.form.num_cia[{next}].select();}" onFocus="form.temp.value=this.value; form.temporal.value=this.value;">
		</td>

      <td>
	  <select name="concepto[]" size="1" class="insert" id="concepto">
        <!-- START BLOCK : codigo -->
	    <option value="{num_gasto}" {checked}>{descripcion}</option>
        <!-- END BLOCK : codigo -->
      </select></td>
	  <td>
	  <input name="comentario[]" type="text" class="vinsert" id="comentario" size="25" maxlength="30">
	  </td>
      <td>
      <select name="balance[]" size="1" class="insert" id="balance">
        <option value="true" {selec}>Si Balances</option>
        <option value="false"  {selec1}>No balance</option>
      </select>
	  </td>
      <td>
	  	<input name="fecha[]" type="hidden" class="insert" id="fecha" value="{fecha}" size="10" maxlength="10">
	  	<input name="operador[]" type="hidden" id="operador" value="{operador}" size="3" maxlength="10">
    
          <label>
          <input type="radio" name="tipo_mov{i}" value="0" {sel} onChange="document.form.operador[{i}].value=0; totales3();">
  E</label>
          <label>
          <input name="tipo_mov{i}" type="radio" value="1"{sel1} onChange="document.form.operador[{i}].value=1; totales3();" >
			I</label>
</td>
    </tr>
<!-- END BLOCK : rows -->
	<tr class="tabla">
      <th class="rtabla">Total Ingresos</th>
      <td><input name="total_ingresos" type="text" class="insert" id="total_ingresos" value="0.00" size="9" maxlength="15" readonly></td>
      <td colspan="4" class="tabla">&nbsp;</td>
	</tr>
	<tr class="tabla">
      <th class="rtabla">Total Egresos</th>
      <td class="tabla"><input name="total_egresos" type="text" class="insert" id="total_egresos" value="0.00" size="9" maxlength="15" readonly></td>
      <td colspan="4" class="tabla">&nbsp;</td>
	</tr>

    <tr class="vtabla">
      <th class="rtabla">GRAN TOTAL </th>
      <td class="tabla">
	  <input name="gran_total" type="text" class="insert" id="gran_total" value="0.00" size="9" maxlength="15" readonly></td>
      <td colspan="4" class="tabla">&nbsp;</td>

    </tr>

  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='document.form.submit()'>
    &nbsp;
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
  <p>
<!--   <input type="button" name="regresar" value="Regresar" class="boton" onClick="document.location='./bal_gast_caja.php'">  -->
<input type="button" name="regresar" value="Regresar" class="boton" onClick="parent.history.back();">
</p>


</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia[0].select();
//window.onload=window.print();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : gastos_caja -->


<!-- START BLOCK : listado -->
<style type="text/css">
@media print {
	.noshow {
		display: none;
	}
}
</style>
<table width="98%"  height="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">GASTOS DE CAJA DEL {fecha}</p>
<table class="print">
  <tr class="print">
    <th scope="col" colspan="2" class="print">COMPAÑÍA</th>
    <th scope="col" class="print">CONCEPTO</th>
    <th scope="col" class="print">CLAVE</th>
    <th scope="col" class="print">TIPO</th>
    <th scope="col" class="print">IMPORTE</th>
  </tr>
  <!-- START BLOCK : listado_rows -->
  <tr class="print">
    <td class="print">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="vprint">{concepto1}
	</td>
	
    <td class="print">{balance1}</td>
    <td class="vprint">{tipo1}</td>
    <td class="print">{importe1}</td>
  </tr>
    <!-- END BLOCK : listado_rows -->  
  <tr class="print">
    <th colspan="5" class="rprint"><font size="2">Total Egresos</font></th>
    <td class="rprint"><font size="2"><strong>{total_egresos}</strong></font></td>
  </tr>
  <tr class="print">
    <th class="rprint" colspan="5"><font size="2">Total Ingresos</font></th>
    <td class="rprint"><font size="2"><strong>{total_ingresos}</strong></font></td>
  </tr>
  <tr class="print">
    <th class="rprint" colspan="5"><font size="2">Gran Total</font></th>
    <td class="rprint"><font size="2"><strong>{gran_total}</strong></font></td>
  </tr>
  <tr class="print">
    <th class="rprint" colspan="5"><font size="2">Total Anterior</font></th>
    <td class="rprint"><font size="2"><strong>{total_anterior}</strong></font></td>
  </tr>
  <tr class="print">
    <th class="rprint" colspan="5"><font size="2">TOTAL MES</font></th>
    <td class="rprint"><font size="3"><strong>{total_total}</strong></font></td>
  </tr>
</table>
<p class="noshow">
  <input type="button" value="Terminar" onclick="document.location='./bal_gast_caja_v2.php'" />
</p></td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=window.print();
//window.onload=document.form.submit();
</script>

<!-- END BLOCK : listado -->

