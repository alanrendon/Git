<table class="tabla_captura" id="TablaDatos">
  <!-- START BLOCK : cia -->
  <tr>
	<th colspan="7" align="left" class="font14" scope="col">{num_cia} {nombre_cia} 
    <input name="arrastre_diferencia" type="hidden" id="arrastre_diferencia" value="{arrastre_diferencia}" cia="{num_cia}"></th>
  </tr>
  <tr>
  	<th colspan="6" align="right">Diferencia Inicial</th>
  	<th align="right">{diferencia_inicial}</th>
  	</tr>
  <tr>
	<th><input name="checkblock" type="checkbox" id="checkblock" checked="checked" cia="{num_cia}" /></th>
	<th>D&iacute;a</th>
	<th>Dep&oacute;sitos</th>
	<th>Facturas Clientes<br />
	(pagadas)</th>
	<th>Faturas Clientes<br />
	  (pendientes)</th>
	<th>Facturas Venta</th>
	<th>Diferencia</th>
  </tr>
  <!-- START BLOCK : dia -->
  <tr class="linea_{color_row}">
	<td align="center"><input name="datos[]" type="checkbox" id="datos" value="{datos}"{checked} cia="{num_cia}"{disabled} /></td>
	<td align="right">{dia}</td>
	<td align="right" class="blue">{depositos}</td>
	<td align="right" class="red">{facturas_pagadas}</td>
	<td align="right" class="red">{facturas_pendientes}</td>
	<td align="right" id="fp-{num_cia}-{dia}" class="red">{facturas_panaderia}</td>
	<td align="right" id="dif-{num_cia}-{dia}" class="{color}">{diferencia}</td>
  </tr>
  <!-- END BLOCK : dia -->
  <tr>
	<th colspan="2" align="right">Total</th>
	<th align="right" class="blue">{depositos}</th>
	<th align="right" class="red">{facturas_pagadas}</th>
	<th align="right" class="red">{facturas_pendientes}</th>
	<th align="right" id="tfp-{num_cia}" class="red">{facturas_panaderia}</th>
	<th align="right" id="tdif-{num_cia}" class="{color}">{diferencia}</th>
  </tr>
  <tr>
	<td colspan="7">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<p>
  <input name="regresar" type="button" id="regresar" value="Regresar">
&nbsp;&nbsp;
<input name="generar" type="button" id="generar" value="Generar Facturas Electr&oacute;nicas">
</p>
