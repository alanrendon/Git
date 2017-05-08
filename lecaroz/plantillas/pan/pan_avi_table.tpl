<form action="./pan_avi_cap.php?tabla={tabla}" method="post" name="form" target="mainFrame">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<input name="numelementos" type="hidden"  value="{numelementos}">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
<input name="fecha" type="hidden" value="{fecha}">
<input name="pro_fd" type="hidden" value="{pro_fd}">
<input name="pro_fn" type="hidden" value="{pro_fn}">
<table>
    <!-- START BLOCK : mp -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
      <th width="84" class="vtabla" scope="row" onclick="aux({num_cia},{codmp},{mes},{anio})"><font size="-2" color="#000000">{mp}</font></th>
      <td width="83" class="rtabla" {color}><input name="existencia_anterior{i}" type="hidden" class="nombre" id="existencia_anterior{i}" value="{existencia}"><strong><font color="#{color}">{fexistencia}</font></strong></td>
      <td width="85" class="tabla">
	  <input name="codmp_entrada{i}" type="hidden" id="codmp_entrada{i}" value="{codmp_entrada}">
	  <input name="precio_unidad_entrada{i}" type="hidden" value="{precio_unidad_entrada}">
	  <input name="entrada{i}" type="text" class="insert" id="entrada{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) entrada(this,form.existencia_actual{i},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) form.consumo{next}.select();
else if (event.keyCode == 37) form.consumo{back}.select();
else if (event.keyCode == 38) form.entrada{top}.select();
else if (event.keyCode == 40) form.entrada{bottom}.select();" size="8" maxlength="10"></td>
      <td width="85" class="tabla">
	  <!-- START BLOCK : fd -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="1">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
          <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">		  
        <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : fd -->
	  <!-- START BLOCK : no_fd -->
	  &nbsp;
	  <!-- END BLOCK : no_fd -->	  </td>
	  <td width="85" class="tabla">
		  <!-- START BLOCK : fn -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="2">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
          <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">		  
        <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : fn -->
	  <!-- START BLOCK : no_fn -->
	  &nbsp;
	  <!-- END BLOCK : no_fn -->	  </td>
	  <td width="85" class="tabla">
		  <!-- START BLOCK : bd -->
		  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
		  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="3">
		  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
          <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">		  
        <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : bd -->
	  <!-- START BLOCK : no_bd -->
	  &nbsp;
	  <!-- END BLOCK : no_bd -->	  </td>
	  <td width="85" class="tabla">
	  <!-- START BLOCK : repostero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">

	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="4">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
      <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">	  
      <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : repostero -->
	  <!-- START BLOCK : no_repostero -->
	  &nbsp;
	  <!-- END BLOCK : no_repostero -->	  </td>
	  <td width="85" class="tabla">
	  <!-- START BLOCK : piconero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="8">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
      <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">	  
      <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : piconero -->
	  <!-- START BLOCK : no_piconero -->
	  &nbsp;
	  <!-- END BLOCK : no_piconero -->	  </td>
	  <td width="85" class="tabla">
	  <!-- START BLOCK : gelatinero -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="9">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
      <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">	  
      <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : gelatinero -->
	  <!-- START BLOCK : no_gelatinero -->
	  &nbsp;
	  <!-- END BLOCK : no_gelatinero -->	  </td>
	  <td width="85" class="tabla">
	  <!-- START BLOCK : despacho -->
	  <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
	  <input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="10">
	  <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}">
      <input name="avg{i}" type="hidden" id="avg{i}" value="{avg}">	  
      <input name="consumo{i}" type="text" class="insert" id="consumo{i}" onfocus="form.temp.value=this.value" onchange="if (isFloat(this,2,form.temp)) calcula_existencia(this,form.existencia_actual{fila},form.temp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) {right};
else if (event.keyCode == 37) {left};
else if (event.keyCode == 38) {up};
else if (event.keyCode == 40) {down};" size="8" maxlength="10">
	  <!-- END BLOCK : despacho -->
	  <!-- START BLOCK : no_despacho -->
	  &nbsp;
	  <!-- END BLOCK : no_despacho -->	  </td>
      <td class="tabla">
	  <input name="existencia_actual{i}" type="text" class="rnombre" id="existencia_actual{i}" value="{existencia}" size="8" maxlength="10" readonly="true"></td>
    </tr>
	<!-- END BLOCK : mp -->
  </table>
  <p align="center">    <input type="button" class="boton" value="Regresar" onclick="window.mainFrame.document.location='./pan_avi_cap.php'">
