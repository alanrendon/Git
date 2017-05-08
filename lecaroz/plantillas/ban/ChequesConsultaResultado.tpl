<table class="table">
	<thead>
		<tr>
			<th colspan="10" align="left"><input name="seleccionar_todos" type="checkbox" id="seleccionar_todos" /> Seleccionar todos</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th><input name="seleccionar_cia" type="checkbox" id="seleccionar_cia" data-cia="{num_cia}" /></th>
			<th colspan="9" align="left" class="font14">{num_cia} {nombre_cia}</th>
		</tr>
		<!-- START BLOCK : banco -->
		<tr>
			<th><input name="seleccionar_banco" type="checkbox" id="seleccionar_banco" data-cia="{num_cia}" data-banco="{banco}" /></th>
			<th colspan="9" align="left" class="font12">{logo_banco} {nombre_banco}<span style="float:right;">{cuenta}</span></th>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<th>Folio</th>
			<th>Fecha</th>
			<th>Cobrado</th>
			<th>Cancelado</th>
			<th>Beneficiario</th>
			<th>Concepto</th>
			<th>Gasto</th>
			<th>Importe</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}"{class_cancelado}>
			<td align="center"><input name="data[]" type="checkbox" id="data" data-cia="{num_cia}" data-banco="{banco}" value='{data}'{checkbox_disabled} /></td>
			<td align="right">{folio}</td>
			<td align="center" class="orange">{fecha}</td>
			<td align="center" class="green">{cobrado}</td>
			<td align="center" class="red">{cancelado}</td>
			<td>{num_pro} {nombre_pro}</td>
			<td>{concepto}</td>
			<td>{gasto} {nombre_gasto}</td>
			<td align="right">{importe}</td>
			<td align="right">
				<img src="/lecaroz/iconos/cancel_round{cancel_disabled}.png" data-row='{data}'{class_icono_cancel} style="margin-right:4px;" id="cancelar" width="16" height="16" />
				<img src="/lecaroz/iconos/printer{print_disabled}.png" data-id='{id}'{class_icono_print} style="margin-right:4px;" id="imprimir" width="16" height="16" />
				<img src="/lecaroz/iconos/article_download{pasivo_disabled}.png" data-id='{id}'{class_icono_pasivo} style="margin-right:4px;" id="pasivo" width="16" height="16" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="8" align="right">Total cuenta</th>
			<th align="right" class="font12">{total_banco}</th>
			<th>&nbsp;</th>
		</tr>
		<!-- END BLOCK : banco -->
		<tr>
			<th colspan="8" align="right">Total compa&ntilde;&iacute;a</th>
			<th align="right" class="font14">{total_cia}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td colspan="10" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
		<tr>
			<th colspan="8" align="right">Total general</th>
			<th align="right" class="font14">{total}</th>
			<th>&nbsp;</th>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	<!-- &nbsp;&nbsp;
	<input type="button" name="listado" id="listado" value="Listado para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" /> -->
	&nbsp;&nbsp;
	<button type="button" id="cancelar_seleccion">Cancelar selecci&oacute;n</button>
	&nbsp;&nbsp;
	<button type="button" id="imprimir_seleccion">Imprimir selecci&oacute;n</button>
	&nbsp;&nbsp;
	<button type="button" id="cambiar_fecha_seleccion">Cambiar fecha selecci&oacute;n</button>
</p>
