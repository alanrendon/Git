<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="10" align="left" class="font12" scope="col">[{fecha}] {num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th><input name="checkall" type="checkbox" id="checkall" /></th>
			<th>Producto</th>
			<th nowrap>Cantidad<br>
				de pedido</th>
			<th nowrap>Unidad de<br />
				Pedido</th>
			<th nowrap><input name="check_tomar_consumo" type="checkbox" id="check_tomar_consumo" checked="checked" />
				Interpretar<br>
				como unidad<br>
				de consumo</th>
			<th>Observaciones</th>
			<th>Fijo</th>
			<th>Proveedor</th>
			<th>Presentación</th>
			<th>Entregar</th>
		</tr>
		<!-- START BLOCK : pedido -->
		<tr id="row" class="linea_{row_color}">
			<td align="center"><input name="pedid[]" type="hidden" id="pedid" value="{id}" /><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
				<input name="id{i}" type="checkbox" id="id{i}" value="{id}"{checked} /></td>
			<td align="center" nowrap><input name="codmp{i}" type="text" class="valid Focus toPosInt center" id="codmp{i}" value="{codmp}" size="3" />
				<input name="nombre_mp{i}" type="text" disabled="disabled" id="nombre_mp{i}" value="{nombre_mp}" size="30" />
				<input name="existencia{i}" type="hidden" id="existencia{i}" value="{existencia}" />
				<input name="consumo{i}" type="hidden" id="consumo{i}" value="{consumo}" /></td>
			<td align="center"><input name="cantidad{i}" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad{i}" value="{cantidad}" size="8" /></td>
			<td nowrap>{unidad}</td>
			<td align="center"><input name="tomar_consumo{i}" type="checkbox" id="tomar_consumo{i}" value="1"{tomar_consumo}></td>
			<td>{observaciones}</td>
			<td align="center"><input name="fijo{i}" type="checkbox" id="fijo{i}" value="1"{fijo} /></td>
			<td><select name="num_pro{i}" id="num_pro{i}" style="width:100%;">
					<!-- START BLOCK : pro -->
					<option value="{value}"{selected}>{text}</option>
					<!-- END BLOCK : pro -->
				</select></td>
			<td><select name="presentacion{i}" id="presentacion{i}" style="width:100%;">
				<!-- START BLOCK : pre -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : pre -->
			</select></td>
			<td align="center"><input name="entregar{i}" type="text" class="bold right" id="entregar{i}" value="{entregar}" size="8" readonly="readonly" /></td>
		</tr>
		<!-- END BLOCK : pedido -->
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
</form>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="borrar" id="borrar" value="Borrar seleccionados" />
	&nbsp;&nbsp;
	<input type="button" name="guardar" id="guardar" value="Guardar pedido" />
</p>
