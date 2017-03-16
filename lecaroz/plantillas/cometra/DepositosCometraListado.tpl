<form name="Datos" class="FormStyles" id="Datos">
<table class="tabla_captura">
  <tr class="enlace">
	<th align="left" scope="row">Banco</th>
	<td><select name="banco" class="font14 bold" id="banco">
	  <option value="NULL"{0}></option>
	  <option value="1"{1}>BANORTE</option>
	  <option value="2"{2}>SANTANDER</option>
	</select>
	</td>
  </tr>
</table>
<br>
<table class="tabla_captura">
  <tr>
	<th scope="col">Compa&ntilde;&iacute;a</th>
	<th scope="col">Fecha</th>
	<th scope="col">Comprobante</th>
	<th scope="col">Total</th>
	<th scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
  </tr>
  <!-- START BLOCK : row -->
  <tr class="linea_{color}">
	<td>{num_cia} {nombre_cia} </td>
	<td align="center">{fecha}</td>
	<td align="center"><a name="{comprobante}" id="{comprobante}" class="comprobante" title="{comprobante}">{comprobante}</a></td>
	<td align="right"><a class="depositos" title="{depositos}">{total}</a></td>
	<td><img src="imagenes/pencil16x16.png" name="mod" width="16" height="16" id="mod" title="{comprobante}" /><img src="imagenes/delete16x16.png" name="del" width="16" height="16" id="del" title="{comprobante}" /></td>
  </tr>
  <!-- END BLOCK : row -->
</table>
<p>
  <input name="nuevo" type="button" id="nuevo" value="Empezar un nuevo d&iacute;a" />
</p>
</form>