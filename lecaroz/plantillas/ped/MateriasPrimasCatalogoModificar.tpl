<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="codmp" type="hidden" id="codmp" value="{codmp}">
	<table class="tabla_captura">
		<tr class="linea_on">
			<th align="left" scope="row">Nombre del producto</th>
			<td><input name="nombre" type="text" class="valid toText cleanText toUpper" id="nombre" value="{nombre}" size="40" maxlength="50" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Im&aacute;gen</th>
			<td>
				<div style="height:132px;text-align:center;">
					<img src="{imagen_src}" width="128" height="128" id="img" />
				</div>
				<input name="imagen" type="file" id="imagen" size="30" value="" />
				<input name="imagen_src" type="hidden" id="imagen_src" value="{imagen_src}" />
				<input name="imagen_tmp" type="hidden" id="imagen_tmp" value="" />
				<img src="/lecaroz/iconos/picture_delete.png" width="16" height="16" class="icono" id="drop_img" />
			</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Producto de</th>
			<td><input name="tipo_cia" type="radio" value="TRUE"{tipo_cia_t} />
				Panadería
				<input type="radio" name="tipo_cia" value="FALSE"{tipo_cia_f} />
				Rosticería</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Categoría</th>
			<td><select name="tipo" id="tipo">
					<option value="1"{tipo_1}>MATERIA PRIMA</option>
					<option value="2"{tipo_2}>MATERIAL DE EMPAQUE</option>
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Unidad de consumo</th>
			<td><select name="unidad" id="unidad">
					<!-- START BLOCK : unidad -->
					<option value="{value}"{selected}>{text}</option>
					<!-- END BLOCK : unidad -->
				</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Producto controlado</th>
			<td><input name="controlada" type="radio" value="TRUE"{controlada_t} />
				Si
				<input type="radio" name="controlada" value="FALSE"{controlada_f} />
				No</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Pedido automático</th>
			<td><input name="procpedautomat" type="radio" value="TRUE"{procpedautomat_t} />
				Si
				<input name="procpedautomat" type="radio" value="FALSE"{procpedautomat_f} />
				No</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Sin existencia <br />
				(producto solo para venta)</th>
			<td><input type="radio" name="no_exi" value="TRUE"{no_exi_t} />
				Si
				<input name="no_exi" type="radio" value="FALSE"{no_exi_f} />
				No</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Reporte de m&aacute;ximos</th>
			<td><input type="radio" name="reporte_consumos_mas" value="TRUE"{reporte_consumos_mas_t} />
				Si
				<input name="reporte_consumos_mas" type="radio" value="FALSE"{reporte_consumos_mas_f} />
				No</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Grasa</th>
			<td><input type="radio" name="grasa" value="TRUE"{grasa_t} />
				Si
				<input name="grasa" type="radio" value="FALSE"{grasa_f} />
				No</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Az&uacute;car</th>
			<td><input type="radio" name="azucar" value="TRUE"{azucar_t} />
				Si
				<input name="azucar" type="radio" value="FALSE"{azucar_f} />
				No</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Prioridad</th>
			<td><input name="prioridad_orden" type="text" class="valid Focus toPosInt right" id="prioridad_orden" value="{prioridad_orden}" size="3" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">% I.E.P.S.</th>
			<td><input name="porcentaje_ieps" type="text" class="valid Focus numberPosFormat right" precision="2" id="porcentaje_ieps" value="{porcentaje_ieps}" size="5" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
