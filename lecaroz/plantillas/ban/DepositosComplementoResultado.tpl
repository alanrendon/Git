<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="6" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th colspan="4" align="right">Promedio mensual</th>
			<th colspan="2" align="right" class="font12">{promedio}</th>
		</tr>
		<tr>
			<th>Día</th>
			<th>Efectivo</th>
			<th>Depósitos</th>
			<th>Complemento</th>
			<th>Total</th>
			<th><img src="/lecaroz/iconos/accept.png" width="16" height="16"></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right">{dia}</td>
			<td align="right" class="blue">{efectivo}</td>
			<td align="right" class="green">{depositos}</td>
			<td align="right" class="red">{complemento}</td>
			<td align="right" class="blue">{total}</td>
			<td align="center"><input name="data[]" type="checkbox" id="data" value="{data}"></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="2" align="right">Totales</th>
			<th align="right">{depositos}</th>
			<th align="right">{complementos}</th>
			<th align="right">{total}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="registrar" id="registrar" value="Registrar depósitos complementarios" />
	</p>
</form>