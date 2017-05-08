<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Estados de Cuenta de Bancos<br>
      del {dia1} de {mes1} al {dia2} de {mes2} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : concepto -->
  <table align="center" class="print">
  <tr>
    <th class="print" scope="col"><font size="+1">{cod_mov} - {descripcion}</font></th>
  </tr>
</table>
<br>
  <!-- END BLOCK : concepto -->
  <!-- START BLOCK : cia -->
  <table width="100%" class="print">
    <tr>
      <th width="10%" class="print" scope="col"><font size="+1">Cia.: {num_cia}</font> </th>
      <th colspan="2" class="print" scope="col"><font size="+1">Cuenta.: {cuenta}</font> </th>
      <th colspan="4" class="print" scope="col"><font size="+1">{nombre_cia} ({nombre_corto})</font> </th>
    </tr>
    <!-- START BLOCK : saldo_anterior -->
	<tr>
      <th class="print" scope="col">Saldo Anterior Libros </th>
      <th colspan="2" class="print_total" scope="col">{saldo_anterior}</th>
      <th class="print" scope="col">Saldo Anterior Bancos </th>
      <th class="print_total" scope="col">{saldo_anterior_bancos}</th>
      <th colspan="2" class="print" scope="col">&nbsp;</th>
    </tr>
	<!-- END BLOCK : saldo_anterior -->
    <tr>
      <th width="10%" class="print" scope="col">Fecha</th>
      <th width="10%" class="print" scope="col">Dep&oacute;sito</th>
      <th width="10%" class="print" scope="col">Retiro</th>
      <th width="10%" class="print" scope="col">Cheque</th>
      <th width="30%" class="print" scope="col">Beneficiario</th>
      <th width="20%" class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Fecha conciliaci&oacute;n </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="10%" class="print">{fecha}</td>
      <td width="10%" class="rprint">{deposito}</td>
      <td width="10%" class="rprint">{retiro}</td>
      <td width="10%" class="print">{folio}</td>
      <td width="30%" class="vprint">{beneficiario}</td>
      <td width="20%" class="vprint">{concepto}</td>
      <td class="print">{fecha_con}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th width="10%" class="print">Total Cuenta </th>
      <th width="10%" class="rprint_total">{total_depositos}</th>
      <th width="10%" class="rprint_total">{total_retiros}</th>
      <th colspan="4" class="print">&nbsp;</th>
    </tr>
    <!-- START BLOCK : saldo_actual -->
	<tr>
      <th class="print">Saldo Actual Libros</th>
      <th colspan="2" class="print_total">{saldo_actual}</th>
      <th class="print">Saldo Actual Bancos </th>
      <th class="print_total">{saldo_actual_bancos}</th>
      <th class="print">Diferencia</th>
      <th class="print_total">{diferencia}</th>
    </tr>
	<!-- END BLOCK : saldo_actual -->
  </table>
  <!-- START BLOCK : gran_total -->
  <br>
  <table align="center" class="print">
  <tr>
    <th class="print" scope="col">Dep&oacute;sitos</th>
    <th class="print" scope="col">Retiros</th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{depositos}</font></th>
    <th class="print"><font size="+1">{retiros}</font></th>
  </tr>
  </table>
  <!-- END BLOCK : gran_total -->
  <!-- END BLOCK : cia -->
<!-- END BLOCK : listado -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.print();
		self.close();
	}
	
	window.onload = cerrar();
</script>
</body>
</html>
