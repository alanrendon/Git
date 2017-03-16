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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Secuencial de Rosticer&iacute;as</p>
  <form action="./ros_pro_sec_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="if (isInt(this,tmp)) cambiaCia(this, nombre)" onKeyDown="if (event.keyCode == 13) siguiente.focus()" size="3" maxlength="3">
        <input name="nombre" type="text" class="vnombre" id="nombre" size="30" readonly="true"></td>
    </tr>
  </table>  
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="validar()">
  </p></form>  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function validar() {
	if (form.num_cia.value <= 100) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : fecha -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Secuencial de Rosticer&iacute;as</p>
  <form action="./ros_pro_sec_v2.php" method="get" name="form">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) siguiente.focus()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ros_pro_sec_v2.php?cancel=1'">
    &nbsp;&nbsp;
    <input name="siguiente" type="button" class="boton" id="siguiente" onClick="validar(this.form)" value="Siguiente >>">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha de captura");
		form.fecha.select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
}

window.onload = document.form.fecha.select();
-->
</script>
<!-- END BLOCK : fecha -->
<!-- START BLOCK : compras -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Compra Directa</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Fecha Pago </th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha_pago}</td>
    </tr>
  </table>  
  <br>
  <form action="./ros_pro_sec_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="screen" type="hidden" id="screen" value="cd">
    <input name="bloqueo" type="hidden" id="bloqueo" value="{bloqueo}">    
    <table class="tabla">
	<tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Kilos</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Aplica<br>
        Gastos</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Folio</th>
    </tr>
	<!-- START BLOCK : cdrow -->
    <tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select();colorRow({i});" onChange="if (isInt(this,tmp)) cambiaMP({i})" onKeyDown="if (event.keyCode == 13) cantidad[{i}].select()" value="{codmp}" size="3" maxlength="4">
        <input name="nombre_mp[]" type="text" class="vnombre" id="nombre_mp" value="{nombre_mp}" size="30" readonly="true">
        </td>
      <td class="tabla"><input name="cantidad[]" type="text" class="rinsert" id="cantidad" onFocus="tmp.value=this.value;this.select();" onBlur="if (formatoCampo(this)) totalProducto({i})" onKeyDown="if (event.keyCode == 13) kilos[{i}].select()" value="{cantidad}" size="8"></td>
      <td class="tabla"><input name="kilos[]" type="text" class="rinsert" id="kilos" onFocus="tmp.value=this.value;this.select();" onBlur="if (formatoCampo(this)) totalProducto({i})" onKeyDown="if (event.keyCode == 13) {
if (aplica_gasto{i}.checked) folio[{i}].select();
else num_pro[{i}].select();
}" value="{kilos}" size="8"></td>
      <td class="tabla"><input name="precio[]" type="text" class="rnombre" id="precio" value="{precio}" size="8" readonly="true"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color: #0000CC;" value="{importe}" size="8" readonly="true"></td>
      <td class="tabla"><input name="aplica_gasto{i}" type="checkbox" id="aplica_gasto{i}" onClick="AplicaGasto({i})" value="1" {checked}></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) folio[{i}].select()" value="{num_pro}" size="3" maxlength="4" readonly="true">
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" readonly="true"></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[{next}].select()" value="{folio}" size="8"></td>
    </tr>
	<!-- END BLOCK : cdrow -->
    <tr>
      <th class="tabla"><input name="Button" type="button" class="boton" value="Precios" onClick="cambiaPrecios()"></th>
      <th colspan="3" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="8"></th>
      <th colspan="3" class="tabla">&nbsp;</th>
      </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ros_pro_sec_v2.php?cancel=1'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar()"> 
<input name="next_screen" type="hidden" id="next_screen" value="hd"> 
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var pro = new Array(), mp = new Array();
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : codmp -->
mp[{codmp}] = new Array();
mp[{codmp}]['nombre'] = "{nombre}";
mp[{codmp}]['precio'] = {precio};
mp[{codmp}]['min'] = {min};
mp[{codmp}]['max'] = {max};
<!-- END BLOCK : codmp -->

