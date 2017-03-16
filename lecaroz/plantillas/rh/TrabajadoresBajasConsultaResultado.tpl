<p style="font-weight:bold; color:#C00;">Le recordamos que los empleados no afiliados al I.M.S.S. y los cambios de compa&ntilde;&iacute;a no generan folio de baja ni correo electr&oacute;nico.</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="11" align="left">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="11" align="left" class="font14">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>Folio<br />baja</th>
			<th>#</th>
			<th>Empleado</th>
			<th>R.F.C.</th>
			<th>Puesto</th>
			<th>Turno</th>
			<th>¿Afiliado al<br />I.M.S.S.?</th>
			<th>Saldo<br />prestamo</th>
			<th>Fecha<br />baja</th>
			<th>Fecha<br />baja<br />I.M.S.S.</th>
			<th>Usuario</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right" class="orange">{folio}</td>
			<td align="right" class="green">{num_emp}</td>
			<td>{nombre_trabajador}</td>
			<td>{rfc}</td>
			<td class="blue">{puesto}</td>
			<td class="purple">{turno}</td>
			<td align="center">{afiliado}</td>
			<td align="right" class="red">{saldo}</td>
			<td align="center" class="red">{fecha_baja}</td>
			<td align="center" class="red">{fecha_baja_imss}</td>
			<td align="center">{usuario}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="11" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	<!-- &nbsp;&nbsp;
	<input type="button" name="listado" id="listado" value="Listado para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" /> -->
</p>
