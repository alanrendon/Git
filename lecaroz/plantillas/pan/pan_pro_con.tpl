<!-- START BLOCK : obtener_datos -->
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Producción</p>
<form name="form" action="./pan_pro_con.php" method="get">
<input name="temp" type="hidden">
<table class="tabla">
	<tr>
		<th class="vtabla">Compa&ntilde;&iacute;a</th>
		<td class="vtabla">
		<input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha.select();" size="5" maxlength="5">
		</td>
		<th class="vtabla">Fecha <font size="-2">(ddmmaa)</font></th>
		<td class="vtabla">
		<input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) codpro.select();" value="{fecha}" size="10" maxlength="10">
		</td>
	</tr>
	<tr>
		<th class="vtabla" colspan="2">Producto</th>
		<td class="vtabla" colspan="2">
			<input name="codpro" type="text" class="insert" id="codpro" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia.select();" size="5" maxlength="5">
		</td>
	</tr>
	<tr>
		<td class="vtabla" colspan="4">
			<input name="tipo_consulta" type="radio" value="dia" checked> Día
			<br>
			<input name="tipo_consulta" type="radio" value="acumulado"> Acumulado del mes
		</td>
	</tr>
</table>
<p>
	<input class="boton" type="button" value="Siguiente" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.num_cia.select();
		}
		else if(document.form.fecha.value == "") {
			alert('Debe especificar la fecha');
			document.form.fecha.select();
		}
		else {
				document.form.submit();
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_dia -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Producci&oacute;n y Raya por Turno <br>
      del d&iacute;a {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table class="print" width="100%">
	<tr>
		<th class="print" colspan="2" width="30%">C&oacute;digo y nombre de producto</th>
		<th class="print" width="10%">Unidades Producidas </th>
		<th class="print" width="10%">Raya Ganada </th>
		<th class="print" width="10%">Raya Pagada </th>
		<th class="print" width="10%">Valor de la Producci&oacute;n</th>
		<th class="print" width="10%">Precio de Raya </th>
		<th class="print" width="10%">% de Raya </th>
		<th class="print" width="10%">Precio de Venta </th>
	</tr>
	
	<!-- START BLOCK : turno -->
  	<tr>
		<th class="vprint" colspan="9">{turno}</th>
	</tr>
	<!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vprint" width="5%">{cod_producto}</td>
		<td class="vprint" width="25%">{nombre}</td>
		<td class="print" width="10%">{piezas}</td>
		<td class="rprint" width="10%">{raya_ganada}</td>
		<td class="print" width="10%">&nbsp;</td>
		<td class="rprint" width="10%">{produccion}</td>
		<td class="rprint" width="10%">{precio_raya}</td>
		<td class="rprint" width="10%">{porcentaje_raya}</td>
		<td class="rprint" width="10%">{precio_venta}</td>
	</tr>
	<!-- END BLOCK : fila -->
	<tr>
		<th class="print" colspan="2" width="30%">Total de Turno</th>
		<th class="print_total" width="10%">&nbsp;</th>
		<th class="rprint_total" width="10%">{total_raya_ganada}</th>
		<th class="rprint_total" width="10%">{total_raya_pagada}</th>
		<th class="rprint_total" width="10%">{total_produccion}</th>
		<th class="print_total" colspan="3">&nbsp;</th>
	</tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td colspan="3">&nbsp;</td>
  </tr>
	<!-- END BLOCK : turno -->
	<tr>
		<th class="print" colspan="2" width="30%">Total Panaderia</th>
		<th class="print_total" width="10%">&nbsp;</th>
		<th class="rprint_total" width="10%">{total_raya_ganada}</th>
		<th class="rprint_total" width="10%">{total_raya_pagada}</th>
		<th class="rprint_total" width="10%">{total_produccion}</th>
		<th class="print_total" colspan="3">&nbsp;</th>
	</tr>
</table>
<!-- END BLOCK : listado_dia -->

<!-- START BLOCK : listado_acumulado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Producci&oacute;n Acumulada por Turno <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table class="print" width="100%">
	<tr>
		<th class="print" colspan="2" width="30%">C&oacute;digo y nombre de producto</th>
		<th class="print" width="10%">Unidades Producidas </th>
		<th class="print" width="10%">Raya Ganada </th>
		<th class="print" width="10%">Raya Pagada </th>
		<th class="print" width="10%">Produccion Acumulada </th>
		<th class="print" width="10%">Precio de Raya </th>
		<th class="print" width="10%">% de Raya </th>
		<th class="print" width="10%">Precio de Venta </th>
	</tr>
	
	<!-- START BLOCK : turno_acu -->
  	<tr>
		<th class="vprint" colspan="9">{turno}</th>
	</tr>
	<!-- START BLOCK : fila_acu -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vprint" width="5%">{cod_producto}</td>
		<td class="vprint" width="25%">{nombre}</td>
		<td class="print" width="10%">{piezas}</td>
		<td class="rprint" width="10%">{raya_ganada}</td>
		<td class="print" width="10%">&nbsp;</td>
		<td class="rprint" width="10%">{produccion}</td>
		<td class="rprint" width="10%">{precio_raya}</td>
		<td class="rprint" width="10%">{porcentaje_raya}</td>
		<td class="rprint" width="10%">{precio_venta}</td>
	</tr>
	<!-- END BLOCK : fila_acu -->
	<tr>
		<th class="print" colspan="2" width="30%">Total de Turno</th>
		<th class="print_total" width="10%">&nbsp;</th>
		<th class="rprint_total" width="10%">{total_raya_ganada}</th>
		<th class="rprint_total" width="10%">{total_raya_pagada}</th>
		<th class="rprint_total" width="10%">{total_produccion}</th>
		<th class="print_total" colspan="3">&nbsp;</th>
	</tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td colspan="3">&nbsp;</td>
  </tr>
	<!-- END BLOCK : turno_acu -->
	<tr>
		<th class="print" colspan="2" width="30%">Total Panaderia</th>
		<th class="print_total" width="10%">&nbsp;</th>
		<th class="rprint_total" width="10%">{total_raya_ganada}</th>
		<th class="rprint_total" width="10%">{total_raya_pagada}</th>
		<th class="rprint_total" width="10%">{total_produccion}</th>
		<th class="print_total" colspan="3">&nbsp;</th>
	</tr>
</table>
<!-- END BLOCK : listado_acumulado -->