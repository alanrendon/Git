<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Desglose de Gastos</title>
<style type="text/css">
<!--
body {
	margin-left: 0mm;
	margin-top: 0mm;
	margin-right: 0mm;
	margin-bottom: 0mm;
	font: 10pt Arial, Helvetica, sans-serif;
}

td {
	padding: 1px 8px;
}

.RowHeader {
	background-color: #999;
}

.RowData {
	background-color: #CCC;
}
-->
</style>
</head>

<body>
<div>
<table align="center" style="border-collapse:collapse;empty-cells:show;">
  <tr>
    <th class="RowHeader" scope="col">Fecha</th>
    <th class="RowHeader" scope="col">Concepto</th>
    <th class="RowHeader" scope="col">Importe</th>
    <th class="RowHeader" scope="col">Proveedor</th>
    <th class="RowHeader" scope="col">Facturas</th>
    <th class="RowHeader" scope="col">Banco</th>
    <th class="RowHeader" scope="col">Folio</th>
    <th class="RowHeader" scope="col">Conciliado</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr class="{RowData}">
    <td align="center">{fecha}</td>
    <td>{concepto}</td>
    <td align="right">{importe}</td>
    <td>{num_pro} {nombre}</td>
    <td>{facturas}</td>
    <td>{banco}</td>
    <td align="right">{folio}</td>
    <td align="center">{fecha_con}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="2" align="right" class="RowHeader">Total</th>
    <th align="right" class="RowHeader">{total}</th>
    <th colspan="5" align="right" class="RowHeader">&nbsp;</th>
    </tr>
</table>
<p align="center">
  <input name="Button" type="button" onclick="self.close()" value="Cerrar" />
</p>
</div>
</body>
</html>
