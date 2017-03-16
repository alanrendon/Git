<!-- START BLOCK : obtener_compania -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Captura de Producción</p>
<form name="form" action="./pan_pro_cap.php" method="get" onKeyPress="if (event.keyCode == 13) return false">
<input name="temp" type="hidden">
<table class="tabla">
	<tr>
		<th class="vtabla">Compa&ntilde;&iacute;a</th>
		<td class="vtabla">
          <input name="compania" type="text" class="insert" id="compania" size="3" maxlength="3" onKeyDown="if (event.keyCode == 13) valida_registro(this)">
</td>
		<!--
		<th class="vtabla">Fecha (ddmmaa)</th>
		<td class="vtabla">
		<input name="fecha" type="text" class="insert" id="fecha" onChange="isDate(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.compania.select();" value="{fecha}" size="10" maxlength="10">
		</td>
		-->
	</tr>
</table>
<p>
  <input name="next" type="button" class="boton" id="next" onClick="valida_registro(compania)" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script type="text/javascript" language="JavaScript">
	function valida_registro(num_cia) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no es tuya");
				num_cia.value = "";
				num_cia.select();
				return false;
			}
			else {
				document.form.submit();
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			return false;
		}
	}
	
	window.onload = document.form.compania.select();
</script>
<!-- END BLOCK : obtener_compania -->

