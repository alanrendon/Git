<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Comparativo de Gastos Mensual</p>
  <form action="./bal_com_gas_mes.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) cod.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Opciones</th>
      <td class="vtabla"><input name="ord_adm" type="checkbox" id="ord_adm" value="1" />
        Ordenar por admin. 
        <input name="div" type="checkbox" id="div" value="1" />
        Hoja por admin.</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="cod" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="if (event.keyCode == 13) anio1.select()" size="3" />
        <input name="desc" type="text" disabled="disabled" class="vnombre" id="desc" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes 1 </th>
      <td class="vtabla"><select name="mes1" class="insert" id="mes1">
        <option value="1"{mes1_1}>ENERO</option>
        <option value="2"{mes1_2}>FEBRERO</option>
        <option value="3"{mes1_3}>MARZO</option>
        <option value="4"{mes1_4}>ABRIL</option>
        <option value="5"{mes1_5}>MAYO</option>
        <option value="6"{mes1_6}>JUNIO</option>
        <option value="7"{mes1_7}>JULIO</option>
        <option value="8"{mes1_8}>AGOSTO</option>
        <option value="9"{mes1_9}>SEPTIEMBRE</option>
        <option value="10"{mes1_10}>OCTUBRE</option>
        <option value="11"{mes1_11}>NOVIEMBRE</option>
        <option value="12"{mes1_12}>DICIEMBRE</option>
      </select>
        <input name="anio1" type="text" class="insert" id="anio1" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) anio2.select()" value="{anio1}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes 2 </th>
      <td class="vtabla"><select name="mes2" class="insert" id="mes2">
        <option value="1"{mes2_1}>ENERO</option>
        <option value="2"{mes2_2}>FEBRERO</option>
        <option value="3"{mes2_3}>MARZO</option>
        <option value="4"{mes2_4}>ABRIL</option>
        <option value="5"{mes2_5}>MAYO</option>
        <option value="6"{mes2_6}>JUNIO</option>
        <option value="7"{mes2_7}>JULIO</option>
        <option value="8"{mes2_8}>AGOSTO</option>
        <option value="9"{mes2_9}>SEPTIEMBRE</option>
        <option value="10"{mes2_10}>OCTUBRE</option>
        <option value="11"{mes2_11}>NOVIEMBRE</option>
        <option value="12"{mes2_12}>DICIEMBRE</option>
      </select>
        <input name="anio2" type="text" class="insert" id="anio2" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio2}" size="4" maxlength="4" /></td>
    </tr>
  </table>  <p>
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
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./bal_com_gas_mes.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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

function cambiaCod() {
	if (f.cod.value == '' || f.cod.value == '0') {
		f.cod.value = '';
		f.desc.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./bal_com_gas_mes.php', 'GET', 'g=' + get_val(f.cod), obtenerCod);
	}
}

var obtenerCod = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El código no se encuentra en el catálogo');
		f.cod.value = f.tmp.value;
		f.cod.select();
	}
	else
		f.desc.value = result;
}

function validar() {
	if (get_val(f.cod) <= 0) {
		alert('Debe especificar el código del gasto');
		f.cod.select();
	}
	else if (get_val(f.anio1) <= 2000) {
		alert('Debe especificar el año de consulta');
		f.anio1.select();
	}
	else if (get_val(f.anio2) <= 2000) {
		alert('Debe especificar el año de consulta');
		f.anio2.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comparativo Mensual de Gastos<br />
    {cod} {desc}{admin}</td>
    <td width="20%" class="rprint_encabezado">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : fila_admin -->
  <tr>
    <th colspan="4" class="vprint_total" scope="row">{admin}</th>
  </tr>
  <!-- END BLOCK : fila_admin -->
  <tr>
    <th class="print" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="print">{mes1} {anio1} </th>
    <th class="print">{mes2} {anio2} </th>
    <th class="print">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint" scope="row">{num_cia} {nombre} </td>
    <td class="rprint">{importe1}</td>
    <td class="rprint">{importe2}</td>
    <td class="rprint">{dif}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint" scope="row">Totales</th>
    <th class="rprint_total">{total1}</th>
    <th class="rprint_total">{total2}</th>
    <th class="rprint_total">{dif}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
