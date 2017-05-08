<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">ULTIMOS NUMEROS DE CHEQUES </p>
<p class="title">AL {fecha}</p>

<table border="1" class="tabla">
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <th class="tabla">Número y nombre de la compañía</th>
	    <th class="tabla">Folio</th>
	    </tr>
	<!-- START BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<td class="vtabla">{num_cia}&#8212;{nombre_cia} ({nombre_corto}) </td>	
		<th class="tabla">{folio}</th>
		</tr>
	<!-- END BLOCK : rows --> 
	</table>
</td>
</tr>
</table>