<!-- START BLOCK : hoja -->
<script language="javascript" type="text/javascript">
	function editar_producto(id) {
		window.open('','editar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480');
		document.form.method = "post";
		document.form.target = "editar";
		document.form.action = "./pan_pro_minimod.php?id="+id;
		document.form.submit();
		
		// Restablecer valores
		document.form.method = "post";
		document.form.target = "_self";
		document.form.action = "./hojadiaria.php?tabla=produccion";
		return;
	}
	
	function borrar_producto(id) {
		window.open('','borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200');
		document.form.method = "post";
		document.form.target = "borrar";
		document.form.action = "./pan_pro_minidel.php?id="+id;
		document.form.submit();
		
		// Restablecer valores
		document.form.method = "post";
		document.form.target = "_self";
		document.form.action = "./hojadiaria.php?tabla=produccion";
		return;
	}
	
	function insertar_producto(num_cia,turno) {
		window.open('','insertar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=480');
		document.form.method = "post";
		document.form.target = "insertar";
		document.form.action = "./pan_pro_minialta.php?num_cia="+num_cia+"&cod_turno="+turno;
		document.form.submit();
		
		// Restablecer valores
		document.form.method = "post";
		document.form.target = "_self";
		document.form.action = "./hojadiaria.php?tabla=produccion";
		return;
	}
	
	function importe(piezas, precio, importe) {
		var value;
		
		if (parseFloat(piezas.value) >= 0 && parseFloat(precio.value) >= 0) {
			value = parseFloat(piezas.value) * parseFloat(precio.value);
			importe.value = value.toFixed(2);
		}
		else
			importe.value = "";
	}
	
	function porcentaje(porcentaje, produccion, importe) {
		var value;
		
		if (parseFloat(produccion.value) >= 0 && parseFloat(porcentaje.value) >= 0) {
			value = parseFloat(produccion.value)*(parseFloat(porcentaje.value)/100);
			importe.value = value.toFixed(2);
		}
		else
			importe.value = "";
	}
	
	function total(temp_raya,temp_produccion, importe_raya, importe_produccion, total_raya, total_produccion) {
		// Convertir totales a valores flotantes
		var value_raya        = parseFloat(total_raya.value);
		var value_produccion  = parseFloat(total_produccion.value);
		
		// Restarle cualquier valor anterior a la suma del total
		if (temp_raya.value > 0 || temp_produccion.value > 0) {
			value_raya        = value_raya - parseFloat(temp_raya.value);
			value_produccion  = value_produccion - parseFloat(temp_produccion.value);
		}

		// Sumar totales
		if (parseFloat(importe_raya.value) >= 0 && parseFloat(importe_produccion.value) >= 0) {
			value_raya        = value_raya + parseFloat(importe_raya.value);
			value_produccion  = value_produccion + parseFloat(importe_produccion.value);
			// Actualizar campos
			total_raya.value        = value_raya.toFixed(2);
			total_produccion.value  = value_produccion.toFixed(2);
		}
		else {
			total_raya.value        = value_raya.toFixed(2);
			total_produccion.value  = value_produccion.toFixed(2);
		}
	}
	
	function total_raya_pagada(temp_raya, importe_raya, total_raya) {
		// Convertir totales a valores flotantes
		var value_raya = parseFloat(total_raya.value);
		
		// Restarle cualquier valor anterior a la suma del total
		if (temp_raya.value > 0) {
			value_raya = value_raya - parseFloat(temp_raya.value);
		}

		// Sumar totales
		if (parseFloat(importe_raya.value) >= 0) {
			value_raya = value_raya + parseFloat(importe_raya.value);
			// Actualizar campos
			total_raya.value = value_raya.toFixed(2);
		}
		else {
			total_raya.value = value_raya.toFixed(2);
		}
	}
	
	function cambio_raya_pagada(raya_debida, temp_raya_pagada, raya_ganada, raya_pagada, total_raya_ganada, total_raya_pagada, raya_fija) {
		// Convertir totales a valores flotantes
		var value_raya_ganada = parseFloat(total_raya_ganada.value);
		var value_raya_pagada = parseFloat(total_raya_pagada.value);
		
		// Si raya fija no esta marcado
		// raya_pagada no puede ser mayor a raya_ganada
		if (!raya_fija.checked) {
			if ((parseFloat(raya_pagada.value) > (parseFloat(raya_ganada.value)+parseFloat(raya_debida.value))) || (parseFloat(raya_pagada.value) < 0)) {
				var temp1 = parseFloat(raya_ganada.value);
				var temp2 = parseFloat(raya_debida.value);
				var temp = temp1 + temp2;
				raya_pagada.value = temp_raya_pagada.value;
				alert("'Importe de Raya Pagada' no puede ser mayor a "+temp.toFixed(2)+", favor de verificar los datos.");
				raya_pagada.select();
			}
			else {
				// Restarle cualquier valor anterior a la suma del total
				if (temp_raya_pagada.value >= 0) {
					value_raya_pagada = value_raya_pagada - parseFloat(temp_raya_pagada.value);
				}
				// Sumar totales
				if (parseFloat(raya_pagada.value) >= 0) {
					value_raya_pagada = value_raya_pagada + parseFloat(raya_pagada.value);
					// Actualizar campos
					total_raya_pagada.value = value_raya_pagada.toFixed(2);
				}
				else {
					total_raya_pagada.value = value_raya_pagada.toFixed(2);
				}
			}
		}
		// Si raya fija esta marcado
		// raya_pagada puede ser mayor a raya_ganada y no se resta del total del día
		else {
			if (parseFloat(raya_pagada.value) > 10000.00 || parseFloat(raya_pagada.value) < 0) {
				raya_pagada.value = temp_raya_pagada.value;
				alert("'Importe de Raya Pagada' no puede ser mayor a 1000.00, favor de verificar los datos.");
				raya_pagada.select();
			}
			else {
				// Restarle cualquier valor anterior a la suma del total
				if (temp_raya_pagada.value > 0) {
					value_raya_pagada = value_raya_pagada - parseFloat(temp_raya_pagada.value);
					value_raya_ganada = value_raya_ganada - parseFloat(raya_ganada.value);
				}
				// Sumar totales
				if (parseFloat(raya_pagada.value) >= 0) {
					value_raya_pagada = value_raya_pagada + parseFloat(raya_pagada.value);
					value_raya_ganada = value_raya_ganada + parseFloat(raya_pagada.value);
					// Actualizar campos
					total_raya_pagada.value = value_raya_pagada.toFixed(2);
					total_raya_ganada.value = value_raya_ganada.toFixed(2);
				}
				else {
					total_raya_pagada.value = value_raya_pagada.toFixed(2);
				}
			}
		}
	}
	
	function raya_fija(temp_raya_pagada, raya_ganada, raya_pagada, total_raya_ganada, total_raya_pagada) {
		var value_raya_ganada = parseFloat(total_raya_ganada.value);
		var value_raya_pagada = parseFloat(total_raya_pagada.value);
	
		// Restarle cualquier valor anterior a la suma del total
		if (temp_raya_pagada.value >= 0) {
			value_raya_pagada = value_raya_pagada - parseFloat(temp_raya_pagada.value);
			value_raya_ganada = value_raya_ganada - parseFloat(temp_raya_pagada.value);
		}
		// Sumar totales
		if (parseFloat(raya_pagada.value) >= 0) {
			value_raya_pagada = value_raya_pagada + parseFloat(raya_pagada.value);
			value_raya_ganada = value_raya_ganada + parseFloat(raya_ganada.value);
			// Actualizar campos
			total_raya_pagada.value = value_raya_pagada.toFixed(2);
			total_raya_ganada.value = value_raya_ganada.toFixed(2);
		}
		else {
			total_raya_pagada.value = value_raya_pagada.toFixed(2);
			total_raya_ganada.value = value_raya_ganada.toFoxed(2);
		}
	}

	function error(piezas, valor_anterior) {
		piezas.value = valor_anterior.value;
		alert("No se permiten caracteres, valores negativos o mayores a 100,000 piezas.");
		piezas.select();
	}
	
	function valida_registro() {
		if (confirm("¿Estan correctos los datos?"))
			document.form.submit();
		else
			return false;
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.piezas0.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Captura de Producción</p>
<form name="form" method="post" action="./hojadiaria.php?tabla={tabla}" >
<input type="hidden" name="temp_piezas">
<input type="hidden" name="temp_raya">
<input type="hidden" name="temp_produccion">
<input type="hidden" name="temp_raya_pagada">
<table class="tabla">
	<tr>
		<th class="vtabla">Compa&ntilde;&iacute;a</th>
		<td class="vtabla"><font size="+1"><b>{num_cia} &#8212; {nombre_cia}</b></font></td>
		<th class="vtabla">Fecha</th>
		<td class="vtabla"><font size="+1"><b>{fecha}</b></font></td>
	</tr>
</table>
<!-- START BLOCK : turno -->
<hr>
<table class="tabla">
	<tr>
		<th class="vtabla">Turno</th>
		<td class="vtabla"><font size="+1"><b>{turno}</b></font></td>
	</tr>
</table>
<br>
<table class="tabla" width="100%">
  <tr>
	<th class="tabla" colspan="3" width="50%">C&oacute;digo y nombre de producto </th>
		<th class="tabla" width="10%">Piezas</th>
		<th class="tabla" width="10%">Precio raya</th>
		<th class="tabla" width="10%">Importe Raya </th>
		<th class="tabla" width="10%">Precio venta</th>
	<th class="tabla" width="10%">Importe Producci&oacute;n</th>
	</tr>
	<!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<input type="hidden" name="cod_producto{i}" value="{cod_producto}">
		<input type="hidden" name="cod_turnos{i}" value="{cod_turnos}">
		<input type="hidden" name="num_cia{i}" value="{compania}">
		<input type="hidden" name="fecha{i}" value="{fecha}">
		<td class="vtabla" width="10"><input name="id{i}" type="hidden" value="{id}"><font size="+1"><b>{cod_producto}</b></font></td>
		<td class="vtabla" width="30"><font size="+1"><b>{nombre}</b></font></td>
		<td class="vtabla" width="10"><input type="button" class="boton" value="M" onClick="editar_producto({id})">
		<input type="button" class="boton" value="B" onClick="borrar_producto({id})">
		<input type="button" class="boton" value="I" onClick="insertar_producto({compania},{cod_turnos})">
		</td>
		<td class="tabla">
			<!-- START BLOCK : piezas_precio -->
			<input name="piezas{i}" type="text" class="insert" 
			onFocus="form.temp_piezas.value=this.value;form.temp_raya.value=form.imp_raya{i}.value;form.temp_produccion.value=form.imp_produccion{i}.value;" 
			onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) < 100000) || this.value == '') {
			importe(this,form.precio_raya{i},form.imp_raya{i});
			importe(this,form.precio_venta{i},form.imp_produccion{i});
			total(form.temp_raya,form.temp_produccion,form.imp_raya{i},form.imp_produccion{i},form.raya_ganada{tot},form.total_produccion{tot});
			total(form.temp_raya,form.temp_produccion,form.imp_raya{i},form.imp_produccion{i},form.raya_ganada,form.total_produccion);
			form.raya_pagada{tot}.value=form.raya_ganada{tot}.value;
			total_raya_pagada(temp_raya,imp_raya{i}, raya_pagada);} 
			else error(this,form.temp_piezas);" 
			onKeyDown="if ((event.keyCode == 13 || event.keyCode == 40) && ((parseFloat(this.value) >= 0 && parseFloat(this.value) < 100000) || this.value == '')) {