function cambiaPro(i) {
	if (form.num_pro[i].value == "") {
		form.nombre_pro[i].value = "";
	}
	else if (pro[form.num_pro[i].value] != null) {
		form.nombre_pro[i].value = pro[form.num_pro[i].value];
	}
	else {
		alert("El proveedor no se encuentra en el catalogo");
		form.num_pro[i].value = form.tmp.value;
		form.num_pro[i].select();
	}
}

function cambiaMP(i) {
	var tmp;
	
	if (form.codmp[i].value == "") {
		// Borrar todos los datos
		form.nombre_mp[i].value = "";
		form.cantidad[i].value = "";
		form.kilos[i].value = "";
		form.precio[i].value = "";
		form.importe[i].value = "";
		eval("form.aplica_gasto" + i).checked = false;
		form.num_pro[i].value = "";
		form.nombre_pro[i].value = "";
		form.folio[i].value = "";
	}
	else if (mp[form.codmp[i].value] != null) {
		form.nombre_mp[i].value = mp[form.codmp[i].value]['nombre'];
		form.cantidad[i].value = "";
		form.kilos[i].value = "";
		tmp = new oNumero(mp[form.codmp[i].value]['precio']);
		form.precio[i].value = tmp.formato(2, true);
		eval("form.aplica_gasto" + i).checked = true;
		form.num_pro[i].value = 289;
		form.nombre_pro[i].value = "COMPRAS DIRECTAS";
	}
	else {
		alert("El producto no se encuentra en el catálogo");
		form.codmp[i].value = form.tmp.value;
		form.codmp[i].select();
	}
}

function cambiaPrecios() {
	var win = window.open("./ros_prc_minimod_v2.php","cambia_precio","left=192,top=144,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
	win.focus();
}

function AplicaGasto(i) {
	if (eval("form.aplica_gasto" + i).checked) {
		form.num_pro[i].readOnly = true;
		form.nombre_pro[i].readOnly = true;
		form.num_pro[i].value = 289;
		form.nombre_pro[i].value = "COMPRAS DIRECTAS";
		form.folio[i].select();
	}
	else {
		form.num_pro[i].readOnly = false;
		form.nombre_pro[i].readOnly = false;
		form.num_pro[i].select();
	}
}

function formatoCampo(campo) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
		return true;
	}
	else if (isNaN(parseFloat(campo.value.replace(",", "")))) {
		alert("Solo se permiten números");
		campo.value = campo.form.tmp.value;
		return false;
	}
	
	var value = parseFloat(campo.value.replace(",", ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		campo.value = campo.form.tmp.value;
		return false;
	}
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(2, true);
	
	return true;
}

function totalProducto(i) {
	var cantidad, kilos, precio, pmax, pmin, importe, tmp;
	
	if (form.codmp[i].value > 0) {
		cantidad = !isNaN(parseFloat(form.cantidad[i].value.replace(",", "."))) ? parseFloat(form.cantidad[i].value.replace(",", "")) : 0;
		kilos = !isNaN(parseFloat(form.kilos[i].value.replace(",", "."))) ? parseFloat(form.kilos[i].value.replace(",", "")) : 0;
		precio = !isNaN(parseFloat(form.precio[i].value.replace(",", "."))) ? parseFloat(form.precio[i].value.replace(",", "")) : 0;
		pmin = mp[form.codmp[i].value]['min'];
		pmax = mp[form.codmp[i].value]['max'];
		
		// Si kilos y precio son mayores a cero, calcular el total
		if (cantidad > 0 && kilos > 0 && precio > 0) {
			importe = kilos * precio;
			// Verificar si el precio por pieza no pasa de +-20% del precio promedio
			if (form.bloqueo.value == "1" && (importe / cantidad < pmin || importe / cantidad > pmax)) {
				var cmax = new oNumero(importe / pmin);
				var cmin = new oNumero(importe / pmax);
				var cantidad = (importe / pmin + importe / pmax) / 2;
				
				alert("'Cantidad' debe de estar entre " + cmin.formato(2, true) + " y " + cmax.formato(2, true) + " unidades");
				tmp = new oNumero(cantidad);
				form.cantidad[i].value = tmp.formato(2, true);
				form.kilos[i].select();
			}
			
			tmp = new oNumero(importe);
			form.importe[i].value = tmp.formato(2, true);
		}
		// Si solo precio es mayor a cero, calcular el total multiplicando por las unidades
		else if (cantidad > 0 && precio > 0 && kilos == 0) {
			importe = cantidad * precio;
			
			tmp = new oNumero(importe);
			form.importe[i].value = tmp.formato(2, true);
		}
		else {
			form.importe[i].value = "";
		}
	}
	
	totalCompra();
}

