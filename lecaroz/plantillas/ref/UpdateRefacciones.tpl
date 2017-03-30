
	<div id="titulo">Modificar Refacciones</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos" >
			<input type="hidden" name="id" id="id" value="{id}" >
			<table class="tabla_captura"><tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">No. Parte</th>
					<td>{num_part}
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Precio</th>
					<td>
						<input name="price" type="text" class="cap numPosFormat2 alignRight" id="price" value="{price}" size="5" align="left" maxlength="20">
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
				<input type="button" name="modificar" id="modificar" value="Modificar Refacción">
			</p>
		</form>
	</div>
