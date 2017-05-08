
<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.compania.select();
		}
		else
			document.form.compania.select();
	}
	
function valida_registro(){
if(document.form.compania.value=="" || document.form.compania.value==0){
	alert("Revise el número de la compañía");
	document.form.compania.select();
	}
else if(document.form.num_proveedor.value=="" || document.form.num_proveedor.value==0){
	alert("Revise el número del proveedor");
	document.form.num_proveedor.select();
	}
else if(document.form.num_documento.value=="" || document.form.num_documento.value==0){
	alert("Revise el número de la factura");
	document.form.num_documento.select();
	}
else if(document.form.totalf.value=="" || document.form.totalf.value==0){
	alert("Revise el importe de la factura");
	document.form.totalf.select();
	}
else if(document.form.fecha.value==""){
	alert("Revise la fecha");
	document.form.fecha.select();
	}
else
	document.form.submit();
}

function valida_fecha(campo_fecha)
{
	var fecha = campo_fecha.value;
	var dia_m= {dia};
	var mes_m= {mes};
	var anio_m={anio_actual};
	var bandera=false;
	if (fecha.length == 8) 
	{
		// Descomponer fecha en dia, mes y año en caso de que el año se presente en cuatro digitos
		if (parseInt(fecha.charAt(0)) == 0)
			dia = parseInt(fecha.charAt(1));
		else
			dia = parseInt(fecha.substring(0,2));
		if (parseInt(fecha.charAt(2)) == 0)
			mes = parseInt(fecha.charAt(3));
		else
			mes = parseInt(fecha.substring(2,4));
		anio = parseInt(fecha.substring(4));
	}
	else if (fecha.length == 6) 
	{
		// Descomponer fecha en dia, mes y año en caso de que el año se presente en dos digitos
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10) + 2000;
	}
