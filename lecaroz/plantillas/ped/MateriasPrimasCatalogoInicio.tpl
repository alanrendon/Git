<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left">Producto(s)</th>
			<td><input name="productos" type="text" class="valid toInterval" id="productos" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Nombre</th>
			<td><input name="nombre" type="text" class="valid toText cleanText toUpper" id="nombre" size="40" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Categoría(s)</th>
			<td><input name="cat[]" type="checkbox" id="cat" value="1" checked="checked" />
				Materia prima<br />
				<input name="cat[]" type="checkbox" id="cat" value="2" checked="checked" />
				Material de empaque</td>
		</tr>
		<tr class="linea_on">
			<th align="left">Control</th>
			<td><input name="controlada[]" type="checkbox" id="controlada" value="TRUE" checked="checked" />
				Controlada<br />
				<input name="controlada[]" type="checkbox" id="controlada" value="FALSE" checked="checked" />
				No controlada</td>
		</tr>
		<tr class="linea_off">
			<th align="left">Tipo</th>
			<td><input name="tipo[]" type="checkbox" id="tipo" value="TRUE" checked="checked" />
				Producto de panadería<br />
				<input name="tipo[]" type="checkbox" id="tipo" value="FALSE" checked="checked" />
				Producto de rosticería</td>
		</tr>
		<tr class="linea_on">
			<th align="left">Pedido</th>
			<td><input name="pedido[]" type="checkbox" id="pedido" value="TRUE" checked="checked" />
				Automático<br />
				<input name="pedido[]" type="checkbox" id="pedido" value="FALSE" checked="checked" />
				Manual</td>
		</tr>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
