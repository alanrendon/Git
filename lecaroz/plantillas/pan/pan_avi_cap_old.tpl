<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.compania.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.compania.select();
		}
		else {
				document.form.submit();
		}
	}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consumo de Av&iacute;os</p>
<form action="./pan_avi_cap.php" method="get" name="form" id="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="3" maxlength="3"></td>
  </tr>
</table>
<p>
<input name="" type="button" value="Siguiente" class="boton" onClick="form.submit()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : hoja -->
<script type="text/javascript" language="JavaScript">
	function calcula_existencia(consumo, ex_final, temp) {
		var value_final   = !isNaN(parseFloat(ex_final.value))?parseFloat(ex_final.value):0;
		var value_consumo = !isNaN(parseFloat(consumo.value))?parseFloat(consumo.value):0;
		var value_temp    = !isNaN(parseFloat(temp.value))?parseFloat(temp.value):0;
		
		if (value_consumo > 0) {
			if (value_consumo > value_final) {
				alert("El consumo del turno no debe ser mayor a la existencia final");
				consumo.value = temp.value;
				consumo.select();
				return false;
			}
			
			if (value_temp > 0)
				value_final = value_final + value_temp;
			
			value_final = value_final - value_consumo;
			ex_final.value = value_final.toFixed(2);
		}
		else if (consumo.value == "") {
			if (value_temp > 0)
				value_final = value_final + value_temp;
			
			ex_final.value = value_final.toFixed(2);
		}
	}
	
	function entrada(entrada, ex_final, temp) {
		var value_entrada = !isNaN(parseFloat(entrada.value))?parseFloat(entrada.value):0;
		var value_final   = !isNaN(parseFloat(ex_final.value))?parseFloat(ex_final.value):0;
		var value_temp    = !isNaN(parseFloat(temp.value))?parseFloat(temp.value):0;
		
		if (value_entrada > 0) {
			if (value_temp > 0)
				value_final = value_final - value_temp;
			
			value_final = value_final + value_entrada;
			
			if (value_final < 0) {
				alert("El cálculo de la entrada produce una existencia final negativa.\nFavor de revisar los datos.");
				entrada.value = temp.value;
				entrada.select();
				return false;
			}
			
			ex_final.value = value_final.toFixed(2);
			return true;
		}
		else if (entrada.value == "") {
			if (value_temp > 0)
				value_final = value_final - value_temp;
			
			if (value_final < 0) {
				alert("El cálculo de la entrada produce una existencia final negativa.\nFavor de revisar los datos.");
				entrada.value = temp.value;
				entrada.select();
				return false;
			}
			
			ex_final.value = value_final.toFixed(2);
			return true;
		}
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			document.form.entrada0.select();
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.existencia_anterior0.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consumo de Av&iacute;os</p>
<form name="form" method="post" action="./pan_avi_cap.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<input name="numelementos" type="hidden"  value="{numelementos}">
  <table class="tabla">
  <tr>
    <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
    <td class="vtabla" scope="col"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
      <font size="+1"><strong>{num_cia} - {nombre_cia}</strong></font> </td>
    <th class="vtabla" scope="col">Fecha</th>
    <td class="vtabla" scope="col"><input name="fecha" type="hidden" value="{fecha}">
      <font size="+1"><strong>{fecha}</strong></font></td>
  </tr>
</table>
  <br>
  <table width="100%" class="tabla">
    <tr>
      <th class="tabla" scope="col" width="5%">Producto</th>
      <th class="tabla" scope="col">Existencia<br>anterior </th>
      <th class="tabla" scope="col">Entrada</th>
      <th class="tabla" scope="col">FD</th>
      <th class="tabla" scope="col">FN</th>
      <th class="tabla" scope="col">BD</th>
      <th class="tabla" scope="col">Repostero</th>
      <th class="tabla" scope="col">Piconero</th>
      <th class="tabla" scope="col">Gelatinero</th>
      <th class="tabla" scope="col">Despacho</th>
      <th class="tabla" scope="col">Existencia<br>final</th>
    </tr>
    <!-- START BLOCK : mp -->
	<tr height="28" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row" width="5%"><font size="-2" color="#000000">{mp}</font></th>
      <td class="tabla"><input name="existencia_anterior{i}" type="text" class="nombre" id="existencia_anterior{i}" value="{existencia}" size="8" maxlength="10" readonly="true"></td>
      <td class="tabla">
	  <input name="codmp_entrada{i}" type="hidden" id="codmp_entrada{i}" value="{codmp_entrada}">
	  <input name="precio_unidad_entrada{i}" type="hidden" value="{precio_unidad_entrada}">
	  <input name="entrada{i}" type="text" class="insert" id="entrada{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) entrada(this,form.existencia_actual{i},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.consumo{next}.select();
else if (event.keyCode == 37) form.consumo{back}.select();
else if (event.keyCode == 38) form.entrada{top}.select();
else if (event.keyCode == 40) form.entrada{bottom}.select();" size="8" maxlength="10"></td>
      <td class="tabla">
		  <!-- START BLOCK : fd -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="1">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
		  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
		  <!-- END BLOCK : fd -->
	  </td>
	  <td class="tabla">
		  <!-- START BLOCK : fn -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="2">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
		  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
		  <!-- END BLOCK : fn -->
	  </td>
	  <td class="tabla">
		  <!-- START BLOCK : bd -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="3">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
		  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
		  <!-- END BLOCK : bd -->
	  </td>
	  <td class="tabla">
	  <!-- START BLOCK : repostero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="4">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
	  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
	  <!-- END BLOCK : repostero -->
	  </td>
	  <td class="tabla">
	  <!-- START BLOCK : piconero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="8">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
	  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
	  <!-- END BLOCK : piconero -->
	  </td>
	  <td class="tabla">
	  <!-- START BLOCK : gelatinero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="9">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
	  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
	  <!-- END BLOCK : gelatinero -->
	  </td>
	  <td class="tabla">
	  <!-- START BLOCK : despacho -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="10">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
	  <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.{next}.select();
else if (event.keyCode == 37) form.{back}.select();" size="8" maxlength="10">
	  <!-- END BLOCK : despacho -->
	  </td>
      <td class="tabla">
	  <input name="existencia_actual{i}" type="text" class="nombre" id="existencia_actual{i}" value="{existencia}" size="8" maxlength="10" readonly="true"></td>
    </tr>
	<!-- END BLOCK : mp -->
  </table>
  <p align="center">
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>&nbsp;&nbsp;&nbsp;&nbsp;
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" name="enviar" value="Capturar consumos" onclick="valida_registro()">
    
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.entrada0.select()</script>
<!-- END BLOCK : hoja -->