function totalCompra() {
	var tmp, total = 0;
	
	for (i = 0; i < form.importe.length; i++) {
		total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	}
	
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
}

function colorRow(i) {
	for (var j = 0; j < form.codmp.length; j++)
		if (j == i)
			document.getElementById("row" + j).style.backgroundColor = "#ACD2DD";
		else
			document.getElementById("row" + j).style.backgroundColor = "";
}

function validar() {
	for (var i = 0; i < form.codmp.length; i++) {
		if (form.codmp[i].value > 0 && form.codmp[i].value == 303 && (form.kilos[i].value == '' || form.kilos[i].value == 0)) {
			alert('PARA LA [303] SALCHICHA ES OBLIGATORIO CAPTURAR LOS KILOS');
			
			form.kilos[i].select();
			
			return false;
		}
	}
	
	form.submit();
}

window.onload = form.codmp[0].select();
-->
</script>
<!-- END BLOCK : compras -->
<!-- START BLOCK : nuevos_pro -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Productos Nuevos </p>
  <p style="font-family:Arial, Helvetica, sans-serif; ">Los siguientes productos no estan en el inventario o no tienen precio de venta </p>  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Precio</th>
    </tr>
    <!-- START BLOCK : new_pro -->
	<tr>
      <td class="vtabla">{codmp} {nombre} </td>
      <td class="rtabla">{precio}</td>
    </tr>
	<!-- END BLOCK : new_pro -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='ros_pro_sec_v2.php?cancel=1'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : nuevos_pro -->
<!-- START BLOCK : cambio_precios -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cambio de Precios de Venta</p>
  <form action="./ros_pro_sec_v2.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Precio Actual </th>
      <th class="tabla" scope="col">Precio Nuevo </th>
    </tr>
    <!-- START BLOCK : cam_pre -->
	<tr>
      <td class="tabla">{codmp} {producto}</td>
      <td class="tabla"><input name="precio_act{i}" type="radio" value="{pact}" checked>
        {precio_act}</td>
      <td class="tabla"><input name="precio_ant{i}" type="radio" value="{pant}">
        {precio_new}</td>
    </tr>
	<!-- END BLOCK : cam_pre -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Cambiar">
  </p>
  </form></td>
</tr>
</table>
<!-- END BLOCK : cambio_precios -->
<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Hoja Diaria</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="./ros_pro_sec_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="next_screen" type="hidden" id="next_screen">
    <input name="screen" type="hidden" id="screen" value="hd">    
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Existencia</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : hdrow -->
	<tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla"><input name="codmp[]" type="hidden" id="codmp" value="{codmp}">
        <strong>{codmp}</strong></td>
      <td class="vtabla"><strong>{nombre}</strong></td>
      <td class="tabla"><input name="no_exi[]" type="hidden" id="no_exi" value="{no_exi}">
      <input name="existencia[]" type="hidden" id="existencia" value="{existencia}"><input name="existencia_real[]" type="text" class="rnombre" id="existencia_real" value="{existencia_real}" size="8" readonly="true"></td>
      <td class="tabla"><input name="cantidad[]" type="text" class="rinsert" id="cantidad" onFocus="tmp.value=this.value;this.select();colorRow({i});" onBlur="if (formatoCampo(this)) calculaImporte({i})" onKeyDown="if (event.keyCode == 13) {
