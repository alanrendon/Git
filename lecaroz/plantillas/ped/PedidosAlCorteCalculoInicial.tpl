<form action="" method="get" name="Datos" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Producto</th>
			<td class="font12 bold">{codmp} {nombre_mp}</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Pedir para el d&iacute;a</th>
			<td class="font12 bold">{fecha}</td>
		</tr>
	</table>
	<p class="bold font12">Paso 1. Calculo inicial</p>
	<table class="tabla_captura">
		<tr>
			<th scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked"></th>
			<th scope="col">Compañía</th>
			<th scope="col">Unidad de<br>
				consumo</th>
			<th scope="col">Existencia</th>
			<th scope="col">Consumo</th>
			<th scope="col">Promedio</th>
			<th scope="col">Ultimo d&iacute;a<br>
				de consumo</th>
			<th scope="col">Días a<br>
				pedir</th>
			<th scope="col">Pedido</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}">
			<td align="center"><input name="pedido[]" type="checkbox" id="pedido" value="{datos_pedido}" checked="checked"></td>
			<td>{num_cia} {nombre_cia}</td>
			<td align="left" class="blue">{unidad}</td>
			<td align="right" class="blue">{existencia}</td>
			<td align="right" class="red">{consumo}</td>
			<td align="right" class="red">{promedio}</td>
			<td align="center" class="red">{fecha}</td>
			<td align="right" class="green">{dias}</td>
			<td align="right" class="green">{pedido}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr class="linea_{row_color}">
			<th colspan="8" align="right">Total pedido (aprox.)</th>
			<th align="right" class="green">{total}</th>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>