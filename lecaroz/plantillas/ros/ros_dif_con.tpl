
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

<script type="text/javascript" language="JavaScript">
	function valida_registro() {
	if(document.form.tipo_con2.value == 0 && document.form.num_cia.value =="")
		{
			alert("Necesita un numero de compañía");
			document.form.num_cia.focus();
			return;
		}
	
		else	document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Diferencias</p>
<form name="form" action="./ros_dif_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="print">
	<tr>
		<th class="vtabla">Mes </th>
		<td class="vtabla">
<select name="mes" class="insert">
  <option value="1" selected>Enero</option>
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
	<tr>
	  <td class="vtabla" colspan="4">
	  <input name="tipo_con" type="radio" value="0" onChange="form.bandera.value=0; form.tipo_con2.value=1;">
	  Todas las compa&ntilde;&iacute;as <br>
	  <input name="tipo_con" type="radio" onChange="form.bandera.value=1; form.tipo_con2.value=0;" value="1" checked>
	  Compañía	
	  <input name="num_cia" type="text" class="insert" id="num_cia" size="4" maxlength="3">
	  <input name="bandera" type="hidden" class="insert" id="bandera" size="4" disabled value="0">
      <input name="tipo_con2" type="hidden" class="nombre" value="0" size="3">
</td>
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

<!-- START BLOCK : diferencias -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<table class="print">
	<tr>
		<td class="print_encabezado" align="center">DIFERENCIAS EN MATERIA PRIMA </td>
	</tr>
	<tr>
		<td class="print_encabezado" align="center">CORRESPONDIENTES AL MES DE {mes} de {anio} </td>
	</tr>
</table>
<br>

<!-- START BLOCK : companias -->
<table class="print" width="800">
	<tr>
		<th class="print_encabezado" align="center" colspan="9">{num_cia}&#8212;{nombre_cia}</th>
	</tr>

	<tr>
		<th class="print" align="center" colspan="2" rowspan="2" width="250">Materia Prima </th>
	    <th class="print" align="center" rowspan="2" width="110">Costo Unitario </th>
	    <th class="print" align="center" rowspan="2" width="110">Existencia c&oacute;mputo </th>
	    <th class="print" align="center" rowspan="2" width="110">Existencia F&iacute;sica </th>
	    <th class="print" align="center" colspan="2" width="110">Faltantes</th>
	    <th class="print" align="center" colspan="2" width="110">Sobrantes</th>
	</tr>
	<tr>
	    <th class="print" align="center">Unidades </th>
	    <th class="print" align="center">Valores</th>
	    <th class="print" align="center">Unidades </th>
	    <th class="print" align="center">Valores</th>
	</tr>

<!-- START BLOCK : rows -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<th class="print" align="center">{codmp}</th>
		<th class="print" align="center"><div align="left">{nombre_mp}</div></th>
	    <td class="print" align="center">{costo_unitario}</td>
	    <td class="print" align="center">{existencia}</td>
	    <td class="print" align="center">{inventario}</td>
	    <td class="print" align="center">{faltante_unidad} </td>
	    <td class="print" align="center">{faltante_valor}</td>
	    <td class="print" align="center">{sobrante_unidad}</td>
	    <td class="print" align="center">{sobrante_valor}</td>
	</tr>
<!-- END BLOCK : rows -->

	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="print_total" align="center" colspan="5" rowspan="2">TOTAL</th>
	  <th class="print" align="center"></th>
	  <td class="print"align="center"> <font size="1"><strong>{total_faltante}</strong></font></td>
	  <th class="print" align="center"></th>
  	  <td class="print" align="center"><font size="1"><strong>{total_sobrante}</strong></font></td>
  </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="print" align="center"></th>
	  <td class="print"align="center"><strong><font size="1">{dif_fal}</font></strong></td>
	  <th class="print" align="center"></th>
  	  <td class="print" align="center"><strong><font size="1">{dif_sob}</font></strong></td>
  </tr>

</table>
<br>
<!-- END BLOCK : companias -->
</td>
</tr>
</table>
<!-- END BLOCK : diferencias -->

