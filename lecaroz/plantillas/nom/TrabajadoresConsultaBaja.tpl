
<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="id_trabajador" type="hidden" id="id_trabajador" value="{id}" />
	<p>Para dar de baja al empleado debe seleccionar una opci&oacute;n en la lista:</p>
	<p>
		<select name="tipo_baja" id="tipo_baja">
			<!-- START BLOCK : tipo_baja -->
			<option value="{value}">{text}</option>
			<!-- END BLOCK : tipo_baja -->
		</select>
	</p>
	<p>
		<input type="button" name="cancelar_baja" id="cancelar_baja" value="Cancelar">&nbsp;&nbsp;
		<input type="button" name="aceptar_baja" id="aceptar_baja" value="Aceptar">
	</p>
</form>
