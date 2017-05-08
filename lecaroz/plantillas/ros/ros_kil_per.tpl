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
<td align="center" valign="middle"><p class="title">Consulta de Kilos de Pollo por Periodo </p>
  <form action="./ros_kil_per.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) fecha1.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Productos</th>
      <td class="vtabla"><input name="codmp[]" type="checkbox" id="codmp" value="160" checked="checked" />
        Pollo normal<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="600" checked="checked" />
        Pollo chico<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="700" checked="checked" />
        Pollo grande <br />
        <input name="codmp[]" type="checkbox" id="codmp" value="573" checked="checked" />
        Pollo grande marinado <br />
        <input name="codmp[]" type="checkbox" id="codmp" value="334" checked="checked" />
        Pollo navideño <br />
        <input name="codmp[]" type="checkbox" id="codmp" value="297" checked="checked" />
        Pescuezos<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="363" checked="checked" />
        Alas de pollo<br />
		    <input name="codmp[]" type="checkbox" id="codmp" value="434" checked="checked" />
		    Ala marinada
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
        <option value="" selected="selected"></option>
		<option value="13">13 POLLOS GUERRA</option>
		<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
        <option value="204">204 GONZALEZ AYALA JOSE REGINO</option>
      </select></td>
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
		f.nombre.value = '';
	}
	else {
		var myConn = new XHConn();

		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

		// Pedir datos
		myConn.connect('./ros_kil_per.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
		f.nombre.value = result;
}

function validar() {
	if (f.fecha1.value.length < 8 || f.fecha2.value.length < 8) {
		alert('Debe especificar el periodo de consulta');
		f.fecha1.select();
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
    <td width="60%" class="print_encabezado" align="center">Kilos comprados en el periodo<br />
    {fecha1} al {fecha2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="print">Cantidad (Kg)</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint" scope="row">{num_cia} {nombre} </td>
    <td class="rprint">{kilos}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : total -->
  <tr>
    <th class="rprint" scope="row">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <!-- END BLOCK : total -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
<!-- START BLOCK : back -->
<style type="text/css" media="print">
#boton {
	display: none;
}
</style>
<div id="boton">
<p align="center">
<input type="button" class="boton" value="Regresar" onclick="document.location='./ros_kil_per.php'">
</p>
</div>
<!-- END BLOCK : back -->
</body>
</html>
