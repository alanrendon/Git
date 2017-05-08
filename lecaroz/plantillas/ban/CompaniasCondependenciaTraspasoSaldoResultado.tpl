<form action="" method="get" name="Datos" id="Datos">
	<table class="tabla_captura">
		<tr>
			<th colspan="6" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
				Seleccionar todos</th>
		</tr>
		<tr>
			<th colspan="6" class="font12" scope="col"><input name="banco" type="hidden" id="banco" value="{banco}">
			{nombre_banco}</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="6" align="left" class="font12" scope="col">{num_cia} {nombre_cia} ({cuenta})</th>
		</tr>
		<tr>
			<th><input name="checkblock[]" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" /></th>
			<th>Compañías con-dependencia</th>
			<th>Cuenta</th>
			<th>Saldo en<br />
				libros</th>
			<th>Saldo a<br />
				proveedores</th>
			<th>Total a<br />
				traspasar</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}" id="row">
			<td align="center"><input name="data[]" type="checkbox" id="data" value="{data}" checked="checked" num_cia="{num_cia_pri}"{disabled} /></td>
			<td>{num_cia} {nombre_cia}</td>
			<td align="left">{cuenta}</td>
			<td align="right" class="blue">{saldo_cia}</td>
			<td align="right" class="red">{saldo_pro}</td>
			<td align="right" class="green bold">{saldo_tra}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr class="font12">
			<th colspan="5" align="right">Total</th>
			<th align="right" id="total{num_cia}">{total}</th>
		</tr>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
		&nbsp;&nbsp;
		<input type="button" name="traspasar" id="traspasar" value="Traspasar saldo" />
	</p>
</form>
