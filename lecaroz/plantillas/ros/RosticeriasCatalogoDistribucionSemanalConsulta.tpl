<input name="fecha_inicio_semana" type="hidden" id="fecha_inicio_semana" value="{fecha1}" />
<table class="table">
	<thead>
		<tr>
			<th>Proveedor</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12 center">{num_pro} {nombre_pro}</td>
		</tr>
		<tr>
			<td class="bold font12 center">Semana del {fecha1} al {fecha2}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th colspan="9">Semana de referencia del {fecha_referencia1} al {fecha_referencia2}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<th>Lun</th>
			<th>Mar</th>
			<th>Mi&eacute;</th>
			<th>Jue</th>
			<th>Vie</th>
			<th>S&aacute;b</th>
			<th>Dom</th>
			<th>%</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_1}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="1" data-id="{id_1}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_2}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="2" data-id="{id_2}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_3}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="3" data-id="{id_3}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_4}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="4" data-id="{id_4}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_5}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="5" data-id="{id_5}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_6}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="6" data-id="{id_6}" />
			</td>
			<td class="center">
				<img src="/lecaroz/iconos/accept{dia_0}.png" id="dia_icon" class="icono" width="16" height="16" data-cia="{num_cia}" data-pro="{num_pro}" data-dia="0" data-id="{id_0}" />
			</td>
			<td>
				<span id="porc" data-cia="{num_cia}">{porc}</span>
			</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">
		Regresar
	</button>
</p>
