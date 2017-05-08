<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : prestamos -->
<script language="javascript" type="text/javascript">
	function calcular_faltante(debe, importe, falta, total, temp) {
		var debe_value = parseFloat(debe.value);
		var importe_value = parseFloat(importe.value);
		var falta_value = parseFloat(falta.value);
		var total_value = parseFloat(total.value);
		var temp_value = parseFloat(temp.value);
		
		if (importe_value >= 0) {
			falta_value = falta_value - importe_value;
			if (temp_value > 0)
				total_value = total_value - temp_value;
			
			total_value += importe_value;
			
			falta.value = falta_value.toFixed(2);
			total.value = total_value.toFixed(2);
		}
		else if (importe.value == "") {
			if (temp_value > 0)
				total_value -= temp_value;
			
			total.value = total_value.toFixed(2);
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Pago de Prestamos</p>
<form name="form" method="post" action="./ros_pre_pago.php?tabla=prestamos">
<input type="hidden" name="numfilas" value="{numfilas}">
<input type="hidden" name="temp">
<input type="hidden" name="temp_total">
<input name="num_cia" type="hidden" value="{num_cia}">
<input name="fecha" type="hidden" value="{fecha}">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
	<td class="vtabla">{num_cia} - {nombre_cia}</td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla">{fecha}</td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">N&uacute;mero y nombre trabajador </th>
    <th class="tabla" scope="col">Prestamo</th>
    <th class="tabla" scope="col">A cuenta </th>
    <th class="tabla" scope="col">Debe</th>
  </tr>
<!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla"><input name="id_empleado{i}" type="hidden" id="id_empleado{i}" value="{id_empleado}">
      {num_emp}</td>
    <td class="vtabla">{nombre_trabajador}</td>
    <td class="tabla"><input name="debe{i}" type="hidden" id="debe{i}" value="{debe}">
    {fdebe}</td>
    <td class="tabla"><input name="importe{i}" type="text" class="insert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) <= parseFloat(form.total_debido{i}.value)) || this.value == '')
calcular_faltante(form.debe{i},this,form.falta{i},form.total,form.temp);
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.importe{next}.select();
else if (event.keyCode == 38) form.importe{back}.select();" size="12" maxlength="12"></td>
    <td class="tabla"><input name="total_debido{i}" type="hidden" value="{falta}"><input name="falta{i}" type="text" class="total" value="{falta}" size="12" maxlength="12"></td>
  </tr>
<!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rtabla">Total:</th>
    <th class="tabla"><input name="total" type="text" class="total" id="total" value="0" size="12" maxlength="12"></th>
    <th class="tabla">&nbsp;</th>
  </tr>
</table>
<p>
  <input type="button" class="boton" onClick="parent.close();" value="Cerrar ventana">&nbsp;&nbsp;<input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro()">
</p>
</form>
<!-- END BLOCK : prestamos -->
<!-- START BLOCK : error -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : error -->
