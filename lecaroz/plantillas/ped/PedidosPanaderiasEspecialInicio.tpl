<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Pedidos recibidos</th>
			<td><select name="pedido" id="pedido" style="font-weight:bold;">
			  <!-- START BLOCK : pedido -->
           <option value="{pedido}" style="color:#{color};">[{fecha}] {num_cia} {nombre_cia}</option>
           <!-- START BLOCK : pedido -->
		   </select></td>
		</tr>
	</table>
	<p>
		<input type="button" name="registrar" id="registrar" value="Registrar pedidos">
		&nbsp;&nbsp;
	<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>