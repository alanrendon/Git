<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr>
			<th colspan="11" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" />
				Seleccionar todo</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="11" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th><input name="checkblock" type="checkbox" id="checkblock" value="{num_cia}" /></th>
			<th>Fecha</th>
			<th>Producto<br />
			solicitado</th>
			<th>Cantidad<br>
				de pedido</th>
			<th>Unidad de<br />
			Pedido</th>
			<th nowrap>Interpretar<br>
				como unidad<br>
				de consumo</th>
			<th>Observaciones</th>
			<th>Código de<br />
				producto</th>
			<th>Proveedor</th>
			<th>Presentación</th>
			<th>Entregar</th>
		</tr>
		<!-- START BLOCK : pedido -->
		<tr id="row" class="linea_{row_color}">
			<td align="center"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" /><input name="id{i}" type="checkbox" id="id{i}" value="{id}" num_cia="{num_cia}" /></td>
			<td align="center"><input name="fecha_solicitud{i}" type="hidden" id="fecha_solicitud{i}" value="{fecha}" />
			{fecha}</td>
			<td nowrap>{producto}</td>
			<td align="center"><input name="cantidad{i}" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad{i}" value="{cantidad}" size="8" /></td>
			<td nowrap>{unidad}</td>
			<td align="center"><input name="tomar_consumo{i}" type="checkbox" id="tomar_consumo{i}" value="1"></td>
			<td>{observaciones}</td>
			<td align="center" nowrap><input name="codmp{i}" type="text" class="valid Focus toPosInt center" id="codmp{i}" value="{codmp}" size="3" />
			<input name="nombre_mp{i}" type="text" disabled="disabled" id="nombre_mp{i}" size="30" />
			<input type="hidden" name="existencia{i}" id="existencia{i}" />
			<input type="hidden" name="consumo{i}" id="consumo{i}" /></td>
			<td><select name="num_pro{i}" id="num_pro{i}" style="width:100;">
			</select></td>
			<td><select name="presentacion{i}" id="presentacion{i}" style="width:100%;">
			</select></td>
			<td align="center"><input name="entregar{i}" type="text" id="entregar{i}" class="bold right" size="8" readonly="readonly" /></td>
		</tr>
		<!-- END BLOCK : pedido -->
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
</form>

<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input type="button" name="borrar" id="borrar" value="Borrar seleccionados" />
&nbsp;&nbsp;
<input type="button" name="pedido" id="pedido" value="Realizar pedido" />
</p>