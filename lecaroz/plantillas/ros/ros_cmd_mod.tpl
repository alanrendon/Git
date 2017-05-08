<!-- START BLOCK : buscar_datos -->

<!-- END BLOCK : buscar_datos -->

<!-- START BLOCK : compra_directa -->
<script language="javascript" type="text/javascript">
	// Verificar y actualizar fecha de movimiento y de pago
	function actualiza_fecha() {
		var fecha = document.form.fecha_mov.value;
		var anio_actual = {anio_actual};
			
		// Si la fecha tiene el formato ddmmaaaa
		if (fecha.length == 8) {
			// Descomponer fecha en dia, mes y año
			if (parseInt(fecha.charAt(0)) == 0)
				dia = parseInt(fecha.charAt(1));
			else
				dia = parseInt(fecha.substring(0,2));
			if (parseInt(fecha.charAt(2)) == 0)
				mes = parseInt(fecha.charAt(3));
			else
				mes = parseInt(fecha.substring(2,4));
			anio = parseInt(fecha.substring(4));

			// El año de captura de ser el año en curso
			if (anio == anio_actual) {
				// Generar dias por mes
				var diasxmes = new Array();
				diasxmes[1] = 31; // Enero
				if (anio%4 == 0)
					diasxmes[2] = 29; // Febrero año bisiesto
				else
					diasxmes[2] = 28; // Febrero
				diasxmes[3] = 31; // Marzo
				diasxmes[4] = 30; // Abril
				diasxmes[5] = 31; // Mayo
				diasxmes[6] = 30; // Junio
				diasxmes[7] = 31; // Julio
				diasxmes[8] = 31; // Agosto
				diasxmes[9] = 30; // Septiembre
				diasxmes[10] = 31; // Octubre
				diasxmes[11] = 30; // Noviembre
				diasxmes[12] = 31; // Diciembre
				
				if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
					if (dia == diasxmes[mes] && mes < 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = 1+"/"+(mes+1)+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = 1+"/"+1+"/"+(anio+1);
					}
					else {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = (dia+1)+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha_mov.value = "";
					document.form.fecha_pago.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha_mov.focus();
					return;
				}
			}
			else {
				document.form.fecha_mov.value = "";
				document.form.fecha_pago.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha_mov.focus();
				return;
			}
		}
		else if (fecha.length == 6) {
			// Descomponer fecha en dia, mes y año
			if (parseInt(fecha.charAt(0)) == 0)
				dia = parseInt(fecha.charAt(1));
			else
				dia = parseInt(fecha.substring(0,2));
			if (parseInt(fecha.charAt(2)) == 0)
				mes = parseInt(fecha.charAt(3));
			else
				mes = parseInt(fecha.substring(2,4));
			anio = parseInt(fecha.substring(4)) + 2000;

			// El año de captura de ser el año en curso
			if (anio == (anio_actual)) {
				// Generar dias por mes
				var diasxmes = new Array();
				diasxmes[1] = 31; // Enero
				if (anio%4 == 0)
					diasxmes[2] = 29; // Febrero año bisiesto
				else
					diasxmes[2] = 28; // Febrero
				diasxmes[3] = 31; // Marzo
				diasxmes[4] = 30; // Abril
				diasxmes[5] = 31; // Mayo
				diasxmes[6] = 30; // Junio
				diasxmes[7] = 31; // Julio
				diasxmes[8] = 31; // Agosto
				diasxmes[9] = 30; // Septiembre
				diasxmes[10] = 31; // Octubre
				diasxmes[11] = 30; // Noviembre
				diasxmes[12] = 31; // Diciembre
				
				if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
					if (dia == diasxmes[mes] && mes < 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = 1+"/"+(mes+1)+"/"+anio;
					}
					else if (dia == diasxmes[mes] && mes == 12) {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = 1+"/"+1+"/"+(anio+1);
					}
					else {
						document.form.fecha_mov.value = dia+"/"+mes+"/"+anio;
						document.form.fecha_pago.value = (dia+1)+"/"+mes+"/"+anio;
					}
				}
				else {
					document.form.fecha_mov.value = "";
					document.form.fecha_pago.value = "";
					alert("Rango de fecha no valido");
					document.form.fecha_mov.focus();
					return;
				}
			}
			else {
				document.form.fecha_mov.value = "";
				document.form.fecha_pago.value = "";
				alert("Año no valido. Debe ser el año en curso ("+anio_actual+")");
				document.form.fecha_mov.focus();
				return;
			}
		}
		else {
			document.form.fecha_mov.value = "";
			document.form.fecha_pago.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
			document.form.fecha_mov.focus();
			return;
		}
	}

	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}
	
	// Validar y actualizar número y nombre del proveedor
	function actualiza_proveedor(num_proveedor, nombre) {
		// Arreglo con los nombres de las materias primas
		proveedor = new Array();				// Materias primas
		<!-- START BLOCK : nombre_proveedor -->
		proveedor[{num_proveedor}] = '{nombre_proveedor}';
		<!-- END BLOCK : nombre_proveedor -->
				
		if (num_proveedor.value > 0) {
			if (proveedor[num_proveedor.value] == null) {
				alert("Proveedor "+num_proveedor.value+" no esta en el catálogo de proveedores");
				num_proveedor.value = "";
				nombre.value  = "";
				num_proveedor.focus();
			}
			else {
				nombre.value   = proveedor[num_proveedor.value];				
			}
		}
		else if (num_proveedor.value == "") {
			num_proveedor.value = "";
			nombre.value  = "";
		}
	}

	function actualiza_mp(codmp, nombre, cantidad, kilos, precio, importe, temp_codmp, temp_total) {
		var total;
		
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}] = '{nombre_mp}';
		<!-- END BLOCK : nombre_mp -->
				
		if (codmp.value > 0) {
			if (mp[codmp.value] == null) {
				codmp.value = temp_codmp.value;
				alert("Código "+codmp.value+" no esta en el catálogo de materias primas");
				codmp.focus();
				return false;
			}
			else {
				nombre.value  = mp[codmp.value];
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
				codmp.focus();
				return false;
		}
	}
	
	function totales(codmp, unidades, kilos, precio, importe, temp) {
		var importe_parcial;
		var importe_total = parseFloat(document.form.total.value);
		var value_unidades = parseFloat(unidades.value);
		var value_kilos = parseFloat(kilos.value);
		var value_precio = parseFloat(precio.value);
		var value_temp = parseFloat(temp.value);
		
		// Validar primero que haya un código de materia prima
		if (codmp.value > 0) {
			// Si kilos y precio son mayores a cero, calcular el total
			if (value_unidades >= 0 && value_kilos >= 0 && value_precio >= 0) {
				importe_parcial = value_kilos * value_precio;
				if (value_temp >= 0 && importe_total > 0)
					importe_total = importe_total - value_temp;
				importe_total = importe_total + importe_parcial;
				
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
<p class="title">Compra Directa</p>
<form name="form" method="post" action="./ros_pro_sec.php?tabla=hoja_dia_rost">
<input name="temp_codmp" type="hidden" value="">
<input name="temp_total" type="hidden" value="">
<table class="tabla">
      <tr>
        <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
        <th class="tabla" align="center">C&oacute;digo proveedor </th>
        <th class="tabla" align="center">N&uacute;mero<br>documento </th>
        <th class="tabla" align="center">Fecha del<br>movimiento<br>(ddmmaaaa)</th>
        <th class="tabla" align="center">Fecha de pago</th>
        <th class="tabla" align="center">Aplica a gastos </th>
      </tr>
      <tr>
        <td class="tabla" align="center">
          <input name="num_cia" type="hidden" value="{cd_num_cia}">
          {cd_nombre_cia}
		</td>
        <td class="tabla" align="center">
          <input name="num_proveedor" type="hidden" value="{cd_num_proveedor}">
          {cd_nombre_proveedor}
        </td>
        <td class="tabla" align="center">{cd_numero_fact}</td>
        <td class="tabla" align="center">{cd_fecha_mov}</td>
        <td class="tabla"  align="center">{cd_fecha_pago}</td>
        <td class="tabla"  align="center">          
            <input name="aplica_gasto" type="hidden" value="cd_aplica_gasto">{cd_aplica_gasto_nombre}
        </td>
      </tr>
</table>
<br>
<table class="tabla" >
      <tr>
        <th class="tabla" align="center">C&oacute;digo y nombre de materia prima  </th>
        <th class="tabla" align="center">Cantidad </th>
        <th class="tabla" align="center">Kilos </th>
        <th class="tabla" align="center">Precio unitario</th>
        <th class="tabla" align="center">Total </th>
      </tr>
      <!-- START BLOCK : fila -->
	  <tr>
        <td class="tabla" align="center">
          <input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="5" maxlength="5" value="{cd_codmp}" 
          onFocus="form.temp_codmp.value=this.value;form.temp_total.value=form.total{i}.value;"
          onChange="actualiza_mp(this,form.nombre_mp{i},form.cantidad{i},form.kilos{i},form.precio_unit{i},form.total{i},form.temp_codmp,form.temp_total)" 
		  onKeyDown="if(event.keyCode == 13) form.cantidad{i}.select()">
          <input name="nombre_mp{i}" class="vnombre" type="text" id="nombre_mp{i}" size="40" value="{cd_nombre_mp}" disabled>
        </td>
        <td class="tabla" align="center">
          <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" size="12" maxlength="12" value="{cd_cantidad}" 
		  onFocus="form.temp_total.value=form.total{i}.value;" 
          onChange="totales(form.codmp{i},this,form.kilos{i},form.precio_unit{i},form.total{i},form.temp_total)"
		  onKeyDown="if(event.keyCode == 13) form.kilos{i}.select()">
        </td>
        <td class="tabla" align="center">
          <input name="kilos{i}" type="text" class="insert" id="kilos{i}" size="12" maxlength="12" value="{cd_kilos}" 
          onFocus="form.temp_total.value=form.total{i}.value;" 
          onChange="totales(form.codmp{i},form.cantidad{i},this,form.precio_unit{i},form.total{i},form.temp_total)" 
		  onKeyDown="if(event.keyCode == 13) form.precio_unit{i}.select()">
        </td>
        <td class="tabla" align="center">
          <input name="precio_unit{i}" type="text" class="insert" id="precio_unit{i}" size="12" maxlength="12" value="{cd_precio_unit}" 
          onFocus="form.temp_total.value=form.total{i}.value;" 
          onChange="totales(form.codmp{i},form.cantidad{i},form.kilos{i},this,form.total{i},form.temp_total)" 
		  onKeyDown="if(event.keyCode == 13) form.codmp{next}.select()">
        </td>
        <th class="tabla" align="center"><input name="total{i}" type="text" class="total" id="total{i}" size="12" maxlength="12" value="{cd_total}" readonly>        </th>
      </tr>
	  <!-- END BLOCK : fila -->
	  <tr>
	    <td colspan="2"></td>
		<th  class="tabla"colspan="2">Total factura </th>
		<th class="tabla"><input name="total" type="text" class="total" size="12" maxlength="12" value="{cd_total}" readonly></th>
	  </tr>
</table>
<p>
	<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>&nbsp;&nbsp;&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" value="Actualizar Hoja" onclick='valida_registro()'></p>
</form>
<!-- END BLOCK : compra_directa -->