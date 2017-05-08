<form action="" method="post" name="facturacion_electronica" id="facturacion_electronica" style="width:500px; height:400px; overflow:auto;">
	<p class="red bold">Informaci&oacute;n: Los registros en rojo no se facturan debido a errores.</p>
	<table width="98%" align="center" class="table">
		<thead>
			<tr>
				<th colspan="6" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : cia -->
			<tr>
				<th colspan="6" align="left" nowrap scope="col">{num_cia} {nombre_cia}</th>
			</tr>
			<tr>
				<th colspan="5" align="right">Diferencia inicial</th>
				<th align="right"><input name="arrastre_diferencia" type="hidden" id="arrastre_diferencia" value="{arrastre_diferencia}" cia="{num_cia}" />
					<input name="periodo" type="hidden" id="periodo" value="{periodo}" cia="{num_cia}" />
					{diferencia_inicial}</th>
			</tr>
			<tr>
				<th><input name="checkblock" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" /></th>
				<th>Día</th>
				<th>Efectivo</th>
				<th>Clientes</th>
				<th>Venta</th>
				<th>Diferencia</th>
			</tr>
			<!-- START BLOCK : dia -->
			<tr{row_color}>
				<td align="center"><input name="datos[]" type="checkbox" id="datos" value="{datos}" cia="{num_cia}" dia="{dia}" index="{index}"{checked}{disabled} /></td>
				<td align="right">{dia}</td>
				<td align="right" class="blue">{efectivo}</td>
				<td align="right" nowrap><a id="clientes-{num_cia}-{dia}" title="{param}" class="enlace orange">{clientes}</a></td>
				<td align="right"><span id="venta-{num_cia}-{dia}" class="green">{venta}</span></td>
				<td align="right"><span id="diferencia-{num_cia}-{dia}" class="{color_diferencia}">{diferencia}</span></td>
			</tr>
			<!-- END BLOCK : dia  -->
			<tr>
				<th colspan="2" align="right">Total</th>
				<th align="right"><span class="blue">{efectivo}</span></th>
				<th align="right"><span id="total_clientes-{num_cia}" class="orange">{clientes}</span></th>
				<th align="right"><span id="total_venta-{num_cia}" class="green">{venta}</span></th>
				<th align="right"><span id="total_diferencia-{num_cia}">{diferencia}</span></th>
			</tr>
			<tr>
				<td colspan="6" align="right">&nbsp;</td>
			</tr>
			<!-- END BLOCK : cia -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
