<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Pr&eacute;stamos</p>
<form action="./pan_pre_con.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) return false;" size="3" maxlength="3"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo de Listado </th>
    <td class="vtabla"><input name="tipo" type="radio" value="mov_emp" onClick="num_emp.select();" checked>      
      Empleado 
      <input name="num_emp" type="text" class="insert" id="num_emp" size="4" maxlength="4">      <br>
      <input name="tipo" type="radio" value="emp">
        Pendientes por Compa&ntilde;&iacute;a <br>
        <input name="tipo" type="radio" value="cia">
        Saldos por Compa&ntilde;&iacute;a<br>
        <!-- START BLOCK : admin -->
	    <input name="tipo" type="radio" value="esc">
        Estado de cuenta del &uacute;ltimo pr&eacute;stamo
	    <!-- END BLOCK : admin -->	  </td></tr>
</table>
<p>
  <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.tipo[0].checked && form.num_emp.value <= 0) {
			alert("Debe especificar el número de empleado");
			form.num_emp.select();
			return false;
		}
		else if (form.tipo[1].checked && form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else {
			form.submit();
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : por_emp -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Prestamos Personales<br>
    al d&iacute;a {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">C&oacute;digo y nombre del empleado </th>
      <th class="print" scope="col">Fecha de &uacute;ltimo prestamo </th>
      <th class="print" scope="col">Saldo</th>
      <th class="print" scope="col">Abonos</th>
      <th class="print" scope="col">Fecha del &uacute;ltimo pago </th>
      <th class="print" scope="col">Importe del &uacute;ltimo pago </th>
      <th class="print" scope="col">Dias de atraso </th>
    </tr>
    <!-- START BLOCK : fila_emp -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_emp}</td>
      <td class="vprint">{nombre}</td>
      <td class="print" style="color:#C00;">{fecha}</td>
      <td class="rprint" style="color:#C00;">{saldo}</td>
      <td class="rprint" style="color:#00C;">{abonos}</td>
      <td class="print" style="color:#00C;">{fecha_ultimo}</td>
      <td class="rprint" style="color:#00C;">{importe}</td>
      <td class="print" style="color:#C00;">{dias}</td>
	</tr>
	<!-- END BLOCK : fila_emp -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="print">Total</th>
	  <th class="rprint_total">{saldo_total}</th>
	  <th class="rprint_total">{abonos_total}</th>
	  <th class="print">&nbsp;</th>
	  <th class="rprint">&nbsp;</th>
      <th class="rprint">&nbsp;</th>
	</tr>
  </table>
<!-- END BLOCK : por_emp -->

<!-- START BLOCK : saldo_cia -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td align="right" class="print_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de Prestamos Personales<br>
      al d&iacute;a {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre del Empleado </th>
      <th class="print" scope="col">Saldo</th>
    </tr>
    <!-- START BLOCK : fila_cia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_emp}</td>
      <td class="vprint">{nombre}</td>
      <td class="rprint">{saldo}</td>
    </tr>
	<!-- END BLOCK : fila_cia -->
    <tr>
      <th colspan="2" class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
    </tr>
  </table>
<!-- END BLOCK : saldo_cia -->

<!-- START BLOCK : saldo_all -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de Prestamos Personales <br>
      al {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre de la Compa&ntilde;&iacute;a </th>
      <th class="print" scope="col">Saldo</th>
    </tr>
    <!-- START BLOCK : fila_all -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{saldo}</td>
    </tr>
	<!-- END BLOCK : fila_all -->
    <tr>
      <th colspan="2" class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
    </tr>
  </table>
<!-- END BLOCK : saldo_all -->

<!-- START BLOCK : saldo_emp -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center" style="font-size:14pt;">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldo de Empleado <br>
    {num_emp} {nombre_emp}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Tipo</th>
    <th class="print" scope="col">Importe</th>
	<th class="print" scope="col">Fecha</th>
  </tr>
  <!-- START BLOCK : fila_mov -->
  <tr>
    <td class="vprint">{tipo}</td>
    <td class="rprint">{importe}</td>
	<td class="print">{fecha}</td>
  </tr>
  <!-- END BLOCK : fila_mov -->
  <tr>
    <th class="rprint">Saldo Total</th>
    <th class="rprint_total">{total}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
</table>
<!-- END BLOCK : saldo_emp -->
</body>
</html>
