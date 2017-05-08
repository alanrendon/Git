<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<!-- START BLOCK : centrado -->
<br>
<br>
<!-- END BLOCK : centrado -->


<table width="80%" class="print">
  <tr class="print">
    <th colspan="7" class="print"><font size="2"><strong>{num_cia}&nbsp;{nom_compania}</strong></font></th>
  </tr>
  <tr class="print">
    <th class="print" colspan="2">Proveedor</th>
    <th class="print">Fecha movimiento </th>
    <th class="print">N&uacute;mero de Factura </th>
    <th class="print">V</th>
    <th class="print">CFDI</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : rows1 -->
  <tr class="print">
    <td class="print">{num_proveedor}</td>
	<td class="vprint">{nom_proveedor}</td>
    <td class="print">{fecha_mov}</td>
    <td class="print">{num_fact}</td>
    <td class="print">{v}</td>
    <td class="print">{cfdi}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  <tr class="print">
    <th class="rprint" colspan="6"><font size="2">TOTAL COMPA&Ntilde;&Iacute;A</font></th>
    <th class="rprint"><font size="2">{total_cia}</font></th>
  </tr>
</table>
<p>
  <input type="button" value="Cerrar" class="boton" onClick="self.close()">
</p></td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : compania -->