if (cantidad.length == undefined) otros.select();
else {next}.select();
}" value="{cantidad}" size="8" {readonly}></td>
      <td class="tabla"><input name="precio[]" type="text" class="rnombre" id="precio" onClick="cambiaPrecio({codprecio},{i})" onMouseOver="this.style.cursor = 'pointer'" onMouseOut="this.style.cursor = 'default'" value="{precio}" size="8" readonly="true"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color: #0000CC;" value="{importe}" size="8" readonly="true"></td>
    </tr>
	<!-- END BLOCK : hdrow -->
    <tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td colspan="5" class="rtabla"><strong>OTROS</strong></td>
      <td class="tabla"><input name="otros" type="text" class="rnombre" id="otros" style="color: #0000CC;" onFocus="tmp.value=this.value;this.select();colorRow({i})" onBlur="calculaOtros()" onKeyDown="if (event.keyCode == 13) {
if (cantidad.length == undefined) cantidad.select();
else cantidad[0].select();
}" value="{otros}" size="8"></td>
    </tr>
    <tr>
      <th colspan="2" class="tabla"><input type="button" class="boton" value="Prestamos" onClick="pagoPrestamos()"></th>
      <th colspan="3" class="rtabla">Venta Total </th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="8"></th>
    </tr>
  </table>  
    {prestamos}
    <p>
    <input type="button" class="boton" value="<< Regresar" onClick="nextScreen('cd')">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar" onClick="document.location='./ros_pro_sec_v2.php?cancel=1'">
&nbsp;&nbsp;    
<input name="siguiente" type="button" class="boton" id="siguiente" onClick="nextScreen('gs')" value="Siguiente >>"{disabled}>
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function formatoCampo(campo) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
		return true;
	}
	else if (isNaN(parseInt(campo.value.replace(",", "")))) {
		alert("Solo se permiten números");
		campo.value = campo.form.tmp.value;
		campo.select();
		return false;
	}
	
	var value = parseInt(campo.value.replace(",", ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		campo.value = campo.form.tmp.value;
		campo.select();
		return false;
	}
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(-1, true);
	
	return true;
}

function calculaImporte(i) {
	var codmp = form.codmp.length == undefined ? get_val(form.codmp) : get_val(form.codmp[i]);
	var cantidad = form.cantidad.length == undefined ? form.cantidad : form.cantidad[i];
	var importe = form.importe.length == undefined ? form.importe : form.importe[i];
	var precio = form.precio.length == undefined ? form.precio : form.precio[i];
	var existencia = form.existencia.length == undefined ? form.existencia : form.existencia[i];
	var existencia_real = form.existencia_real.length == undefined ? form.existencia_real : form.existencia_real[i];
	var no_exi = form.no_exi.length == undefined ? form.no_exi.value : form.no_exi[i].value;
	var tmp;
	var otros = 0;
	
	if (form.codmp.length != undefined)
		for (var j = 0; j < form.codmp.length; j++)
			if (get_val(form.codmp[j]) == codmp && j != i)
				otros += get_val(form.cantidad[j]);
	
	if (cantidad.value == "" || cantidad.value == "0" || precio.value == "" || precio.value == "0") {
		importe.value = "";
		existencia_real.value = no_exi == 't' ? '' : get_val(existencia) - otros;
	}
	else if (/*codmp == 717 || codmp == 718 || codmp == 719 || codmp == 804 || codmp == 761 || codmp == 726 || codmp == 817 || codmp == 732*/no_exi == 't') {
		var vimporte = 0;
		var vcantidad = parseInt(cantidad.value.replace(",", ""));
		var vprecio = parseFloat(precio.value.replace(",", ""));
		var vexistencia = 0;
		
		vimporte = vcantidad * vprecio;
		tmp = new oNumero(vimporte);
		importe.value = tmp.formato(2, true);
	}
	else {
		var vimporte = 0;
		var vcantidad = parseInt(cantidad.value.replace(",", ""));
		var vprecio = parseFloat(precio.value.replace(",", ""));
		var vexistencia = parseInt(existencia.value.replace(",", "")) - otros;
		
		if (vcantidad > vexistencia) {
			alert("'Cantidad' no puede ser mayor a 'Existencia'");
			cantidad.value = form.tmp.value;
			cantidad.select();
			return false;
		}
		
		vimporte = vcantidad * vprecio;
		vexistencia -= vcantidad;
		tmp = new oNumero(vimporte);
		importe.value = tmp.formato(2, true);
		tmp = new oNumero(vexistencia);
		existencia_real.value = vexistencia != 0 ? tmp.formato(-1, true) : "";
	}
	
	if (form.codmp.length != undefined)
		for (var j = 0; j < form.codmp.length; j++)
			if (get_val(form.codmp[j]) == codmp)
				form.existencia_real[j].value = existencia_real.value;
	
	calculaTotal();
}

