<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Rendimi&iexcl;entos de Harina</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ConsumosDeMasReporte.js"></script>

<style>
.NombreCia {
	font-weight: bold;
	margin-top: 10px;
}

.NombreReporte {
	font-weight: bold;
	margin-bottom: 10px;
}

.bgWhite {
	background-color: #FFF;
}

.bgGray {
	background-color: #CCC;
}
td.blue {	color: #00C;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte1">
	<div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
	<div class="NombreReporte" align="center">CONSUMOS DE MAS AL {dia}  DE {mes} DE {anio} </div>
	<div class="Datos">
		<table width="99%" align="center" class="print">
			<tr>
				<th rowspan="2" class="print" scope="col">Producto</th>
				<th colspan="4" class="print" scope="col">Frances de d&iacute;a</th>
				<th colspan="4" class="print" scope="col">Frances de noche</th>
				<th colspan="4" class="print" scope="col">Bizcochero</th>
				<th colspan="4" class="print" scope="col">Repostero</th>
				<th colspan="4" class="print" scope="col">Piconero</th>
			</tr>
			<tr>
				<th class="print">Aut.</th>
				<th class="print">Con.</th>
				<th class="print">Costo</th>
				<th class="print">Exc.</th>
				<th class="print">Aut.</th>
				<th class="print">Con.</th>
				<th class="print">Costo</th>
				<th class="print">Exc.</th>
				<th class="print">Aut.</th>
				<th class="print">Con.</th>
				<th class="print">Costo</th>
				<th class="print">Exc.</th>
				<th class="print">Aut.</th>
				<th class="print">Con.</th>
				<th class="print">Costo</th>
				<th class="print">Exc.</th>
				<th class="print">Aut.</th>
				<th class="print">Con.</th>
				<th class="print">Costo</th>
				<th class="print">Exc.</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr id="row">
				<td class="print">{codmp} {nombre_mp}</td>
				<td align="right" class="print green">{aut1}</td>
				<td align="right" class="print blue">{con1}</td>
				<td align="right" class="print blue">{costo1}</td>
				<td align="right" class="print red">{dif1}</td>
				<td align="right" class="print green">{aut2}</td>
				<td align="right" class="print blue">{con2}</td>
				<td align="right" class="print blue">{costo2}</td>
				<td align="right" class="print red">{dif2}</td>
				<td align="right" class="print green">{aut3}</td>
				<td align="right" class="print blue">{con3}</td>
				<td align="right" class="print blue">{costo3}</td>
				<td align="right" class="print red">{dif3}</td>
				<td align="right" class="print green">{aut4}</td>
				<td align="right" class="print blue">{con4}</td>
				<td align="right" class="print blue">{costo4}</td>
				<td align="right" class="print red">{dif4}</td>
				<td align="right" class="print green">{aut8}</td>
				<td align="right" class="print blue">{con8}</td>
				<td align="right" class="print blue">{costo8}</td>
				<td align="right" class="print red">{dif8}</td>
			</tr>
			<!-- END BLOCK : row -->
		</table>
	</div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