form.piezas{next}.select();
window.scrollByLines(2)
}
else if (event.keyCode == 38 && ((parseFloat(this.value) >= 0 && parseFloat(this.value) < 100000) || this.value == '')) {
form.piezas{back}.select();
window.scrollByLines(-2)
}" value="{piezas}" size="12" maxlength="12">
			<!-- END BLOCK : piezas_precio -->
			<!-- START BLOCK : piezas_porc -->
			<input name="piezas{i}" type="text" class="insert" 
			onFocus="form.temp_piezas.value=this.value;form.temp_raya.value=form.imp_raya{i}.value;form.temp_produccion.value=form.imp_produccion{i}.value;" 
			onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) < 100000) || this.value == '') {
			importe(this,form.precio_venta{i},form.imp_produccion{i});
			porcentaje(form.porc_raya{i},form.imp_produccion{i},form.imp_raya{i});
			total(form.temp_raya,form.temp_produccion,form.imp_raya{i},form.imp_produccion{i},form.raya_ganada{tot},form.total_produccion{tot});
			total(form.temp_raya,form.temp_produccion,form.imp_raya{i},form.imp_produccion{i},form.raya_ganada,form.total_produccion);
			form.raya_pagada{tot}.value=form.raya_ganada{tot}.value;
			total_raya_pagada(temp_raya,imp_raya{i}, raya_pagada);} 
			else error(this,form.temp_piezas);" 
			onKeyDown="if ((event.keyCode == 13 || event.keyCode == 40) && (parseFloat(this.value) >= 0 || this.value == '')) {
