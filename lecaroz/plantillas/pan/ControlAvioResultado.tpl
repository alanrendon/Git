<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura" id="controles">
		<thead>
			<tr>
				<th colspan="9" align="left" class="font12" scope="col"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
				{num_cia} {nombre_cia}</th>
			</tr>
			<tr>
				<th>Cod.</th>
				<th>Producto</th>
				<th>FD</th>
				<th>FN</th>
				<th>BZ</th>
				<th>RP</th>
				<th>PC</th>
				<th>GL</th>
				<th>DS</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr class="linea_{row_color}" id="row_{codmp}">
				<td align="right" class="dragme" style="cursor:move;">{codmp}</td>
				<td align="left">{nombre_mp}</td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:1&#125;"{checked1} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:2&#125;"{checked2} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:3&#125;"{checked3} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:4&#125;"{checked4} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:8&#125;"{checked8} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:9&#125;"{checked9} /></td>
				<td align="center"><input name="t[]" type="checkbox" id="t" value="&#123;&quot;num_cia&quot;:{num_cia},&quot;codmp&quot;:{codmp},&quot;turno&quot;:10&#125;"{checked10} /></td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>
