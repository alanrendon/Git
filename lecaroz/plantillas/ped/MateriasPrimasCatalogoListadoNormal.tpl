<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Rentas</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ped/MateriasPrimasCatalogoListadoNormal.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de materias primas</td>
	</tr>
</table>
<br />
<table width="90%" align="center" class="print">
	<tr>
		<th align="center" class="print" scope="col">#</th>
		<th align="center" class="print" scope="col">Producto</th>
		<th align="center" class="print" scope="col">Unidad de<br />
		consumo</th>
		<th align="center" class="print" scope="col">Categoría</th>
		<th align="center" class="print" scope="col">Controlada</th>
		<th align="center" class="print" scope="col">Pedido<br />
			automático</th>
		<th align="center" class="print" scope="col">Sin <br />
		existencia</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td align="right" class="print">{codmp}</td>
		<td class="print">{nombre_mp}</td>
		<td class="print">{unidad}</td>
		<td class="print {categoria_color}">{categoria}</td>
		<td align="center" class="print green">{controlada}</td>
		<td align="center" class="print green">{pedido}</td>
		<td align="center" class="print green">{sin_existencia}</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
 {salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
