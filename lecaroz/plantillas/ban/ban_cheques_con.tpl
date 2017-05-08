<link href="/styles/prints.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/prints.css" rel="stylesheet" type="text/css">
<link href="/styles/print.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">LISTADO DE CHEQUES EMITIDOS </p>
<p class="print_encabezado">AL {fecha}</p>

<table border="1" class="print">
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="print">
	    <th class="print">Folio</th>
	    <th class="print">Cuenta</th>
	    <th class="print" colspan="2">Compa&ntilde;&iacute;a</th>
	    <th class="print" colspan="2">Proveedor</th>

	    <th class="print">Fecha</th>
	    <th class="print">Importe</th>
	  </tr>
<!-- START BLOCK : ordena -->

<!-- START BLOCK : total_cia -->
	<tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="print">
	    <td class="rprint" colspan="7">Total compañía</td>
	    <th class="rprint">{total_cia}</th>
    </tr>
<!-- END BLOCK : total_cia -->

	<!-- START BLOCK : rows -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="print">
		<td class="vprint">{folio}</td>	
		<td class="print">{cuenta}</td>
		<td class="print">{num_cia}</td>
		<td class="vprint">{nom_cia}</td>
		<td class="rprint">{num_proveedor}</td>
		<td class="vprint">{nom_proveedor}</td>
		<td class="print">{fecha}</td>
		<th class="rprint">
		<!-- START BLOCK : cheque_ok -->
		{importe}
		<!-- END BLOCK : cheque_ok -->
		<!-- START BLOCK : cheque_error -->
		<font color="#FFFF00">{importe}</font>
		<!-- END BLOCK : cheque_error -->
		
		</th>
	  </tr>
<!-- END BLOCK : rows -->


<!-- END BLOCK : ordena -->
	<tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="print">
	    <td class="rprint" colspan="7">TOTAL</td>
	    <th class="rprint">{total}</th>
    </tr>

	</table>
</td>
</tr>
</table>