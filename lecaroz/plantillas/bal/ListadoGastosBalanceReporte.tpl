<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Facturas Electr&oacute;nicas</title>
<link href="/lecaroz/styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturasElectronicasConsultaReporte.js"></script>
</head>

<body>
<!-- START BLOCK : hoja -->
<div id="hoja4" class="hoja_oficio">
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">{nombre_cia}{leyenda}<br />
	Gastos del {fecha1} al {fecha2}</div>
  </div>
  <div class="Seccion">
    <table align="center" class="Border" style="border-collapse:collapse;empty-cells:show;table-layout:fixed;" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
      <tr>
        <td class="Border" style="width:7mm;">&nbsp;</td>
        <th class="Border" style="width:35mm;">Gasto</th>
        <th class="Border" style="width:50mm;">Beneficiario</th>
        <th class="Border" style="width:60mm;">Concepto</th>
        <th class="Border" style="width:15mm;">Fecha</th>
        <th class="Border" style="width:10mm;">Cheque</th>
        <th class="Border" style="width:18mm;">Importe</th>
      </tr>
	  <!-- START BLOCK : gasto_cheque -->
      <!-- START BLOCK : row_cheque -->
	  <tr>
        <td valign="middle" class="Border" style="height:10mm;" onclick="MarcaCheque(this,{num_cia})"><input name="g{num_cia}" type="checkbox" class="DisplayNone" id="g{num_cia}" value="{importe_checkbox}" />{cod}</td>
        <td valign="middle" class="Border">{desc}</td>
        <td valign="middle" class="Border">{a_nombre}</td>
        <td valign="middle" class="Border">{facturas}{concepto}</td>
        <td align="center" valign="middle" class="Border">{fecha}</td>
        <td align="center" valign="middle" class="Border">{folio}</td>
        <td align="right" valign="middle" class="Border">{importe}</td>
      </tr>
	  <!-- END BLOCK : row_cheque -->
      <!-- START BLOCK : cheque_subtotal -->
	  <tr>
        <th colspan="6" align="right" valign="middle" class="Border">Subtotal</th>
        <th align="right" valign="middle" class="Border">{subtotal}</th>
      </tr>
	  <!-- END BLOCK : cheque_subtotal -->
      <tr>
        <td colspan="7" valign="middle" class="Border">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : gasto_cheque -->
      <!-- START BLOCK : total_cheques-->
	  <tr>
        <td colspan="6" align="right" valign="middle" class="Border Font10 Bold">Total</td>
        <td align="right" valign="middle" class="Border Font10 Bold">{total}</td>
      </tr>
	  <!-- END BLOCK : total_cheques -->
    </table>
  </div>
</div>
<div class="page_break"></div>
<!-- END BLOCK : hoja -->
<!-- START BLOCK : hoja_blanca -->
<div class="hoja_blanca_oficio">
  &nbsp;
</div>
<div class="page_break"></div>
<!-- END BLOCK : hoja_blanca -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
