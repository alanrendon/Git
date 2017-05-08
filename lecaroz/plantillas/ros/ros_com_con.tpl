
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

<script type="text/javascript" language="JavaScript">
	function valida_registro() {
				document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Comisiones de Rosticer&iacute;as</p>
<form name="form" action="./ros_com_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr>
		<th class="vtabla">Mes </th>
		<td class="vtabla">
<select name="mes" class="insert">
  <option value="1">Enero</option>
  <option value="2">Febrero</option>
  <option value="3">Marzo</option>
  <option value="4">Abril</option>
  <option value="5">Mayo</option>
  <option value="6">Junio</option>
  <option value="7">Julio</option>
  <option value="8">Agosto</option>
  <option value="9">Septiembre</option>
  <option value="10">Octubre</option>
  <option value="11">Noviembre</option>
  <option value="12">Diciembre</option>
</select>		</td>
	    <th class="vtabla">Año </th>
	    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onChange="actualiza_fecha1()" value="{anio}" size="10" maxlength="10"></td>
	</tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : comisiones -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
	<tr>
		<td class="print_encabezado" align="center">COMISIONES DE LAS ROSTICERIAS</td>
	</tr>
	<tr>
		<td class="print_encabezado" align="center">CORRESPONDIENTES AL MES DE {mes} de {anio} </td>
	</tr>
	
</table>
<br>
<table width="100%">
	<tr>
		<th class="print" align="center" colspan="2">Rosticeria</th>
	    <th class="print" align="center">Pollos vendidos </th>
	    <th class="print" align="center">Piernas de pavo vendidas </th>
	    <th class="print" align="center">Comisi&oacute;n</th>
	    <th class="print" align="center">Pescuezos vendidos </th>
	    <th class="print" align="center">Alas vendidas </th>
	    <th class="print" align="center">Comisi&oacute;n</th>
	    <th class="print" align="center">Total Comisi&oacute;n </th>
	</tr>
<!-- START BLOCK : rows -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="print" align="center">{num_cia}</td>
		<td class="vprint">{nombre_cia}</td>
	    <td class="print" align="center">{pollos}</td>
	    <td class="print" align="center">{pavo}</td>
	    <td class="print" align="center">{comision_pollos}</td>
	    <td class="print" align="center">{pescuezos} </td>
	    <td class="print" align="center">{alas}</td>
	    <td class="print" align="center">{comision_pescuezo}</td>
	    <td class="print" align="center">{total}</td>
	</tr>
<!-- END BLOCK : rows -->
<!-- START BLOCK : totales -->
	<tr>
		<th class="print" align="center" colspan="2">TOTALES</th>
	    <th class="print" align="center">{total_pollos}</th>
	    <th class="print" align="center">{total_pavo}</th>
	    <th class="print" align="center">{total_comision_pollos}</th>
	    <th class="print" align="center">{total_pescuezos} </th>
	    <th class="print" align="center">{total_alas}</th>
	    <th class="print" align="center">{total_comision_pescuezo}</th>
	    <th class="print" align="center">{total_general}</th>
	</tr>
<!-- START BLOCK : totales -->
</table>
</td>
</tr>
</table>


<br>
<!-- END BLOCK : comisiones -->
<br>
