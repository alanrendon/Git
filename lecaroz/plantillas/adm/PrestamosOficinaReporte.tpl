<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Prestamos de Oficina </title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/adm/PrestamosOficinaReporte.js"></script>

<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}

.tip-title {
	font-weight: bold;
	font-size: 8pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}

.tip-text {
	font-weight: bold;
	font-size: 8pt;
	padding: 0 5px;
}
</style>

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="font8">&nbsp;</td>
    <td width="70%" align="center">Reporte de Prestamos de Oficina <br />
    {dia} de {mes} de {anio} </td>
    <td width="15%" align="right" class="font8">&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="7" align="left" class="print font12" scope="col">{num_cia} {nombre_cia} </th>
  </tr>
  <tr>
    <th class="print">Empleado</th>
    <th class="print">&Uacute;ltimo<br />
    prestamo</th>
    <th class="print">Saldo</th>
    <th class="print">Pagos</th>
    <th class="print">&Uacute;ltimo<br />
    pago</th>
    <th class="print">Importe<br />
    &Uacute;ltimo pago </th>
    <th class="print">D&iacute;as de<br />
      atraso</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td class="print">{num} {empleado} </td>
    <td align="center" class="print red">{ultimo_prestamo}</td>
    <td align="right" class="print red"><a class="detalle" title="{detalle}">{saldo}</a></td>
    <td align="right" class="print blue">{pagos}</td>
    <td align="center" class="print blue">{ultimo_pago}</td>
    <td align="right" class="print blue">{importe_ultimo_pago}</td>
    <td align="right" class="print red">{dias_atraso}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="2" align="right" class="print">Totales</th>
    <th align="right" class="print font12 red">{saldo}</th>
    <th align="right" class="print font12 blue">{pagos}</th>
    <th colspan="3" class="print">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="7" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
