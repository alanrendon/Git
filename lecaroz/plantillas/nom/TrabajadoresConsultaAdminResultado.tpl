<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<p><img src="/lecaroz/iconos/plus.png" width="16" height="16">
		<input type="button" name="alta" id="alta" value="Alta de trabajador">
	&nbsp;&nbsp;<img src="/lecaroz/iconos/refresh.png" width="16" height="16" />
	<input type="button" name="recargar" id="recargar" value="Actualizar búsqueda" />
	&nbsp;&nbsp;
	<input type="button" name="calculadora" id="calculadora" value="Calculadora" />
	</p>
	<table class="tabla_captura">
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="12" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th colspan="12" align="left" class="font10" scope="col">
				<img src="/lecaroz/iconos/fingerprint_green.png" width="16" height="16" alt=""> Chequeos correctos,
				<img src="/lecaroz/iconos/fingerprint_yellow.png" width="16" height="16" alt=""> No ha checado en m&aacute;s de 20 d&iacute;as,
				<img src="/lecaroz/iconos/fingerprint_red.png" width="16" height="16" alt=""> No ha ingresado el id, el id es incorrecto o nunca ha checado
			</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Trabajador</th>
			<th>Puesto</th>
			<th>Turno</th>
			<th nowrap>No. afiliación</th>
			<th>Saldo</th>
			<th>Recibe<br />
				aguinaldo</th>
			<th>Tipo de<br />
				cálculo</th>
			<th>Antigüedad</th>
			<th>Aguinaldo <br />
				{anio_ant}</th>
			<th>Aguinaldo<br />
				{anio_act}</th>
			<th><img src="/lecaroz/iconos/plus.png" alt="{num_cia}" name="alta_cia" width="16" height="16" id="alta_cia" title="Alta"></th>
		</tr>
		<!-- START BLOCK : trabajador -->
		<tr class="linea_{row_color}">
			<td align="right"{no_firma}>{num_emp}</td>
			<td nowrap class="{trabajador_color}">{nombre_trabajador}</td>
			<td nowrap><select name="puesto[]" id="puesto" data="{data}">
				<!-- START BLOCK : puesto -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : puesto -->
			</select></td>
			<td nowrap><select name="turno[]" id="turno" data="{data}">
				<!-- START BLOCK : turno -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : turno -->
			</select></td>
			<td nowrap>{num_afiliacion}</td>
			<td align="right" class="red">{saldo}</td>
			<td align="center"><input name="aguinaldo[]" type="checkbox" id="aguinaldo" value="{id}"{aguinaldo} /></td>
			<td align="center"><select name="tipo[]" id="tipo" data="{data}">
				<option value="0"{tipo_0}>NORMAL</option>
				<option value="1"{tipo_1}>A 1 AÑO</option>
				<option value="2"{tipo_2}>A 3 MESES</option>
			</select></td>
			<td nowrap><a id="antiguedad" class="enlace purple" alt="{id}" bloqueado="{bloqueado}">{antiguedad}</a></td>
			<td align="right" nowrap><a id="aguinaldo_ant" class="enlace green" alt="{data_aguinaldo}" num_cia="{num_cia}" bloqueado="{bloqueado}">{aguinaldo_ant}</a></td>
			<td align="right" nowrap><a id="aguinaldo_act" class="enlace blue" alt="{data_aguinaldo}" num_cia="{num_cia}" bloqueado="{bloqueado}">{aguinaldo_act}</a></td>
			<td nowrap><img src="/lecaroz/iconos/pencil{no_modificar}.png" alt="{id}" name="modificar" width="16" height="16" id="modificar" title="Modificar" /> <img src="/lecaroz/iconos/cancel_round{no_baja}.png" alt="{id}" name="baja" width="16" height="16" id="baja" title="Baja" /> <img src="/lecaroz/iconos/accept_green{no_reactivar}.png" alt="{id}" name="reactivar" width="16" height="16" id="reactivar" title="Reactivar" /> <img src="/lecaroz/iconos/info.png" alt="{id}" name="info" width="16" height="16" id="info" /></td>
		</tr>
		<!-- END BLOCK : trabajador -->
		<tr>
			<th colspan="9" align="right">Totales</th>
			<th align="right" id="total_aguinaldo_ant" num_cia="{num_cia}">{aguinaldo_ant}</th>
			<th align="right" id="total_aguinaldo_act" num_cia="{num_cia}">{aguinaldo_act}</th>
			<th>{emp}</th>
		</tr>
		<tr>
			<td colspan="12">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="listado" id="listado" value="Listado de trabajadores" />
&nbsp;&nbsp;
<input type="button" name="repetidos" id="repetidos" value="Trabajadores repetidos" />
&nbsp;&nbsp;
<input type="button" name="regresar" id="regresar" value="Regresar" />
	</p>
</form>
