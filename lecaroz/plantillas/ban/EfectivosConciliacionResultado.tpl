<form action="" method="post" name="efectivos" class="FormValidator" id="efectivos">
	<table class="table" id="info">
		<thead>
			<tr>
				<th colspan="2">Información</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold font10">Compañ&iacute;a</td>
				<td><a id="info_cia" class="bold underline font16 blue" title="{razon_social}">{num_cia} {nombre_cia}</a></td>
			</tr>
			<tr>
				<td class="bold font10">Mes</td>
				<td class="bold font12">{mes_corte} {anio_corte}</td>
			</tr>
			<tr>
				<td class="bold font10">D&iacute;a de corte</td>
				<td class="bold font12">{dia_corte}</td>
			</tr>
			<tr>
				<td class="bold font10">Encargado</td>
				<td class="bold font12">{encargado}</td>
			</tr>
			<tr>
				<td class="bold font10">Operadora</td>
				<td class="bold font12">{operadora}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<input name="fecha" type="hidden" id="fecha" value="{fecha}">
	<input name="tipo" type="hidden" id="tipo" value="{tipo}" />
	<table align="center" class="table" id="reporte">
		<thead>
			<tr>
				<th scope="col">Día</th>
				<th scope="col">Efectivo</th>
				<!-- START BLOCK : deposito_titulo -->
				<th scope="col">Depósito {i}</th>
				<!-- END BLOCK : deposito_titulo -->
				<!-- START BLOCK : cheque_titulo -->
				<th scope="col">Cheque {i}</th>
				<!-- END BLOCK : cheque_titulo -->
				<!-- START BLOCK : tarjeta_titulo -->
				<th scope="col">Tarjeta {i}</th>
				<!-- END BLOCK : tarjeta_titulo -->
				<th scope="col"><span class="orange">Oficina</span></th>
				<th scope="col">Faltantes y<br />Sobrantes</th>
				<th scope="col">Diferencia</th>
				<th scope="col">Total</th>
				<th scope="col"><img src="/lecaroz/iconos/arrow_up_blue_round.png" class="icono" name="subtract" width="16" height="16" id="subtract" />&nbsp;<img src="/lecaroz/iconos/arrow_down_blue_round.png" class="icono" name="add" width="16" height="16" id="add" /></th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr style="{row_style}">
				<td align="right" class="bold font12">{dia_row}</td>
				<td align="right" class="bold font12"{status_efectivo}>{efectivo}</td>
				<!-- START BLOCK : deposito -->
				<td align="right" class="font12"{deposito_no_conciliado}>{deposito}</td>
				<!-- END BLOCK : deposito -->
				<!-- START BLOCK : cheque -->
				<td align="right" class="font12 green"{cheque_no_conciliado}>{cheque}</td>
				<!-- END BLOCK : cheque -->
				<!-- START BLOCK : tarjeta -->
				<td align="right" class="font12 blue"{tarjeta_no_conciliado}>{tarjeta}</td>
				<!-- END BLOCK : tarjeta -->
				<td align="right" class="font12 orange">{oficina}</td>
				<td align="right" class="font12">{faltantes}</td>
				<td align="right" class="bold font12">{diferencia}</td>
				<td align="right" class="bold font12">{total_depositos}</td>
				<td align="center"><input name="dia[]" type="checkbox" id="dia" value="{dia}" /></td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td align="right" class="bold">Total</td>
				<td align="right" class="bold font12">{total_efectivo}</td>
				<!-- START BLOCK : total_depositos -->
				<td align="right" class="bold font12">{total_depositos}</td>
				<!-- END BLOCK : total_depositos -->
				<!-- START BLOCK : total_cheques -->
				<td align="right" class="bold font12">{total_cheques}</td>
				<!-- END BLOCK : total_cheques -->
				<!-- START BLOCK : total_tarjetas -->
				<td align="right" class="bold font12">{total_tarjetas}</td>
				<!-- END BLOCK : total_tarjetas -->
				<td align="right" class="bold font12"><span class="orange">{total_oficina}</span></td>
				<td align="right" class="bold font12">{total_faltantes}</td>
				<td align="right" class="bold font12">{total_diferencia}</td>
				<td align="right" class="bold font12">{total}</td>
				<td><img src="/lecaroz/iconos/arrow_up_blue_round.png" class="icono" name="subtract" width="16" height="16" id="subtract" />&nbsp;<img src="/lecaroz/iconos/arrow_down_blue_round.png" class="icono" name="add" width="16" height="16" id="add" /></td>
			</tr>
			<tr>
				<td align="right" class="bold">%</td>
				<td align="right">&nbsp;</td>
				<!-- START BLOCK : p_depositos -->
				<td colspan="{p_depositos_columnas}" align="center" class="bold font12"><span class="blue">{p_depositos}</span></td>
				<!-- END BLOCK : p_depositos -->
				<!-- START BLOCK : p_cheques -->
				<td colspan="{p_cheques_columnas}" align="center" class="bold font12"><span class="blue">{p_cheques}</span></td>
				<!-- END BLOCK : p_cheques -->
				<!-- START BLOCK : p_tarjetas -->
				<td colspan="{p_tarjetas_columnas}" align="center" class="bold font12"><span class="blue">{p_tarjetas}</span></td>
				<!-- END BLOCK : p_tarjetas -->
				<td align="right" class="bold font12 red"><span class="red">{p_oficina}</span></td>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td align="right" class="bold">Prom.</td>
				<td align="right" class="bold font12">{prom_efectivo}</td>
				<!-- START BLOCK : prom_depositos -->
				<td align="right" class="bold font12">{prom_depositos}</td>
				<!-- END BLOCK : prom_depositos -->
				<!-- START BLOCK : prom_cheques -->
				<td align="right" class="bold font12">{prom_cheques}</td>
				<!-- END BLOCK : prom_cheques -->
				<!-- START BLOCK : prom_tarjetas -->
				<td align="right" class="bold font12">{prom_tarjetas}</td>
				<!-- END BLOCK : prom_tarjetas -->
				<td colspan="3" align="right" class="bold font12">&nbsp;</td>
				<td align="right" class="bold font12">{prom_total}</td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<table class="table" id="herramientas">
		<thead>
			<tr>
				<th scope="col">Herramientas</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><a id="estado_cuenta_tool" class="enlace bold font10">Estado de cuenta</a></td>
			</tr>
			<tr>
				<td><a id="depositos_oficina_tool" class="enlace bold font10">Dep&oacute;sitos de oficina</a></td>
			</tr>
			<tr>
				<td><a id="facturas_electronicas_tool" class="enlace bold font10">Facturas electr&oacute;nicas</a></td>
			</tr>
			<tr>
				<td><a id="email_tool" class="enlace bold font10">Enviar email</a></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<table class="table" id="leyendas">
		<thead>
			<tr>
				<th colspan="2">Leyendas</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="background-color:#f33;">&nbsp;</td>
				<td class="bold font10">Efectivo con errores</td>
			</tr>
			<tr>
				<td style="background-color:#fc0;">&nbsp;</td>
				<td class="bold font10">Efectivo directo</td>
			</tr>
			<tr>
				<td style="background-color:#6c0;">&nbsp;</td>
				<td class="bold font10">Efectivo incompleto</td>
			</tr>
			<tr>
				<td style="background-color:#69f;">&nbsp;</td>
				<td class="bold font10">Efectivo posterior al corte</td>
			</tr>
			<tr>
				<td style="background-color:#f99;">&nbsp;</td>
				<td class="bold font10">Clientes con errores</td>
			</tr>
			<tr>
				<td style="background-color:#f80;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="bold font10">Dep&oacute;sito no conciliado</td>
			</tr>
			<tr>
				<td align="center" class="bold" style="color:#06f">X</td>
				<td class="bold font10">No es depósito de Cometra</td>
			</tr>
			<tr>
				<td align="center" class="bold" style="color:#60f">X</td>
				<td class="bold font10">En otra cuenta</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" align="center">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<table class="table" id="navegacion">
		<thead>
			<tr>
				<th>Navegación</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="center"><input type="button" name="anterior" id="anterior" value="&lt;&lt; Anterior" />
					&nbsp;
					<input type="button" name="siguiente" id="siguiente" value="Siguiente &gt;&gt;" /></td>
			</tr>
			<tr>
				<td align="center"><input name="next" type="text" class="validate focus toPosInt center" id="next" value="{next}" size="3" />
					<input name="next_nombre" type="text" disabled="disabled" id="next_nombre" value="{next_nombre}" size="20" />
					&nbsp;
					<input type="button" name="ir" id="ir" value="Ir &gt;&gt;" />
				</td>
			</tr>
			<tr>
				<td align="center"><input type="button" name="terminar" id="terminar" value="Terminar" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
