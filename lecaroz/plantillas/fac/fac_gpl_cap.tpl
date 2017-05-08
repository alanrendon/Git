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
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_cia.select();
	}
</script>
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
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha.select" size="5" maxlength="5"></td>
    <th class="vtabla">Fecha <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" value="{fecha}" size="12" maxlength="12"></td>
  </tr>
</table>
<p>
  <img src="../../menus/delete.gif" width="16" height="16">
  <input name="button" type="button" value="Borrar" onClick="borrar();">
  &nbsp;&nbsp;&nbsp;&nbsp;<img src="../../menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" id="enviar" value="Enviar">
</p>
</form>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<script language="javascript" type="text/javascript">
	function total(precio,litros,total) {
		var value_precio = parseFloat(precio.value);
		var value_litros = parseFloat(litros.value);
		var value_total  = parseFloat(total.value);
		
		if (value_precio > 0 && value_litros > 0) {
			value_total = value_precio * value_litros;
			
			precio.value = value_precio.toFixed(2);
			litros.value = value_litros.toFixed(2);
			total.value  = value_total.toFixed(2);
		}
		else if (precio.value == "" || litros.value == "") {
			return;
		}
	}
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
</script>
<p class="title">Captura de Facturas de Gas</p>
<form name="form" method="post" action="./fac_glp_cap.php">
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
    <td class="tabla"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
      <strong>{fecha}</strong></td>
  </tr>
</table>
<p></p>
<!-- START BLOCK : tanque -->
<table class="tabla">
  <tr>
    <th class="vtabla">N&uacute;mero de tanque </th>
    <td class="vtabla"><input name="num_tanque{i}" type="text" class="insert" value="{num_tanque}" size="5" maxlength="1" readonly="true"></td>
    <th class="vtabla">Capacidad</th>
    <td class="vtabla"><input name="capacidad{i}" type="text" class="insert" id="capacidad{i}" value="{capacidad}" size="8" maxlength="8" readonly="true"></td>
    <th class="vtabla">Precio/litro</th>
    <td class="vtabla"><input name="precio_unit{i}" type="text" class="insert" id="precio_unit{i}" value="{precio_unit}" size="12" maxlength="12"></td>
    <th class="vtabla"><input name="xlitros{i}" type="checkbox" id="xlitros{i}" value="checkbox" checked>
Litros</th>
    <td class="vtabla"><input name="litros{i}" type="text" class="insert" id="litros{i}" size="8" maxlength="8"></td>
  </tr>
  <tr>
    <th class="vtabla">N&uacute;mero de Factura </th>
    <td class="vtabla"><input name="num_fact{i}" type="text" class="insert" id="num_fact{i}" size="10" maxlength="10"></td>
    <th class="vtabla">% Inicial </th>
    <td class="vtabla"><input name="proc_inic{i}" type="text" class="insert" id="proc_inic{i}" value="0" size="6" maxlength="6"></td>
    <th class="vtabla">% Final</th>
    <td class="vtabla"><input name="proc_final{i}" type="text" class="insert" id="proc_final{i}" value="0" size="6" maxlength="6"></td>
    <th class="vtabla">Total de factura</th>
    <th class="vtabla"><input name="total{i}" type="text" class="total" id="total{i}" value="0" size="12" maxlength="12"></th>
  </tr>
</table>
<p></p>
<!-- END BLOCK : tanque -->
</form>
<!-- END BLOCK : captura -->
</body>
</html>