form.piezas{next}.select();
window.scrollByLines(2)
}
else if (event.keyCode == 38 && (parseFloat(this.value) >= 0 || this.value == '')) {
form.piezas{back}.select();
window.scrollByLines(-2)
}" value="{piezas}" size="12" maxlength="12">
			<!-- END BLOCK : piezas_porc -->
		</td>
		<td class="tabla">
			<!-- START BLOCK : precio_raya -->
			<input type="hidden" name="precio_raya{i}" value="{precio_raya}">
			<input type="hidden" name="porc_raya{i}" value="">
			{precio_raya_for}
			<!-- END BLOCK : precio_raya -->
			<!-- START BLOCK : porc_raya -->
			<input type="hidden" name="precio_raya{i}" value="">
			<input type="hidden" name="porc_raya{i}" value="{porc_raya}">
			{porc_raya_for}%
			<!-- END BLOCK : porc_raya -->
		</td>
		<th class="tabla">
			<input name="imp_raya{i}" type="text" class="rnombre" value="{importe_raya}" size="12" readonly>
		</th>
		<td class="tabla">
			<input type="hidden" name="precio_venta{i}" value="{precio_venta}">
			{precio_venta_for}
		</td>
		<th class="tabla">
			<input name="imp_produccion{i}" type="text" class="rnombre" value="{importe_produccion}" size="12" readonly>
		</th>
	</tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : totales -->
	<tr>
		<th class="tabla" colspan="5">Totales</th>
		<th class="tabla"><input name="raya_ganada{i}" type="text" class="rnombre" id="raya_ganada{i}" size="12" maxlength="12" value="{importe_raya_turno}" readonly></th>
		<th class="tabla">&nbsp;</th>
		<th class="tabla"><input name="total_produccion{i}" type="text" class="rnombre" id="total_produccion{i}" size="12" maxlength="12" value="{importe_produccion_turno}" readonly></th>
	</tr>
