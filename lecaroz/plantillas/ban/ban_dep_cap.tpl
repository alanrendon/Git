<!-- START BLOCK : numfilas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Depósitos Manuales</p>
<form action="./ban_dep_cap.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">N&uacute;mero de Depositos </th>
    <td class="vtabla"><input name="numfilas" type="text" id="numfilas" class="insert" size="3" maxlength="3" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)"></td>
  </tr>
</table>
<p>
  <input type="submit" name="Submit" value="Siguiente" class="boton">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.numfilas.select();</script>
<!-- END BLOCK : numfilas -->
<!-- START BLOCK : captura -->
<script type="text/javascript" language="JavaScript">
	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia_ini -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia_ini -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos del formulario?"))
			document.form.submit();
		else
			return false;
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Depósitos Manuales</p>
<!-- movimientos_bancarios -->
<form action="./ban_dep_cap.php?tabla={tabla}" method="post" name="form">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<table class="tabla">
   <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo Movimiento</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Imp. ficha </th>
   </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_compania(this,form.nombre_cia{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha_mov{i}.select();
else if (event.keyCode == 37) form.concepto{back}.select();
else if (event.keyCode == 38) form.num_cia{back}.select();
else if (event.keyCode == 40) form.num_cia{next}.select();" value="{num_cia}" size="3" maxlength="3">
  	  <input name="nombre_cia{i}" type="text" class="vnombre" id="nombre_cia{i}" value="{nombre_cia}" size="20" maxlength="20" readonly="true"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="fecha_mov{i}" type="text" class="insert" id="fecha_mov{i}" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe{i}.select();
else if (event.keyCode == 37) form.num_cia{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{fecha_mov}" size="10" maxlength="10"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<select name="cod_mov{i}" class="insert" id="cod_mov{i}">
		<!-- START BLOCK : mov -->
			<option value="{id}" {selected}>{id} - {descripcion}</option>
		<!-- END BLOCK : mov -->
      </select>	  </td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37) form.fecha_mov{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{importe}" size="12" maxlength="12"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia{next}.select();
else if (event.keyCode == 37) form.importe{i}.select();
else if (event.keyCode == 38) form.concepto{back}.select();
else if (event.keyCode == 40) form.concepto{next}.select();" value="{concepto}" size="30" maxlength="50"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="ficha{i}" type="checkbox" id="ficha{i}" value="TRUE" checked></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p> 
      <input name="enviar" type="button" class="boton" onClick='valida_registro()' value="Capturar">
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia0.select();</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Dep&oacute;sitos capturados <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>

<table width="100%" class="print">
  <tr>
    <th width="5%" class="print" scope="row">Cia.</th>
    <th width="10%" class="print" scope="row">Cuenta</th>
    <th width="25%" class="print" scope="row">Nombre</th>
    <th colspan="2" class="print">Codigo de Movimiento </th>
    <th class="print">Concepto</th>
    <th width="15%" class="print">Importe</th>
    <th width="10%" class="print">Fecha</th>
  </tr>
  <!-- START BLOCK : fila_lis -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print" scope="row">{num_cia}</td>
    <td class="print" scope="row">{cuenta}</td>
    <td class="vprint" scope="row">{nombre}</td>
    <td width="5%" class="print">{cod_mov}</td>
    <td width="15%" class="vprint">{descripcion}</td>
    <td width="15%" class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
    <td class="print">{fecha}</td>
  </tr>
  <!-- END BLOCK : fila_lis -->
  <tr>
    <th colspan="6" class="rprint" scope="row">Total</th>
    <th class="rprint_total">{total}</th>
    <th class="rprint_total">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="8" scope="row">&nbsp;</td>
    </tr>
</table>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function fichas() {
		if (confirm("¿Imprimir listado de depósitos?")) {
			window.print();
		}
		//if (confirm("¿imprimir fichas de depósito?")) {
			//if (confirm("Por favor ponga las fichas en la impresora y presione 'Aceptar'"))
				window.open("./ban_fic_dep.php","fichas","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		//}
		document.location = "./ban_dep_cap.php";
	}
	
	window.onload = fichas();
</script>
<!-- END BLOCK : listado -->