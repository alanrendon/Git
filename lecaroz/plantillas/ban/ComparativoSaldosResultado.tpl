<table class="table">
	<thead>
		<tr>
			<th colspan="14" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<th>Saldo<br />banco {anio1}</th>
			<th>Saldo<br />proveedores {anio1}</th>
			<th>Diferencia {anio1}</th>
			<th>Saldo<br />banco {anio2}</th>
			<th>Saldo<br />proveedores {anio2}</th>
			<th>Diferencia {anio2}</th>
			<th>Saldo<br />banco</th>
			<th>Saldo<br />proveedores</th>
			<th>Diferencia</th>
			<th>Utilidad {anio1}</th>
			<th>Reparto {anio1}</th>
			<th>Utilidad {anio2}</th>
			<th>Reparto {anio2}</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td>{num_cia} {nombre_cia}</td>
			<td align="right" class="green">{saldo_banco_1}</td>
			<td align="right" class="orange">{saldo_proveedores_1}</td>
			<td align="right">{diferencia_1}</td>
			<td align="right" class="green">{saldo_banco_2}</td>
			<td align="right" class="orange">{saldo_proveedores_2}</td>
			<td align="right">{diferencia_2}</td>
			<td align="right" class="green">{saldo_banco}</td>
			<td align="right" class="orange">{saldo_proveedores}</td>
			<td align="right">{diferencia}</td>
			<td align="right" class="purple">{utilidad_1}</td>
			<td align="right" class="orange">{reparto_1}</td>
			<td align="right" class="purple">{utilidad_2}</td>
			<td align="right" class="orange">{reparto_2}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th class="bold right">Totales</th>
			<td class="bold right">{saldo_banco_1}</td>
			<td class="bold right">{saldo_proveedores_1}</td>
			<td class="bold right">{diferencia_1}</td>
			<td class="bold right">{saldo_banco_2}</td>
			<td class="bold right">{saldo_proveedores_2}</td>
			<td class="bold right">{diferencia_2}</td>
			<td class="bold right">{saldo_banco}</td>
			<td class="bold right">{saldo_proveedores}</td>
			<td class="bold right">{diferencia}</td>
			<td class="bold right">{utilidad_1}</td>
			<td class="bold right">{reparto_1}</td>
			<td class="bold right">{utilidad_2}</td>
			<td class="bold right">{reparto_2}</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="reporte" id="reporte" value="Reporte para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" />
</p>
