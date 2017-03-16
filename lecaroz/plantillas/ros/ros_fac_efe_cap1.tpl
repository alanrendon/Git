<script type="text/javascript" language="JavaScript">

function valida(){
if (!(parseInt(document.formi.compania.value) > 100 && parseInt(document.formi.compania.value) < 200 || parseInt(document.formi.compania.value) == 702))
	{
	alert("No es una rosticeria"); 
	document.formi.compania.value="";
	}
else document.formi.submit();
}


	function borrar() {
		if (confirm("¿Desea borrar la pantalla?")) {
			document.formi.reset();
			document.formi.compania.select();
		}
		else
			document.formi.compania.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de Facturas Especiales de Rosticerias</P>
<!-- START BLOCK : obtener_dato -->
<form name="formi" method="get" action="ros_fac_efe_cap.php">

  <table class="tabla">
    <tr>
      <th class="tabla" align="center"><font size="+1">Compa&ntilde;&iacute;a</font></th>
    </tr>

    <tr>
      <td class="tabla" align="center">
		  <select name="compania" class="insert">
        <!-- START BLOCK : CIA -->
		    <option value="{num_cia}">{nom_cia}</option>
        <!-- END BLOCK : CIA -->
		  </select>      </td>
    </tr>
<input name="contador" type="hidden" class="insert" id="contador" value="{cont}" size="5">

  </table><br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">No de factura</th>
    </tr>
    <tr>
      <td class="tabla" align="center"><input name="num_fac" type="text" class="insert" size="10" onKeyDown="if (event.keyCode == 13) document.formi.cantidad0.focus();">
      </td>
    </tr>
    <input name="contador2" type="hidden" class="insert" id="contador2" value="{cont}" size="5">
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center" colspan="2">C&oacute;digo de Materia Primas</th>
      <th align="center" class="tabla">Cantidad</th>
      <th align="center" class="tabla">Kilos</th>
    </tr>
    <!-- START BLOCK : rows -->
    <tr>
      <th class="vtabla"> {codmp}
          <input name="codmp{i}" type="hidden" class="insert" id="codmp{i}" value="{codmp}" size="5">
      </th>
      <td class="vtabla"> {nom_codmp} </td>
      <td class="tabla" align="center">
        <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" size="5" onKeyDown="if (event.keyCode == 13) document.formi.kilos{i}.focus();">
      </td>
      <td class="tabla" align="center">
        <input name="kilos{i}" type="text" class="insert" id="kilos{i}" size="5" onKeyDown="if (event.keyCode == 13) document.formi.cantidad{next}.focus();">
</td>
    </tr>
    <!-- END BLOCK : rows -->

  </table>
  <br>

  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Continuar" onclick='valida()'>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>
<!-- END BLOCK : obtener_dato -->



<!-- START BLOCK : factura -->

<script language="JavaScript" type="text/JavaScript">

function valida(){
document.form.submit();
}
//---------------------------------------------------VALIDA FECHA-------------------------------------------------------------

function valida_fecha()
{
	var fecha = document.form.fecha.value;
	var dia_m={dia};

	var mes_m={mes};
	var anio_m={anio_actual};
	var bandera=false;
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
	}
	else if (fecha.length == 6) 
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
		anio = parseInt(fecha.substring(4)) + 2000;
	}
