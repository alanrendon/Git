<table class="tabla_captura">
  <tr>
	<th scope="col">Compa&ntilde;&iacute;a</th>
	<th scope="col">Fecha</th>
	<th scope="col">Comprobante</th>
	<th scope="col">Total</th>
	<th scope="col"><input name="checkall" type="checkbox" id="checkall" /></th>
	<th scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
  </tr>
  <!-- START BLOCK : row -->
  <tr class="linea_{color}">
	<td>{num_cia} {nombre_cia} </td>
	<td align="center">{fecha}</td>
	<td align="center"><a name="{comprobante}" id="{comprobante}" class="comprobante" title="{comprobante}">{comprobante}</a></td>
	<td align="right"><a class="depositos" title="{depositos}">{total}</a></td>
	<td align="center"><input name="comprobante[]" type="checkbox" id="comprobante" value="{comprobante}" /></td>
	<td><img src="imagenes/pencil16x16.png" name="mod" width="16" height="16" id="mod" title="{comprobante}" /><img src="imagenes/delete16x16.png" name="del" width="16" height="16" id="del" title="{comprobante}" /></td>
  </tr>
  <!-- END BLOCK : row -->
</table>
<p>
  <input name="actualizar" type="button" id="actualizar" value="Obtener dep&oacute;sitos de panader&iacute;as" />
  &nbsp;&nbsp;
  <input name="registrar" type="button" id="registrar" value="Registrar dep&oacute;sitos en sistema" />
</p>