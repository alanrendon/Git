<!-- START BLOCK : num_cia -->
<script language="javascript" type="text/javascript">
	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia_ini -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia_ini -->
		
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
	
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		/*else if (document.form.fecha.value == "") {
			alert("Debe especificar la fecha");
			document.form.fecha.select();
			return false;
		}*/
		else {
			document.form.enviar.disabled = true;
			document.form.submit();
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Proceso Secuencial</p>
<form name="form" method="get" action="./ros_pro_sec.php">
<input name="tabla" type="hidden" value="compra_directa">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if(isInt(this,form.temp)) actualiza_compania(this,form.nombre)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.fecha.select();" size="3" maxlength="3"><input name="nombre" type="text" disabled="true" class="vnombre" size="30" maxlength="30"></td>
    <!--
	<th class="vtabla">Fecha <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha" type="text" id="fecha" class="insert" onChange="/*isDate(this)*/" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) form.num_cia.select();" size="10" maxlength="10" value="{fecha}"></td>
	-->  
  </tr>
</table>
<p>
<input name="enviar" type="button" class="boton" id="enviar" value="Ir a Compra Directa >>" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : num_cia -->

<!-- START BLOCK : compra_directa -->
<script language="javascript" type="text/javascript">
	function cambia_precio(form) {
		window.open("","cambia_precio","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
		form.target = "cambia_precio";
		form.action = "./ros_prc_minimod.php";
		form.submit();
		
		form.target = "mainFrame";
		form.action = "./ros_pro_sec.php?tabla=hoja_diaria_rost";
	}
	
	// Validar y actualizar número y nombre del proveedor
	function actualiza_proveedor(num_proveedor, nombre) {
		// Arreglo con los nombres de las materias primas
		proveedor = new Array();				// Materias primas
		<!-- START BLOCK : nombre_proveedor -->
		proveedor[{num_proveedor}] = '{nombre_proveedor}';
		<!-- END BLOCK : nombre_proveedor -->
				
		if (num_proveedor.value > 0) {
			if (proveedor[parseInt(num_proveedor.value)] == null) {
				alert("Proveedor "+parseInt(num_proveedor.value)+" no esta en el catálogo de proveedores");
				num_proveedor.value = "";
				nombre.value  = "";
				num_proveedor.focus();
				return false;
			}
			else {
				num_proveedor.value = parseInt(num_proveedor.value);
				nombre.value   = proveedor[parseInt(num_proveedor.value)];		
				return;
			}
		}
		else if (num_proveedor.value == "") {
			num_proveedor.value = "";
			nombre.value  = "";
			return;
		}
	}

	function actualiza_mp(codmp, nombre, cantidad, kilos, precio, importe, aplica, num_proveedor, nombre_proveedor, numero_fact, temp_codmp, temp_total, preMin, preMax) {
		var total;
		
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		precio_mp = new Array();		// Precios por materia prima
		precio_min = new Array();		// Precio mínimo de la materia prima por pieza
		precio_max = new Array();		// Precio máximo de la materia prima por pieza
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}]     = '{nombre_mp}';
		precio_mp[{codmp}] = {precio_mp};
		precio_min[{codmp}] = {precio_min};
		precio_max[{codmp}] = {precio_max};
		<!-- END BLOCK : nombre_mp -->
				
		if (parseInt(codmp.value) > 0) {
			if (mp[parseInt(codmp.value)] == null) {
				alert("Código "+parseInt(codmp.value)+" no esta en el catálogo de materias primas para rosticerías");
				codmp.value = temp_codmp.value;
				codmp.focus();
				return false;
			}
			else {
				codmp.value = parseInt(codmp.value);
				nombre.value = mp[parseInt(codmp.value)];
				precio.value = precio_mp[parseInt(codmp.value)].toFixed(2);
				preMin.value = precio_min[parseInt(codmp.value)].toFixed(2);
				preMax.value = precio_max[parseInt(codmp.value)].toFixed(2);
				return;
			}
		}
		else if (codmp.value == "") {
				if (parseFloat(temp_total.value) > 0) {
					total = parseFloat(document.form.total.value) - parseFloat(temp_total.value);
					document.form.total.value = total.toFixed(2);
				}
				nombre.value   = "";
				cantidad.value = "";
				kilos.value    = "";
				precio.value   = "";
				importe.value  = "";
				aplica.checked = true;
				num_proveedor.value = 289;
				nombre_proveedor.value = "COMPRAS DIRECTAS";
				numero_fact.value = "";
				preMin.value = "";
				preMax.value = "";
				codmp.focus();
				return false;
		}
	}
	
	function totales(codmp, unidades, kilos, precio, importe, temp, precio_min, precio_max) {
		var importe_parcial;
		var importe_total = parseFloat(document.form.total.value);
		var value_unidades = parseFloat(unidades.value);
		var value_kilos = parseFloat(kilos.value);
		var value_precio = parseFloat(precio.value);
		var value_temp = parseFloat(temp.value);
		var value_precio_min = parseFloat(precio_min.value);
		var value_precio_max = parseFloat(precio_max.value);
		
		// Validar primero que haya un código de materia prima
		if (codmp.value > 0) {
			// Si kilos y precio son mayores a cero, calcular el total
			if (value_unidades >= 0 && value_kilos >= 0 && value_precio >= 0) {
				importe_parcial = value_kilos * value_precio;
				// Verificar si el precio por pieza no pasa del +-20% del precio promedio
				if (importe_parcial/value_unidades < value_precio_min || importe_parcial/value_unidades > value_precio_max) {
					var cantidad_max = importe_parcial / value_precio_min;
					var cantidad_min = importe_parcial / value_precio_max;
					alert("Cantidad debe estar entre "+Math.ceil(cantidad_min)+" y "+Math.floor(cantidad_max)+" unidades");
					value_unidades = (cantidad_min + cantidad_max) / 2;
					kilos.focus();
				}
				
				if (value_temp >= 0 && importe_total > 0)
					importe_total = importe_total - value_temp;
				importe_total = importe_total + importe_parcial;
				
				unidades.value = Math.floor(value_unidades);
				kilos.value    = value_kilos.toFixed(2);
				precio.value   = value_precio.toFixed(2);
				
				importe.value = importe_parcial.toFixed(2);
				document.form.total.value = importe_total.toFixed(2);
				return false;
			}
			// Si solo precio es mayor a cero, calcular el total multiplicando por las unidades
			if (value_unidades >= 0 && value_precio >= 0 && (kilos.value < 0 || kilos.value == "")) {
				importe_parcial = value_unidades * value_precio;
				if (value_temp >= 0 && importe_total > 0)
					importe_total = importe_total - value_temp;
				importe_total = importe_total + importe_parcial;
				
				unidades.value = value_unidades.toFixed(2);
				precio.value   = value_precio.toFixed(2);
				
				importe.value = importe_parcial.toFixed(2);
				document.form.total.value = importe_total.toFixed(2);
				return false;
			}
			else {
				if (value_temp >= 0 && importe_total > 0)
					importe_total = importe_total - value_temp;
				
				importe.value = "";
				document.form.total.value = importe_total.toFixed(2);
				return false;
			}
		}
		else {
			alert("Debe especificar un código de materia prima");
			unidades.value = "";
			kilos.value = "";
			precio.value = "";
			importe.value = "";
			
			if (value_temp >= 0 && importe_total > 0) {
				importe_total = importe_total - value_temp;
				document.form.total.value = importe_total.toFixed(2);
			}
			codmp.focus();
			return false;
		}
	}
	
	function aplica_gasto(aplica, codprov, nombreprov, fact) {
		if (aplica.checked) {
			codprov.value = 289;
			nombreprov.value = "COMPRAS DIRECTAS";
			codprov.readOnly = true;
			fact.select();
		}
		else {
			codprov.value = "";
			nombreprov.value = "";
			codprov.readOnly = false;
			codprov.select();
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
	
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.focus();
		}
		else if (document.form.fecha_mov.value == "") {
			alert("Debe especificar la fecha de movimiento");
			document.form.fecha_mov.focus();
		}
		else {
			document.form.enviar.disabled = true;
			document.form.submit();
		}
	}
	
	function borrar() {
		if (confirm("Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.focus();
		}
		else
			document.form.num_cia.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Compra Directa</p>
<form name="form" method="post" action="./ros_pro_sec.php?tabla=hoja_diaria_rost">
<input name="tabla" type="hidden" value="compra_directa">
<input name="temp" type="hidden" value="">
<input name="temp_codmp" type="hidden" value="">
<input name="temp_total" type="hidden" value="">
<table class="tabla">
      <tr>
        <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
        <th class="tabla" align="center">Fecha del movimiento</th>
        <th class="tabla" align="center">Fecha de pago</th>
      </tr>
      <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla" align="center">
          <input name="num_cia" type="text" class="nombre" id="num_cia" value="{cd_num_cia}" size="3" maxlength="3" readonly="true">
          <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="40" value="{cd_nombre_cia}" disabled>
		</td>
        <td class="tabla" align="center">
			<input name="fecha_mov" type="text" class="nombre" id="fecha_mov" size="12" maxlength="12" value="{cd_fecha_mov}" readonly="true">
		</td>
        <td class="tabla"  align="center"><input name="fecha_pago" type="text" class="nombre" id="fecha_pago" size="12" maxlength="12" value="{cd_fecha_pago}" readonly="true"></td>
      </tr>
</table>
<br>
<table class="tabla" >
      <tr>
        <th class="tabla" align="center">C&oacute;digo y nombre de materia prima  </th>
        <th class="tabla" align="center">Cantidad </th>
        <th class="tabla" align="center">Kilos </th>
        <th class="tabla" align="center">Precio<br>unitario</th>
        <th class="tabla" align="center">Total </th>
        <th class="tabla" align="center">Aplica a<br>Gastos </th>
        <th class="tabla" align="center">C&oacute;digo proveedor</th>
        <th class="tabla" align="center">N&uacute;mero de<br>
        documento</th>
      </tr>
      <!-- START BLOCK : fila -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla" align="center">
          <input name="precio_min{i}" type="hidden" value="{precio_min}">
		  <input name="precio_max{i}" type="hidden" value="{precio_max}">
		  <input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="3" maxlength="5" value="{cd_codmp}" 
          onFocus="form.temp_codmp.value=this.value;form.temp_total.value=form.total{i}.value;"
          onChange="if(parseInt(this.value) >= 0 || this.value == '') {
		  actualiza_mp(this,form.nombre_mp{i},form.cantidad{i},form.kilos{i},form.precio_unit{i},form.total{i},form.aplica_gasto{i},form.num_proveedor{i},form.nombre_proveedor{i},form.numero_fact{i},form.temp_codmp,form.temp_total,form.precio_min{i},form.precio_max{i});}
		  else error(this,form.temp_codmp);" 
		  onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.cantidad{i}.select(); 
		  else if (event.keyCode == 37) form.numero_fact{back}.select();
		  else if (event.keyCode == 38) form.codmp{back}.select();
		  else if (event.keyCode == 40) form.codmp{next}.select();">
          <input name="nombre_mp{i}" class="vnombre" type="text" id="nombre_mp{i}" size="20" value="{cd_nombre_mp}" disabled>
        </td>
        <td class="tabla" align="center">
          <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" size="8" maxlength="12" value="{cd_cantidad}" 
		  onFocus="form.temp.value=this.value;form.temp_total.value=form.total{i}.value;" 
          onChange="if(parseFloat(this.value) >= 0 || this.value == '') {
		  totales(form.codmp{i},this,form.kilos{i},form.precio_unit{i},form.total{i},form.temp_total,form.precio_min{i},form.precio_max{i});}
		  else error(this,form.temp);"
		  onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.kilos{i}.select(); 
		  else if (event.keyCode == 37) form.codmp{i}.select();
		  else if (event.keyCode == 38) form.cantidad{back}.select();
		  else if (event.keyCode == 40) form.cantidad{next}.select();">
        </td>
        <td class="tabla" align="center">
          <input name="kilos{i}" type="text" class="insert" id="kilos{i}" 
          onFocus="form.temp.value=this.value;form.temp_total.value=form.total{i}.value;" 
          onChange="if(parseFloat(this.value) >= 0 || this.value == '') {
		  totales(form.codmp{i},form.cantidad{i},this,form.precio_unit{i},form.total{i},form.temp_total,form.precio_min{i},form.precio_max{i});}
		  else error(this,form.temp);" 
		  onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.num_proveedor{i}.select(); 
		  else if (event.keyCode == 37) form.cantidad{i}.select();
		  else if (event.keyCode == 38) form.kilos{back}.select();
		  else if (event.keyCode == 40) form.kilos{next}.select();" value="{cd_kilos}" size="8" maxlength="12">
        </td>
        <td class="tabla" align="center">
          <input name="precio_unit{i}" type="text" class="rnombre" id="precio_unit{i}" value="{cd_precio_unit}" size="8" maxlength="12" readonly="true">
        </td>
        <th class="tabla" align="center"><input name="total{i}" type="text" class="rnombre" id="total{i}" value="{cd_total}" size="8" maxlength="12" readonly></th>
        <th class="tabla" align="center">
			<label>
			<input name="aplica_gasto{i}" type="checkbox" id="aplica_gasto{i}" value="TRUE" {check} onClick="aplica_gasto(this,form.num_proveedor{i},form.nombre_proveedor{i},form.numero_fact{i})">
			S&iacute;
			</label>
		</th>
	    <td class="tabla" align="center"><input name="num_proveedor{i}" type="text" class="insert" id="num_proveedor{i}" 
		  onFocus="form.temp.value=this.value" 
		  onChange="if(parseInt(this.value) >= 0 || this.value == '') {
		  this.value=parseInt(this.value);
		  actualiza_proveedor(this,form.nombre_proveedor{i});}
		  else error(this,form.temp);" value="{cd_num_proveedor}" size="3" maxlength="3" 
		  onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.numero_fact{i}.select(); 
		  else if(event.keyCode == 37) form.kilos{i}.select();
		  else if(event.keyCode == 38) form.num_proveedor{back}.select();
		  else if(event.keyCode == 40) form.num_proveedor{next}.select();" readonly="true">
          <input name="nombre_proveedor{i}" type="text" disabled class="vnombre" id="nombre_proveedor{i}" value="{cd_nombre_proveedor}" size="20"></td>
	    <td class="tabla" align="center"><input name="numero_fact{i}" type="text" class="insert" id="numero_fact{i}" size="8" maxlength="8" value="{cd_numero_fact}" 
		  onFocus="form.temp.value=this.value" 
		  onChange="if(!(parseInt(this.value) >= 0 || this.value == '')) error(this,form.temp);"
		  onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.codmp{next}.select(); 
		  else if(event.keyCode == 37) form.num_proveedor{i}.select();
		  else if(event.keyCode == 38) form.numero_fact{back}.select();
		  else if(event.keyCode == 40) form.numero_fact{next}.select();"></td>
	  </tr>
	  <!-- END BLOCK : fila -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	    <td colspan="2"><input name="button" type="button" class="boton" onClick="/*window.open('./ros_prc_minimod.php','precios','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480')*/cambia_precio(form)" value="Precios"></td>
		<th  class="tabla"colspan="2">Total factura</th>
		<th class="tabla"><input name="total" type="text" class="rnombre" size="8" maxlength="12" value="{cd_total}" readonly></th>
	    <td colspan="3" class="tabla"></td>
      </tr>
