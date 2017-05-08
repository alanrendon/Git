<script language="javascript" type="text/javascript">

	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Gastos pagados por otras compa&ntilde;&iacute;as</p>
<form name="form" method="post" action="./bal_goc_cap.php?tabla={tabla}">
<input type="hidden" name="temp">
<table class="tabla">
  <tr>
    <th class="vtabla">Mes</th>
	<td class="vinsert">
		<select name="fecha" class="insert">
		  <option value="1/1/{anio}" {1}>ENERO</option>
		  <option value="1/2/{anio}" {2}>FEBRERO</option>
		  <option value="1/3/{anio}" {3}>MARZO</option>
		  <option value="1/4/{anio}" {4}>ABRIL</option>
		  <option value="1/5/{anio}" {5}>MAYO</option>
		  <option value="1/6/{anio}" {6}>JUNIO</option>
		  <option value="1/7/{anio}" {7}>JULIO</option>
		  <option value="1/8/{anio}" {8}>AGOSTO</option>
		  <option value="1/9/{anio}" {9}>SEPTIEMBRE</option>
		  <option value="1/10/{anio}" {10}>OCTUBRE</option>
		  <option value="1/11/{anio}" {11}>NOVIEMBRE</option>
		  <option value="1/12/{anio}" {12}>DICIEMBRE</option>
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Cia. que presto </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">Cia. a la que prestaron </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="num_cia_egreso{i}" type="text" class="insert" id="num_cia{i}" onFocus="form.temp.value = this.value" onChange="if (parseInt(this.value) >= 0 || this.value == '') {
actualiza_compania(this,form.nombre_cia_egreso{i});}
else error(this,form.temp);" onKeyUp="if (event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37)
form.num_cia_ingreso{back}.select();
else if (event.keyCode == 40)
form.num_cia_egreso{next}.select();
else if (event.keyCode == 38)
form.num_cia_egreso{back}.select();" size="5" maxlength="5"><input name="nombre_cia_egreso{i}" type="text" disabled="true" class="vnombre" size="30"></td>
    <td class="tabla"><input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyUp="if (event.keyCode == 13 || event.keyCode == 39) form.monto{i}.select();
else if (event.keyCode == 37)
form.num_cia_egreso{i}.select();
else if (event.keyCode == 40)
form.concepto{next}.select();
else if (event.keyCode == 38)
form.concepto{back}.select();" size="50" maxlength="50"></td>
    <td class="tabla"><input name="monto{i}" type="text" class="insert" id="monto{i}" onFocus="form.temp.value=this.value" onChange="if (parseFloat(this.value) >= 0) {
var temp = parseFloat(this.value);
this.value = temp.toFixed(2);
}
else if (this.value == '')
this.value = null;
else error(this,form.temp);" onKeyUp="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia_ingreso{i}.select();
else if (event.keyCode == 37)
form.concepto{i}.select();
else if (event.keyCode == 40)
form.monto{next}.select();
else if (event.keyCode == 38)
form.monto{back}.select();" size="12" maxlength="12"></td>
    <td class="tabla"><input name="num_cia_ingreso{i}" type="text" class="insert" id="num_cia_ingreso{i}" onFocus="form.temp.value=this.value" onChange="if (parseInt(this.value) >= 0 || this.value == '') {
actualiza_compania(this,form.nombre_cia_ingreso{i});}
else error(this,form.temp);" onKeyUp="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia_egreso{next}.select();
else if (event.keyCode == 37)
form.monto{i}.select();
else if (event.keyCode == 40)
form.num_cia_ingreso{next}.select();
else if (event.keyCode == 38)
form.num_cia_ingreso{back}.select();" size="5" maxlength="5">
    <input name="nombre_cia_ingreso{i}" type="text" disabled="true" class="vnombre" id="nombre_cia_ingreso{i}" size="30"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input name="" type="button" class="boton" onClick="if (confirm('¿Desea borrar el formulario?'))
document.form.reset();
else
return false;" value="Borrar">
&nbsp;&nbsp;&nbsp;&nbsp;<img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" onClick="if (confirm('¿Son correctos los datos?'))
document.form.submit();
else
return false;" value="Capturar gastos">
</p>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia_egreso0.select();</script>
</form>
</td>
</tr>
</table>