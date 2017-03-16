<!-- START BLOCK : obtener_datos -->
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Porcentajes</P>
<form name="form" method="get" action="./bal_comp_list.php">
<input name="temp" type="hidden">
  <table class="tabla">
  <tr class="tabla">
  <th class="tabla" colspan="2">Mes 
		<select name="mes" size="1" class="insert" id="mes">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		
    A&ntilde;o
    <input name="anio" type="text" class="insert" value="{anio_actual}" size="5"> </th>
  </tr>
</table>
  <p>
  <input type="button" name="enviar" class="boton" value="Consultar" onclick='document.form.submit();'>
  </p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_todos -->
<table width="100%"  height="99%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. <br> 
COMPARATIVO MENSUAL DE {mes} del {anio} </p>

	<table class="print" width="100%">
	  <tr class="print">
		<th scope="col" class="print" width="30%">Cia</th>
		<th scope="col" class="print">Distribuci&oacute;n</th>
		<th scope="col" class="print">General</th>
	    <th scope="col" class="print">Diferencia</th>
	    <th scope="col" class="print">&nbsp;</th>
	    <th scope="col" class="print">Efectivo</th>
	    <th scope="col" class="print">Dep&oacute;sitos</th>
	    <th scope="col" class="print">Diferencia</th>
	  </tr>
		<!-- START BLOCK : rows1 -->
	  <tr class="print" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<th class="vprint" width="30%">
		{num_cia}&nbsp;&nbsp;{nom_cia}
		</th>
		<td class="rlistado">{distribucion}</td>
		<td class="rlistado">{general}</td>
	    <td class="rlistado">{diferencia}</td>
	    <td class="rlistado">&nbsp;</td>
	    <td class="rlistado">{efectivo}</td>
	    <td class="rlistado">{deposito}</td>
	    <td class="rlistado">{diferencia1}</td>
	  </tr>
		<!-- END BLOCK : rows1 -->
			<!-- START BLOCK : totales -->
			<tr class="print">
			<td class="print" width="30%"><strong>
			TOTAL
			</strong></td>
			<td class="rprint">
			{total_distribucion}</td>

			<td class="rprint">{total_general}</td>

			<td class="rprint">{total_diferencia}</td>
			<td class="rprint">&nbsp;</td>
			<td class="rprint">{total_efectivo}</td>
			<td class="rprint">{total_deposito}</td>
			<td class="rprint">{total_diferencia1}</td>
			</tr>
			<!-- END BLOCK : totales -->
	</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_todos -->
