<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Reporte de Consumos</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/bal/ReporteConsumosHoja.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
  <tr>
    <td>{num_cia}</td>
    <td align="center">{nombre}<br />
      {nombre_corto}</td>
    <td align="right">{num_cia}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center"><p>Reporte de Consumos &#8212; {dia} de {mes} de {anyo} </p>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
<br />
<table width="98%" align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Materia Prima </th>
    <th class="print" scope="col">Precio<br />
    Unitario</th>
    <th class="print" scope="col">FD</th>
    <th class="print" scope="col">FN</th>
    <th class="print" scope="col">BD</th>
    <th class="print" scope="col">REP</th>
    <th class="print" scope="col">PIC</th>
    <th class="print" scope="col">GEL</th>
    <th class="print" scope="col">DES</th>
    <th class="print" scope="col">Consumo<br />
    por Producto </th>
  </tr>
  <!-- START BLOCK : producto_controlado -->
  <tr id="row">
    <td class="print">{codmp}</td>
    <td class="print">{nombre}</td>
    <td align="right" class="print">{precio}</td>
    <td align="right" class="print" style="color:#00C;">{1}</td>
    <td align="right" class="print" style="color:#009;">{2}</td>
    <td align="right" class="print" style="color:#066;">{3}</td>
    <td align="right" class="print" style="color:#900;">{4}</td>
    <td align="right" class="print" style="color:#C30;">{8}</td>
    <td align="right" class="print" style="color:#630;">{9}</td>
    <td align="right" class="print" style="color:#C09;">{10}</td>
    <td align="right" class="print bold">{consumo}</td>
  </tr>
  <!-- END BLOCK : producto_controlado -->
  <tr>
    <th colspan="3" align="left" class="print">Consumo por Turno (Controlados) </th>
    <th align="right" class="print">{c1}</th>
    <th align="right" class="print">{c2}</th>
    <th align="right" class="print">{c3}</th>
    <th align="right" class="print">{c4}</th>
    <th align="right" class="print">{c8}</th>
    <th align="right" class="print">{c9}</th>
    <th align="right" class="print">{c10}</th>
    <th align="right" class="print">{ctotal}</th>
  </tr>
  <!-- START BLOCK : no_controlados -->
  <tr>
    <td colspan="11" class="print">&nbsp;</td>
  </tr>
  <!-- START BLOCK : producto_no_controlado -->
  <tr id="row">
    <td class="print">{codmp}</td>
    <td class="print">{nombre}</td>
    <td align="right" class="print">{precio}</td>
    <td align="right" class="print" style="color:#00C;">{1}</td>
    <td align="right" class="print" style="color:#009;">{2}</td>
    <td align="right" class="print" style="color:#066;">{3}</td>
    <td align="right" class="print" style="color:#900;">{4}</td>
    <td align="right" class="print" style="color:#C30;">{8}</td>
    <td align="right" class="print" style="color:#630;">{9}</td>
    <td align="right" class="print" style="color:#C09;">{10}</td>
    <td align="right" class="print bold">{consumo}</td>
  </tr>
  <!-- END BLOCK : producto_no_controlado -->
  <tr>
    <th colspan="3" align="left" class="print">Consumo por Turno (No controlados) </th>
    <th align="right" class="print">{c1}</th>
    <th align="right" class="print">{c2}</th>
    <th align="right" class="print">{c3}</th>
    <th align="right" class="print">{c4}</th>
    <th align="right" class="print">{c8}</th>
    <th align="right" class="print">{c9}</th>
    <th align="right" class="print">{c10}</th>
    <th align="right" class="print">{ctotal}</th>
  </tr>
  <!-- END BLOCK : no_controlados -->
  <tr>
    <td colspan="11" class="print">&nbsp;</td>
  </tr>
  <tr id="row">
    <!-- <th colspan="3" align="left" class="print">+ Mercancias, leche y vino </th> -->
    <th colspan="3" align="left" class="print">+ Mercancias y vino </th>
    <td align="right" class="print" style="color:#00C;">{mercancias1}</td>
    <td align="right" class="print" style="color:#009;">{mercancias2}</td>
    <td align="right" class="print" style="color:#066;">{mercancias3}</td>
    <td align="right" class="print" style="color:#900;">{mercancias4}</td>
    <td align="right" class="print" style="color:#C30;">{mercancias8}</td>
    <td align="right" class="print" style="color:#630;">{mercancias9}</td>
    <td align="right" class="print" style="color:#C09;">{mercancias10}</td>
    <td align="right" class="print bold">{mercancias}</td>
  </tr>
  <tr id="row">
    <th colspan="3" align="left" class="print">Consumo Total </th>
    <td align="right" class="print" style="color:#00C;">{consumo1}</td>
    <td align="right" class="print" style="color:#009;">{consumo2}</td>
    <td align="right" class="print" style="color:#066;">{consumo3}</td>
    <td align="right" class="print" style="color:#900;">{consumo4}</td>
    <td align="right" class="print" style="color:#C30;">{consumo8}</td>
    <td align="right" class="print" style="color:#630;">{consumo9}</td>
    <td align="right" class="print" style="color:#C09;">{consumo10}</td>
    <td align="right" class="print bold">{consumos}</td>
  </tr>
  <tr id="row">
    <th colspan="3" align="left" class="print">Producci&oacute;n</th>
    <td align="right" class="print" style="color:#00C;">{produccion1}</td>
    <td align="right" class="print" style="color:#009;">{produccion2}</td>
    <td align="right" class="print" style="color:#066;">{produccion3}</td>
    <td align="right" class="print" style="color:#900;">{produccion4}</td>
    <td align="right" class="print" style="color:#C30;">{produccion8}</td>
    <td align="right" class="print" style="color:#630;">{produccion9}</td>
    <td align="right" class="print" style="color:#C09;">{produccion10}</td>
    <td align="right" class="print bold">{produccion}</td>
  </tr>
  <tr id="row">
    <th colspan="3" align="left" class="print">Consumo / Prod. </th>
    <td align="right" class="print" style="color:#00C;">{prom1}</td>
    <td align="right" class="print" style="color:#009;">{prom2}</td>
    <td align="right" class="print" style="color:#066;">{prom3}</td>
    <td align="right" class="print" style="color:#900;">{prom4}</td>
    <td align="right" class="print" style="color:#C30;">{prom8}</td>
    <td align="right" class="print" style="color:#630;">{prom9}</td>
    <td align="right" class="print" style="color:#C09;">{prom10}</td>
    <td align="right" class="print bold">{prom}</td>
  </tr>
  <tr>
    <th colspan="3" align="left" class="print">MP / Prod (Balance) </th>
    <td colspan="7" align="right" class="print">&nbsp;</td>
    <td align="right" class="print bold">{prom_bal}</td>
  </tr>
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
