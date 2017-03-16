<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Variaci&oacute;n Anual de Precios de Compra</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ped/VariacionAnualPreciosCompraReporte.js"></script>

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

</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center" style="margin-bottom:20px;">Variaci&oacute;n Anual de Precios de Compra {anio} </div>
  <div class="Datos">
    <table align="center" class="print">
      <tr>
        <th class="print" scope="col">Producto</th>
        <th class="print" scope="col">Precio<br />
        Inicial</th>
        <th class="print" scope="col">Ene</th>
        <th class="print" scope="col">Feb</th>
        <th class="print" scope="col">Mar</th>
        <th class="print" scope="col">Abr</th>
        <th class="print" scope="col">May</th>
        <th class="print" scope="col">Jun</th>
        <th class="print" scope="col">Jul</th>
        <th class="print" scope="col">Ago</th>
        <th class="print" scope="col">Sep</th>
        <th class="print" scope="col">Oct</th>
        <th class="print" scope="col">Nov</th>
        <th class="print" scope="col">Dic</th>
        <th class="print" scope="col">Precio<br />
        Final</th>
      </tr>
      <!-- START BLOCK : producto -->
	  <tr id="row">
        <td class="print">{codmp} {nombre_mp} </td>
        <td align="right" class="print bold"><a class="info" title="{info_ini}">{precio_ini}</a></td>
        <td align="right" class="print"><a class="info" title="{info_1}">{var_1}{precio_1}</a></td>
        <td align="right" class="print"><a class="info" title="{info_2}">{var_2}{precio_2}</a></td>
        <td align="right" class="print"><a class="info" title="{info_3}">{var_3}{precio_3}</a></td>
        <td align="right" class="print"><a class="info" title="{info_4}">{var_4}{precio_4}</a></td>
        <td align="right" class="print"><a class="info" title="{info_5}">{var_5}{precio_5}</a></td>
        <td align="right" class="print"><a class="info" title="{info_6}">{var_6}{precio_6}</a></td>
        <td align="right" class="print"><a class="info" title="{info_7}">{var_7}{precio_7}</a></td>
        <td align="right" class="print"><a class="info" title="{info_8}">{var_8}{precio_8}</a></td>
        <td align="right" class="print"><a class="info" title="{info_9}">{var_9}{precio_9}</a></td>
        <td align="right" class="print"><a class="info" title="{info_10}">{var_10}{precio_10}</a></td>
        <td align="right" class="print"><a class="info" title="{info_11}">{var_11}{precio_11}</a></td>
        <td align="right" class="print"><a class="info" title="{info_12}">{var_12}{precio_12}</a></td>
        <td align="right" class="print bold">{var}{precio_fin}</td>
      </tr>
	  <!-- END BLOCK : producto -->
    </table>
  </div>
</div>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
