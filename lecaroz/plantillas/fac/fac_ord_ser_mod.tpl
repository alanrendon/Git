<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Ordenes de Servicio</p>
  <form action="./fac_ord_ser_mod.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) folio.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Maquina</th>
      <td class="vtabla"><select name="idmaq" class="insert" id="idmaq">
        <option value="" selected="selected"></option>
		<!-- START BLOCK : idm -->
		<option value="{id}">{desc}</option>
		<!-- END BLOCK : idm -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Orden de Servicio </th>
      <td class="vtabla"><input name="folio" type="text" class="rinsert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha1.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo_orden" type="radio" checked="checked" />
      Todos
        <input name="tipo_orden" type="radio" value="1" />
        Reparaci&oacute;n
          <input name="tipo_orden" type="radio" value="2" />
          Mantenimiento</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estatus</th>
      <td class="vtabla"><input name="estatus" type="radio" value="-1" checked="checked" />
        Todos
          <input name="estatus" type="radio" value="0" />
          Pendientes
          <input name="estatus" type="radio" value="1" />
        Terminado</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
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

		listaMaquinaria();
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./fac_ord_ser_alta.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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

	listaMaquinaria();
}

function listaMaquinaria() {
	var myConn = new XHConn();

	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

	// Pedir datos
	myConn.connect('./fac_ord_ser_mod.php', 'GET', 'ce=' + get_val(f.num_cia), generarListadoMaquinaria);
}

