
	<div id="titulo">Modificar Pregunta</div>
	<div id="cre" align="center"></div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<input type="hidden" name="id" id="id" value="{id}" >
			<input type="hidden" id="departamento" name="departamento" value="{departamento}" >
			<table class="tabla_captura"><tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Pregunta</th>
					<td class="linea_on">
						{pregunta}
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Departamento</th>
					<td>
						

						<input type="text" id="departamento_ref" name="departamento_ref" size="5" class="cap toPosInt alignLeft bold fontSize12pt" value="{ref}" >
						<input name="nombre_departamento" type="text" disabled="disabled" id="nombre_departamento" size="20" value="{label}">
					</td>
				</tr>

				<tr class="linea_off">
					<th align="left" scope="row">Periodicidad</th>
					<td>
						<select name="peri" id="peri" class="">
				          {peri}
				        </select>
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Enviar Correo al Contestar?</th>
					<td>
						{correo}
					</td>
				</tr>

				
				<tr class="linea_on">
					<th align="left" scope="row">Observaciones</th>
					<td>
						<textarea name="observations" cols="50" rows="5" class="cap toText clean" id="observations" style="width:98%;">{observations}</textarea>
					</td>
				</tr>
				
			</tbody></table>
			<p>
				<input type="button" name="modificar" class="boton" id="modificar" value="Modificar Pregunta">
			</p>
		</form>
	</div>
