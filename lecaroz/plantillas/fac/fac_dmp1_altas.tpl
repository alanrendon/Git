<!-- tabla catalogo_productos_proveedor -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./insert_fac_dmp_altas.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="tabla" scope="row"><font size="+1">PROVEEDOR</font></th>
    <td>
	<!-- START BLOCK : prov_ok -->
	<font size="+1">{num_proveedor1}&#8212;{nombre}</font>
	<!-- END BLOCK : prov_ok -->
	<!-- START BLOCK : prov_error -->
	<font size="+1">{num_proveedor1}&#8212;{nombre}</font>
	<!-- END BLOCK : prov_error -->
	</td>
  </tr>
</table>
<table class="tabla">
    <tr>
      <th class="tabla" align="center">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Presentaci&oacute;n</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Descuento 1 </th>
      <th class="tabla" align="center">Descuento 2 </th>
      <th class="tabla" align="center">Descuento 3 </th>
      <th class="tabla" align="center">I.V.A.</th>
      <th class="tabla" align="center">IEPS</th>
    </tr>
	<!-- START BLOCK : rows -->
	<input name="num_proveedor{i}" type="hidden" id="num_proveedor{i}" value="{num_proveedor}">
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
      <td class="vtabla" align="center">
        <input name="codmp{i}" type="hidden" class="insert" id="codmp{i}" maxlength="4" value="{codmp}">
		<!-- START BLOCK : rows_ok -->
		{codmp1}&#8212; {nombre1} 
		<!-- END BLOCK : rows_ok -->
		<!-- START BLOCK : rows_error -->
		<font color="#FF0000">
		{codmp1}&#8212; {nombre1} 
		</font>
		<!-- END BLOCK : rows_error -->
	  </td>
      <td class="tabla" align="center"><input name="presentacion{i}" type="hidden" id="presentacion{i}" value="{presentacion}" />
      	{tipo_presentacion}</td>
      <td class="tabla" align="center">
        <input name="contenido{i}" type="hidden" class="insert" id="contenido{i}" size="9" maxlength="7" value="{contenido}">
{contenido}      </td>
      <td  class="tabla" align="center">
        <input name="precio{i}" type="hidden" class="insert" id="precio{i}" size="9" maxlength="6" value="{precio}">
      {precio}</td>
      <td class="tabla" align="center">
        <input name="desc1{i}" type="hidden" class="insert" id="desc1{i}" size="9" maxlength="6" value="{desc1}">
{desc1}      </td>
      <td class="tabla" align="center">
        <input name="desc2{i}" type="hidden" class="insert" id="desc2{i}" size="9" maxlength="6" value="{desc2}">
        {desc2}
      </td>
      <td class="tabla" align="center">
        <input name="desc3{i}" type="hidden" class="insert" id="desc3{i}" size="9" maxlength="6" value="{desc3}">
        {desc3}
      </td>
      <td class="tabla" align="center">
        <input name="iva{i}" type="hidden" class="insert" id="iva{i}" size="9" value="{iva}">
        {iva}
      </td>
      <td class="tabla" align="center">
        <input name="ieps{i}" type="hidden" class="insert" id="ieps{i}" size="9" maxlength="6" value="{ieps}">
        {ieps}<span class="vtabla">
        <input name="num_proveedor{i}" type="hidden" class="insert" id="num_proveedor{i}2" maxlength="4" value="{num_proveedor}">
      </span>      </td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Regresar" onclick='parent.history.back()'>
	<!-- START BLOCK : captura -->  
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
	<!-- END BLOCK : captura --> 
 
  </p>
</form>
</td>
</tr>
</table>
