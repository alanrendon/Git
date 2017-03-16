<form action="" method="get" name="EmailForm" class="FormValidator FormStyles" id="EmailForm">
<input name="id" type="hidden" id="id" value="{id}" />
<table class="tabla_captura">
  <tr>
	<th align="left" scope="row">Destinatario 1 </th>
	<td><input name="email[]" type="text" class="valid toEmail" id="email" value="{email}" size="40" /></td>
  </tr>
  <tr>
	<th align="left" scope="row">Destinatario 2 </th>
	<td><input name="email[]" type="text" class="valid toEmail" id="email" size="40" /></td>
  </tr>
  <tr>
	<th align="left" scope="row">Destinatario 3 </th>
	<td><input name="email[]" type="text" class="valid toEmail" id="email" size="40" /></td>
  </tr>
</table>
<p>
  <input name="cancelar_email" type="button" id="cancelar_email" value="Cancelar" />
&nbsp;&nbsp;
<input name="enviar_email" type="button" id="enviar_email" value="Enviar" />
</p>
</form>