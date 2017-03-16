<p class="bold font12">Simulaci&oacute;n a {dias} d&iacute;as{complemento_leyenda}</p>
<table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="11" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
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
		<td colspan="11">&nbsp;</td>
	</tr>
	<!-- START BLOCK : cia -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
