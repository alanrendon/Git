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
<td align="center" valign="middle"><p class="title">Captura de Notas de Cr&eacute;dito</p>
  <form action="./zap_not_cre.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
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
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,num_pro[{i}],null,num_pro[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre_cia[]" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],num_cia[{i}],fecha[{i}],num_pro[{back}],num_pro[{next}])" size="3" />
        <input name="nombre_pro[]" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,folio[{i}],num_pro[{i}],folio[{i}],fecha[{back}],fecha[{next}])" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="folio[]" type="text" class="rinsert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) validar_folio({i})" onkeydown="movCursor(event.keyCode,concepto[{i}],fecha[{i}],concepto[{i}],folio[{back}],folio[{next}])" size="8" /></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onkeydown="movCursor(event.keyCode,importe[{i}],folio[{i}],importe[{i}],concepto[{back}],concepto[{next}])" size="30" maxlength="255" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,num_cia[{next}],concepto[{i}],null,importe[{back}],importe[{next}])" size="10" /></td>
      <td class="tabla">
      	<select name="impuestos[]" id="impuestos" class="insert">
      		<option value="IVA 0"></option>
      		<option value="IVA">I.V.A.</option>
      		<option value="IVA + RET 4%">I.V.A. + RET 4%</option>
      		<option value="HONORARIOS/ARRENDAMIENTOS">HONORARIOS / ARRENDAMIENTOS</option>
      		<option value="ARRENDAMIENTO HABITACION">ARRENDAMIENTO HABITACION</option>
      		<option value="R35%">RETENCION 35%</option>
      	</select>
      </td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre_cia[i].value = '';
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./zap_not_cre.php', 'GET', 'c=' + get_val(f.num_cia[i]) + '&i=' + i, obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText.split('|'), i = get_val2(result[0]), nombre = result[1];

	if (nombre == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
	}
	else
		f.nombre_cia[i].value = nombre;
}

function cambiaPro(i) {
	if (f.num_pro[i].value == '' || f.num_pro[i].value == '0') {
		f.num_pro[i].value = '';
		f.nombre_pro[i].value = '';
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./zap_not_cre.php', 'GET', 'p=' + get_val(f.num_pro[i]) + '&i=' + i, obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText.split('|'), i = get_val2(result[0]), nombre = result[1];

	if (nombre == '') {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro[i].value = f.tmp.value;
		f.num_pro[i].select();
	}
	else
		f.nombre_pro[i].value = nombre;

	validar_folio(i);
}

function validar_folio(i) {
	if (get_val(f.num_pro[i]) == 0 || get_val(f.folio[i]) == 0)
		return false;
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./zap_not_cre.php', 'GET', 'f=' + get_val(f.folio[i]) + '&p=' + get_val(f.num_pro[i]) + '&i=' + i, result_folio);
	}
}

var result_folio = function (oXML) {
	var result = oXML.responseText.split('|'), i = get_val2(result[0]), ok = get_val2(result[1]);

	if (ok < 0) {
		alert('La nota de crédito ya existe en el sistema');
		f.folio[i].value = f.tmp.value;
		f.folio[i].select();
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

window.onload = f.num_cia[0].select();
//-->
</script>
</body>
</html>
