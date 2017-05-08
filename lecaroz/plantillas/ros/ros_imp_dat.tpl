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
<td align="center" valign="middle"><p class="title">Revisi&oacute;n de Hojas Recibidas </p>
  <form action="./ros_imp_dat.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" maxlength="10" /></td>
    </tr>
  </table>  <p>
    <label>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
    </label>
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.num_cia) == 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha de consulta');
		f.fecha.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- END BLOCK : result -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">{num_cia} {nombre} </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Hoja Diaria<br />
    {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br><table align="center" class="print">
  <tr>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">Existencia</th>
    <th class="print" scope="col">Mercancia<br />
    recibida</th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Venta<br />
    total</th>
    <th class="print" scope="col">Para<br />
    ma&ntilde;ana</th>
    <th class="print" scope="col">Precio<br />
    Venta</th>
    <th class="print" scope="col">Total<br />
    Vendido</th>
    <th class="print" scope="col">Importe<br />
    Venta</th>
  </tr>
  <!-- START BLOCK : producto -->
  <tr>
    <td class="vprint" style="font-size:10pt;">{codmp} {nombre} </td>
    <td class="rprint" style="font-size:10pt;">{existencia}</td>
    <td class="rprint" style="font-size:10pt;">{mer_rec}</td>
    <td class="rprint" style="font-size:10pt;">{total}</td>
    <td class="rprint" style="font-size:10pt;">{venta_total}</td>
    <td class="rprint" style="font-size:10pt;">{manana}</td>
    <td class="rprint" style="font-size:10pt;">{precio_venta}</td>
    <td class="rprint" style="font-size:10pt;">{vendido}</td>
    <td class="rprint" style="font-size:10pt;">{venta}</td>
  </tr>
  <!-- END BLOCK : producto -->
  <tr>
    <th colspan="8" class="rprint">Total Venta </th>
    <th class="rprint_total" style="color:#0000CC;">{total_venta}</th>
  </tr>
  <tr>
    <td colspan="9" class="print">&nbsp;</td>
  </tr>
  <tr>
    <th colspan="8" class="print">Gastos</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : gasto -->
  <tr>
    <td colspan="8" class="vprint" style="font-size:10pt;">{concepto}</td>
    <td class="rprint" style="font-size:10pt;">{importe}</td>
  </tr>
  <!-- END BLOCK : gasto -->
  <tr>
    <th colspan="8" class="rprint">Total Gastos </th>
    <th class="rprint_total" style="color:#CC0000;">{total_gastos}</th>
  </tr>
  <tr>
    <td colspan="9" class="rprint">&nbsp;</td>
  </tr>
  <tr>
    <th colspan="8" class="print">Otros</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : otro -->
  <tr>
    <td colspan="8" class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : otro -->
  <tr>
    <th colspan="8" class="rprint">Total Otros </th>
    <th class="rprint_total" style="color:#0000CC">{importe_otros}</th>
  </tr>
  <tr>
    <td colspan="9" class="print">&nbsp;</td>
  </tr>
  <tr>
    <th colspan="8" class="rprint">Efectivo</th>
    <th class="rprint_total" style="font-size:14pt;">{efectivo}</th>
  </tr>
</table>
<!-- END BLOCK : result -->
</body>
</html>
