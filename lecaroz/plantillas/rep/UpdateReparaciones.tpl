
	<div id="titulo">Modificar Reparación</div>
	<div id="cre"></div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<input type="hidden" name="id" id="id" value="{id}" >
			<table class="tabla_captura"><tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">No. Reparación</th>
					<td>{label}
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Proveedor</th>
					<td class="linea_on">
						<input type="text" id="proveedor" name="proveedor" size="5" value="{options}" >
						<input name="nombre_pro" type="text" disabled="disabled" id="nombre_pro" size="20" value="{options2}">
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Precio</th>
					<td>
						<input name="price" type="text" class="cap numPosFormat2 alignLeft" id="price" value="{price}" size="5" align="left" maxlength="20">
					</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row">&nbsp;</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Descripción</th>
					<td>
						<textarea name="description" cols="40" rows="5" class="cap toText toUpper clean" id="description" style="width:98%;">{description}</textarea>
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Observaciones</th>
					<td>
						<textarea name="observations" cols="50" rows="5" class="cap toText toUpper clean" id="observations" style="width:98%;">{observations}</textarea>
					</td>
				</tr>
				
			</tbody></table>
			<p>
				<input type="button" name="modificar" class="boton" id="modificar" value="Modificar Reparación">
			</p>
		</form>
	</div>
