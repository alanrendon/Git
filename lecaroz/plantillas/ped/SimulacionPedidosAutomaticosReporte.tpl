<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedidos</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ped/SimulacionPedidosAutomaticosReporte.js"></script>

<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}

.tip-title {
	font-weight: bold;
	font-size: 8pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}

.tip-text {
	font-weight: bold;
	font-size: 8pt;
	padding: 0 5px;
}
</style>

<!--<style type="text/css" media="print">
.Tip, .tip-title, .tip-text {
	display: none;
}
</style>-->

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="font14">{num_cia}</td>
    <td width="70%" align="center" class="font14">{nombre_cia}</td>
    <td width="15%" align="right" class="font14">{num_cia}</td>
  </tr>
  <tr>
    <td class="font8">&nbsp;</td>
    <td align="center">Pedidos al {dia} de {mes} de {anio}<br />
    (d&iacute;as pedidos: {dias})</td>
    <td align="right" class="font8">&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th class="print" scope="col">Unidad</th>
    <th class="print" scope="col">Consumo</th>
    <th class="print" scope="col">Inventario</th>
    <th class="print" scope="col">Pedido</th>
    <th class="print" scope="col">Entregar</th>
    <th class="print" scope="col">Diferencia</th>
    <th width="50" scope="col">&nbsp;</th>
    <th class="print" scope="col">Inventario<br />
    	estimado</th>
    <th class="print" scope="col">D&iacute;as<br />
    	consumo</th>
    <th scope="col">&nbsp;</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Tel&eacute;fono</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right" class="print">{cod}</td>
    <td class="print">{producto}</td>
    <td class="print">{unidad}</td>
    <td align="right" class="print red">{consumo}</td>
    <td align="right" class="print green">{inventario}</td>
    <td align="right" class="print blue">{pedido}</td>
    <td align="right" class="print orange">{pedido_pro}</td>
    <td align="right" class="print green">{diferencia}</td>
    <td align="right">&nbsp;</td>
    <td align="right" class="print">{estimado}</td>
    <td align="right" class="print{color_dias}">{dias_consumo}</td>
    <td class="{color_dias}">{checar}</td>
    <td class="print">{num_pro} {nombre_pro}</td>
    <td class="print">{telefono}</td>
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
