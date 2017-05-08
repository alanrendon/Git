
	<div id="titulo">Modificar Método de Pago</div>
	<div id="cre"></div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<input type="hidden" name="id" id="id" value="{id}" >
			<table class="tabla_captura"><tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Nombre</th>
					<td><b>{label}</b>
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Clave</th>
					<td class="linea_on">
						<input name="clave" type="text" class="cap toText toUpper alignLeft bold fontSize12pt" id="clave" size="20" maxlength="50" value="{clave}" >
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
				<input type="button" name="modificar" class="boton" id="modificar" value="Modificar Método de Pago">
			</p>
		</form>
	</div>
