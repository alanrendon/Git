
	<div id="titulo">Modificar Refacciones</div>
	<div id="cre"></div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<input type="hidden" name="id" id="id" value="{id}" >
			<table class="tabla_captura"><tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Referencia</th>
					<td class="linea_on">
						<input name="label" type="text" class="cap toText toUpper alignLeft bold fontSize12pt" id="ref" size="20" maxlength="50" value="{ref}" >
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Nombre</th>
					<td class="linea_on">
						<input name="label" type="text" class="cap toText toUpper alignLeft bold fontSize12pt" id="label" size="20" maxlength="50" value="{label}" >
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
				<input type="button" name="modificar" class="boton" id="modificar" value="Modificar Departamento">
			</p>
		</form>
	</div>
