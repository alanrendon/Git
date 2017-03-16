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
	function actualiza_empleado(num_emp, nombre, id_emp) {
		var emp = new Array();
		var id = new Array();
		<!-- START BLOCK : nombre_emp -->
		emp[{num_emp}] = "{nombre_emp}";
		id[{num_emp}] = {id};
		<!-- END BLOCK : nombre_emp -->
		
		if (num_emp.value > 0) {
			if (emp[parseInt(num_emp.value)] == null) {
				alert("El empleado no. "+parseInt(num_emp.value)+" no existe para esta compañía");
				num_emp.value = "";
				nombre.value  = "";
				id_emp.value = "";
				num_emp.focus();
				return false;
			}
			else {
				num_emp.value = parseInt(num_emp.value);
				nombre.value   = emp[parseInt(num_emp.value)];
				id_emp.value = id[parseInt(num_emp.value)];
				return;
			}
		}
		else if (num_emp.value == "") {
			num_emp.value = "";
			nombre.value  = "";
			id_emp.value = "";
			return;
		}
	}
	
	function calcular_total(importe, total, temp) {
		var importe_value = parseFloat(importe.value);
		var total_value = parseFloat(total.value);
		var temp_value = parseFloat(temp.value);
		
		if (importe_value >= 0) {
			if (temp_value > 0)
				total_value = total_value - temp_value;
			
			total_value += importe_value;
			
			total.value = total_value.toFixed(2);
		}
		else if (importe.value == "") {
			if (temp_value > 0)
				total_value = total_value - temp_value;
			
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
<p class="title">Alta de Prestamos</p>
<form name="form" method="post" action="./ros_pre_altas.php?tabla=prestamos">
<input type="hidden" name="temp">
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
    <th class="tabla" scope="col">Importe del prestamo </th>
    </tr>
<!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla"><input name="id_empleado{i}" type="hidden" id="id_empleado{i}" value="{id_empleado}">
    <input name="num_emp{i}" type="text" class="insert" id="num_emp{i}" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_empleado(this,form.nombre{i},form.id_empleado{i})" onKeyDown="if (event.keyCode == 13) form.importe{i}.select();
else if (event.keyCode == 37) form.importe{i}.select();
else if (event.keyCode == 38) form.num_emp{back}.select();
else if (event.keyCode == 39) form.importe{i}.select();
else if (event.keyCode == 40) form.num_emp{next}.select();" value="{num_emp}" size="5" maxlength="5"></td>
    <td class="vtabla"><input name="nombre{i}" type="text" disabled="true" class="vnombre" id="nombre{i}" value="{nombre_emp}" size="30" maxlength="30"></td>
    <td class="tabla"><input name="importe{i}" type="text" class="insert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="if (parseFloat(this.value) >= 0 || this.value == '')
calcular_total(this,form.total,form.temp)
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13) form.num_emp{next}.select();
else if (event.keyCode == 37) form.num_emp{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 39) form.num_emp{i}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{importe}" size="12" maxlength="12"></td>
    </tr>
<!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="rtabla">Total:</th>
    <th class="tabla"><input name="total" type="text" class="total" id="total" value="{total}" size="12" maxlength="12"></th>
    </tr>
</table>
<p>
  <input type="button" class="boton" onClick="parent.close();" value="Cerrar ventana">&nbsp;&nbsp;<input type="button" class="boton" onClick="window.open('./ros_emp_altas.php','alta_empleados','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=480')" value="Alta de Empleados">
  &nbsp;&nbsp;<input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro()">
</p>
</form>
<!-- END BLOCK : prestamos -->
<!-- START BLOCK : error_reg -->
<p class="error">No hay empleados para la compa&ntilde;&iacute;a </p>
<p><input type="button" class="boton" onClick="parent.close();" value="Cerrar ventana">&nbsp;&nbsp;<input type="button" class="boton" onClick="window.open('./ros_emp_altas.php','alta_empleados','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=480')" value="Alta de Empleados"></p>
</td>
</tr>
</table>
<!-- END BLOCK : error_reg -->
