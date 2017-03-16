<table class="table">
	<thead>
		<tr>
			<th scope="col">Compa&ntilde;&iacute;a</th>
			<th scope="col">Mes</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">{num_cia} {nombre_cia}</td>
			<td class="bold font12">{mes} {anio}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<form name="datos-form" class="FormValidator" id="datos-form">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<input name="fecha" type="hidden" id="fecha" value="{fecha}" />
	<table class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Estatus</th>
			</tr>
		</thead>
		<tbody id="datos-table">
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input name="cancelar" type="button" id="cancelar" value="Cancelar" />
		<input name="registrar" type="button" id="registrar" value="Registrar nuevos productos" />
	</p>
</form>