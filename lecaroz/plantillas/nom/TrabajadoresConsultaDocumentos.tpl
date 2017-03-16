
<table class="tabla_captura">
	<thead>
		<tr>
			<th colspan="3" class="font12" scope="col">{num_emp} {nombre_emp}</th>
		</tr>
		<tr>
			<th>Fecha</th>
			<th>Tipo</th>
			<th>Documento</th>
		</tr>
	</thead>
	<tbody id="documents">
		<!-- START BLOCK : row -->
		<tr id="row_doc_{id}">
			<td align="center">{fecha}</td>
			<td align="center" class="{color}">{tipo}</td>
			<td nowrap="nowrap"><img src="/lecaroz/iconos/magnify.png" width="16" height="16" align="absbottom" /> <a href="{url_documento}" target="_new">{nombre_documento}</a>{baja}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
</table>
<!-- START BLOCK : alta_doc -->
<br />
<form method="post" enctype="multipart/form-data" name="document_upload" id="document_upload" class="FormValidator FormStyles">
	<input name="idempleado" type="hidden" id="idempleado" value="{id}">
	<table class="tabla_captura">
		<tr>
			<th align="left" scope="row">Cargar documento</th>
			<td align="center"><input name="archivo[]" type="file" id="archivo" size="30" multiple /></td>
			<td align="center"><select name="tipo" id="tipo">
					<option value="1">ALTA</option>
					<option value="2">BAJA</option>
					<option value="3">MODIFICACION</option>
					<option value="4">AUTORIZACION</option>
				</select></td>
			<td align="center"><input type="button" name="guardar" id="guardar" value="Guardar" /></td>
		</tr>
	</table>
</form>
<!-- END BLOCK : alta_doc -->
<p>
	<input type="button" name="cerrar" id="cerrar" value="Cerrar">
</p>
