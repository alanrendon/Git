<form name="Result" class="formulario" id="Result">
<table class="tabla_captura">
	<tr>
	  <th colspan="6" style="font-size:14pt;"><input name="banco" type="hidden" id="banco" value="{banco}" />
      {nombre_banco}</th>
    </tr>
	<tr>
	  <th>&nbsp;</th>
	  <th>Compa&ntilde;&iacute;a</th>
	  <th>Fecha</th>
	  <th>C&oacute;digo</th>
	  <th>Concepto </th>
	  <th>Importe</th>
    </tr>
	<!-- START BLOCK : row -->
	<tr id="row" class="linea_{color}">
	  <td><input name="id_{tipo}[]" type="checkbox" class="checkbox" id="id_{tipo}" value="{id}" checked="checked" /></td>
	  <td>{num_cia} {nombre_cia}</td>
	  <td align="center">{fecha}</td>
	  <td align="center">{cod_mov}</td>
	  <td>{concepto}</td>
	  <td align="right" style="color:#{color_importe}">{importe}</td>
    </tr>
	<!-- END BLOCK : row -->
	<tr>
	  <th colspan="5" align="right">Total</th>
	  <th align="right">{total}</th>
    </tr>
  </table>
  <p>
	<input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input name="cambiar" type="button" class="boton" id="cambiar" value="Cambiar" />
  </p>
</form>