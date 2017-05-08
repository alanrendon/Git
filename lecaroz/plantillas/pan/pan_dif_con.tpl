
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
<form name="form" action="./pan_dif_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr>
		<th class="vtabla">Mes </th>
		<td class="vtabla">
<select name="mes" class="insert">
	<!-- START BLOCK : mes -->
  <option value="{mes}" {selected}>{nombre_mes}</option>
  <!-- END BLOCK : mes -->
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
      <input name="tipo_con2" type="hidden" class="nombre" value="0" size="3"></td>
	</tr>
	<tr>
	  <th class="vtabla" colspan="3">Tipo</th>
	  <td class="vtabla"><input name="tipo" type="radio" id="radio" value="1" checked="checked" />
	    Controlados<br />
	    <input type="radio" name="tipo" id="radio2" value="2" />
	    No controlados</td>
	</tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="valida_registro();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : diferencias -->
<script language="JavaScript" type="text/JavaScript">
	function aux(num_cia,codmp,mes,anio) {
		var window_aux = window.open("./pan_miniaux.php?num_cia="+num_cia+"&codmp="+codmp+"&mes="+mes+"&anio="+anio,"miniaux","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
		window_aux.moveTo(0,0);
	}
</script>

<!-- START BLOCK : companias -->


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. Y C.V. </P>
<p class="title" style="font-size:12pt;">DIFERENCIAS EN MATERIA PRIMA CORRESPONDIENTES AL MES DE {mes} de {anio}</p>
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
		<th class="print" align="center" onClick="aux({num_cia},{codmp},{mes},{anio})">{codmp}</th>
		<th class="print" align="center" onClick="aux({num_cia},{codmp},{mes},{anio})"><div align="left">{nombre_mp}</div></th>
	    <td class="print" align="center">{costo_unitario}</td>
	    <td class="print" align="center">{existencia}</td>
	    <td class="print" align="center">{inventario}</td>
	    <td class="print" align="center"><font color="#FF0000">{faltante_unidad}</font></td>
	    <td class="print" align="center"><font color="#FF0000">{faltante_valor}</font></td>
	    <td class="print" align="center"><font color="#0000FF">{sobrante_unidad}</font></td>
	    <td class="print" align="center"><font color="#0000FF">{sobrante_valor}</font></td>
	</tr>
<!-- END BLOCK : rows -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="print_total" align="center" colspan="5" rowspan="2">TOTAL</th>
	  <th class="print" align="center"></th>
	  <td class="print"align="center"> <font size="1" color="#FF0000"><strong>{total_faltante}</strong></font></td>
	  <th class="print" align="center"></th>
  	  <td class="print" align="center"><font size="1" color="#0000FF"><strong>{total_sobrante}</strong></font></td>
  </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="print" align="center"></th>
	  <td class="print"align="center"><strong><font size="1" color="#FF0000">{dif_fal}</font></strong></td>
	  <th class="print" align="center"></th>
  	  <td class="print" align="center"><strong><font size="1" color="#0000FF">{dif_sob}</font></strong></td>
  </tr>
</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : companias -->


<!-- END BLOCK : diferencias -->

