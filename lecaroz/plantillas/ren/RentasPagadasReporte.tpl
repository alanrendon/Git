<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Rentas</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/ren/RentasPagadasReporte.js"></script>
<style type="text/css">
.vencido {
	background-color: #FF8000;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de rentas pagadas {anio1} - {anio2}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="21" align="left" class="print font10" scope="col">{arrendador} {nombre_arrendador}</th>
	</tr>
	<tr>
		<th rowspan="2" class="print">Arrendatario</th>
		<th rowspan="2" class="print">Giro</th>
		<th rowspan="2" class="print">Término</th>
		<th colspan="6" class="print">{anio1}</th>
		<th colspan="12" class="print">{anio2}</th>
	</tr>
	<tr>
		<th class="print">Jul</th>
		<th class="print">Ago</th>
		<th class="print">Sep</th>
		<th class="print">Oct</th>
		<th class="print">Nov</th>
		<th class="print">Dic</th>
		<th class="print">Ene</th>
		<th class="print">Feb</th>
		<th class="print">Mar</th>
		<th class="print">Abr</th>
		<th class="print">May</th>
		<th class="print">Jun</th>
		<th class="print">Jul</th>
		<th class="print">Ago</th>
		<th class="print">Sep</th>
		<th class="print">Oct</th>
		<th class="print">Nov</th>
		<th class="print">Dic</th>
	</tr>
	<!-- START BLOCK : arrendatario -->
	<tr>
		<td nowrap="nowrap" class="print"><a title="{info}" class="enlace black" id="idarrendador">{arrendatario} {nombre_arrendatario}</a></td>
		<td nowrap="nowrap" class="print">{giro}</td>
		<td align="center" nowrap="nowrap" class="print{vencido}">{fecha_termino}</td>
		<td align="center" class="print">{0}</td>
		<td align="center" class="print">{1}</td>
		<td align="center" class="print">{2}</td>
		<td align="center" class="print">{3}</td>
		<td align="center" class="print">{4}</td>
		<td align="center" class="print">{5}</td>
		<td align="center" class="print">{6}</td>
		<td align="center" class="print">{7}</td>
		<td align="center" class="print">{8}</td>
		<td align="center" class="print">{9}</td>
		<td align="center" class="print">{10}</td>
		<td align="center" class="print">{11}</td>
		<td align="center" class="print">{12}</td>
		<td align="center" class="print">{13}</td>
		<td align="center" class="print">{14}</td>
		<td align="center" class="print">{15}</td>
		<td align="center" class="print">{16}</td>
		<td align="center" class="print">{17}</td>
	</tr>
	<!-- END BLOCK : arrendatario -->
	<tr>
		<td colspan="21" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : arrendador -->
</table>
<br />
<table align="center" class="print">
	<tr>
		<td align="center" class="print">&nbsp;</td>
		<td class="print">Pendiente</td>
	</tr>
	<tr>
		<td align="center" class="print"><span class="purple bold">VA</span></td>
		<td class="print">Vacío</td>
	</tr>
	<tr>
		<td align="center" class="print"><span class="orange bold">DG</span></td>
		<td class="print">Días de gracia</td>
	</tr>
	<tr>
		<td align="center" class="print"><img src="/lecaroz/imagenes/bloque_blanco_rojo.png" width="24" height="16" /></td>
		<td class="print">Vencimiento de contrato</td>
	</tr>
	<tr>
		<td align="center" class="print"><img src="/lecaroz/imagenes/bloque_negro.png" width="24" height="16" /></td>
		<td class="print">Renta pagada</td>
	</tr>
	<tr>
		<td align="center" class="print"><img src="/lecaroz/imagenes/bloque_azul.png" width="24" height="16" /></td>
		<td class="print">Renta pagada (sin conciliar)</td>
	</tr>
	<tr>
		<td align="center" class="print"><img src="/lecaroz/imagenes/bloque_verde.png" width="24" height="16" /></td>
		<td class="print">Renta pagada (estado impuesto)</td>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
