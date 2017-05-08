<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.anio==""){
			alert("falta el año");
			document.form.anio.select();
			}
		else if (document.form.tipo_con.value==0 && document.form.compania.value==""){
			alert("falta la compañía");
			document.form.compania.select();
			}
		else document.form.submit();

	}

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Efectivos de Rosticerias </p>
<form name="form" action="./ros_efe_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr>
		<th class="vtabla">Mes</th>
	  <td class="vtabla">
		<input class="insert" name="mes_oculto" type="hidden" id="mes_oculto" size="10" maxlength="10">
		
		<select name="mes" size="1" class="insert" id="mes">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		
		</td>
	    <th class="vtabla">Año</th>
	    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onChange="actualiza_fecha1()" value="{anio}" size="10" maxlength="10">
		<input name="tipo_con" type="hidden" class="nombre" value="0" size="3">
		</td>
	</tr>
	<tr>
		<td class="vtabla" colspan="2">
			<input name="tipo_cia" type="radio" value="cia"  onChange="document.form.tipo_con.value=0;"checked> 
			Compañía 
			<input class="insert" name="compania" type="text" id="compania" size="10" maxlength="10">
		  <br>
            <input name="tipo_cia" type="radio" value="todas" onChange="document.form.tipo_con.value=1;">			
          Todas</td>
		  
		  <td class="vtabla" colspan="2">
		  <input name="totales" type="radio" value="desgloce" checked> Desgloce <br>
		  <input name="totales" type="radio" value="total"> Total
		  
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

<!-- START BLOCK : listado_dia -->

<!-- START BLOCK : rosticeria -->
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<table width="100%">
	<tr>
		<td class="print_encabezado"align="center">REPORTE DE EFECTIVOS DE ROSTICERIAS </td>
	</tr>
	<tr>
		<td class="print_encabezado" align="center">DEL MES DE {mes} DEL {anio}</td>
	</tr>
	
</table>
<br>
<table width="100%">
  	<tr>
		<th class="vprint" colspan="4"><strong> <font size="2">{num_cia}&#8212;{nombre_cia}</font></strong></th>
	</tr>
	
	<tr>
		<th class="print" width="20%">Dia</th>
		
		<th class="print" width="20%">Venta</th>
		<th class="print" width="20%">Gastos</th>
		<th class="print" width="20%">Efectivo </th>
	</tr>
	<!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vprint" width="20%">{fecha}</td>
		
		<td class="print" width="20%">{venta}</td>
		<td class="print" width="20%">{gastos}</td>
		<td class="print" width="20%">{efectivo}</td>
	</tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : totales -->
	<tr>
		<th class="print" width="20%"><font size="1"></font>Totales</th>
		<th class="print_total" width="20%"><font size="1">{total_venta}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_gastos}</font></th>
		<th class="print_total" width="20%"><font size="1">{total_efectivo}</font></th>
		</tr>
	<tr>
	  <th colspan="3" class="print">Comisi&oacute;n</th>
	  <th class="print_total">{comision}</th>
  </tr>
  <!--<tr><td height="20" colspan="4"></td></tr>-->
	<!-- END BLOCK : totales -->
</table>
<br style="page-break-after:always;">
	<!-- END BLOCK : rosticeria -->
	<table align="center">
	<!-- START BLOCK : encabezado_solo_totales -->
	<tr>
		<th class="print" width="40%"><font size="1">Rosticería</font></th>
		<td class="print" width="20%"><font size="1">Total Venta</font></td>
		<td class="print" width="20%"><font size="1">Total Gastos</font></td>
		<td class="print" width="20%"><font size="1">Total Efectivo</font></td>
  	<tr>
	<!-- END BLOCK : encabezado_solo_totales -->	
	<!-- START BLOCK : solo_totales -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<th class="vprint" width="40%" ><font size="1">{num_cia}&#8212;{nombre_cia}</font></th>
		<td class="print" width="20%"><font size="1">{total_venta}</font></td>
		<td class="print" width="20%"><font size="1">{total_gastos}</font></td>
		<td class="print" width="20%"><font size="1">{total_efectivo}</font></td>
  	<tr>
	<!-- END BLOCK : solo_totales -->
	</tr>
	<!-- START BLOCK : totalGeneral -->
	<tr>
		<th class="print" width="20%">Total General </th>
		
		<th class="print_total" width="20%">{totalgral_venta}</th>
		<th class="print_total" width="20%">{totalgral_gastos}</th>
		<th class="print_total" width="20%">{totalgral_efectivo}</th>
	</tr>
	<!-- END BLOCK : totalGeneral -->
	<!-- START BLOCK : solo_totalGeneral -->
	<tr>
		<th class="print" width="40%"><font size="1">Total General</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_venta}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_gastos}</font></th>
		<th class="print_total" width="20%"><font size="1">{totalgral_efectivo}</font></th>
	</tr>
	<!-- END BLOCK : solo_totalGeneral -->
</table>

<!-- END BLOCK : listado_dia -->

