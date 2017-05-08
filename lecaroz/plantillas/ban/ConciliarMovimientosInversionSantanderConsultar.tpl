<!-- START BLOCK : resultado -->

<table class="table">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td><input type="radio" name="fecha" value="{fecha}"{checked}{disabled} />
				<strong class="font12">{fecha}</strong></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="conciliar" id="conciliar" value="Conciliar movimientos" />
</p>
<!-- END BLOCK : resultado --> 
<!-- START BLOCK : no_resultado -->
<table class="table">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12 red">No hay movimientos de inversi&oacute;n pendientes</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<!-- END BLOCK : no_resultado -->