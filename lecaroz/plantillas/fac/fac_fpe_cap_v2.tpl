<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas Especiales</p>
  <form action="./fac_fpe_cap_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" disabled />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaCia()" onkeydown="movCursor(event.keyCode,num_pro,null,null,null,num_pro)" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaPro()" onkeydown="movCursor(event.keyCode,num_fact,null,null,num_cia,num_fact)" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="rinsert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))validarFactura()" onkeydown="movCursor(event.keyCode,fecha,null,null,num_pro,fecha)" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="if(inputDateFormat(this))validarFecha()" onkeydown="movCursor(event.keyCode,codgastos,null,null,num_fact,codgastos)" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaGasto()" onkeydown="movCursor(event.keyCode,concepto,null,null,fecha,concepto)" size="3" />
        <input name="desc" type="text" disabled="disabled" class="vnombre" id="desc" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" style="width:100%" onkeydown="movCursor(event.keyCode,total,null,null,codgastos,total)" size="30" maxlength="100" /></td>
    </tr>
    <tr id="agua_row" style="display:none">
      <th class="vtabla" scope="row">Agua</th>
      <td class="vtabla">A&ntilde;o
        <input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,bimestre,null,bimestre,null,concepto,total)" size="4" maxlength="4" />
        Bimestre
        <input name="bimestre" type="text" class="insert" id="bimestre" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,total,anio,null,concepto,total)" size="4" maxlength="2" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Total Factura </th>
      <td class="vtabla"><input name="total" type="text" class="rinsert" id="total" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2,true)" onkeydown="movCursor(event.keyCode,null,null,null,concepto,null)" size="10" /></td>
    </tr>
  </table>  
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fpe_cap_v2.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre_cia.value = result;
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fpe_cap_v2.php', 'GET', 'p=' + get_val(f.num_pro), obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	else {
		f.nombre_pro.value = result;
		
		validarFactura();
	}
}

function cambiaGasto() {
	if (f.codgastos.value == '' || f.codgastos.value == '0') {
		f.codgastos.value = '';
		f.desc.value = '';
		f.anio.value = '';
		f.bimestre.value = '';
		document.getElementById('agua_row').style.display = 'none';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fpe_cap_v2.php', 'GET', 'g=' + get_val(f.codgastos), obtenerGasto);
	}
}

var obtenerGasto = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El código de gasto no se encuentra en el catálogo');
		f.codgastos.value = f.tmp.value;
		f.codgastos.select();
	}
	else {
		f.desc.value = result;
		
		document.getElementById('agua_row').style.display = get_val(f.codgastos) == 79 ? 'table-row' : 'none';
		f.anio.value = '';
		f.bimestre.value = '';
	}
}

function validarFactura() {
	if (get_val(f.num_pro) == 0 || get_val(f.num_fact) == 0)
		return false;
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fpe_cap_v2.php', 'GET', 'pro=' + get_val(f.num_pro) + '&fac=' + get_val(f.num_fact), valFac);
	}
}

var valFac = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '-1') {
		alert('La factura ' + f.num_fact.value + ' ya esta capturada en el sistema');
		f.num_fact.value = '';
	}
}

function validarFecha() {
	if (f.fecha.value.length == 0)
		return false;
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fpe_cap_v2.php', 'GET', 'f=' + f.fecha.value, valFecha);
	}
}

var valFecha = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '-1') {
		alert('No puede capturar facturas de meses pasados si ya se han generado balances');
		f.fecha.value = '';
	}
}

function validar() {
	if (get_val(f.num_cia) == 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.num_pro) == 0) {
		alert('Debe especificar el proveedor');
		f.num_pro.select();
	}
	else if (get_val(f.num_fact) == 0) {
		alert('Debe especificar el número de factura');
		f.num_fact.select();
	}
	else if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha de la factura');
		f.fecha.select();
	}
	else if (get_val(f.codgastos) == 0) {
		alert('Debe especificar el código de gasto');
		f.codgastos.select();
	}
	else if (f.concepto.length == 0) {
		alert('Debe poner un concepto a la factura');
		f.concepto.select();
	}
	else if (get_val(f.codgastos) == 79 && (get_val(f.anio) == 0 || get_val(f.bimestre) == 0)) {
		alert('Para el código 79 AGUA debe especificar el año y el bimestre que se esta pagando');
		f.anio.select();
	}
	else if (get_val(f.total) == 0) {
		alert('Debe especificar el total de la factura');
		f.total.select();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		f.siguiente.disabled = true;
		f.submit();
	}
	else
		f.num_cia.select();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = f.num_cia.select();
//-->
</script>
</body>
</html>
