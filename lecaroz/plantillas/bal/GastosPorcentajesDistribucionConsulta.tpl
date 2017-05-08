<form name="porcentajes_form" class="FormValidator" id="porcentajes_form">
	<input name="codgastos" type="hidden" id="codgastos" value="{codgastos}" />
	<table class="table">
		<thead>
			<tr>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold font14">{codgastos} {descripcion}</td>
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
				<th>Compa&ntilde;&iacute;a</th>
				<th>Rosticer&iacute;a</th>
				<th>%</th>
				<th>Rosticer&iacute;a</th>
				<th>%</th>
				<th>Rosticer&iacute;a</th>
				<th>%</th>
				<th>Rosticer&iacute;a</th>
				<th>%</th>
				<th>Rosticer&iacute;a</th>
				<th>%</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr>
				<td align="left">
					<input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
					{num_cia} {nombre_cia}
				</td>
				<td>
					<input name="id_0[]" type="hidden" id="id_0" value="{id_0}" />
					<input name="num_ros_0[]" type="text" class="validate focus toPosInt right green" id="num_ros_0" size="1" value="{num_ros_0}" />
					<input name="nombre_ros_0[]" type="text" class="green" id="nombre_ros_0" size="10" value="{nombre_ros_0}" disabled="disabled" />
				</td>
				<td>
					<input name="porc_0[]" type="text" class="validate focus numberPosFormat right green" precision="2" id="porc_0" size="3" maxlength="5" value="{porc_0}" />
				</td>
				<td>
					<input name="id_1[]" type="hidden" id="id_1" value="{id_1}" />
					<input name="num_ros_1[]" type="text" class="validate focus toPosInt right blue" id="num_ros_1" size="1" value="{num_ros_1}" />
					<input name="nombre_ros_1[]" type="text" class="blue" id="nombre_ros_1" size="10" value="{nombre_ros_1}" disabled="disabled" />
				</td>
				<td>
					<input name="porc_1[]" type="text" class="validate focus numberPosFormat right blue" precision="2" id="porc_1" size="3" maxlength="5" value="{porc_1}" />
				</td>
				<td>
					<input name="id_2[]" type="hidden" id="id_2" value="{id_2}" />
					<input name="num_ros_2[]" type="text" class="validate focus toPosInt right red" id="num_ros_2" size="1" value="{num_ros_2}" />
					<input name="nombre_ros_2[]" type="text" class="red" id="nombre_ros_2" size="10" value="{nombre_ros_2}" disabled="disabled" />
				</td>
				<td>
					<input name="porc_2[]" type="text" class="validate focus numberPosFormat right red" precision="2" id="porc_2" size="3" maxlength="5" value="{porc_2}" />
				</td>
				<td>
					<input name="id_3[]" type="hidden" id="id_3" value="{id_3}" />
					<input name="num_ros_3[]" type="text" class="validate focus toPosInt right purple" id="num_ros_3" size="1" value="{num_ros_3}" />
					<input name="nombre_ros_3[]" type="text" clas="purple" id="nombre_ros_3" size="10" value="{nombre_ros_3}" disabled="disabled" />
				</td>
				<td>
					<input name="porc_3[]" type="text" class="validate focus numberPosFormat right purple" precision="2" id="porc_3" size="3" maxlength="5" value="{porc_3}" />
				</td>
				<td>
					<input name="id_4[]" type="hidden" id="id_4" value="{id_4}" />
					<input name="num_ros_4[]" type="text" class="validate focus toPosInt right orange" id="num_ros_4" size="1" value="{num_ros_4}" />
					<input name="nombre_ros_4[]" type="text" class="orange" id="nombre_ros_4" size="10" value="{nombre_ros_4}" disabled="disabled" />
				</td>
				<td>
					<input name="porc_4[]" type="text" class="validate focus numberPosFormat right orange" precision="2" id="porc_4" size="3" maxlength="5" value="{porc_4}" />
				</td>

			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="11">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar">
	</p>
</form>
