<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gastos de Mec&aacute;nicos</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
</head>

<body>
<!-- START BLOCK : result -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
  <tr>
    <td align="center">Oficinas Administrativas Mollendo </td>
  </tr>
  <tr>
    <td align="center">{admin}Gastos de Mec&aacute;nicos <br />
    del {fecha1} al {fecha2}</td>
  </tr>
</table>
<br />
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="8" align="left" class="print font10" scope="col">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Conciliado</th>
    <th class="print">Banco</th>
    <th class="print">Folio</th>
    <th class="print">Proveedor</th>
    <th class="print">Concepto</th>
    <th class="print">Facturas</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td align="center" class="print">{fecha}</td>
    <td align="center" class="print">{conciliado}</td>
    <td class="print">{banco}</td>
    <td align="right" class="print">{folio}</td>
    <td class="print">{num_pro} {nombre_pro} </td>
    <td class="print">{concepto}</td>
    <td class="print">{facturas}</td>
    <td align="right" class="print">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="7" align="right" class="print font10">Total</th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <tr>
    <td colspan="8" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="7" align="right" class="print font10">Total General</th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <!-- END BLOCK : total -->
</table>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
{salto}
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	$('cerrar').addEvent('click', function() {
		self.close();
	});
});
//-->
</script>
<!-- END BLOCK : result -->
<!-- START BLOCK : no_result -->
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	alert('No hay resultados');
	self.close();
});
//-->
</script>
<!-- END BLOCK : no_result -->
</body>
</html>
