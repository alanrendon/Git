<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

	
<p class="title">Registro de Facturas de Rosticerias</P>
<form name="form" method="post" action="insert_ros_fac_cap.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">

  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Fecha de pago </th>
    </tr>

    <tr>
      <td class="tabla" align="center">
<!-- START BLOCK : cia_ok -->
<font size="+1">
<strong>{num_cia1}&#8212;{nombre_cia}</strong>
</font>
	<!-- END BLOCK : cia_ok -->

	<!-- START BLOCK : cia_error -->
	<font  size="+1" color="#FF0000">
<strong>{num_cia1}&#8212;{nombre_cia}</strong>
	</font>
	<!-- END BLOCK : cia_error -->

</td>
      <td class="tabla" align="center">
<!-- START BLOCK : pro_ok -->
<font size="+1">
{num_proveedor1}&#8212;{nom_proveedor}
</font>
	<!-- END BLOCK : pro_ok -->

	<!-- START BLOCK : pro_error -->
	<font  size="+1" color="#FF0000">
{num_proveedor1}&#8212;{nom_proveedor}
	</font>
	<!-- END BLOCK : pro_error -->

</td>
      <td class="tabla" align="center">
	<!-- START BLOCK : fac_ok -->
	<font size="+1">
	{num_fac1}
	</font>      
	  <!-- START BLOCK : fac_ok -->
	  <!-- START BLOCK : fac_error -->
	<font size="+1" color="#FF0000">
	{num_fac1}
	</font>      
	  <!-- START BLOCK : fac_error -->
</td>
      <td class="tabla" align="center">
<font size="+1">{fecha_mov1}</font>      </td>
      <td class="tabla" align="center">
<font size="+1">
      {fecha_pago1}
</font>	  </td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th width="306" align="center" class="tabla">C&oacute;digo de Materia Primas</th>
      <th width="112" align="center" class="tabla">Cantidad</th>
      <th width="89" align="center" class="tabla">Kilos</th>
      <th width="101" align="center" class="tabla">Precio unitario </th>
      <th width="88" align="center" class="tabla">Total</th>
    </tr>
    <!-- START BLOCK : rows -->
    <input name="num_cia{var}" type="hidden" class="insert" size="5" value="{num_cia}">
    <input name="codmp{var}" type="hidden" class="insert" size="5" value="{codmp}">
    <input name="num_proveedor{var}" type="hidden" class="insert" size="5" value="{num_proveedor}">
    <input name="num_fac{var}" type="hidden" class="insert" size="5" value="{num_fac}">
    <input name="fecha_mov{var}" type="hidden" class="insert" size="5" value="{fecha_mov}">
    <input name="fecha_pago{var}" type="hidden" class="insert" id="fecha_pago{i}" value="{fecha_mov}" size="5">
    <input name="porcentaje13{var}" type="hidden" class="insert" size="5" value="{porcentaje13}">
    <input name="porcentaje795{var}" type="hidden" class="insert" size="5" value="{porcentaje795}">
	<input name="precio_unidad{var}" type="hidden" class="insert" size="5" value="{precio_unidad}">
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla" align="left">
        <!-- START BLOCK : mp_ok -->
      <strong>{codmp1}&#8212;{nom_mp}</strong>
      <!-- END BLOCK : mp_ok -->
      <!-- START BLOCK : mp_error -->
      <font color="#FF0000"> 
	  <strong>{codmp1}&#8212;{nom_mp}</strong> </font>
      <!-- END BLOCK : mp_error -->
      </td>
      <td class="tabla" align="center">
        <input name="cantidad{var}" type="hidden" class="insert" size="5" value="{cantidad}">
		<!-- START BLOCK : cantidad_ok -->
        <strong>{cantidad1}</strong>
		<!-- END BLOCK : cantidad_ok -->
		<!-- START BLOCK : cantidad_error -->
	    <strong><font color="#FF0000">{cantidad1} </font></strong>
		<!-- END BLOCK : cantidad_error -->
		</td>
      <td class="tabla" align="center">
        <input name="kilos{var}" type="hidden" class="insert" size="5" value="{kilos}">
		
		<!-- START BLOCK : kilos_ok -->
		<strong>{kilos1}</strong>
		<!-- END BLOCK : kilos_ok -->
		<!-- START BLOCK : kilos_mod -->
		<font color="#0000FF"><strong>{kilos1}</strong></font>
		<!-- END BLOCK : kilos_mod -->
		
	  </td>
      <td class="tabla" align="center">
        <input name="precio{var}" type="hidden" class="insert" size="5" value="{precio}"><strong>
      {precio1}</strong> </td>
      <th class="tabla" align="center">
        <input name="total{var}" type="hidden" class="insert" size="5" value="{total1}">
        <strong>{total2}</strong> </th>
    </tr>
    <!-- END BLOCK : rows -->
  <th class="tabla" colspan="4" align="center"><b>Total</b></th>
      <th class="tabla" align="center">
        <!-- START BLOCK : total_ok -->
        <font size="+2">{total}</font></th>
      <!-- END BLOCK : total_ok -->
      <!-- START BLOCK : total_error -->
      <font size="+2">{total}</font>
      <!-- END BLOCK : total_error -->
           
  </table>
  <!-- START BLOCK : control_pollo -->
  <p>
  	<font color="#0000FF"><strong>{aviso}</strong></font>
  </p>
  <!-- END BLOCK : control_pollo -->
  <p>
  <!-- START BLOCK : boton -->
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Continuar" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
	<!-- END BLOCK : boton -->
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Regresar" onclick="document.location='./ros_fac_cap.php?compania={cia}&num_pro={num_pro}'">
    
</p>
</form>
</td>
</tr>
</table>