function calculaOtros() {
	if (form.otros.value == "" || form.otros.value == "0") {
		form.otros.value = "";
	}
	else {
		var tmp, otros = parseFloat(form.otros.value.replace(",", ""));
		
		tmp = new oNumero(otros);
		form.otros.value = tmp.formato(2, true);
	}
	
	calculaTotal();
}

function calculaTotal() {
	var total = 0, tmp;
	
	if (form.importe.length == undefined) {
		total += !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	}
	else {
		for (var i = 0; i < form.importe.length; i++) {
			total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
		}
	}
	total += !isNaN(parseFloat(form.otros.value.replace(",", ""))) ? parseFloat(form.otros.value.replace(",", "")) : 0;
	
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
}

function cambiaPrecio(codmp, i) {
	var win = window.open("./ros_prv_minimod_v2.php?codmp=" + codmp + "&i=" + i,"precio","left=312,top=234,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
	win.focus();
}

function pagoPrestamos() {
	var win = window.open("./ros_pre_pago_v2.php","pagos","left=192,top=144,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=480");
	win.focus();
}

function colorRow(i) {
	for (var j = 0; j < form.codmp.length; j++)
		if (j == i)
			document.getElementById("row" + j).style.backgroundColor = "#ACD2DD";
		else
			document.getElementById("row" + j).style.backgroundColor = "";
}

function nextScreen(nextscreen) {
	form.next_screen.value = nextscreen;
	form.submit();
}

window.onload = form.cantidad.length == undefined ? form.cantidad.select() : form.cantidad[0].select();
-->
</script>
<!-- END BLOCK : hoja -->
<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Gastos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="./ros_pro_sec_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="next_screen" type="hidden" id="next_screen">
    <input name="screen" type="hidden" id="screen" value="gs">    
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : gsrow -->
	<tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select();colorRow({i})" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" value="{codgastos}" size="3">
        <input name="nombre_gasto[]" type="text" class="vnombre" id="nombre_gasto" value="{nombre_gasto}" size="30" readonly="true"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onFocus="colorRow({i})" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" value="{concepto}" size="50" maxlength="255"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select();colorRow({i})" onBlur="formatoCampo(this)" onKeyDown="if (event.keyCode == 13) codgastos[{next}].select()" value="{importe}" size="10">
        <input name="cantidad[]" type="hidden" id="cantidad" value="{cantidad}"></td>
    </tr>
	<!-- END BLOCK : gsrow -->
    <tr>
      <th rowspan="3" class="tabla"><input type="button" class="boton" value="Prestamos" onClick="altaPrestamos()"></th>
      <th class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true"></th>
    </tr>
    <tr>
      <th class="rtabla">Gastos de Compra </th>
      <th class="tabla"><input name="compras" type="text" disabled="true" class="rnombre" id="compras" value="{compras}" size="10"></th>
    </tr>
    <tr>
      <th class="rtabla">Total de Gastos </th>
      <th class="tabla"><input name="total_gastos" type="text" class="rnombre" id="total_gastos" value="{total_gastos}" size="10" readonly="true"></th>
    </tr>
  </table>  
    {prestamos}
    <p>
    <input type="button" class="boton" value="<< Regresar" onClick="nextScreen('hd')">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Cancelar" onClick="document.location='./ros_pro_sec_v2.php?cancel=1'">
&nbsp;&nbsp;    
<input name="siguiente" type="button" class="boton" id="siguiente" onClick="nextScreen('result')" value="Siguiente >>"{disabled}>
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var gasto = new Array(), form = document.form;
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{descripcion}";
<!-- END BLOCK : gasto -->

function cambiaGasto(i) {
	if (form.codgastos[i].value == "") {
		form.nombre_gasto[i].value = "";
		form.concepto[i].value = "";
		form.importe[i].value = "";
		form.cantidad[i].value = "";
	}
	else if (gasto[form.codgastos[i].value] != null) {
		// [11-Sep-2007] Código 90 GAS especificar el número de litros comprados
		if (form.codgastos[i].value == 90)
			do {
				form.cantidad[i].value = prompt('Litros de gas comprados:');
				if (get_val(form.cantidad[i]) == 0)
					alert("Para el código 90 GAS debe especificar la cantidad de litros comprados");
			} while (get_val(form.cantidad[i]) == 0);
		else
			form.cantidad[i].value = "";
		
		form.nombre_gasto[i].value = gasto[form.codgastos[i].value];
	}
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		form.codgastos[i].value = form.tmp.value;
		form.codgastos[i].select();
	}
}

