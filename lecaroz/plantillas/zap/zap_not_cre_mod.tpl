<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./zap_not_cre_mod.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Impuestos</th>
    </tr>
	<tr>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="movCursor(event.keyCode,num_pro,null,num_pro,num_cia,num_cia)" value="{num_cia}" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="25" /></td>
      <td class="tabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="movCursor(event.keyCode,fecha,num_cia,fecha,num_pro,num_pro)" value="{num_pro}" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" /></td>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,folio,num_pro,folio,fecha,fecha)" value="{fecha}" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="folio" type="text" class="rinsert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) validar_folio()" onkeydown="movCursor(event.keyCode,concepto,fecha,concepto,folio,folio)" value="{folio}" size="8" /></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onkeydown="movCursor(event.keyCode,importe,folio,importe,concepto,concepto)" value="{concepto}" size="30" maxlength="255" /></td>
      <td class="tabla"><input name="importe" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,num_cia,concepto,null,importe,importe)" value="{importe}" size="10" /></td>
      <td class="tabla">
      	<select name="impuestos" id="impuestos" class="insert">
      		<option value="IVA 0"{0}></option>
      		<option value="IVA"{1}>I.V.A.</option>
      		<option value="IVA + RET 4%"{2}>I.V.A. + RET 4%</option>
      		<option value="HONORARIOS/ARRENDAMIENTOS"{3}>HONORARIOS / ARRENDAMIENTOS</option>
      		<option value="ARRENDAMIENTO HABITACION"{4}>ARRENDAMIENTO HABITACION</option>
      		<option value="R35%"{5}>RETENCION 35%</option>
      	</select>
      </td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Modificar" onclick="validar()" />
</p>
</form>
</td>
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
		myConn.connect('./zap_not_cre_mod.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
		myConn.connect('./zap_not_cre_mod.php', 'GET', 'p=' + get_val(f.num_pro), obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText;

	if (result == '') {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	else
		f.nombre_pro.value = result;

	validar_folio();
}

function validar_folio() {
	if (get_val(f.num_pro) == 0 || get_val(f.folio) == 0)
		return false;
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./zap_not_cre_mod.php', 'GET', 'f=' + get_val(f.folio) + '&p=' + get_val(f.num_pro), result_folio);
	}
}

var result_folio = function (oXML) {
	var ok = get_val2(oXML.responseText);

	if (ok < 0) {
		alert('La nota de crédito ya existe en el sistema');
		f.folio.value = f.tmp.value;
		f.folio.select();
	}
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : mod -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerras -->
</body>
</html>
