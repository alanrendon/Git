<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Efectivos</title>
<style type="text/css">
body {
	margin: 0mm;
	font: 6pt Arial, Helvetica, sans-serif;
}

.table {
	border-collapse: collapse;
	border: solid 1px #000;
}

.table th {
	border: solid 1px #000;
	padding-left: 1mm;
	padding-right: 1mm;
}

.table td {
	padding-left: 1mm;
	padding-right: 1mm;
	padding-top: 0mm;
	padding-bottom: 0mm;
}
</style>
</head>

<body>
<!-- START BLOCK : hoja -->
<table width="100%">
  <!-- START BLOCK : bloque -->
  <tr>
    <!-- START BLOCK : reporte -->
    <td width="34%" align="center" valign="top">
      <table width="95%" class="table">
        <tr>
          <th colspan="6" style="font-size:7pt;">{num_cia} {nombre}</th>
        </tr>
        <tr>
          <th>D&iacute;a</th>
          <th>Efectivo</th>
          <th>Dep&oacute;sito</th>
          <th>Mayoreo</th>
          <th>Oficina</th>
          <th>Diferencia</th>
          <!--<th>Total</th>-->
        </tr>
        <!-- START BLOCK : fila -->
        <tr>
          <td align="right">{dia}</td>
          <td align="right">{efectivo}</td>
          <td align="right">{deposito}</td>
          <td align="right">{mayoreo}</td>
          <td align="right">{oficina}</td>
          <td align="right"><strong>{diferencia}</strong></td>
          <!--<td align="right">{total}</td>-->
        </tr>
        <!-- END BLOCK : fila -->
        <tr>
          <th align="right" colspan="5">Tot.</th>
          <!--
          <th align="right">{efectivo}</th>
          <th align="right">{deposito}</th>
          <th align="right">{mayoreo}</th>
          <th align="right">{oficina}</th>
          -->
          <th align="right">{diferencia}</th>
          <!--<th align="right">{total}</th>-->
        </tr>
        <!--<tr>
          <td align="right">Prom.</td>
          <th align="right">{pefectivo}</th>
          <th align="right">{pdeposito}</th>
          <th align="right">{pmayoreo}</th>
          <th align="right">{poficina}</th>
          <th align="right">{pdiferencia}</th>
          <th align="right">{ptotal}</th>
        </tr>-->
      </table>
    </td>
    <!-- END BLOCK : reporte -->
    <!-- START BLOCK : vacio -->
    <td width="33%" align="center" valign="middle" style="color:#CCC;">VACIO</td>
    <!-- END BLOCK : vacio -->
  </tr>
  <!-- END BLOCK : bloque -->
</table>
{salto}
{blanco}
<!-- END BLOCK : hoja -->
</body>
</html>
