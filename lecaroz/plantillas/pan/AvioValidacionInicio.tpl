<form name="inicio_form" class="FormValidator" id="inicio_form">
	<input name="num_cia" type="hidden" id="num_cia" value="">
	<input name="fecha" type="hidden" id="fecha" value="">
	<table class="table">
		<thead>
			<tr>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tr>
			<td class="bold font14 center">{usuario}</td>
		</tr>
		<tfoot>
			<tr>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<br>
	<table class="table">
		<thead>
			<tr>
				<th>Compa&ntilde;&iacute;a</th>
				<th>Fecha</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : cia -->
			<!-- START BLOCK : row -->
			<tr>
				<td class="bold">{num_cia} {nombre_cia}</td>
				<td class="bold center">{fecha}</td>
				<td>
					<button type="button" class="revisar"{disabled} data-cia="{num_cia}" data-fecha="{fecha}">Revisar</button>
				</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<!-- END BLOCK : cia -->
			<!-- START BLOCK : no_result -->
			<tr>
				<td class="bold red" colspan="3">No hay resultados</td>
			</tr>
			<!-- END BLOCK : no_result -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
