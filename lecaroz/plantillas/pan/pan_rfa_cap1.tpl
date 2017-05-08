<!-- tabla movimiento_gastos menu panaderias -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="insert_pan_rfa_cap2.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<p>&nbsp;</p>
<table class="tabla">
    <tr>
      <th class="vtabla">compa&ntilde;ia</th>
      <td class="vtabla"><font size="+2">{num_cia1}&#8212;{nombre_cia} </font> </td>
      <th class="vtabla">fecha</th>
      <td class="vtabla"><font size="+2">{fecha1}</font></td>
    </tr>
  </table><br>
  <table class="tabla">
        <tr>
          <th class="tabla">No. factura</th>
          <th class="tabla">Expendio</th>
          <th class="tabla">Kgs</th>
          <th class="tabla">P/unidad</th>
          <th class="tabla">Pan</th>
          <th class="tabla">Base</th>
          <th class="tabla">A cuenta</th>
          <th class="tabla">Devolucion Base </th>
          <th class="tabla">Resta</th>
          <th class="tabla">Fecha entrega</th>
          <th class="tabla">Total factura </th>
          <th class="tabla">Pastillaje</th>
          <th class="tabla">Otros</th>
        </tr>
        
		<!-- START BLOCK : rows -->
	<input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}"  size="1" maxlength="1" value="{num_cia}">
	<input name="fecha{i}" type="hidden" class="insert" id="fecha{i}"  size="10" value="{fecha}">
	<input name="letra_folio{i}" type="hidden" class="insert" id="1etra_folio{i}"  size="1" maxlength="1" value="{let_remi}">
	<input name="num_remi{i}" type="hidden" class="insert" id="num_remi{i}"  size="10" value="{num_remi}">
	
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="vtabla" align="center">
		  
  		  <!-- START BLOCK : rows_ok -->
		  {let_remi1}&nbsp;{num_remi}
		  <b>{numero}</b>
		  <!-- END BLOCK : rows_ok -->
		  
		  <!-- START BLOCK : rows_error_bloc -->
		  <font color="#FF33CC"><b>
		  {let_remi1}
		  &nbsp;
		  {num_remi}
		  </b>
		  <b>{numero}</b>
		  </font>
		  <!-- END BLOCK : rows_error_bloc -->
          
		  <!-- START BLOCK : rows_error_nota -->
		  <font color="#FF0000"><b>
		  {let_remi1}
		  &nbsp;
		  {num_remi}
		  </b>
		  <b>{numero}</b>
		  </font>
		  <!-- END BLOCK : rows_error_nota -->
		  <!-- START BLOCK : rows_error_brincada -->
		  <font color="#0000CC"><b>
		  {let_remi1}
		  &nbsp;
		  {num_remi}
		  </b>
		  <b>{numero}</b>
		  </font>
		  <!-- END BLOCK : rows_error_brincada -->

		  <!-- START BLOCK : rows_error_repetida -->
		  <font color="#336600"><b>
		  {let_remi1}
		  &nbsp;
		  {num_remi}
		  </b>
		  <b>{numero}</b>
		  </font>
		  <!-- END BLOCK : rows_error_repetida -->


		  </td>
          <td class="vtabla" align="center"><input name="idexpendio{i}" type="hidden" class="insert" id="idexpendio{i}" size="4" value="{idexpendio}">{idexpendio}  {expendio}</td>
          <td class="tabla" align="center"><input name="kilos{i}" type="hidden" class="insert" id="kilos{i}" size="10" value="{kilos}">
          {kilos1}</td>
          <td class="tabla" align="center"><input name="precio_unidad{i}" type="hidden" class="insert" id="precio_unidad{i}" size="10" value="{precio_unidad}">
          {precio_unidad1}</td>
          <td class="tabla" align="center"><input name="otros{i}" type="hidden" class="insert" id="otros{i}" size="10" value="{otros}">
          {otros1}</td>
          <td class="tabla" align="center"><input name="base{i}" type="hidden" class="insert" id="base{i}" size="10" value="{base}">
          {base1}</td>
          <td class="tabla" align="center"><input name="cuenta{i}" type="hidden" class="insert" id="cuenta{i}" size="10" value="{cuenta}">
          {cuenta1}</td>
          <td class="tabla" align="center"><input name="dev_base{i}" type="hidden" class="insert" id="dev_base{i}" size="10" value="{dev_base}">
          {dev_base1}</td>
          <td class="tabla" align="center"><input name="resta{i}" type="hidden" class="insert" id="resta{i}" size="10" value="{resta}">  
		  <!-- START BLOCK : resta1 -->
		  {resta1}
		  <input name="resta_pagar{i}" type="hidden" class="insert" id="resta_pagar{i}" size="10" value="0">
		  <!-- END BLOCK : resta1 -->
		  <!-- START BLOCK : resta2 -->
		  <font size="+1">{resta2}</font>
  		  <input name="resta_pagar{i}" type="hidden" class="insert" id="resta_pagar{i}" size="10" value="{resta3}">
		  <!-- END BLOCK : resta2 -->
		  </td>
          <td class="tabla" align="center"><input name="fecha_entrega{i}" type="hidden" class="insert" id="fecha_entrega{i}" size="10" value="{fecha_entrega}">{fecha_entrega}</td>
		  <td class="tabla" align="center"><font size="+1">{total_factura}</font>
	      <font size="+1">{ok}
	      <input name="total_factura{i}" type="hidden" class="insert" id="total{i}" size="10" value="{total}">
	      </font></td>
		  <td class="tabla" align="center">
		    {pastillaje1}
		      <input name="pastillaje{i}" type="hidden" class="insert" id="pastillaje{i}" size="10" value="{pastillaje}">
		  </td>
		  <td class="tabla" align="center">
		    <input name="otros_efectivos{i}" type="hidden" class="insert" id="otros_efectivos{i}" size="10" value="{otros_efectivos}">
            <input name="descuento{i}" type="hidden" class="insert" id="descuento{i}" size="10" value="{descuento}">
			<input name="bloc{i}" type="hidden" id="bloc{i}" value="{bloc}">
			<input name="tipo{i}" type="hidden" id="tipo{i}" value="{tipo}">
            {otros_efectivos1}		  </td>
		</tr>
		<!-- END BLOCK : rows -->
	</table>
	<br>
	<table class="tabla">
	  <tr>
		<td class="vtabla">Error Nota</td>
		<td bgcolor="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	  </tr>
	  <tr>
		<td class="vtabla">Error Block</td>
		<td bgcolor="#FF33CC">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	  </tr>
	  <tr>
		<td class="vtabla">Error Nota brincada</td>
		<td bgcolor="#0000CC">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	  </tr>
	  <tr>
		<td class="vtabla">Error Nota repetida</td>
		<td bgcolor="#336600">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	  </tr>

	</table>	
	
	<br>
	<table class="vtabla">
		<tr>
			<th class="tabla" align="center"><font size="+1">Venta en puerta</font> </th>
			<th class="tabla" align="center"><font size="+1">Abono expendio</font></th>
			<th class="tabla" align="center"><font size="+1">Bases</font></th>
			<th class="tabla" align="center"><font size="+1">Devolucion de bases</font> </th>
		</tr>
		<!-- START BLOCK : vencidas -->
		<tr>
			<td class="tabla" align="center" colspan="4"><font size="+2" color="#FF0000">No puedes meter notas, ya que tienes pendientes de pago</font></td>
		</tr>
		<!-- END BLOCK : vencidas -->
		<!-- START BLOCK : totales -->
		<tr>
		  <td class="tabla" align="center"><font size="+2">{total_vta_pta}</font></th>
		  <td class="tabla" align="center"><font size="+2">{total_ab_exp}</font></th>
		  <td class="tabla" align="center"><font size="+2">{total_base}</font></th>
		  <td class="tabla" align="center"><font size="+2">{total_dev_base}</font></th>
		 <!-- END BLOCK : totales -->
		</tr>
    </table>
		

  <p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input name="enviar" type="button" class="boton" id="enviar" onclick="document.location='./pan_rfa_cap.php'" value="Regresar">
    <!-- START BLOCK : continuar -->
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
    <input type="button" class="boton" value="Capturar datos" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
	<!-- END BLOCK : continuar -->
</p>
</form>
</td>
</tr>
</table>