var generarListadoMaquinaria = function (oXML) {
	var result = oXML.responseText, j, tmp;

	if (result == '-1') {
		alert('La compañía no tiene maquinaria asociada');

		f.num_cia.value = f.tmp.value;
		cambiaCia();
		f.num_cia.select();

		return false;
	}

	result = result.split('|');

	f.idmaq.length = result.length + 1;
	f.idmaq.options[0].value = '';
	f.idmaq.options[0].text = '';
	for (j = 0; j < result.length; j++) {
		tmp = result[j].split('/');

		f.idmaq.options[j + 1].value = tmp[0];
		f.idmaq.options[j + 1].text = tmp[1];
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Ordenes de Servicio</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Opciones</th>
      <th class="tabla" scope="col">OK</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">M&aacute;quina</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Observaciones</th>
      <th class="tabla" scope="col">Costo <br />
        Reparaci&oacute;n </th>
      <th class="tabla" scope="col">Estatus</th>
    </tr>
	<!-- START BLOCK : row -->
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
      <td class="tabla"><input type="button" class="boton" value="Mod" onclick="mod({id})" />
        <input type="button" class="boton" value="Det" onclick="det({id})" /></td>
      <td class="tabla"><input name="ok[]" type="checkbox" id="ok" value="{id}" /></td>
      <td class="rtabla">{folio}</td>
      <td class="vtabla">{num_cia} {nombre_cia} </td>
      <td class="vtabla">{maquina}</td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{tipo}</td>
      <td class="vtabla">{concepto}</td>
      <td class="vtabla">{observaciones}</td>
      <td class="rtabla">{costo_reparacion}</td>
      <td class="tabla">{estatus}</td>
    </tr>
	<!-- END BLOCK : row -->
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
    	<th colspan="9" class="rtabla">Total</th>
    	<th class="rtabla">{total}</th>
    	<th class="tabla">&nbsp;</th>
    	</tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='fac_ord_ser_mod.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Actualizar" />
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function mod(id) {
	var win = window.open('./fac_ord_ser_mod.php?id=' + id, 'mod', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function det(id) {
	var win = window.open('./fac_ord_ser_det.php?id=' + id, 'det', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}
//-->
</script>
<!-- END BLOCK : result -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Orden de Servicio</p>
  <form action="./fac_ord_ser_mod.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <table class="tabla" id="captura">
    <tr>
      <th class="vtabla">Folio</th>
      <td class="vtabla" style="font-weight:bold;"><input name="folio" type="hidden" id="folio" value="{folio}" />
        {folio}</td>
      <th colspan="2" class="vtabla">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) autorizo.select()" value="{num_cia}" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30" /></td>
      <th colspan="2" class="vtabla">No. M&aacute;quina </th>
      <td class="vtabla"><select name="idmaq" class="insert" id="idmaq">
        <option value=""></option>
		<!-- START BLOCK : idmaq -->
		<option value="{id}"{selected}>{desc}</option>
		<!-- END BLOCK : idmaq -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo</th>
      <td class="vtabla"><input name="tipo_orden" type="radio" value="1"{tipo_orden_1} />
        Reparaci&oacute;n
          <input name="tipo_orden" type="radio" value="2"{tipo_orden_2} />
          Mantenimiento</td>
      <th colspan="2" class="vtabla">Estatus</th>
      <td class="vtabla"><input name="estatus" type="checkbox" id="estatus" value="1"{estatus} />
        Terminado</td>
    </tr>
    <tr>
      <th class="vtabla">Autorizo</th>
      <td colspan="4" class="vtabla"><input name="autorizo" type="text" class="vinsert" id="autorizo" style="width:100%;" onkeydown="if (event.keyCode == 13) fecha.select()" value="{autorizo}" /></td>
      </tr>
    <tr>
      <td colspan="5" class="tabla">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="5" class="vtabla">Concepto</th>
      </tr>
    <tr>
      <td colspan="5" class="tabla"><textarea name="concepto" rows="10" class="insert" id="concepto" style="width:100%;">{concepto}</textarea></td>
      </tr>
	<tr>
      <td colspan="5" class="tabla">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="5" class="vtabla">Observaciones</th>
      </tr>
    <tr>
      <td colspan="5" class="tabla"><textarea name="observaciones" rows="5" class="insert" id="observaciones" style="width:100%;">{observaciones}</textarea></td>
      </tr>
    <tr>
      <td colspan="5" class="tabla">&nbsp;</td>
      </tr>
	<tr>
      <th class="tabla"><input type="button" class="boton" value="[+]" onclick="addNewRow()" />
        Factura</th>
      <th class="tabla">Proveedor</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Importe</th>
    </tr>
	<!-- START BLOCK : fac -->
    <tr id="fac">
      <td class="tabla"><input name="id_fac[]" type="hidden" id="id_fac" value="{id_fac}" /><input name="ok[]" type="hidden" id="ok" value="{ok}" /><input name="scan[]" type="button" class="boton" id="scan" onclick="scanFac(0)" value="Scan"{dis} /><input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.replace(/[^a-zA-Z0-9]/g,'').toUpperCase();if(this.value.trim()!='')validarFac(0);else this.value='';" onkeydown="movCursor(event.keyCode,0,'num_pro',null,'num_pro','num_fact','num_fact')" value="{num_fact}" size="8"{ro} /></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro(0)" onkeydown="movCursor(event.keyCode,0,'fecha_fac','num_fact','fecha_fac','num_pro','num_pro')" value="{num_pro}" size="3"{ro} /><input name="nombre_pro[]" type="text" disabled="disabled" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" /></td>
      <td class="tabla"><input name="fecha_fac[]" type="text" class="insert" id="fecha_fac" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,0,'concepto_fac','num_pro','concepto_fac','fecha_fac','fecha_fac')" value="{fecha}" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="concepto_fac[]" type="text" class="vinsert" id="concepto_fac" onkeydown="movCursor(event.keyCode,0,'importe','fecha_fac','importe','concepto_fac','concepto_fac')" value="{concepto}" size="30" maxlength="255" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,0,'num_fact','concepto_fac',null,'importe','importe')" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fac -->
	<!-- START BLOCK : fac_ext -->
	<tr>
	<td class="tabla"><input name="id_fac[]" type="hidden" id="id_fac" value="{id_fac}" /><input name="ok[]" type="hidden" id="ok" value="{ok}" /><input type="button" class="boton" value="Scan" onclick="scanFac({i})"{dis} /><input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) validarFac(' + cont_row + ')" onkeydown="movCursor(event.keyCode,{i},'num_pro',null,'num_pro','num_fact','num_fact')" value="{num_fact}" size="8"{ro} /></td>
	<td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro({i})" onkeydown="movCursor(event.keyCode,{i},'fecha_fac','num_fact','fecha_fac','num_pro','num_pro')" value="{num_pro}" size="3"{ro} />
	<input name="nombre_pro[]" type="text" disabled="disabled" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" /></td>
	<td class="tabla"><input name="fecha_fac[]" type="text" class="insert" id="fecha_fac" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,{i},'concepto_fac','num_pro','concepto_fac','fecha_fac','fecha_fac')" value="{fecha}" size="10" maxlength="10" /></td>
	<td class="tabla"><input name="concepto_fac[]" type="text" class="vinsert" id="concepto_fac" onkeydown="movCursor(event.keyCode,{i},'importe','num_pro','importe','concepto_fac','concepto_fac')" value="{concepto}" size="30" maxlength="255" /></td>
	<td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,{i},'num_fact','concepto_fac',null,'importe','importe')" value="{importe}" size="10" /></td>
	</tr>
	<!-- END BLOCK : fac_ext -->
    <tr>
      <th colspan="4" class="rtabla">Costo Reparaci&oacute;n </th>
      <th class="tabla"><input name="total" type="text" disabled="disabled" class="rnombre" id="total" size="10" style="width:100%;" /></th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Modificar" onclick="validar()" />
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cont_row = {cont_row}, offset = 13;

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
		myConn.connect('./fac_ord_ser_alta.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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

	listaMaquinaria();
}

