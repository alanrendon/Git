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
<!-- START BLOCK : registro -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Venta de Barredura<br>
    Registro de Comprobantes a Pagar</p>
  <form action="./pan_bar_pag.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla">Fecha del pago <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha_pago" type="text" class="insert" id="fecha_pago" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante1.select();
else if (event.keyCode == 38) no_comprobante2.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Comprador</th>
      <td class="vtabla"><select name="color" class="insert" id="color">
        <option value="1" selected>1. AZUL</option>
        <option value="2">2. ROSA</option>
        <option value="3">3. AMARILLO</option>
        <option value="4">4. VERDE</option>
        <option value="5">5. BLANCO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Paga del comprobante No. </th>
      <td class="vtabla"><input name="no_comprobante1" type="text" class="insert" id="no_comprobante1" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante2.select();
else if (event.keyCode == 38) fecha_pago.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="rtabla">al No. </th>
      <td class="vtabla"><input name="no_comprobante2" type="text" class="insert" id="no_comprobante2" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) fecha_pago.select();
else if (event.keyCode == 38) no_comprobante1.select();" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Registrar" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha_pago.value == "") {
			alert("Debe especificar la fecha");
			form.fecha_pago.select();
			return false;
		}
		else if (form.no_comprobante1.value <= 0 || form.no_comprobante2.value <= 0) {
			alert("Debe especificar el número de comprobante");
			form.no_comprobante1.select();
			return false;
		}
		else {
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.fecha_pago.select();
		} 
	}
	
	window.onload = document.form.fecha_pago.select();
</script>
<!-- END BLOCK : registro -->
<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comprobantes de Barredura <br>
      Pagados      <br>
      el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="70%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">Comprobante</th>
      <th class="print" scope="col">Importe</th>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre de la Compa&ntilde;&iacute;a </th>
    </tr>
    <!-- START BLOCK : color -->
	<tr>
      <th colspan="4" class="vprint_total">Comprador: {no_color} {color}</th>
    </tr>
    <!-- START BLOCK : comprador -->
	<tr>
      <td class="print">{comprobante}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
    </tr>
	<!-- END BLOCK : comprador -->
    <tr>
      <th class="print">&nbsp;</th>
      <th class="rprint_total">{total}</th>
      <th colspan="2" class="print">&nbsp;</th>
    </tr>
	  <!-- END BLOCK : color -->
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
</table>
<!-- END BLOCK : listado -->

<!-- START BLOCK : listado2 -->
<br style="page-break-after:always;">

<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Totales Pagados de Barredura<br>
      por Compa&ntilde;&iacute;a<br>
      el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="70%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre de la Compa&ntilde;&iacute;a </th>
      <th class="print" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="print">Total</th>
      <th class="rprint_total">{total}</th>
    </tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		document.location = "./pan_bar_pag.php";
	}
	
	window.onload = imprimir();
</script>
<!-- END BLOCK : listado2 -->
</body>
</html>
