<p>Seleccione un empleado de la lista:</p>
<input name="pres_row_index" type="hidden" id="pres_row_index" value="{index}" />
<div style="height:200px; margin-top:10px; overflow:auto;">
	<!-- START BLOCK : empleado -->
	<div style="background-color:#{color};"><label><input name="empleado" type="radio" value="{empleado}"{checked} />{num_emp} {nombre_emp}</label></div>
	<!-- END BLOCK : empleado -->
</div>