function cambiaPro(i) {
	var num_pro = f.num_pro.length == undefined ? f.num_pro : f.num_pro[i];
	var nombre_pro = f.nombre_pro.length == undefined ? f.nombre_pro : f.nombre_pro[i];

	if (num_pro.value == '' || num_pro.value == '0') {
		num_pro.value = '';
		nombre_pro.value = '';
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./fac_ord_ser_alta.php', 'GET', 'p=' + get_val(num_pro) + '&i=' + i, obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText.split('|'), i = get_val2(result[0]), nombre = result[1];

	var num_pro = f.num_pro.length == undefined ? f.num_pro : f.num_pro[i];
	var nombre_pro = f.nombre_pro.length == undefined ? f.nombre_pro : f.nombre_pro[i];

	if (nombre == '') {
		alert('El proveedor no se encuentra en el catálogo');
		num_pro.value = f.tmp.value;
		num_pro.select();
	}
	else
		nombre_pro.value = nombre;
}

function validarFac(i) {
}

function calculaTotal() {
	var total = 0;

	if (f.importe.length == undefined)
		total = get_val(f.importe);
	else
		for (var i = 0; i < f.importe.length; i++)
			total += get_val(f.importe[i]);

	f.total.value = numberFormat(total, 2);
}

function listaMaquinaria() {
	if (get_val(f.num_cia) <= 0) {
		f.idmaq.length = 1;
		f.idmaq.options[0].value = '';
		f.idmaq.options[0].text = '';
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./fac_ord_ser_alta.php', 'GET', 'ce=' + get_val(f.num_cia), generarListadoMaquinaria);
	}
}

var generarListadoMaquinaria = function (oXML) {
	var result = oXML.responseText, j, tmp;

	if (result == '-1') {
		alert('La compañía no tiene maquinaria asociada');

		f.num_cia.value = '';
		f.nombre_cia.value = '';
		f.num_cia.select();

		f.idmaq.length = 1;
		f.idmaq.options[0].value = '';
		f.idmaq.options[0].text = '';

		return false;
	}

	result = result.split('|');

	f.idmaq.length = result.length + 1;
	f.idmaq.options[0].value = '';
	f.idmaq.options[0].text = '';
	for (j = 0; j < result.length; j++) {
		tmp = result[j].split('/');

		f.idmaq.options[j + 1].value = tmp[0];
		f.idmaq.options[j + 1].text = tmp[1];
	}
	f.idmaq.selectedIndex = 0;
}

function scanOrden() {
	var folio = get_val(f.folio);

	if (folio == 0) {
		alert('Debe especificar el número de orden');
		return false;
	}

	var win = window.open("fac_ord_ser_scan.php?&folio=" + folio, "scan", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");

	win.focus();
}

function scanFac(i) {
	var num_fact = f.num_fact.length == undefined ? f.num_fact.value : f.num_fact[i].value;
	var num_pro = f.num_pro.length == undefined ? get_val(f.num_pro) : get_val(f.num_pro[i]);
	var folio = get_val(f.folio);

	if (num_fact == '' || num_pro == 0) {
		alert('Debe especificar el proveedor y el número de factura antes de escanear');
		return false;
	}

	var win = window.open("fac_fac_ord_ser_scan.php?num_pro=" + num_pro + "&num_fact=" + num_fact + "&i=" + i + "&folio=" + folio, "scan", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");

	win.focus();
}

function addNewRow(){
	// Obtener el objeto 'captura'
	var table = document.getElementById("captura");
	// Obtener el objeto 'fac' para extraer los estilos
	// var trow = document.getElementById("fac");

	// Obtener el contenido de las celdas
	// var content = trow.getElementsByTagName("td");
	// Crear nuevo fila
	var newRow = table.insertRow(cont_row + offset);
	// Incrementar contador de filas
	cont_row++;

	var str = '<td class="tabla"><input name="id_fac[]" type="hidden" id="id_fac" value="" /><input name="ok[]" type="hidden" id="ok" value="0" /><input type="button" class="boton" value="Scan" onclick="scanFac(' + cont_row + ')" />';
	str += '<input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.replace(/[^a-zA-Z0-9]/g,\'\').toUpperCase();if(this.value.trim()!=\'\')validarFac(' + cont_row + ');else this.value=\'\';" onkeydown="movCursor(event.keyCode,' + cont_row + ',\'num_pro\',null,\'num_pro\',\'num_fact\',\'num_fact\')" size="8" /></td>';
	str += '<td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro(' + cont_row + ')" onkeydown="movCursor(event.keyCode,' + cont_row + ',\'fecha_fac\',\'num_fact\',\'fecha_fac\',\'num_pro\',\'num_pro\')" size="3" /><input name="nombre_pro[]" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>';
	str += '<td class="tabla"><input name="fecha_fac[]" type="text" class="insert" id="fecha_fac" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,' + cont_row + ',\'concepto_fac\',\'num_pro\',\'concepto_fac\',\'fecha_fac\',\'fecha_fac\')" size="10" maxlength="10" /></td>';
	str += '<td class="tabla"><input name="concepto_fac[]" type="text" class="vinsert" id="concepto_fac" onkeydown="movCursor(event.keyCode,' + cont_row + ',\'importe\',\'fecha_fac\',\'importe\',\'concepto_fac\',\'concepto_fac\')" size="30" maxlength="255" /></td>';
	str += '<td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2)) calculaTotal()" onkeydown="movCursor(event.keyCode,' + cont_row + ',\'num_fact\',\'concepto_fac\',null,\'importe\',\'importe\')" size="10" /></td>';
	newRow.innerHTML = str;

	// Copiar atributos de la fila a la nueva fila
	//newRow.className = trow.attributes['class'].value;
	// Copiar elementos de la fila
	//copyRow(content, newRow);
}

function copyRow(content, trow) {
	var cnt = 0;
	for (; cnt < content.length; cnt++)
		appendCell(trow, content[cnt].innerHTML);
}

function appendCell(trow, txt) {
	var newCell = trow.insertCell(trow.cells.length);
	newCell.innerHTML = txt;
}

function movCursor(keyCode, Index, enter, lt, rt, up, dn) {
	var Next = Index + 1, Back = Index - 1;

	if (eval('f.num_fact').length == undefined) {
		if (keyCode == 13 && enter != null && document.getElementById(enter)) document.getElementById(enter).select();
		else if (keyCode == 37 && lt != null && document.getElementById(lt)) document.getElementById(lt).select();
		else if (keyCode == 39 && rt != null && document.getElementById(rt)) document.getElementById(rt).select();
		else if (keyCode == 38 && up != null && document.getElementById(up)) document.getElementById(up).select();
		else if (keyCode == 40 && dn != null && document.getElementById(dn)) document.getElementById(dn).select();
	}
	else {
		if (keyCode == 13 && enter != null) {
			if (enter != 'num_fact' && eval('f.' + enter)[Index]) eval('f.' + enter)[Index].select();
			else if (enter == 'num_fact' && eval('f.' + enter)[Next]) eval('f.' + enter)[Next].select();
		}
		else if (keyCode == 37 && lt != null && eval('f.' + lt)[Index]) eval('f.' + lt)[Index].select();
		else if (keyCode == 39 && rt != null && eval('f.' + rt)[Index]) eval('f.' + rt)[Index].select();
		else if (keyCode == 38 && up != null && eval('f.' + up)[Back]) eval('f.' + up)[Back].select();
		else if (keyCode == 40 && dn != null && eval('f.' + dn)[Next]) eval('f.' + dn)[Next].select();
	}
}

function validar() {
	if (f.num_fact.length == undefined && f.num_fact != '' && get_val(f.num_pro) > 0 && f.fecha_fac.value.length > 8 && get_val(f.importe) > 0 && get_val(f.ok) == 0) {
		if (!confirm('Las facturas que no se hayan escaneado no se ingresaran a la orden de servicio, ¿desea continuar?'))
			return false;
	}
	else {
		var cont = 0;
		for (var i = 0; i < f.num_fact.length; i++)
			if (f.num_fact[i].value != '' && get_val(f.num_pro[i]) > 0 && f.fecha_fac[i].value.length > 8 && get_val(f.importe[i]) > 0 && get_val(f.ok[i]) == 0)
				cont++;

		if (cont > 0 && !confirm('Las facturas que no se hayan escaneado no se ingresaran a la orden de servicio, ¿desea continuar?'))
			return false;
	}

	if (f.fecha.length < 8) {
		alert('Debe especificar la fecha');
		f.fecha.select();
	}
	else if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.idmaq) <= 0) {
		alert('Debe especificar la maquina');
		f.idmaq.focus();
	}
	else if (f.autorizo.value.length < 3) {
		alert('Debe especificar el nombre de la persona que autoriza la orden de servicio');
		f.autorizo.select();
	}
	else if (f.concepto.value.length < 3) {
		alert('Debe poner un concepto');
		f.concepto.focus();
	}
	else if (f.concepto.value.length > 512) {
		alert('La longitud del concepto no puede ser mayor a 512 caracteres');
		f.concepto.focus();
	}
	else if (f.observaciones.value.length > 512) {
		alert('La longitud de las observaciones no puede ser mayor a 512 caracteres');
		f.observaciones.focus();
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.fecha.select();
}

window.onload = function () { f.fecha.select(); calculaTotal(); }
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
<!-- END BLOCK : cerrar -->
</body>
</html>
