<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Saldos de Proveedores</p>
  <form action="" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Listado por: <br>        <input name="tipo" type="radio" onClick="form.num_cia.disabled = false" value="cia" checked>
        Compa&ntilde;&iacute;a&nbsp;
        <input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3">        <br>
        <input name="tipo" type="radio" onClick="form.num_cia.disabled = true" value="todas">
        Todas las cias. </th>
      <th class="vtabla" scope="col">Ordenado:<br>
        <input name="orden" type="radio" value="num" checked>     
        Num&eacute;ricamente<br>
        <input name="orden" type="radio" value="alf">
        Alfab&eacute;ticamente</th>
    </tr>
    <tr>
      <th class="vtabla">Fecha de corte <font size="-2">(ddmmaa)</font> </th>
      <th class="vtabla"><input name="fecha_corte" type="text" class="insert" id="fecha_corte" onKeyDown="if (event.keyCode == 13) actualiza_fecha(this)" value="{fecha}" ></th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()"> 
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.tipo[0].checked == true && document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.fecha_corte.value == "") {
			alert("Debe especificar la fecha de corte");
			document.form.fecha_corte.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.fecha_corte.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<!-- START BLOCK : cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Relaci&oacute;n de Saldos de Proveedores con An&aacute;lisis de Antig&uuml;edad <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" class="print">
    <!-- START BLOCK : proveedor -->
	<tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre del Proveedor </th>
      <th class="print" scope="col">Fecha de Emisi&oacute;n </th>
      <th class="print" scope="col">Factura no. </th>
      <th class="print" scope="col">Importe</th>
      <th class="print" scope="col">Fecha de vencimiento </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <!-- START BLOCK : title -->
	  <td width="10%" class="print" rowspan="{rowspan}">{num_pro}</td>
      <td width="40%" class="vprint" rowspan="{rowspan}">{nombre_pro}</td>
	  <!-- END BLOCK : title -->
      <td width="10%" class="print">{fecha_mov}</td>
      <td width="10%" class="print">{num_fact}</td>
      <td width="20%" class="rprint">{importe}</td>
      <td width="10%" class="print">{fecha_pago}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="4" class="rprint">Total de la cuenta </th>
      <th class="rprint_total">{total}</th>
      <th class="print">&nbsp;</th>
    </tr>
    <tr>
      <td colspan="6">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : proveedor -->
  </table>
  <br>
  <table class="print">
  <tr>
    <th class="print" scope="col">Gran Total </th>
  </tr>
  <tr>
    <th class="print_total">{gran_total}</th>
  </tr>
</table>

</td>
</tr>
</table>
<!-- END BLOCK : cia -->
<!-- END BLOCK : listado -->
</body>
</html>
