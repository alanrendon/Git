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
<td align="center" valign="middle"><p class="title">Listado de Precios y Pesos Promedio</p>
  <form action="./ros_pre_pes.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><select name="codmp" class="insert" id="codmp">
        <option value="" selected="selected"></option>
        <option value="160">POLLO NORMAL</option>
        <option value="600">POLLO CHICO</option>
        <option value="700">POLLO GRANDE</option>
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
		<option value="" selected="selected"></option>
		<option value="13">13 POLLOS GUERRA</option>
		<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
		<option value="204">204 GONZALEZ AYALA JOSE REGINO</option>
      </select>
      </td>
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
}

function validar() {
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
    <td class="rprint_encabezado">{fecha}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Precios y Pesos Promedio </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">Precio<br />
    Venta</th>
    <th class="print" scope="col">Peso<br />
    M&aacute;ximo</th>
    <th class="print" scope="col">Peso<br />
      M&iacute;nimo</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <td colspan="4" class="vprint" style="font-weight:bold; font-size:10pt;">{num_cia} {nombre} </td>
  </tr>
  <!-- START BLOCK : pro -->
  <tr>
    <td class="vprint">{codmp} {nombre} {alt} </td>
    <td class="rprint">{precio_venta}</td>
    <td class="rprint">{peso_max}</td>
    <td class="rprint">{peso_min}</td>
  </tr>
  <!-- END BLOCK : pro -->
  <!-- END BLOCK : cia -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
