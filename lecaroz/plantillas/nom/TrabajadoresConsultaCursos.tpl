<table class="tabla_captura">
	<thead>
		<tr>
			<th scope="col">Curso</th>
			<th scope="col">Fecha</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody id="cursos_empleado">
		<!-- START BLOCK : row -->
		<tr id="curso{id}" class="linea_{row_color}">
			<td>{nombre_curso}</td>
			<td align="center">{fecha}</td>
			<td align="center"><img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja_curso" width="16" height="16" id="baja_curso" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
</table>
<br />
<form name="AltaCurso" class="FormValidator FormStyles" id="AltaCurso">
	<input name="id" type="hidden" id="id" value="{id}">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Curso</th>
			<th scope="col">Fecha</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<tr>
			<td align="center"><select name="curso" id="curso">
					<!-- START BLOCK : option -->
					<option value="{value}">{text}</option>
					<!-- END BLOCK : option -->
				</select></td>
			<td align="center"><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
			<td align="center"><input type="button" name="alta_curso" id="alta_curso" value="Añadir curso" /></td>
		</tr>
	</table>
</form>
<p>
	<input type="button" name="cerrar" id="cerrar" value="Cerrar">
</p>
