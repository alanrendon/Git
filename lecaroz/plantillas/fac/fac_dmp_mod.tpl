<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./actualiza_fac_dmp_mod.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">CATÁLOGO DE PRODUCTOS POR PROVEEDOR</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_proveedor}&#8212;{nom_proveedor}</font></strong>
		<input name="num_proveedor" type="hidden" value="{num_proveedor}">
		</td>
	</tr>

</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Presentaci&oacute;n</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Descuento 1 </th>
      <th class="tabla" align="center">Descuento 2 </th>
      <th class="tabla" align="center">Descuento 3 </th>
      <th class="tabla" align="center">I.V.A.</th>
      <th class="tabla" align="center">IEPS</th>
      <th class="tabla" align="center"><input type="checkbox" id="checkall"> Para<br>pedido</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
	  <th class="rtabla">{codmp} <input name="codmp{i}" type="hidden" value="{codmp}">
      <input name="id{i}" type="hidden" value="{id}"></th>
	  <th class="vtabla">{nom_mp}</th>
	  <td class="tabla"><select name="presentacion{i}" id="presentacion{i}" class="insert">
	  	<!-- START BLOCK : presentacion -->
		<option value="{value}"{selected}>{text}</option>
		<!-- END BLOCK : presentacion -->
  	</select></td>
      <td class="tabla"><input name="contenido{i}" type="text" value="{contenido}" size="10" class="insert">      </td>
      <td  class="tabla"><input name="precio{i}" type="text" value="{precio}" size="10" class="insert">      </td>
      <td class="tabla"><input name="desc1{i}" type="text" value="{desc1}" size="10" class="insert">      </td>
      <td class="tabla"><input name="desc2{i}" type="text" value="{desc2}" size="10" class="insert">      </td>
      <td class="tabla"><input name="desc3{i}" type="text" value="{desc3}" size="10" class="insert">      </td>
      <td class="tabla"><input name="iva{i}" type="text" value="{iva}" size="10" class="insert">
      </td>
      <td class="tabla"><input name="ieps{i}" type="text" value="{ieps}" size="10" class="insert">      </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onclick="if (this.checked==true) document.form.para_pedido{i}.value=1; else if(this.checked==false)document.form.para_pedido{i}.value=0;"{checked}><input type="hidden" name="para_pedido{i}" value="{para_pedido}"></td>
    </tr>
	<!-- END BLOCK : rows -->
	<!-- START BLOCK : contador -->
		<input name="cont" type="hidden" value="{cont}">
	<!-- END BLOCK : contador -->
</table>

  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>
<script src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_proveedor.value <= 0) {
			alert('Debe especificar un proveedor');
			document.form.num_proveedor.select();
		}
		else {
				document.form.submit();
			}
	}

	// function borrar() {
	// 	if (confirm("¿Desea borrar el formulario?")) {
	// 		document.form.reset();
	// 		document.form.num_cia.select();
	// 	}
	// 	else
	// 		document.form.num_cia.select();
	// }

	document.id('checkall').addEvent('change', function()
	{
		var status = this.checked;

		$$('input[name^=mod]').set('checked', status).each(function(el, i)
		{
			$$('input[name=para_pedido' + i + ']').set('value', el.checked ? 1 : 0);
		});;
	});

</script>
