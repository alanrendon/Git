<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	// Verificar y actualizar fecha de movimiento y de pago
	function actualiza_fecha(input_fecha) {
		var fecha = input_fecha.value;
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
			if (anio <= anio_actual) {
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
					if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
						input_fecha.value = dia+"/"+mes+"/"+anio;
						return;
					}
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					input_fecha.focus();
					return false;
				}
			}
			else {
				input_fecha.value = "";
				alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
				input_fecha.focus();
				return false;
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
			if (anio <= anio_actual) {
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
					if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
						input_fecha.value = dia+"/"+mes+"/"+anio;
						return;
					}
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					input_fecha.focus();
					return false;
				}
			}
			else {
				input_fecha.value = "";
				alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa' o 'ddmmaa'");
			input_fecha.focus();
			return false;
		}
	}

	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}

	function valida_registro() {
		if (document.form.num_cia.value < 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
		}
		else if (document.form.num_proveedor.value < 0) {
			alert("Debe especificar un proveedor");
			document.form.num_proveedor.select();
		}
		else if (document.form.fecha.value == "") {
			alert("Debe especificar la fecha");
			document.form.fecha.select();
		}
		else
			//if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			//else
				//return;
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_cia.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Facturas de Gas</p>
<form name="form" method="get" action="./fac_glp_cap.php">
<input type="hidden" name="temp" value="">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if (this.value == '') return;
else if (parseInt(this.value) >= 0) {
var temp=parseInt(this.value); this.value=temp;}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_proveedor.select();" size="5" maxlength="5"></td>
    <th class="vtabla">Pro<span class="vtabla">v</span>eedor</th>
    <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="if (this.value == '') return;
else if (parseInt(this.value) >= 0) {
var temp=parseInt(this.value); this.value=temp;}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha.select()" value="{num_pro}" size="5" maxlength="5"></td>
    <th class="vtabla">Fecha <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia.select();
else if (event.keyCode == 37) form.num_proveedor.select();" value="{fecha}" size="12" maxlength="12"></td>
  </tr>
</table>
<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input name="button" type="button" class="boton" onClick="borrar();" value="Borrar">
  &nbsp;&nbsp;&nbsp;&nbsp;<img src="./menus/insert.gif" width="16" height="16">
<input name="enviar" type="button" class="boton" id="enviar" value="Enviar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload=document.form.num_cia.select()</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<script language="javascript" type="text/javascript">
	function calcular_total(mlitros, clitros, litros, precio, total) {
		var value_mlitros = parseFloat(mlitros.value);
		var value_clitros = parseFloat(clitros.value);
		var value_litros;
		var value_precio = parseFloat(precio.value);
		var value_total;
		var value_iva = parseFloat(document.form.iva.value);

		if (value_mlitros >= 0 && value_clitros >= 0) {
			if (value_mlitros < value_clitros)
				value_litros = value_mlitros;
			else if (value_mlitros > value_clitros)
				value_litros = value_clitros;
			else if (value_mlitros == value_clitros)
				value_litros = value_clitros;
			else if (value_mlitros > 0 && value_clitros == 0)
				value_litros = value_mlitros;

			value_total = (value_litros * value_precio) * (1 + (value_iva / 100));

			mlitros.value = value_mlitros.toFixed(2);
			litros.value = value_litros.toFixed(2);
			total.value = value_total.toFixed(2);
			return;
		}
	}

	function calcular_litros(capacidad, porini, porfin, litros_cal) {
		var value_capacidad = parseFloat(capacidad.value);
		var value_porini = parseFloat(porini.value);
		var value_porfin = parseFloat(porfin.value);
		var value_litros_cal = parseFloat(litros_cal.value);

		if (value_porini >= 0 && value_porfin >= 0 && value_porini < value_porfin) {
			value_litros_cal = (value_porfin - value_porini) * value_capacidad / 100;

			porini.value = value_porini.toFixed(2);
			porfin.value = value_porfin.toFixed(2);
			litros_cal.value = value_litros_cal.toFixed(2);
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
		//if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		//else
			return;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Facturas de Gas</p>
<form name="form" method="post" action="./fac_glp_cap.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<input name="iva" type="hidden" value="{iva}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Proveedor</th>
    <th class="tabla" scope="col">Fecha</th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
      <strong>    {num_cia} - {nombre_cia}</strong> </td>
    <td class="tabla"><input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}">
      <strong>    {num_proveedor} - {nombre_proveedor}</strong> </td>
    <td class="tabla"><input name="fecha" type="hidden" id="fecha" value="{fecha}">      <strong>{fecha}</strong></td>
  </tr>
</table>
<br>
<!-- START BLOCK : tanque -->
<table class="tabla">
  <tr>
    <th class="tabla">No. tanque </th>
    <th class="tabla">Capacidad</th>
    <th class="tabla">No. Factura </th>
    <th class="tabla">Precio/litro</th>
    <th class="tabla">IVA</th>
    <th class="tabla">Litros</th>
    <th class="tabla">Litros calculados </th>
    <th class="tabla">% inicial </th>
    <th class="tabla">% final </th>
    <th class="tabla">Total</th>
  </tr>
  <tr>
    <th class="tabla"><input name="num_tanque{i}" type="hidden" class="insert" value="{num_tanque}" size="5" maxlength="1" readonly="true">
      {num_tanque}</th>
    <th class="tabla"><input name="capacidad{i}" type="hidden" class="insert" id="capacidad{i}" value="{capacidad}" size="8" maxlength="8" readonly="true">
      {capacidad}</th>
    <td class="tabla"><input name="num_fact{i}" type="text" class="insert" onFocus="form.temp.value == this.value" onChange="if (this.value == '') return;
else if (this.value != '') {
this.value=this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g,'');this.value=this.value.toUpperCase();
}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.litros_man{i}.select();
else if (event.keyCode == 37) form.porc_final{back}.select();" size="10" maxlength="10"></td>
    <td class="tabla"><input name="precio_unit{i}" type="hidden" value="{precio_unit}">{fprecio_unit}
    </td>
    <td class="tabla">{iva}</td>
    <td class="tabla"><input name="litros_man{i}" type="text" class="insert" id="litros_man{i}" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) <= parseFloat(form.capacidad{i}.value)) || this.value == '')
