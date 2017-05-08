<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location = './ban_conciliacion.php';
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : cerrar2 -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	window.opener.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar2 -->
<!-- START BLOCK : dividir -->
<script language="javascript" type="text/javascript">
	function calcula_total(current) {
		var dep1 = isNaN(parseFloat(document.form.dep1.value))?0:parseFloat(document.form.dep1.value);
		var dep2 = isNaN(parseFloat(document.form.dep2.value))?0:parseFloat(document.form.dep2.value);
		var dep3 = isNaN(parseFloat(document.form.dep3.value))?0:parseFloat(document.form.dep3.value);
		var dep4 = isNaN(parseFloat(document.form.dep4.value))?0:parseFloat(document.form.dep4.value);
		var dep5 = isNaN(parseFloat(document.form.dep5.value))?0:parseFloat(document.form.dep5.value);
		var deposito = parseFloat(document.form.deposito.value);
		var total = 0;
		
		valor_max = Math.floor(deposito) + 0.99;
		total = dep1 + dep2 + dep3 + dep4 + dep5;
		
		if (total > valor_max) {
			alert("La suma de los importes no debe ser mayor al depósito original");
			current.value = document.form.temp.value;
			current.select();
			return false;
		}
		else {
			document.form.total.value = total.toFixed(2);
			return true;
		}
	}
	
	function valida_registro() {
		var dep1 = isNaN(parseFloat(document.form.dep1.value))?0:parseFloat(document.form.dep1.value);
		var dep2 = isNaN(parseFloat(document.form.dep2.value))?0:parseFloat(document.form.dep2.value);
		var dep3 = isNaN(parseFloat(document.form.dep3.value))?0:parseFloat(document.form.dep3.value);
		var dep4 = isNaN(parseFloat(document.form.dep4.value))?0:parseFloat(document.form.dep4.value);
		var dep5 = isNaN(parseFloat(document.form.dep5.value))?0:parseFloat(document.form.dep5.value);
		var deposito = parseFloat(document.form.deposito.value);
		var total = 0;
		
		valor_max = Math.floor(deposito) + 0.99;
		valor_min = Math.floor(deposito);
		total = dep1 + dep2 + dep3 + dep4 + dep5;
		
		if (total < valor_min || total > valor_max) {
			alert("La suma de los importes debe ser igual al depósito");
			document.form.dep1.select();
			return false;
		}
		else {
			document.form.submit();
			return true;
		}
	}
</script>
<p align="center" class="title">Dividir Dep&oacute;sito</p>
<form name="form" method="post" action="./ban_dep_div.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="efe" type="hidden" id="efe" value="{efe}">
<table align="center">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Fecha de dep&oacute;sito </th>
    <th class="tabla" scope="col">Dep&oacute;sito</th>
  </tr>
  <tr>
    <td class="tabla"><strong>{num_cia} - {nombre_cia} </strong></td>
    <td class="tabla"><strong>{fecha_dep}</strong></td>
    <td class="tabla"><input type="hidden" name="id" value="{id}">
    <input name="deposito" type="hidden" value="{deposito}">
    <strong>{fdeposito}</strong></td>
  </tr>
</table>
<br>
<table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col">No</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <tr>
    <td class="tabla">1</td>
    <td class="tabla"><input name="dep1" type="text" class="rinsert" id="dep1" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total(this);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dep2.select();
else if (event.keyCode == 38) form.dep5.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <td class="tabla">2</td>
    <td class="tabla"><input name="dep2" type="text" class="rinsert" id="dep2" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total(this);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dep3.select();
else if (event.keyCode == 38) form.dep1.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <td class="tabla">3</td>
    <td class="tabla"><input name="dep3" type="text" class="rinsert" id="dep3" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total(this);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dep4.select();
else if (event.keyCode == 38) form.dep2.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <td class="tabla">4</td>
    <td class="tabla"><input name="dep4" type="text" class="rinsert" id="dep4" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total(this);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dep5.select();
else if (event.keyCode == 38) form.dep3.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <td class="tabla">5</td>
    <td class="tabla"><input name="dep5" type="text" class="rinsert" id="dep5" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total(this);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.dep1.select();
else if (event.keyCode == 38) form.dep4.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <th class="tabla">Total</th>
    <th class="tabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" size="12" maxlength="12"></th>
  </tr>
</table>
<p align="center">
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
  <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="valida_registro()">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.dep1.select();</script>
<!-- END BLOCK : dividir -->