</table>   
<br>
<table class="tabla">
	<tr>
	  <th class="vtabla">Importe pagado de raya</th>
		<input type="hidden" name="raya_debida{i}" value="{raya_debida}">
		<input type="hidden" name="fecha_total{i}" value="{fecha}">
		<input type="hidden" name="codturno{i}" value="{turno}">
		<input type="hidden" name="numcia{i}" value="{cia}">
	  <th class="vtabla">
	  	<input name="raya_pagada{i}" type="text" class="rnombre" id="raya_pagada{i}" 
	  	onFocus="form.temp_raya_pagada.value=this.value;" 
	  	onChange="if (this.value >= 0 || this.value == '') 
		cambio_raya_pagada(form.raya_debida{i},form.temp_raya_pagada,form.raya_ganada{i},form.raya_pagada{i},form.raya_ganada,form.raya_pagada,form.raya_fija{i});
		else error(this,form.temp_raya_pagada);"
		onKeyDown="if (event.keyCode == 13 && (parseFloat(this.value) >= 0 || this.value == '')) 
		form.piezas{next}.select(); 
		else if (event.keyCode == 38 && (parseFloat(this.value) >= 0 || this.value == '')) 
		form.piezas{back}.select();" value="{raya_pagada}" size="12" maxlength="12">
	  </th>
	  <td class="vtabla"><font size="-2">
	  	<input name="raya_fija{i}" type="checkbox" id="raya_fija{i}" 
		onClick="if (!this.checked) {
		form.temp_raya_pagada.value=form.raya_pagada{i}.value;
		form.raya_pagada{i}.value=form.raya_ganada{i}.value;
		raya_fija(form.temp_raya_pagada,form.raya_ganada{i},form.raya_pagada{i},form.raya_ganada,form.raya_pagada);}">
	  </font>
	  </td>
	  <th class="vtabla"><font size="-2">Raya Fija</font></th>
	</tr>
</table>
<!-- END BLOCK : totales -->
<!-- END BLOCK : turno -->
<hr>
<p class="title">Totales del día</p>
<table class="tabla">
	<tr>
		<th class="vtabla">Total Raya Ganada</th>
		<th class="vtabla"><input name="raya_ganada" type="text" class="total" value="{importe_raya_ganada_total}" size="12" maxlength="12" readonly></th>
		<td width="20"></td>
		<th class="vtabla">Total Raya Pagada</th>
		<th class="vtabla"><input name="raya_pagada" type="text" class="total" value="{importe_raya_pagada_total}" size="12" maxlength="12" readonly></th>
		<td width="20"></td>
		<th class="vtabla">Total Producci&oacute;n</th>
		<th class="vtabla"><input name="total_produccion" type="text" class="total" value="{importe_produccion_total}" size="12" maxlength="12" readonly></th>
	</tr>
</table>
<p>	<input name="" type="button" class="boton" value="Regresar" onclick='history.back()'>
	<input name="" type="button" class="boton" value="Capturar producción" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.piezas0.select();</script>
<!-- END BLOCK : hoja -->
<!-- START BLOCK : agua -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12pt; color:#FF0000">Se le recuerda que no a capturado sus registros de consumo de agua. <br>
  Hasta no haberlo hecho no prodra seguir capturando la producci&oacute;n. </p>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_pro_cap.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : agua -->