</table>
<p>
	<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="enviar" type="button" class="boton" value="Ir a Hoja Diaria >>" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : compra_directa -->

<!-- START BLOCK : hoja_diaria -->
<script language="javascript" type="text/javascript">
	function pago_pres(form) {
		window.open("","prestamos","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
		form.target = "prestamos";
		form.action = "./ros_pre_pago.php";
		form.submit();
		
		form.target = "mainFrame";
		form.action = "./ros_pro_sec.php?tabla=movimiento_gastos";
	}
	
	function total(temp, unidades, precio, total, venta_total) {
		var value_temp        = parseFloat(temp.value);
		var value_unidades    = parseInt(unidades.value);
		var value_precio      = parseFloat(precio.value);
		var value_total       = parseFloat(total.value);
		var value_venta_total = parseFloat(venta_total.value);
		
		if (value_unidades > 0 && value_precio > 0) {
			value_total = value_unidades * value_precio;
			if (value_temp >= 0 && value_venta_total > 0)
				value_venta_total = value_venta_total - value_temp;
			value_venta_total = value_venta_total + value_total;
			
			// Actualizar valores
			unidades.value = value_unidades;
			total.value = value_total.toFixed(2);
			venta_total.value = value_venta_total.toFixed(2);
			return;
		}
		else if ((value_unidades >= 0 && precio.value == "") || (unidades.value == "" && value_precio > 0)){
			if (value_temp >= 0 && value_venta_total > 0)
				value_venta_total = value_venta_total - value_temp;
			
			total.value = null;
			venta_total.value = value_venta_total.toFixed(2);
			return;
		}
		else if (unidades.value == "" || precio.value == ""){
			if (value_temp >= 0 && value_venta_total > 0)
				value_venta_total = value_venta_total - value_temp;
			
			unidades.value = "";
			total.value = null;
			venta_total.value = value_venta_total.toFixed(2);
			return;
		}
	}
	
	function sumar_otros(otros) {
		var value_temp = parseFloat(document.form.temp.value);
		var value_otros = parseFloat(otros.value);
		var value_venta_total = parseFloat(document.form.venta_total.value);
		
		if (value_otros >= 0) {
			if (value_temp >= 0)
				value_venta_total = value_venta_total - value_temp;
			value_venta_total = value_venta_total + value_otros;
			
			otros.value = value_otros.toFixed(2);
			document.form.venta_total.value = value_venta_total.toFixed(2);
			return;
		}
		else if (otros.value == "") {
			if (value_temp >= 0)
				value_venta_total = value_venta_total - value_temp;
			
			otros.value = "";
			document.form.venta_total.value = value_venta_total.toFixed(2);
			return;
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
	
	function valida_registro() {
		document.form.enviar.disabled = true;
		document.form.submit();
	}
	
	function regresar() {
		document.form.action = "./ros_pro_sec.php?tabla=compra_directa";
		document.form.volver.disabled = true;
		document.form.submit();
	}
	
	function borrar() {
		if (confirm("Desea borrar el formulario?")) {
			document.form.reset();
			document.form.unidades0.focus();
		}
		else
			document.form.unidades0.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Hoja Diaria</p>
<form name="form" method="post" action="./ros_pro_sec.php?tabla=movimiento_gastos">
<input name="tabla" type="hidden" value="hoja_diaria_rost">
<input type="hidden" name="temp" value="">
<input type="hidden" name="temp_total" value="">
<input type="hidden" name="numfilas" value="{numfilas}">
<table class="tabla">
      <tr>
        <th class="vtabla" align="center">Compa&ntilde;&iacute;a</th>
        <td class="vtabla" align="center"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
        <font size="+1"><b>{num_cia_hoja} - {nombre_cia_hoja}</b></font> </td>
        <th  class="vtabla" align="center">Fecha</th>
        <td  class="vtabla" align="center"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
        <font size="+1"><b>{fecha_hoja}</b></font></td>
      </tr>
</table>
<br>
<table class="tabla">
	<tr>
		<th colspan="2" align="center" class="tabla">Codigo y nombre de Materia Prima </th>
		<th class="tabla" align="center">Existencia</th>
		<th class="tabla" align="center">Unidades</th>
		<th class="tabla" align="center">Precio unitario </th>
		<th class="tabla" align="center">Precio total </th>
	</tr>
	<!-- START BLOCK : fila_hoja -->
	<tr>
		<td class="vtabla" align="center"><b><input type="hidden" name="codmp{i}" value="{codmp_hoja}">{codmp_hoja}</b></td>
		<td class="vtabla" align="center"><b>{nombre_mp_hoja}</b></td>
		<td class="tabla" align="center">
		<input name="existencia{i}" type="text" disabled="true" class="total" id="existencia{i}" value="{existencia}" size="12" maxlength="12">
		</td>
		<td class="tabla" align="center">
		<input name="unidades{i}" type="text" class="insert" id="unidades{i}" size="12" maxlength="12" value="{hd_unidades}" 
		onFocus="form.temp.value=this.value;form.temp_total.value=form.precio_total{i}.value" 
		onChange="if ((parseInt(this.value) >= 0 && parseInt(this.value) <= parseInt(form.existencia{i}.value)) || this.value == '') {
		total(form.temp_total,this,form.precio_unitario{i},form.precio_total{i},form.venta_total); }
		else error(this,form.temp);" 
		onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.unidades{next}.select(); 
		else if (event.keyCode == 38) form.unidades{back}.select(); 
		else if (event.keyCode == 40) form.unidades{next}.select();">
		</td>
		<td class="tabla" align="center">
		<input name="precio_unitario{i}" type="text" class="rnombre" id="precio_unitario{i}" 
		onFocus="form.temp.value=this.value;form.temp_total.value=form.precio_total{i}.value" 
		onChange="if (parseFloat(this.value) >= 0 || this.value == '') {
		total(form.temp_total,form.unidades{i},this,form.precio_total{i},form.venta_total);}
		else error(this,form.temp);" 
		onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.unidades{next}.select(); 
		else if(event.keyCode == 38) form.precio_unitario{back}.select();
		else if(event.keyCode == 40) form.precio_unitario{next}.select();
		else if(event.keyCode == 37) form.unidades{i}.select();" value="{hd_precio_unitario}" size="12" maxlength="12" readonly="true">
		</td>
		<th class="tabla" align="center"><input name="precio_total{i}" type="text" class="rnombre" id="precio_total{i}" size="12" maxlength="12" value="{hd_precio_total}" readonly="true">
		</th>
	</tr>
	<!-- END BLOCK : fila_hoja -->
	
	<tr>
		<td class="vtabla" align="center" colspan="2"><b>OTROS</b></td>
		<td class="tabla" align="center">&nbsp;</td>
		<td class="tabla" align="center">&nbsp;</td>
		<td class="tabla" align="center">&nbsp;</td>
		<th class="tabla" align="center">
		<input name="precio_total_otros" type="text" class="rnombre" id="precio_total_otros" size="12" maxlength="12" value="{hd_precio_total_otros}" 
		onFocus="form.temp.value=this.value" 
		onChange="if (parseFloat(this.value) >= 0 || this.value == '') {
		sumar_otros(this); }
		else error(this,form.temp);" 
		onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.unidades0.select();">
		</th>
	</tr>
	<tr>
		<td colspan="4"><input name="prestamos" type="button" class="boton" id="prestamos" value="Pago prestamos" onClick="/*window.open('./ros_pre_pago.php','prestamos','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480')*/pago_pres(form)">&nbsp;&nbsp;<input name="button" type="button" class="boton" onClick="window.open('./ros_prv_minimod.php','precios','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480')" value="Precios">
		</td>
		<th class="tabla">Venta total</th>
		<th class="tabla"><input name="venta_total" type="text" class="rnombre" value="{hd_venta_total}" size="12" maxlength="12" readonly="true"></th>
	</tr>
</table>
<p>
	<input name="volver" type="button" class="boton" value="<< Regresar a Compra Directa" onclick="regresar()">&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="enviar" type="button" class="boton" value="Ir a Gastos >>" onclick='valida_registro()' {disabled}>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : hoja_diaria -->

<!-- START BLOCK : gastos -->
<script language="javascript" type="text/javascript">
	function alta_pres(form) {
		window.open("","prestamos","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
		form.target = "prestamos";
		form.action = "./ros_pre_altas.php";
		form.submit();
		
		form.target = "mainFrame";
		form.action = "./ros_pro_sec.php?tabla=total_companias";
	}
	
	function actualiza_nombre(codgasto, nombre, concepto, importe, total, gasto_dia, temp_codgasto, temp_importe) {
		var value_total = parseFloat(total.value);
		var value_gasto_dia = parseFloat(gasto_dia.value);
		
		gasto = new Array();
		<!-- START BLOCK : nombre_gasto -->
		gasto[{codgasto}] = "{nombregasto}";
		<!-- END BLOCK : nombre_gasto -->
		
		if (parseInt(codgasto.value) > 0) {
			if (gasto[parseInt(codgasto.value)] == null  || parseInt(codgasto.value) == 41) {
				alert("El código "+parseInt(codgasto.value)+" no existe en el catálogo de gastos o no permitido");
				codgasto.value = temp_codgasto.value;
				codgasto.focus();
			}
			else {
				codgasto.value = parseInt(codgasto.value);
				nombre.value = gasto[parseInt(codgasto.value)];
				concepto.focus();
			}
		}
		else if (codgasto.value == "") {
			if (parseFloat(temp_importe.value) > 0) {
				value_total = value_total - parseFloat(temp_importe.value);
				value_gasto_dia = value_gasto_dia - parseFloat(temp_importe.value);
				
				total.value = value_total.toFixed(2);
				gasto_dia.value = value_gasto_dia.toFixed(2);
			}
			nombre.value = null;
			concepto.value = null;
			importe.value = null;
		}
	}
	
	function total(importe) {
		var value_importe = parseFloat(importe.value);
		var value_temp_importe = parseFloat(document.form.temp_importe.value);
		var value_total_gastos = parseFloat(document.form.total_gastos.value);
		var value_gastos_dia = parseFloat(document.form.gastos_dia.value);
		var value_gastos_directos = parseFloat(document.form.gastos_directos.value);
		
		if (value_importe >= 0) {
			if (value_temp_importe >= 0 && value_gastos_dia > 0) {
				value_total_gastos = value_total_gastos - value_temp_importe;
				value_gastos_dia = value_gastos_dia - value_temp_importe - value_gastos_directos;
			}
			value_total_gastos = value_total_gastos + value_importe;
			value_gastos_dia = value_total_gastos + value_gastos_directos;
			
			importe.value = value_importe.toFixed(2);
			document.form.total_gastos.value = value_total_gastos.toFixed(2);
			document.form.gastos_dia.value = value_gastos_dia.toFixed(2);
		}
		else if (value_importe == "") {
			if (value_temp_importe >= 0 && value_gastos_dia > 0) {
				value_total_gastos = value_total_gastos - value_temp_importe;
				value_gastos_dia = value_gastos_dia - value_temp_importe - value_gastos_directos;
			}
			
			document.form.total_gastos.value = value_total_gastos.toFixed(2);
			document.form.gastos_dia.value = value_gastos_dia.toFixed(2);
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
	
	function valida_registro() {
		document.form.enviar.disabled = true;
		document.form.submit();
	}
	
	function regresar() {
		document.form.action = "./ros_pro_sec.php?tabla=hoja_diaria_rost";
		document.form.volver.disabled = true;
		document.form.submit();
	}
	
	function borrar() {
		if (confirm("Desea borrar el formulario?")) {
			document.form.reset();
			document.form.codgastos0.focus();
		}
		else {
			document.form.codgastos0.focus();
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Gastos</p>
<form name="form" method="post" action="./ros_pro_sec.php?tabla=total_companias">
<input name="tabla" type="hidden" value="movimiento_gastos">
<input type="hidden" name="temp_codgasto">
<input type="hidden" name="temp_importe">
<table class="tabla">
	<tr>
		<th class="vtabla" align="center">Compa&ntilde;&iacute;a</th>
		<td class="vtabla" align="center"><input name="num_cia" type="hidden" value="{num_cia_gastos}"><font size="+1"><b>{num_cia_gastos} - {nombre_cia_gastos}</b></font></td>
		<th class="vtabla" align="center">Fecha </th>
		<td class="vtabla" align="center"><input name="fecha" type="hidden" value="{fecha_gastos}"><font size="+1"><b>{fecha_gastos}</b></font></td>
	</tr>
</table>
<br>
<table class="tabla">
	<tr>
		<th class="tabla" align="center">C&oacute;digo gasto  </th>
		<th class="tabla" align="center">Concepto </th>
		<th class="tabla" align="center">Importe </th>
	</tr>
	<!-- START BLOCK : fila_gastos -->
	<tr>
	  <td class="tabla" align="center">
		<input name="codgastos{i}" type="text" class="insert" id="codgastos{i}" 
		onFocus="form.temp_importe.value=form.importe{i}.value;form.temp_codgasto.value=this.value;" 
		onChange="if (parseInt(this.value) >= 0 || this.value == '') {
		actualiza_nombre(this,form.nombre{i},form.concepto{i},form.importe{i},form.total_gastos,form.gastos_dia,form.temp_codgasto,form.temp_importe); }
		else error(this,form.temp_codgasto);" 
		onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37) form.importe{back}.select();
else if (event.keyCode == 38) form.codgastos{back}.select();
else if (event.keyCode == 40) form.codgastos{next}.select();" value="{g_codgastos}" size="3" maxlength="3">
		<input class="vnombre" name="nombre{i}" type="text" size="25" value="{g_nombregasto}" disabled>
	  </td>
		<td class="tabla" align="center">
		<input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyDown="if(event.keyCode == 13 || event.keyCode == 39) form.importe{i}.select();
else if (event.keyCode == 37) form.codgastos{i}.select();
else if (event.keyCode == 38) form.concepto{back}.select();
else if (event.keyCode == 40) form.concepto{next}.select();
" value="{g_concepto}" size="50" maxlength="50">
	  </td>
		<th class="tabla" align="center">
		<input name="importe{i}" type="text" class="rinsert" id="importe{i}" 
		onFocus="form.temp_importe.value=this.value;" 
		onChange="if (parseFloat(this.value) >= 0 || this.value == '') {
		total(this); }
		else error(this,form.temp_importe);" 
		onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.codgastos{next}.select();
else if (event.keyCode == 37) form.concepto{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{g_importe}" size="12" maxlength="12">
	  </th>
	</tr>
	<!-- END BLOCK : fila_gastos -->
	<tr>
	  <td class="tabla" align="center">
	  </td>
		<th class="tabla" align="center">Total de gastos </th>
		<th class="tabla" align="center">
		<input class="rnombre" name="total_gastos" value="{g_total_gastos}" type="text" id="total_gastos" size="12" maxlength="12" readonly>
	  </th>
	</tr>
	<tr>
	  <td align="center"><input name="Button" type="button" class="boton" value="Alta de Prestamos"  onClick="/*window.open('./ros_pre_altas.php','prestamos','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480')*/alta_pres(form)">
	  </td>
		<th class="tabla" align="center">Gastos directos</th>
		<th class="tabla" align="center">
		<input name="gastos_directos" type="text" class="rnombre" id="gastos_directos" value="{gastos_directos}" size="12" maxlength="12" readonly>
	  </th>
	</tr>
	<tr>
	  <td class="tabla" align="center">
	  </td>
		<th class="tabla" align="center">Total de gastos del d&iacute;a </th>
		<th class="tabla" align="center">
		<input name="gastos_dia" type="text" class="rnombre" id="gastos_dia" value="{g_gastos_dia}" size="12" maxlength="
		12" readonly>
	  </th>
	</tr>
</table>
<p>
	<input name="volver" type="button" class="boton" value="<< Regresar a Hoja Diaria" onclick="regresar()">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="enviar" type="button" class="boton" value="Ir a Totales >>" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : gastos -->

<!-- START BLOCK : totales -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (confirm("¿Desea registrar todos los procesos?")) {
			document.form.enviar.disabled = true;
			document.form.submit();
		}
	}
	
	function regresar() {
		document.form.action = "./ros_pro_sec.php?tabla=movimiento_gastos";
		document.form.volver.disabled = true;
		document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Totales de Rosticer&iacute;a</p>
<form name="form" method="post" action="./ros_pro_sec.php?tabla=insertar">
<input name="tabla" type="hidden" value="total_companias">
<table class="tabla">
	<tr>
		<th class="vtabla" align="center"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia_total}">Compa&ntilde;&iacute;a</th>
		<td class="vtabla" align="center"><font size="+1"><b>{num_cia_total} - {nombre_cia_total}</b></font></td>
		<th class="vtabla" align="center"><input name="fecha" type="hidden" id="fecha" value="{fecha_total}">Fecha</th>
		<td class="vtabla" align="center"><font size="+1"><b>{fecha_total}</b></font></td>
	</tr>
</table>
<br>
<table width="50%" class="tabla">
	<tr>
		<th class="vtabla" align="center">Venta</th>
		<th class="tabla" align="center">
		<input name="venta" class="total" type="hidden" id="venta" size="20" value="{venta}" readonly>
		<font size="+1" color="#000000">{ventaf}</font>
		</th>
	</tr>
	<tr>
	  <th class="vtabla" align="center">Pago de Prestamos</th>
	  <th class="tabla" align="center"><font size="+1" color="#000000">{pago_pre}</font></th>
    </tr>
	<tr>
		<th class="vtabla" align="center">Gastos</th>
		<th class="tabla" align="center">
		<input name="gastos" class="total" type="hidden" id="gastos" size="20" value="{gastos}" readonly>
		<font size="+1" color="#000000">{gastosf}</font>
		</th>
	</tr>
	<tr>
	  <th class="vtabla" align="center">Prestamos</th>
	  <th class="tabla" align="center"><font size="+1" color="#000000">{pre}</font></th>
    </tr>
	<tr>
		<th class="vtabla" align="center">Efectivo</th>
		<th class="tabla" align="center">
		<input name="efectivo" class="total" type="hidden" id="efectivo" size="20" value="{efectivo}" readonly>
		<font size="+1" color="#000000">{efectivof}</font>
		</th>
	</tr>
</table>
<p>
	<input name="volver" type="button" class="boton" value="<< Regresar a Hoja Diaria" onclick="regresar()">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="enviar" type="button" class="boton" value="Finalizar Proceso Secuencial" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : totales -->
