<p class="bold font12">Paso 1. Calculo inicial a {dias} días{complemento_leyenda}</p>
<form name="Datos" id="Datos">
	<input name="dias" type="hidden" id="dias" value="{dias}" />
	<input name="complemento" type="hidden" id="complemento" value="{complemento}" />
	<table class="tabla_captura">
		<tr>
			<th colspan="12" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
				Seleccionar todo</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="12" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th><input name="checkblock[]" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" /></th>
			<th colspan="2">Producto</th>
			<th>Unidad de<br />consumo</th>
			<th>Existencia</th>
			<th>Consumo<br />mensual</th>
			<th>Consumo<br />
				Promedio<br />
				Diario</th>
			<th>Ultimo día<br />
				de consumo</th>
			<th>Pedido</th>
			<th>Diferencia</th>
			<th>Inventario<br />estimado</th>
			<th>D&iacute;as de<br />consumo</th>
		</tr>
		<!-- START BLOCK : producto -->
		<tr class="linea_{row_color}" id="row">
			<td align="center"><input name="pedido[]" type="checkbox" id="pedido" value="{datos_pedido}" checked="checked" num_cia="{num_cia}"{disabled} /></td>
			<td>{codmp}</td>
			<td>{nombre_mp}</td>
			<td>{unidad}</td>
			<td align="right" class="blue">{existencia}</td>
			<td align="right" class="red">{consumo_mes}</td>
			<td align="right" class="red">{consumo_dia}</td>
			<td align="center" class="red">{consumo_fecha}</td>
			<td align="right" class="green bold">{pedido}</td>
			<td align="right" class="orange">{diferencia}</td>
			<td align="right" class="blue">{estimado}</td>
			<td align="right" class="{dias_color}">{dias}</td>
		</tr>
		<!-- END BLOCK : producto -->
		<tr>
			<td colspan="12">&nbsp;</td>
		</tr>
		<!-- START BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="reporte" id="reporte" value="Reporte" />
	&nbsp;&nbsp;
	<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>