//---------revision de dias

	if (dia_m==1 || dia_m==2 || dia_m==3 || dia_m==4)
	{

		if (anio > anio_m || anio < anio_m)
		{//año mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise año");
			return;
		}
		else if (mes > mes_m && anio==anio_m)
		{//mes mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
		else if (mes == (mes_m -1)&& anio==anio_m)
			actualiza_fecha();
		else if (mes==mes_m && anio==anio_m)
			actualiza_fecha();
		else if (mes < mes_m-1)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			return;
		}
	}
	else 
	{//bloqueo de fechas mayores
		if (anio > anio_m || anio < anio_m)
		{//año mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el año");
			return;
		}
		else if (mes > mes_m && anio==anio_m || mes < mes_m)
		{//mes mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el mes");
			return;
		}
		else if (dia > dia_m && mes==mes_m && anio==anio_m)
		{//dia mayor
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el dia");
			return;
		}
		//caso en que la fecha es igual al dia corriente no se permite entrada
		else if (dia==dia_m && mes==mes_m && anio==anio_m)
		{
			document.form.fecha.value="";
			document.form.fecha.select();
			alert("Revise el dia");
			return;
		}
		else if ((dia < dia_m) && (mes==mes_m) && (anio==anio_m))
			actualiza_fecha();
//		document.form.fecha_mov.value="";
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




<form name="form" method="post" action="insert_ros_fac_efe.php?tabla={tabla}" >
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">No de factura</th>
    </tr>
    <tr>
      <td class="tabla" align="center"><input name="num_fac" type="hidden" class="insert" id="num_fac" value="{num_fac}" size="10"> 
      {num_fac}
      </td>
    </tr>
    <input name="contador22" type="hidden" class="insert" id="contador22" value="{cont}" size="5">
  </table>
<br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center" colspan="2">C&oacute;digo de Materia Primas</th>
      <th align="center" class="tabla">Cantidad</th>
      <th align="center" class="tabla">Kilos</th>
      <th align="center" class="tabla">Precio unitario</th>
      <th align="center" class="tabla">Total</th>
	  <input name="cont_productos" type="hidden" class="insert" value="{cont_productos}" size="5">
	  <input name="cont_cias" type="hidden" class="insert" value="{cont_cias}" size="5">
    </tr>
    <!-- START BLOCK : rows1 -->
    <tr>
      <th class="vtabla"> {codmp}
          <input name="codmp{var1}" type="hidden" class="insert" id="codmp{var1}" value="{codmp}" size="5">
      </th>
      <td class="vtabla"> {nom_codmp} </td>
      <td class="tabla" align="center">
        <input name="cantidad{var1}" type="hidden" class="insert" id="cantidad{var1}" onKeyDown="if (event.keyCode == 13) document.form.kilos{i}.focus();" value="{cantidad}" size="5"> 
        {cantidad1}
      </td>
      <td class="tabla" align="center">
	  <!-- START BLOCK : kilos_ok -->
	  {kilos1}
  	  <!-- END BLOCK : kilos_ok -->
  	  <!-- START BLOCK : kilos_mod -->
	  <font color="#0000CC">{kilos1}</font>
  	  <!-- END BLOCK : kilos_mod -->

        <input name="kilos{var1}" type="hidden" class="insert" id="kilos{var1}" onChange="document.form.total{i}.value=parseFloat(this.value) * parseFloat(document.form.precio{i}.value)" value="{kilos}" size="5">
      </td>
      <td class="tabla" align="center"><input name="precio{var1}" type="hidden" class="nombre" id="precio{var1}" value="{precio}" size="5" readonly>
      {precio1}</td>
      <td class="tabla" align="center"><input name="total{var1}" type="hidden" id="total{var1}" size="5" readonly>
      {total1}</td>
    </tr>
    <!-- END BLOCK : rows1 -->

    <tr>
      <th class="tabla" colspan="5"><font size="3">TOTAL</font></th>
      <th class="rtabla"><font size="3">{total_fac}</font></th>
    </tr>
  </table>
  <br>
        <table class="tabla">
          <tr>
            <th class="tabla" align="center">Fecha</th>
          </tr>
          <tr>
            <td class="tabla" align="center"><input name="fecha" type="text" value="{fecha}" size="10" class="nombre" onChange="valida_fecha();" onKeyDown="if (event.keyCode == 13) document.form.campo00.focus();"></td>
          </tr>
          <input name="contador222" type="hidden" class="insert" id="contador222" value="{cont}" size="5">
        </table>
  <br>
  <table class="tabla">
    <tr>

      <th class="tabla" align="center" colspan="2">Materia Prima<input name="temporal" type="hidden" size="10" class="nombre"><input name="temporal2" type="hidden" size="10" class="nombre"></th>
		
      <!-- START BLOCK : companias -->
	  <th class="tabla" align="center" colspan="3">{nom_cia}<br>
      <input name="fecha{i}" type="hidden" value="{fecha}" size="10" class="nombre" readonly>
      <input name="cia{i}" type="hidden" value="{num_cia}" size="10" class="nombre" readonly></th>
	  <!-- END BLOCK : companias -->
    </tr>
	
	<!-- START BLOCK : renglones -->
	<tr>
	<th class="tabla" align="center">  <input name="codmp{d}" type="hidden" class="insert" id="campo{d}{j}" value="{codmp}" size="5">{codmp}</th>
	<td class="vtabla"> {nom_codmp} <input name="total_cantidad{d}" type="hidden" class="nombre" id="total_cantidad{d}" size="5" readonly></td>

	<!-- START BLOCK : cmp -->
	<script language="JavaScript" type="text/JavaScript">
	function verifica(total_reg,tem,tot_gral)
	{
	if (tem.value=="") tem.value=0;
	if (tot_gral.value=="") tot_gral.value=0;

	var total=parseFloat(total_reg.value);
	var temp=parseFloat(tem.value);
	var general=parseFloat(tot_gral.value);

	if(temp > 0)
		{
		general-=temp;
		general+=total;
		}
	else
		general+=total;
	
	tot_gral.value=general.toFixed(2);
	}


	function verifica2(total_cant,tem2,ent,valor)
	{
	if (tem2.value=="") tem2.value=0;
	if (total_cant.value=="") total_cant.value=0;

	var entrada=parseFloat(ent.value);
	var temp=parseFloat(tem2.value);
	var total=parseFloat(total_cant.value);
	
	
	if(temp > 0)
		{
		total-=temp;
		total+=entrada;
		}
	else
		total+=entrada;
	
	
	total_cant.value=total
	if(valor.value==total_cant.value)
		{
		document.form.enviar.disabled=false;
//		alert("toy aqui");
		}
	else
		{
		document.form.enviar.disabled=true;
//		alert("toy aca");
		}
		total_cant.value=total.toFixed(2);
	}
	
	</script>

	<td class="tabla" align="center">  
	<input name="campo{d}{j}" type="text" class="insert" id="campo{d}{j}" 
	onChange="document.form.kilos{d}{j}.value=parseFloat(document.form.campo{d}{j}.value) * (parseFloat(document.form.kilos{d}.value)/ parseFloat(document.form.cantidad{d}.value)); 
	numero=new Number(document.form.kilos{d}{j}.value);
	document.form.kilos{d}{j}.value=numero.toFixed(2); 
	document.form.total{d}{j}.value=parseFloat(document.form.precio{d}.value)*( parseFloat(document.form.campo{d}{j}.value) * (parseFloat(document.form.kilos{d}.value)/ parseFloat(document.form.cantidad{d}.value) ) ); 
	numero2=new Number(document.form.total{d}{j}.value);
	document.form.total{d}{j}.value=numero2.toFixed(2); 
	verifica(form.total{d}{j},form.temporal,form.total_gral{j}); 
	verifica2(form.total_cantidad{d}, form.temporal2, form.campo{d}{j},form.cantidad{d}); " 
	onKeyDown="if (event.keyCode == 13) { if({sig}=={con}) document.form.campo{next}0.focus(); else document.form.campo{d}{sig}.focus();}" size="5" onFocus="form.temporal.value=form.total{d}{j}.value; form.temporal2.value=form.campo{d}{j}.value; "></td>
	<td class="tabla" align="center">  <input name="kilos{d}{j}" type="text" class="nombre" id="kilos{d}{j}" size="5"></td>
	<td class="tabla" align="center">  <input name="total{d}{j}" type="text" class="nombre" id="total{d}{j}" size="5"></td>
	<!-- END BLOCK : cmp -->
	</tr>
	<!-- END BLOCK : renglones -->
	<tr>
	  <th class="tabla" align="center" colspan="2"></th>
	<!-- START BLOCK : totales -->
	  <th class="tabla" align="center" colspan="2"></th>
	  <td class="tabla" align="center"><input name="total_gral{d}" type="text" class="nombre" id="total_gral{d}" size="5" readonly></td>
	<!-- END BLOCK : totales -->
    </tr>
	
  </table>
  <br>

  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Movimientos" disabled onClick="valida();">
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='document.form.reset();'>
	<br><br><input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>

</p>
</form>
<!-- END BLOCK : factura -->
</td>
</tr>
</table>