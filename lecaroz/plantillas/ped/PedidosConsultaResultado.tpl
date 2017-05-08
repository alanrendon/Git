<form name="Datos" id="Datos">
	<input type="hidden" name="tipo" id="tipo" />
	<table class="tabla_captura">
		<tr></tr>
		<tr>
			<th colspan="12" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
				Seleccionar todo</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="12" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th><input name="checkblock" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" /></th>
			<th colspan="2">Producto</th>
			<th>Folio</th>
			<th>Fecha</th>
			<th>Solicitado</th>
			<th colspan="2">Pedido</th>
			<th colspan="2">Entregar</th>
			<th colspan="2">Proveedor</th>
		</tr>
		<!-- START BLOCK : pedido -->
		<tr id="row" class="linea_{row_color}">
			<td align="center"><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" num_cia="{num_cia}" /></td>
			<td align="right">{codmp}</td>
			<td>{nombre_mp}</td>
			<td align="right"><a href="#" title="{info}" class="enlace">{folio}</a></td>
			<td align="center">{fecha}</td>
			<td align="center" class="orange">{fecha_solicitud}</td>
			<td align="right" class="green">{pedido}</td>
			<td class="green">{unidad}</td>
			<td align="right" class="orange">{entregar}</td>
			<td class="orange">{presentacion}</td>
			<td align="right">{num_pro}</td>
			<td>{nombre_pro}</td>
		</tr>
		<!-- END BLOCK : pedido -->
		<!-- START BLOCK : totales -->
		<tr>
			<th colspan="6">&nbsp;</th>
			<th align="right">{pedido}</th>
			<th align="left">{unidad}</th>
			<th align="right">{entregar}</th>
			<th align="left">{presentacion}</th>
			<th colspan="2">&nbsp;</th>
		</tr>
		<!-- END BLOCK : totales -->
		<tr>
			<td colspan="12">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
		<!-- START BLOCK : totales_generales -->
		<tr>
			<th colspan="6">&nbsp;</th>
			<th align="right">{pedido}</th>
			<th align="left">{unidad}</th>
			<th align="right">{entregar}</th>
			<th align="left">{presentacion}</th>
			<th colspan="2">&nbsp;</th>
		</tr>
		<!-- END BLOCK : totales_generales -->
	</table>
	<p>
		<input type="button" name="reporte_cia" id="reporte_cia" value="Reporte por compa&ntilde;&iacute;a" />
	&nbsp;&nbsp;
	<input type="button" name="reporte_mp" id="reporte_mp" value="Reporte por producto" />
	&nbsp;&nbsp;
	<input type="button" name="reporte_pro" id="reporte_pro" value="Reporte por proveedor" />
	</p>
	<p>
		<input type="button" name="memo" id="memo" value="Memor&aacute;ndum para proveedores" />
		&nbsp;&nbsp;
		<input type="button" name="email" id="email" value="Enviar pedidos por email" />
	</p>
	<p>
		<input type="button" name="borrar" id="borrar" value="Borrar seleccionados" />
	</p>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar">
	</p>
</form>
