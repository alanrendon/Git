<!-- START BLOCK : tipo_listado -->
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Gastos de Oficina </p>
<form name="form" method="get" action="./bal_gof_con.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla">Fecha de Inicio</th>
      <th class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.fecha2.select();
else if (event.keyCode == 40) form.num_cia.select();" value="{fecha1}" size="12" maxlength="12"></th>
      <th class="vtabla">Fecha Final </th>
      <th class="vtabla"><input name="fecha2" type="text" class="insert" id="fecha2"  onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) form.num_cia.select();
else if (event.keyCode == 37) form.fecha1.select();" value="{fecha2}" size="12" maxlength="12"></th>
    </tr>
    <tr>
      <th colspan="2" class="vtabla"><input name="tipo" type="radio" value="una" checked onClick="form.num_cia.style.visibility='visible'">
      Por compa&ntilde;&iacute;a </th>
      <th class="vtabla">Compa&ntilde;&iacute;a </th>
      <th class="vtabla" id="cia"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38) form.fecha2.select();" size="5" maxlength="3"></th>
    </tr>
    <tr>
      <th colspan="4" class="vtabla"><input name="tipo" type="radio" value="todas" onClick="form.num_cia.style.visibility='hidden'">
      Todas las compa&ntilde;&iacute;as </th>
    </tr>
  </table>
  <p>
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha1.value == "") {
			alert("Debe especificar la fecha inicial");
			form.fecha1.select();
			return false;
		}
		else if (form.fecha2.value == "") {
			alert("Debe especificar la fecha final");
			form.fecha2.select();
			return false;
		}
		else {
			if (form.tipo[0].checked == true && form.num_cia.value <= 0) {
				alert("Debe especificar el número de compañía");
				form.num_cia.select();
				return false;
			}
			else
				form.submit();
		}
	}
	
	window.onload = document.form.fecha1.select();
</script>
<!-- END BLOCK : tipo_listado -->

<!-- START BLOCK : listado_one -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<script language="javascript" type="text/javascript">
	function imprimir(boton) {
		boton.style.visibility = 'hidden';
		window.print();
		alert("Imprimiendo...");
		boton.style.visibility = 'visible';
	}
</script>
<table width="100%">
	<tr>
		<td class="print_encabezado">Cia.: {num_cia}</td>
		<td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
		<td class="print_encabezado">Cia.: {num_cia}</td>
	</tr>
	<tr>
		<td class="encabezado" align="left">&nbsp;</td>
		<td class="print_encabezado" align="center">{nombre_cia}</td>
		<td class="encabezado" align="right">&nbsp;</td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td class="print_encabezado" align="center">Gastos de Oficina del {fecha1_one} al {fecha2_one} </td>
	  <td>&nbsp;</td>
  </tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
<table class="print">
    <tr>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Egreso</th>
    <th class="print" scope="col">Ingreso</th>
	<th class="print" scope="col">Balance</th>
    <th class="print" scope="col">Fecha</th>
  </tr>
  <!-- START BLOCK : fila_one -->
  <tr>
    <td class="vprint">{concepto_one}</td>
    <td class="rprint">{egreso_one}</td>
    <td class="rprint">{ingreso_one}</td>
    <td class="print">{afecta_one}</td>
    <td class="print">{fecha_one}</td>
  </tr>
  <!-- END BLOCK : fila_one -->
  <tr>
    <th class="print">Total de Gastos </th>
    <th class="rprint_total">{total_egreso_one}</th>
    <th class="rprint_total">{total_ingreso_one}</th>
	<th class="rprint_total">Total de la Compa&ntilde;&iacute;a </th>
    <th class="rprint_total" >{total_compania_one}</th>
  </tr>
</table>
<p>
  <input name="button" type="button" class="boton" onClick="imprimir(this)" value="Imprimir listado">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : listado_one -->

<!-- START BLOCK : listado_all -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<script language="javascript" type="text/javascript">
	function imprimir(boton) {
		boton.style.visibility = 'hidden';
		window.print();
		alert("Imprimiendo...");
		boton.style.visibility = 'visible';
	}
</script>
<table width="100%">
	<tr>
		<td class="encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
	</tr>
	<tr>
	  <td class="encabezado" align="center">Gastos de Oficina del {fecha1_all} al {fecha2_all} </td>
  </tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<table class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Egreso</th>
    <th class="print" scope="col">Ingreso</th>
	<th class="print" scope="col">Balance</th>
    <th class="print" scope="col">Fecha</th>
  </tr>
  <!-- START BLOCK : cia_all -->
  <tr>
    <th class="vprint" rowspan="{span}" valign="top">{num_cia_all} - {nombre_cia_all}</th>
  </tr>
  <!-- START BLOCK : fila_all -->
  <tr>
    <td class="vprint">{concepto_all}</td>
    <td class="rprint">{egreso_all}</td>
    <td class="rprint">{ingreso_all}</td>
    <td class="print">{afecta_all}</td>
    <td class="print">{fecha_all}</td>
  </tr>
  <!-- END BLOCK : fila_all -->
  <tr>
    <th></th>
	<th class="rprint">Total de Gastos </th>
    <th class="rprint_total">{total_egreso_all}</th>
    <th class="rprint_total">{total_ingreso_all}</th>
  </tr>
  <tr>
    <th></th>
	<th class="rprint">Total de la Compa&ntilde;&iacute;a </th>
    <th colspan="2" class="print_total">{total_compania_all}</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia_all -->
  <tr>
  	<th class="rprint" colspan="2">Gran Total</th>
	<th class="rprint_total"><font size="+1">{gran_total_egreso_all}</font></th>
	<th class="rprint_total"><font size="+1">{gran_total_ingreso_all}</font></th>
  </tr>
  <tr>
  	<th class="rprint" colspan="2">Neto</th>
	<th class="print_total" colspan="2"><font size="+1">{gran_total_compania_all}</font></th>
  </tr>
</table>
<p>
  <input name="button" type="button" class="boton" onClick="imprimir(this)" value="Imprimir listado">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : listado_all -->