function formatoCampo(campo) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
		calculaTotal();
		return true;
	}
	else if (isNaN(parseFloat(campo.value.replace(",", "")))) {
		alert("Solo se permiten números");
		campo.value = campo.form.tmp.value;
		campo.select();
		return false;
	}
	
	var value = parseFloat(campo.value.replace(",", ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		campo.value = campo.form.tmp.value;
		campo.select();
		return false;
	}
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(2, true);
	
	calculaTotal();
	
	return true;
}

function calculaTotal() {
	var tmp, total = 0;
	
	for (var i = 0; i < form.importe.length; i++) {
		total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	}
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
	
	total += !isNaN(parseFloat(form.compras.value.replace(",", ""))) ? parseFloat(form.compras.value.replace(",", "")) : 0;
	
	tmp = new oNumero(total);
	form.total_gastos.value = tmp.formato(2, true);
}

function altaPrestamos() {
	var win = window.open("./ros_pre_altas_v2.php","altas","left=192,top=144,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=480");
	win.focus();
}

function colorRow(i) {
	for (var j = 0; j < form.codgastos.length; j++)
		if (j == i)
			document.getElementById("row" + j).style.backgroundColor = "#ACD2DD";
		else
			document.getElementById("row" + j).style.backgroundColor = "";
}

function nextScreen(nextscreen) {
	form.next_screen.value = nextscreen;
	form.submit();
}

function alCargar() {
	calculaTotal();
	form.codgastos[0].select();
}

window.onload = alCargar();
-->
</script>
<!-- END BLOCK : gastos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Totales</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row" style="font-size: 12pt;">Ventas</th>
      <th class="rtabla" style="font-size: 12pt;">{ventas}</th>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size: 12pt;">Pago de Prestamos </th>
      <th class="rtabla" style="font-size: 12pt;">{pago_prestamos}</th>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size: 12pt;">Gastos</th>
      <th class="rtabla" style="font-size: 12pt;">{gastos}</th>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size: 12pt;">Prestamos</th>
      <th class="rtabla" style="font-size: 12pt;">{prestamos}</th>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size: 18pt;">Efectivo</th>
      <th class="rtabla" style="font-size: 18pt;">{efectivo}</th>
    </tr>
  </table>  <form action="./ros_pro_sec_v2.php" method="post" name="form"><p>
    <input name="screen" type="hidden" id="screen" value="result">
    <input name="next_screen" type="hidden" id="next_screen">
    <input name="ventas" type="hidden" id="ventas" value="{rventas}">
    <input name="gastos" type="hidden" id="gastos" value="{rgastos}">
    <input name="efectivo" type="hidden" id="efectivo" value="{refectivo}">
    <input type="button" class="boton" value="<< Regresar" onClick="nextScreen('gs')">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Cancelar">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Terminar >>" onClick="nextScreen('finish')"{disabled}>
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function nextScreen(nextscreen) {
	form.next_screen.value = nextscreen;
	form.submit();
}
-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
