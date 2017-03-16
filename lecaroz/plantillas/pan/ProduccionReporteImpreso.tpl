<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de producci&oacute;n</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="jscripts/bal/GastosTalleresReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte_diario -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de producci&oacute;n
		del día {fecha}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : d_cia -->
	<tr>
		<th colspan="7" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<!-- START BLOCK : d_turno -->
	<tr>
		<th colspan="7" align="left" class="print bold font10">{turno}</th>
	</tr>
	<tr>
		<th class="print">Producto</th>
		<th class="print">Bultos</th>
		<th class="print">Piezas</th>
		<th class="print">Precio<br />
		raya</th>
		<th class="print">Importe<br />
		raya</th>
		<th class="print">Precio<br />
		venta</th>
		<th class="print">Producción</th>
	</tr>
	<!-- START BLOCK : d_row -->
	<tr>
		<td class="print">{cod} {producto}</td>
		<td align="right" class="print green">{bultos}</td>
		<td align="right" class="print green">{piezas}</td>
		<td align="right" class="print red">{precio_raya}</td>
		<td align="right" class="print red">{imp_raya}</td>
		<td align="right" class="print blue">{precio_venta}</td>
		<td align="right" class="print blue">{imp_produccion}</td>
	</tr>
	<!-- END BLOCK : d_row -->
	<tr>
		<th align="right" class="print font10">Raya ganada</th>
		<th align="right" class="print font10"><span class="green">{bultos}</span></th>
		<th align="right" class="print font10"><span class="green">{piezas}</span></th>
		<th align="right" class="print font10">&nbsp;</th>
		<th align="right" class="print font10"><span class="red">{raya_ganada}</span></th>
		<th align="right" class="print font10">Producción</th>
		<th align="right" class="print font10"><span class="blue">{produccion}</span></th>
	</tr>
	<!-- START BLOCK : d_raya_pagada -->
	<tr>
		<th colspan="4" align="right" class="print font10">Raya pagada</th>
		<th align="right" class="print font10"><span class="red">{raya_pagada}</span></th>
		<th colspan="2" class="print">&nbsp;</th>
	</tr>
	<!-- END BLOCK : d_raya_pagada -->
	<tr>
		<td colspan="7" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : d_turno -->
	<tr>
		<td colspan="7" class="print">
			<!-- START BLOCK : d_totales -->
			<table align="center" class="print">
				<tr>
					<th class="print font12" scope="col">Raya ganada</th>
					<th class="print font12" scope="col">Raya pagada</th>
					<th class="print font12" scope="col">Producción</th>
				</tr>
				<tr>
					<td align="center" class="print font12 bold red">{raya_ganada}</td>
					<td align="center" class="print font12 bold red">{raya_pagada}</td>
					<td align="center" class="print font12 bold blue">{produccion}</td>
				</tr>
			</table>
			<!-- END BLOCK : d_totales -->
			<!-- START BLOCK : d_totales_small -->
			<table align="center" class="print">
				<tr>
					<th class="print font12" scope="col">Raya ganada</th>
					<th class="print font12" scope="col">Producción</th>
				</tr>
				<tr>
					<td align="center" class="print font12 bold red">{raya_ganada}</td>
					<td align="center" class="print font12 bold blue">{produccion}</td>
				</tr>
			</table>
			<!-- END BLOCK : d_totales_small -->
		</td>
	</tr>
	<tr>
		<td colspan="7" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : d_cia -->
</table>
{salto} 
<!-- END BLOCK : reporte_diario -->
<!-- START BLOCK : reporte_acumulado -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de producci&oacute;n
		del periodo {fecha1} al {fecha2}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : a_cia -->
	<tr>
		<th colspan="5" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<!-- START BLOCK : a_turno -->
	<tr>
		<th colspan="5" align="left" class="print bold font10">{turno}</th>
	</tr>
	<tr>
		<th class="print">Producto</th>
		<th class="print">Bultos</th>
		<th class="print">Piezas</th>
		<th class="print">Importe<br />
		raya</th>
		<th class="print">Producción</th>
	</tr>
	<!-- START BLOCK : a_row -->
	<tr>
		<td class="print">{cod} {producto}</td>
		<td align="right" class="print green">{bultos}</td>
		<td align="right" class="print green">{piezas}</td>
		<td align="right" class="print red">{imp_raya}</td>
		<td align="right" class="print blue">{imp_produccion}</td>
	</tr>
	<!-- END BLOCK : a_row -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<th align="right" class="print font10"><span class="green">{bultos}</span></th>
		<th align="right" class="print font10"><span class="green">{piezas}</span></th>
		<th align="right" class="print font10"><span class="red">{raya_ganada}</span></th>
		<th align="right" class="print font10"><span class="blue">{produccion}</span></th>
	</tr>
	<!-- START BLOCK : a_raya_pagada -->
	<tr>
		<th colspan="3" align="right" class="print font10">Raya pagada</th>
		<th align="right" class="print font10"><span class="red">{raya_pagada}</span></th>
		<th class="print">&nbsp;</th>
	</tr>
	<!-- END BLOCK : a_raya_pagada -->
	<tr>
		<td colspan="5" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : a_turno -->
	<tr>
		<td colspan="5" class="print">
			<!-- START BLOCK : a_totales -->
			<table align="center" class="print">
				<tr>
					<th class="print font12" scope="col">Raya ganada</th>
					<th class="print font12" scope="col">Raya pagada</th>
					<th class="print font12" scope="col">Producción</th>
				</tr>
				<tr>
					<td align="center" class="print font12 bold red">{raya_ganada}</td>
					<td align="center" class="print font12 bold red">{raya_pagada}</td>
					<td align="center" class="print font12 bold blue">{produccion}</td>
				</tr>
			</table>
			<!-- END BLOCK : a_totales -->
			<!-- START BLOCK : a_totales_small -->
			<table align="center" class="print">
				<tr>
					<th class="print font12" scope="col">Raya ganada</th>
					<th class="print font12" scope="col">Producción</th>
				</tr>
				<tr>
					<td align="center" class="print font12 bold red">{raya_ganada}</td>
					<td align="center" class="print font12 bold blue">{produccion}</td>
				</tr>
			</table>
			<!-- END BLOCK : a_totales_small -->
		</td>
	</tr>
	<tr>
		<td colspan="5" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : a_cia -->
</table>
{salto} 
<!-- END BLOCK : reporte_acumulado -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
