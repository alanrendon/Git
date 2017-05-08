<form action="" method="post" name="modificar_riesgo" class="FormValidator" id="modificar_riesgo">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" align="left" scope="col"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Información general</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compañía</td>
				<td><input name="id" type="hidden" id="id" value="{id}">
					<input name="num_cia" type="text" class="validate focus toPosInt center" id="num_cia" value="{num_cia}" size="3" />
				<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" value="{nombre_cia}" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Trabajador</td>
				<td><select name="idtrabajador" id="idtrabajador">
					<!-- START BLOCK : trabajador -->
					<option value="{value}"{class}{selected}>{text}</option>
					<!-- END BLOCK : trabajador -->
				</select></td>
			</tr>
			<tr>
				<td colspan="2" class="bold" id="info_trabajador">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="bold">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" align="left" class="bold"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Información del riesgo de trabajo</th>
			</tr>
			<tr>
				<td class="bold">Tipo de identificación</td>
				<td><input name="tipo_identificacion" type="text" class="validate toText cleanText toUpper" id="tipo_identificacion" value="{tipo_identificacion}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">UMF de adscripción</td>
				<td><input name="umf_adscripcion" type="text" class="validate toText cleanText toUpper" id="umf_adscripcion" value="{umf_adscripcion}" size="10" maxlength="20" /></td>
			</tr>
			<tr>
				<td class="bold">Delegación (IMSS)</td>
				<td><input name="delegacion_imss" type="text" class="validate toText cleanText toUpper" id="delegacion_imss" value="{delegacion_imss}" size="10" maxlength="20" /></td>
			</tr>
			<tr>
				<td class="bold">Día de descanso previo al accidente</td>
				<td><input name="dia_descanso_previo_accidente" type="text" class="validate focus toDate center" id="dia_descanso_previo_accidente" value="{dia_descanso_previo_accidente}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Horario de trabajo el día del accidente</td>
				<td><input name="hora_entrada" type="text" class="validate focus toTime center" id="hora_entrada" value="{hora_entrada}" size="5" maxlength="5" />
					a
				<input name="hora_salida" type="text" class="validate focus toTime center" id="hora_salida" value="{hora_salida}" size="5" maxlength="5" /></td>
			</tr>
			<tr>
				<td class="bold">Fecha y hora del probable accidente de trabajo</td>
				<td><input name="fecha_accidente" type="text" class="validate focus toDate center" id="fecha_accidente" value="{fecha_accidente}" size="10" maxlength="10" />
				<input name="hora_accidente" type="text" class="validate focus toTime center" id="hora_accidente" value="{hora_accidente}" size="5" maxlength="5" /></td>
			</tr>
			<tr>
				<td class="bold">Fecha y hora en el que el trabajador suspendio labores a causa del accidente</td>
				<td><input name="fecha_suspenso" type="text" class="validate focus toDate center" id="fecha_suspenso" value="{fecha_suspenso}" size="10" maxlength="10" />
				<input name="hora_suspenso" type="text" class="validate focus toTime center" id="hora_suspenso" value="{hora_suspenso}" size="5" maxlength="5" /></td>
			</tr>
			<tr>
				<td class="bold">Fecha y hora de recepcion en el servicio médico</td>
				<td><input name="fecha_servicio_medico" type="text" class="validate focus toDate center" id="fecha_servicio_medico" value="{fecha_servicio_medico}" size="10" maxlength="10" />
				<input name="hora_servicio_medico" type="text" class="validate focus toTime center" id="hora_servicio_medico" value="{hora_servicio_medico}" size="5" maxlength="5" /></td>
			</tr>
			<tr>
				<td class="bold">Señalar claramente como ocurrio el accidente</td>
				<td><textarea name="descripcion_accidente" cols="45" rows="5" class="validate toText toUpper" id="descripcion_accidente">{descripcion_accidente}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Descripcion de la(s) lesion(es) y tiempo de evolución</td>
				<td><textarea name="descripcion_lesiones" cols="45" rows="5" class="validate toText toUpper" id="descripcion_lesiones">{descripcion_lesiones}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Impresión diagnostica</td>
				<td><textarea name="impresion_diagnostica" cols="45" rows="5" class="validate toText toUpper" id="impresion_diagnostica">{impresion_diagnostica}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Tratamiento(s)</td>
				<td><textarea name="tratamientos" cols="45" rows="5" class="validate toText toUpper" id="tratamientos">{tratamientos}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Signos y síntomas</td>
				<td>Intoxicación alcohólica:
					<input name="intoxicacion_alcoholica" type="radio" value="TRUE"{intoxicacion_alcoholica_t} />
					Si
					<input type="radio" name="intoxicacion_alcoholica" value="FALSE"{intoxicacion_alcoholica_f} />
					No
					<input name="intoxicacion_alcoholica" type="radio" value="NULL"{intoxicacion_alcoholica_} />
					Sin definir<br />
					Intoxicación por enervantes:
					<input type="radio" name="intoxicacion_enervantes" value="TRUE"{intoxicacion_enervantes_t} />
					Si
					<input type="radio" name="intoxicacion_enervantes" value="FALSE"{intoxicacion_enervantes_f} />
					No
					<input name="intoxicacion_enervantes" type="radio" value="NULL"{intoxicacion_enervantes_} />
					Sin definir</td>
			</tr>
			<tr>
				<td class="bold">Otras condiciones</td>
				<td>Hubo riña:
					<input type="radio" name="hubo_rina" value="TRUE"{hubo_rina_t} />
					Si
					<input type="radio" name="hubo_rina" value="FALSE"{hubo_rina_f} />
					No
					<input name="hubo_rina" type="radio" value="NULL"{hubo_rina_} />
					Sin definir</td>
			</tr>
			<tr>
				<td class="bold">Atención médica previa extrainstitucional</td>
				<td><input name="nombre_servicio_medico_externo" type="text" class="validate toText cleanText toUpper" id="nombre_servicio_medico_externo" value="{nombre_servicio_medico_externo}" size="40" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="bold">Amerita incapacidad</td>
				<td><input type="radio" name="amerita_incapacidad" value="TRUE"{amerita_incapacidad_t} />
					Si
					<input type="radio" name="amerita_incapacidad" value="FALSE"{amerita_incapacidad_f} />
					No
					<input name="amerita_incapacidad" type="radio" value="NULL"{amerita_incapacidad_} />
					Sin definir</td>
			</tr>
			<tr>
				<td class="bold">Fecha de inicio de incapacidad</td>
				<td><input name="fecha_inicio_incapacidad" type="text" class="validate focus toDate center" id="fecha_inicio_incapacidad" value="{fecha_inicio_incapacidad}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Número de folio</td>
				<td><input name="folio_incapacidad" type="text" class="validate focus onlyNumbersAndLetters right" id="folio_incapacidad" value="{folio_incapacidad}" size="8" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Número de días autorizados</td>
				<td><input name="dias_incapacidad" type="text" class="validate focus toPosInt right" id="dias_incapacidad" value="{dias_incapacidad}" size="8" /></td>
			</tr>
			<tr>
				<td class="bold">Se envía paciente al servicio de</td>
				<td><input name="nombre_servicio" type="text" class="validate toText cleanText toUpper" id="nombre_servicio" value="{nombre_servicio}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Nombre del médico tratante</td>
				<td><input name="nombre_medico" type="text" class="validate toText cleanText toUpper" id="nombre_medico" value="{nombre_medico}" size="40" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="bold">Matrícula (médico)</td>
				<td><input name="matricula_medico" type="text" class="validate focus onlyNumbersAndLetters toUpper" id="matricula_medico" value="{matricula_medico}" size="8" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Unidad médica y delegación</td>
				<td><input name="unidad_medica" type="text" class="validate toText cleanText toUpper" id="unidad_medica" value="{unidad_medica}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Ocupación que desempeñaba al momento del accidente</td>
				<td><input name="ocupacion_trabajador" type="text" class="validate toText cleanText toUpper" id="ocupacion_trabajador" value="{ocupacion_trabajador}" size="40" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="bold">Antigüedad en la ocupación</td>
				<td><input name="antiguedad_trabajador" type="text" class="validate toText cleanText toUpper" id="antiguedad_trabajador" value="{antiguedad_trabajador}" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Salario diario</td>
				<td><input name="salario_diario" type="text" class="validate focus numberPosFormat right" id="salario_diario" value="{salario_diario}" size="12" precision="2" /></td>
			</tr>
			<tr>
				<td class="bold">Matrícula (trabajador IMSS)</td>
				<td><input name="matricula_trabajador" type="text" class="validate toText cleanText toUpper" id="matricula_trabajador" value="{matricula_trabajador}" size="8" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Clave presupuestal de la unidad IMSS de adscripción (trabajador IMSS)</td>
				<td><input name="clave_presupuestal_trabajador" type="text" class="validate toText cleanText toUpper" id="clave_presupuestal_trabajador" value="{clave_presupuestal_trabajador}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Circunstancias en que ocurrio el accidente</td>
				<td><input type="radio" name="circunstancias_accidente" value="1"{circunstancias_accidente_1} />
					En la empresa
					<input type="radio" name="circunstancias_accidente" value="2"{circunstancias_accidente_2} />
					En una comisión
					<input type="radio" name="circunstancias_accidente" value="3"{circunstancias_accidente_3} />
					En trayecto a su trabajo
					<input type="radio" name="circunstancias_accidente" value="4"{circunstancias_accidente_4} />
					En trayecto a su domicilio
					<input type="radio" name="circunstancias_accidente" value="5"{circunstancias_accidente_5} />
					Trabajando tiempo extra
					<input name="circunstancias_accidente" type="radio" value="NULL"{circunstancias_accidente_} />
					Sin definir</td>
			</tr>
			<tr>
				<td class="bold">Descripción precisa de la forma, sitio o área de trabajo en que ocurrio el accidente</td>
				<td><textarea name="descripcion_area_trabajo" cols="45" rows="5" class="validate toText toUpper" id="descripcion_area_trabajo">{descripcion_area_trabajo}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Nombre y cargo de la persona de la empresa que tomo conocimiento inicial del accidente</td>
				<td><input name="nombre_informante" type="text" class="validate toText cleanText toUpper" id="nombre_informante" value="{nombre_informante}" size="40" maxlength="100" />
				<input name="cargo_informante" type="text" class="validate toText cleanText toUpper" id="cargo_informante" value="{cargo_informante}" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Fecha y hora de comunicación del accidente</td>
				<td><input name="fecha_informe_accidente" type="text" class="validate focus toDate center" id="fecha_informe_accidente" value="{fecha_informe_accidente}" size="10" maxlength="10" />
				<input name="hora_informe_accidente" type="text" class="validate focus toTime center" id="hora_informe_accidente" value="{hora_informe_accidente}" size="5" maxlength="5" /></td>
			</tr>
			<tr>
				<td class="bold">Nombre y domicilio de las personas que preseciaron el accidente</td>
				<td><textarea name="informacion_testigos" cols="45" rows="5" class="validate toText toUpper" id="informacion_testigos">{informacion_testigos}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Anotar que autoridades oficiales tomaron conocimiento del accidente</td>
				<td><input name="informacion_autoridades" type="text" class="validate toText cleanText toUpper" id="informacion_autoridades" value="{informacion_autoridades}" size="40" maxlength="2000"></td>
			</tr>
			<tr>
				<td class="bold">Aclaraciones y observaciones</td>
				<td><textarea name="observaciones" cols="45" rows="5" class="validate toText toUpper" id="observaciones">{observaciones}</textarea></td>
			</tr>
			<tr>
				<td class="bold">Nombre del patrón o su representante legal</td>
				<td><input name="nombre_representante_legal" type="text" class="validate toText cleanText toUpper" id="nombre_representante_legal" value="{nombre_representante_legal}" size="40" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="bold">Lugar y fecha</td>
				<td><input name="lugar" type="text" class="validate toText cleanText toUpper" id="lugar" value="{lugar}" size="40" maxlength="100" />
				<input name="fecha" type="text" class="validate focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar riesgo de trabajo" />
	</p>
</form>