&nbsp;&nbsp;&nbsp;    
<input type="button" class="boton" name="enviar" value="Capturar consumos" onclick="valida_registro(this.form)" ondblclick="return false">
</p>
</form>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		/*var harina_fd = !isNaN(parseFloat(form.consumo{harina_fd}.value)) ? parseFloat(form.consumo{harina_fd}.value) : 0;
		var harina_fn = !isNaN(parseFloat(form.consumo{harina_fn}.value)) ? parseFloat(form.consumo{harina_fn}.value) : 0;
		var levadura_fd = !isNaN(parseFloat(form.consumo{levadura_fd}.value)) ? parseFloat(form.consumo{levadura_fd}.value) : 0;
		var levadura_fn = !isNaN(parseFloat(form.consumo{levadura_fn}.value)) ? parseFloat(form.consumo{levadura_fn}.value) : 0;
		var grasa_fd = !isNaN(parseFloat(form.consumo{grasa_fd}.value)) ? parseFloat(form.consumo{grasa_fd}.value) : 0;
		var grasa_fn = !isNaN(parseFloat(form.consumo{grasa_fn}.value)) ? parseFloat(form.consumo{grasa_fn}.value) : 0;
		var aceite_fd = !isNaN(parseFloat(form.consumo{aceite_fd}.value)) ? parseFloat(form.consumo{aceite_fd}.value) : 0;
		var aceite_fn = !isNaN(parseFloat(form.consumo{aceite_fn}.value)) ? parseFloat(form.consumo{aceite_fn}.value) : 0;
		var consumo_levadura_fd = harina_fd * 3;
		var consumo_levadura_fn = harina_fn * 3;
		var consumo_grasas_fd = harina_fd * 0.2;
		var consumo_grasas_fn = harina_fn * 0.2;
		
		if ((form.pro_fd.value == 1 && harina_fd == 0) || (form.pro_fn.value == 1 && harina_fn == 0)) {
			alert("El consumo de harina para los franceseros no puede ser menor a 0");
			return false;
		}
		else if ((form.pro_fd.value == 1 && levadura_fd == 0) || (form.pro_fn.value == 1 && levadura_fn == 0)) {
			alert("El consumo de levadura para los franceseros no puede ser menor a 0");
			return false;
		}
		else if (levadura_fd > consumo_levadura_fd) {
			alert("El consumo de levadura de Frances de Día no puede sobrepasar los " + consumo_levadura_fd.toFixed(2));
			return false;
		}
		else if (levadura_fn > consumo_levadura_fn) {
			alert("El consumo de levadura de Frances de Noche no puede sobrepasar los " + consumo_levadura_fn.toFixed(2));
			return false;
		}
		else if (grasa_fd + aceite_fd > consumo_grasas_fd) {
			alert("El consumo de grasas para los franceseros no puede ser mayor a " + consumo_grasas_fd.toFixed(2));
			return false;
		}
		else if (grasa_fn + aceite_fn > consumo_grasas_fn) {
			alert("El consumo de grasas para los franceseros no puede ser mayor a " + consumo_grasas_fn.toFixed(2));
			return false;
		}
		else */if (confirm("¿Son correctos los datos?")) {
			form.submit();
			form.enviar.disabled = true;
		}
		else
			return false;
	}
	
	function calcula_existencia(consumo, ex_final, temp/*, avg*/) {
		var value_final   = !isNaN(parseFloat(ex_final.value))?parseFloat(ex_final.value):0;
		var value_consumo = !isNaN(parseFloat(consumo.value))?parseFloat(consumo.value):0;
		var value_temp    = !isNaN(parseFloat(temp.value))?parseFloat(temp.value):0;
		//var value_avg     = !isNaN(parseFloat(avg.value))?parseFloat(avg.value):0;
		
		if (value_consumo > 0) {
			/*if (value_consumo > value_avg)
				if (!confirm("La valor capturado es mayor al consumo promedio del turno. ¿Desea continuar?"))
					return false;*/
			
			if (/*value_consumo > value_final*/value_final-value_consumo < 0) {
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
	
	function aux(num_cia,codmp,mes,anio) {
		var window_aux = window.open("./pan_miniaux.php?num_cia="+num_cia+"&codmp="+codmp+"&mes="+mes+"&anio="+anio,"miniaux","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
		window_aux.moveTo(0,0);
	}
	
	function alcargar() {
		window.focus();
		document.form.entrada0.select();
	}
	
	window.onload = alcargar();
</script>