calcular_total(this,form.litros_cal{i},form.litros{i},form.precio_unit{i},form.total{i});
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.porc_inic{i}.select();
else if (event.keyCode == 37) form.num_fact{i}.select();" size="8" maxlength="8"></td>
    <td class="tabla"><input name="litros_cal{i}" type="text" class="total" id="litros_cal{i}2" value="0" size="8" maxlength="8" readonly="true">
    </td>
    <td class="tabla"><input name="porc_inic{i}" type="text" class="insert" onFocus="form.temp.value = this.value" onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) <= 100) || this.value == '')
if (parseFloat(form.porc_final{i}.value) >= 0) {
calcular_litros(form.capacidad{i}, this, form.porc_final{i}, form.litros_cal{i});
calcular_total(form.litros_man{i},form.litros_cal{i},form.litros{i},form.precio_unit{i},form.total{i});}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.porc_final{i}.select();
else if (event.keyCode == 37) form.litros_man{i}.select();" value="0.00" size="6" maxlength="6"></td>
    <td class="tabla"><input name="porc_final{i}" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) <= 100) || this.value == '')
if (parseFloat(form.porc_inic{i}.value) >= 0) {
calcular_litros(form.capacidad{i}, form.porc_inic{i}, this, form.litros_cal{i});
calcular_total(form.litros_man{i},form.litros_cal{i},form.litros{i},form.precio_unit{i},form.total{i});}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_fact{next}.select();
else if (event.keyCode == 37) form.porc_inic{i}.select();" value="100.00" size="6" maxlength="6"></td>
    <th class="tabla"><input name="litros{i}" type="hidden">
        <input name="total{i}" type="text" class="total" value="0.00" size="12" maxlength="12"></th>
  </tr>
</table>
<p></p>
<!-- END BLOCK : tanque -->
<input name="regresar" type="button" class="boton" onClick='parent.history.back()' value="Regresar">
<input type="button" class="boton" name="enviar" value="Capturar" onClick="valida_registro()">
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload=document.form.num_fact0.select()</script>
<!-- END BLOCK : captura -->
</body>
</html>