//---------revision de dias
//VALIDACION PARA LOS 4 PRIMEROS DIAS DEL MES EN CURSO PARA CAPTURAR EL MES ANTERIOR
	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4 || dia_m==5 || dia_m==6 || dia_m==7)
	{
		if (anio > anio_m)
		{//año mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revisar el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m)
		{//mes mayor
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			actualiza_fecha(campo_fecha);
		}
		//CASO ESPECIAL EN QUE SEA INICIO DE AÑO, TOMA EN CUENTA CAPTURAS DEL AÑO ANTERIOR
		else if(anio == (anio_m -1) && mes==12 && mes_m==1) 
			actualiza_fecha(campo_fecha);
		//CAPTURA DEL MES ANTERIOR EN EL AÑO EN CURSO
		else if (mes == (mes_m -1)&& anio==anio_m)
			actualiza_fecha(campo_fecha);
		//CAPTURA DEL MES Y AÑO EN CURSO
		else if (mes==mes_m && anio==anio_m)
			actualiza_fecha(campo_fecha);
			
		else if (mes < mes_m-1){
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
		else if(anio < anio_m){
			campo_fecha.value="";
			campo_fecha.select();
			return;
		}
	}
	else 
	{//bloqueo de fechas mayores
		if (anio > anio_m || anio < anio_m)
		{//año mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m || mes < mes_m)
		{//mes mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el mes");
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			campo_fecha.value="";
			campo_fecha.select();
			alert("Revise el dia");
			return;
		}
		//caso en que la fecha es igual al dia corriente no se permite entrada
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			actualiza_fecha(campo_fecha);
		}
		else if ((dia < dia_m) && (mes==mes_m) && (anio==anio_m))
			actualiza_fecha(campo_fecha);
//		document.form.fecha_mov.value="";
	}
}
//---------------------------------------------------------------------------------------------------------------------------
	function actualiza_fecha(campo_fecha) {//---------------------------------------ACTUALIZA FECHA ----
		var fecha = campo_fecha.value;
		var anio_actual = {anio_actual};
		var mes_m={mes};
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
			else if(anio == (anio_actual -1) && mes==12 && mes_m==1){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				campo_fecha.select();
				return;
			}
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
			else if(anio == (anio_actual - 1) && mes==12 && mes_m==1){
				campo_fecha.value = dia+"/"+mes+"/"+anio;
			}
			else {
				campo_fecha.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Factura de materia prima especial</p>
<form name="form" method="get" action="./fac_esp_cap.php">
<input type="hidden" name="temp">
<table class="tabla">
	<tr>
		<th class="vtabla">N&uacute;mero de compa&ntilde;ia</th>
		<td class="vtabla">
			<input name="compania" type="text" class="insert" id="compania" 
			onFocus="form.temp.value=this.value" 
			onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" 
			onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_proveedor.select();" 
			size="5" maxlength="5"></td>
	</tr>
	<tr>
		<th class="vtabla">N&uacute;mero de proveedor</th>
		<td class="vtabla">
			<input name="num_proveedor" type="text" class="insert" id="num_proveedor"
			onFocus="form.temp.value=this.value" 
			onChange="valor=isInt(this,form.temp); if (valor==false) this.select();"
			onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_documento.select(); 
			else if (event.keyCode == 38) form.num_cia.select();" value="{num_pro}" size="5" maxlength="5">
		</td>
	</tr>
    <tr>
		<th class="vtabla">N&uacute;mero de documento </th>
    	<td class="vtabla">
		<input name="num_documento" type="text" class="insert" id="num_documento" 
		onFocus="form.temp.value=this.value" 
		onChange="valor=isInt(this,form.temp); if (valor==false) this.select();"
		onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.totalf.select();
		else if (event.keyCode == 38) form.num_proveedor.select();" size="10" maxlength="10"></td>
	</tr>
	<tr>
	<th class="vtabla">Total del documento</th>
    	<td class="vtabla">
		<input name="totalf" type="text" class="rinsert" 
		onFocus="form.temp.value=this.value" 
		onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"
		onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha.select();
else if (event.keyCode == 38) form.num_documento.select();" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<th class="vtabla">Fecha <font size="-2">(ddmmaa)</font> </th>
		<td class="vtabla">
		<input name="fecha" type="text" class="insert" id="fecha" onChange="valida_fecha(this);" onKeyDown="if (event.keyCode == 13) form.enviar.focus();
else if (event.keyCode == 40) form.num_cia.select();
else if (event.keyCode == 38) form.totalf.select();" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input name="enviar" class="boton" type="button" id="enviar" onClick="valida_registro()" value="Enviar">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.compania.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : factura -->

<script language="JavaScript" type="text/JavaScript">

function total(){

}

function actualiza_mp(codmp, nombre, desc, desc1, ivac, conte, prec) {
	mp = new Array();// Materias primas
	des = new Array();//descuento 1
	des1 = new Array();// descuento 2
	iva = new Array();//iva
	contenido= new Array();//contenido
	precio=new Array();//precio
	<!-- START BLOCK : nom_mp -->
	mp[{codmp}] = '{nombre_mp}';
	des[{codmp}]='{des}';
	des1[{codmp}]='{des1}';
	iva[{codmp}]='{iva}';
	contenido[{codmp}]='{cont}';
	precio[{codmp}]='{prec}'
	<!-- END BLOCK : nom_mp -->
			
	if (codmp.value > 0) {
		if (mp[codmp.value] == null) {
			alert("Materia Prima "+codmp.value+" no esta en el catálogo de productos por proveedor");
			codmp.value = "";
			nombre.value  = "";
			desc.value = 0;
			desc1.value = 0;
			ivac.value = 0;
			conte.value = 0;
			prec.value = 0;
			codmp.select();
		}
		else {
			nombre.value   = mp[codmp.value];
			if(des[codmp.value]=='') desc.value = 0; else desc.value = des[codmp.value];
			if(des1[codmp.value]=='') desc1.value = 0; else desc1.value = des1[codmp.value];
			if(iva[codmp.value]=='') ivac.value = 0; else ivac.value = iva[codmp.value];
			if(contenido[codmp.value]=='') conte.value = 0; else conte.value = contenido[codmp.value];
			if(precio[codmp.value]=='') prec.value = 0; else prec.value = precio[codmp.value];
			
		}
	}
	else if (codmp.value == "") {
		codmp.value = "";
		nombre.value  = "";
	}
}

function revisa_mp(codmp,contenido)
{
if(codmp.value==148) contenido.value=360;

}

function descuenta(checador,total_producto,total_general){
if(total_producto.value=="" || parseFloat(total_producto.value)==0) return;
//if(total_general.value=="" || parseFloat(total_general.value)==0)  return;
var auxiliar=0;
var total_prod=parseFloat(total_producto.value);
var total_gral=parseFloat(total_general.value);
if (checador.value==1){
	auxiliar = total_gral - total_prod;
	total_general.value = auxiliar.toFixed(2);
	}
else if(checador.value==0){
	auxiliar = total_gral + total_prod;
	total_general.value = auxiliar.toFixed(2);
	}
}

function suma2(neto,des1,des2,iva,total,total_gral,totalf,importe_iva,regalado)
{
	var total_mp=0;
	
	if(neto.value=="") 
		total_mp=0;
	else
		total_mp=parseFloat(neto.value);
	
	total_mp=total_mp.toFixed(2);
	
	var desc1=parseFloat(des1.value);
	var desc2=parseFloat(des2.value);
	var impuesto=parseFloat(iva.value);
	var factura=parseFloat(totalf.value);	
	var total_fac=parseFloat(total_gral.value);
	var imp_iva=0;
	total_mp -= total_mp*(desc1/100);
	total_mp -= total_mp*(desc2/100);
	imp_iva = total_mp*(impuesto/100);
	total_mp += total_mp*(impuesto/100);

	if(regalado.checked==false)
		total_fac += total_mp;
	
	total.value = total_mp.toFixed(2);
	importe_iva.value=imp_iva;
}

function suma(unidad,precio,neto,des1,des2,iva,total,total_gral,totalf,importe_iva,regalado)
{
	var unid=0;
	var prec=0;
	var tot=0;
	if(unidad.value=="") unid=0;
	else unid=parseFloat(unidad.value);
	if(precio.value=="") prec=0;
	else prec=parseFloat(precio.value);
	tot=prec*unid;
	unidad.value=unid.toFixed(2);
	precio.value=prec.toFixed(2);
	neto.value=tot.toFixed(2);

	var total_mp=tot.toFixed(2);
	var desc1=parseFloat(des1.value);
	var desc2=parseFloat(des2.value);
	var impuesto=parseFloat(iva.value);
	var factura=parseFloat(totalf.value);	
	var total_fac=parseFloat(total_gral.value);
	var imp_iva=0;
	total_mp -= total_mp*(desc1/100);
	total_mp -= total_mp*(desc2/100);
	imp_iva = total_mp*(impuesto/100);
	total_mp += total_mp*(impuesto/100);

	if(regalado.checked==false)
		total_fac += total_mp;
	
	total.value = total_mp.toFixed(2);
	importe_iva.value=imp_iva;
}
//---------------------------------------------------------
	function verifica(total_prod,total_gral,temp, total_fac, regalado)
	{
	if(regalado.checked==true) return;
	if (temp.value=="") temp.value=0;
	if (total_prod.value=="") total_prod.value=0;
	if (parseFloat(total_gral.value)==0) total_gral.value=total_gral.value;

	var entrada=parseFloat(total_prod.value);
	var tem=parseFloat(temp.value);
	var total=parseFloat(total_gral.value);
	
	if(tem > 0)
		{
		total-=tem;
		total+=entrada;
		}
	else
		total+=entrada;
	
	total_gral.value=total.toFixed(2);
	
	compara_totales(total_gral.value,total_fac.value)
//		total_cant.value=total.toFixed(2);
	}
//------------------------------------------------------------------
function compara_totales(total_calculado,total_general)
{
	var diferencia=0;
	diferencia=parseFloat(total_calculado)-parseFloat(total_general);
//	diferencia=parseFloat(diferencia);
	diferencia=diferencia.toFixed(2);
	diferencia=Math.abs(diferencia);
//	alert(diferencia);
	
	if(total_calculado==total_general)
		{
		document.form.enviar1.disabled=false;

		}
		
	else if(diferencia == .01)
	{
		if (confirm("El total de la factura no coincide con el total calculado.\n¿Desea cambiar el total de la factura?")) 
		{
			var temp = prompt("Total de factura : "+total_general+"\nTotal calculado  : "+total_calculado+"\n\nEscriba el nuevo total de la factura","");
			temp=parseFloat(temp);
			if (temp == total_calculado) 
			{
				document.form.totalf.value = temp.toFixed(2);
//				alert("voy a desbloquear");
				document.form.enviar1.disabled=false;
			}
			else
				alert("no son iguales las cantidades");
		}
	}	
	else{
		document.form.enviar1.disabled=true;
		}
}



	function valida_registro() {
	if (confirm("¿Son correctos los datos?"))
		document.form.submit();
	else
		return;
	}

	function total_neto(unidad,precio,total)
	{
		var unid=0;
		var prec=0;
		var tot=0;
		if(unidad.value=="") unid=0;
		else unid=parseFloat(unidad.value);
		if(precio.value=="") prec=0;
		else prec=parseFloat(precio.value);
		tot=prec*unid;
		unidad.value=unid.toFixed(2);
		precio.value=prec.toFixed(2);
		
		total.value=tot.toFixed(2);
	}

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Facturas de materia prima especial </p>
<form name="form" method="post" action="./insert_fac_esp_cap.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="temp_total" type="hidden">
<input name="tem" type="hidden" value="">
<input name="temp2" type="hidden">
<input name="temporal" type="hidden" id="temporal">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Proveedor</th>
    <th class="tabla" scope="col">Documento</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col"><p>Total de factura</p>      </th>
  </tr>
  <tr>
    <td class="tabla">
      <input name="num_cia" type="hidden" value="{num_cia}">
      <font size="+1">{num_cia} - {nombre_corto}</font></td>
    <td class="tabla">
      <input name="num_proveedor" type="hidden" value="{num_proveedor}">
	  <font size="+1">{num_proveedor} - {nombre}</font></td>
    <td class="tabla">
      <input name="num_documento" type="hidden" value="{num_documento}">
      <font size="+1">{num_documento}</font></td>
    <td class="tabla">
      <input name="fecha" type="hidden" value="{fecha}">
      <font size="+1">{fecha}</font></td>
    <td class="tabla">
	<input name="totalf" type="text" class="nombre" disabled="true" id="totalf" value="{totalf}" size="12" maxlength="12">
	</td>
  </tr>
</table>
<br>
  <table class="tabla">
    <tr>
	  
	
      <th class="tabla" align="center">C&oacute;digo</th>
      <th class="tabla" align="center">Nombre</th>
      <th class="tabla" align="center">Cantidad</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Unidad</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Total Neto</th>

      <th class="tabla" align="center">Desc 1 </th>
      <th class="tabla" align="center">Desc 2</th>

      <th class="tabla" align="center">I.V.A.</th>

      <th class="tabla" align="center">Total por<br> producto</th>
      <th class="tabla" align="center">R</th>
    </tr>
    <!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">
        <input name="codmp{i}" id="codmp{i}" type="text" class="insert" size="3" maxlength="3" onKeyDown="if (event.keyCode == 13) form.cantidad{i}.select();" onFocus="form.temp2.value=this.value;" onChange="actualiza_mp(this, form.nombremp{i}, form.desc1{i}, form.desc2{i}, form.iva{i}, form.contenido{i}, form.precio{i}); revisa_mp(this,form.contenido{i}); valor=isInt(this,form.temp); if (valor==false) this.select();">
</td>
      <td class="tabla">
         <input name="nombremp{i}" type="text" id="nombremp{i}" size="35" disabled class="vnombre">
      </td>
	  
      <td class="vtabla">
        <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" onKeyDown="if(event.keyCode == 13) form.contenido{i}.select();" value="0" size="5" maxlength="10">
</td>
      <td class="tabla">
        
		<input name="contenido{i}" type="text" class="insert" size="5" maxlength="10" onKeyDown="if(event.keyCode == 13) form.unidad{i}.select();">
		</td>
      <td class="tabla">
	    <input name="unidad{i}" type="text" class="insert" size="5" maxlength="10" onKeyDown="if(event.keyCode == 13) form.precio{i}.select();" onChange="suma(this,form.precio{i},form.total{i},form.desc1{i},form.desc2{i},form.iva{i},form.costo_unitario{i}, form.costo_total, form.totalf, form.importe_iva{i}, form.regalado{i});verifica(form.costo_unitario{i},form.costo_total,form.tem, form.totalf, form.regalado{i});" onFocus="form.tem.value=costo_unitario{i}.value">
	  </td>
      <td class="tabla">
		<input name="precio{i}" type="text" class="insert" size="5" maxlength="10" onKeyDown="if(event.keyCode == 13) form.total{i}.select();" onChange="suma(form.unidad{i},this,form.total{i},form.desc1{i},form.desc2{i},form.iva{i},form.costo_unitario{i}, form.costo_total, form.totalf, form.importe_iva{i}, form.regalado{i});verifica(form.costo_unitario{i},form.costo_total,form.tem, form.totalf, form.regalado{i});" onFocus="form.tem.value=costo_unitario{i}.value">
	  </td>
      <td class="tabla">
	  <input name="total{i}" type="text" class="insert" id="total{i}" size="5" maxlength="10" 
	  onKeyDown="if(event.keyCode == 13) form.desc1{i}.select();" 
	  onFocus="form.tem.value=this.value;form.temporal.value=this.value" 
	  onChange="suma2(this,form.desc1{i},form.desc2{i},form.iva{i},form.costo_unitario{i}, form.costo_total, form.totalf, form.importe_iva{i}, form.regalado{i});valor=isFloat(this,2,form.temporal); if (valor==false) this.select(); verifica(form.costo_unitario{i},form.costo_total,form.tem, form.totalf, form.regalado{i});">
	  </td>
      <td class="tabla">
        <input name="desc1{i}" type="text" class="insert" onKeyDown="if(event.keyCode == 13) form.desc2{i}.select();" size="5" maxlength="10">
	</td>
      <td class="tabla">
        <input name="desc2{i}" type="text" class="insert" onKeyDown="if(event.keyCode == 13) form.iva{i}.select();" size="5" maxlength="10">
        </td>
      <td class="tabla">
        <input name="iva{i}" type="text" class="insert" onKeyDown="if(event.keyCode == 13) form.codmp{next}.select();" size="5" maxlength="10">
        <input name="importe_iva{i}" type="hidden" id="importe_iva{i}" size="7">
        </td>

      <th class="tabla">
        <input name="costo_unitario{i}" type="text" class="total" size="10" maxlength="12" readonly="true">
</th>
      <th class="tabla"><input name="regalado{i}" type="checkbox" id="regalado{i}" value="checkbox" onChange="if(this.checked==false) {form.pago_proveedor{i}.value=0; descuenta(form.pago_proveedor{i},form.costo_unitario{i},form.costo_total);} else if (this.checked==true){ form.pago_proveedor{i}.value=1; descuenta(form.pago_proveedor{i},form.costo_unitario{i},form.costo_total);}">
        <input name="pago_proveedor{i}" type="hidden" id="pago_proveedor{i}" value="0" size="7"></th>
    </tr>
    <!-- END BLOCK : rows -->
    <tr>
      <th class="rtabla" colspan="10">Total</th>
      <th class="tabla">
	  <input name="costo_total" type="text" class="total" value="0.00" size="12" maxlength="12" readonly="true">
	  </th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Para<br>
        aclaraci&oacute;n</th>
      <th class="tabla" scope="col">Observaciones</th>
    </tr>
    <tr>
      <td class="tabla"><input name="aclaracion" type="checkbox" id="aclaracion" value="1">
        Si</td>
      <td class="tabla"><textarea name="obs" class="insert" id="obs"></textarea></td>
    </tr>
  </table>
  <p>
  <input type="button" class="boton" value="<< Regresar" onclick='parent.history.back()'>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <img src="./menus/delete.gif" align="middle">&nbsp;<input type="button" class="boton" value="Borrar" onclick='document.form.reset();'>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <img src="./menus/insert.gif" align="middle">&nbsp;<input type="button" name="enviar1" class="boton" value="Capturar" id="enviar1" onclick='valida_registro();' disabled>
  </p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.codmp0.select();</script>
</td>
</tr>
</table>
<!-- START BLOCK : factura -->