<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Reporte de Producci&oacute;n, Ingresos y Gastos</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print font10.css" rel="stylesheet" type="text/css" media="print font10" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print font10.css" rel="stylesheet" type="text/css" media="print font10" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ReporteProduccionIngresosGastos.js"></script>

<style>
.NombreCia {
	font-weight: bold;
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
</style>

</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">REPORTE DE PRODUCCI&Oacute;N, INGRESOS Y GASTOS AL  {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center" class="print">
      <tr>
        <th colspan="2" class="print font10" scope="col">D&iacute;a</th>
        <th class="print font10" scope="col">Producci&oacute;n</th>
        <th class="print font10" scope="col">Venta<br />
        Puerta</th>
        <th class="print font10" scope="col">Abonos</th>
        <th class="print font10" scope="col">Otros</th>
        <th class="print font10" scope="col">Ingresos</th>
        <th class="print font10" scope="col">Raya</th>
        <th class="print font10" scope="col">Sueldo<br />
        Empleados</th>
        <th class="print font10" scope="col">Sueldo<br />
        Encargado</th>
        <th class="print font10" scope="col">Panaderos</th>
        <th class="print font10" scope="col">Gastos</th>
        <th class="print font10" scope="col">Efectivo</th>
        <th class="print font10" scope="col">Clientes</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr>
        <td class="print font10">{dial}</td>
        <td class="print font10"> {dian}</td>
        <td align="right" class="print font10 green">{produccion}</td>
        <td align="right" class="print font10 blue">{venta_puerta}</td>
        <td align="right" class="print font10 blue">{abono}</td>
        <td align="right" class="print font10 blue">{otros}</td>
        <td align="right" class="print font10 blue">{ingresos}</td>
        <td align="right" class="print font10 red">{raya}</td>
        <td align="right" class="print font10 red">{sueldo_empleados}</td>
        <td align="right" class="print font10 red">{sueldo_encargado}</td>
        <td align="right" class="print font10 red">{panaderos}</td>
        <td align="right" class="print font10 red">{gastos}</td>
        <td align="right" class="print font10 green">{efectivo}</td>
        <td align="right" class="print font10">{clientes}</td>
	  </tr>
	  <!-- END BLOCK : row -->
      <tr>
        <th colspan="2" class="print font10">&nbsp;</th>
        <th align="right" class="print font10 green">{produccion}</th>
        <th align="right" class="print font10 blue">{venta_puerta}</th>
        <th align="right" class="print font10 blue">{abono}</th>
        <th align="right" class="print font10 blue">{otros}</th>
        <th align="right" class="print font10 blue">{ingresos}</th>
        <th align="right" class="print font10 red">{raya}</th>
        <th align="right" class="print font10 red">{sueldo_empleados}</th>
        <th align="right" class="print font10 red">{sueldo_encargado}</th>
        <th align="right" class="print font10 red">{panaderos}</th>
        <th align="right" class="print font10 red">{gastos}</th>
        <th align="right" class="print font10 green">{efectivo}</th>
        <th align="right" class="print font10">{clientes}</th>
      </tr>
      <tr>
        <th colspan="2" class="print font10">&nbsp;</th>
        <th align="right" class="print font10 green">{p_produccion}</th>
        <th align="right" class="print font10 blue">{p_venta_puerta}</th>
        <th align="right" class="print font10 blue">{p_abono}</th>
        <th align="right" class="print font10 blue">{p_otros}</th>
        <th align="right" class="print font10 blue">{p_ingresos}</th>
        <th align="right" class="print font10 red">{p_raya}</th>
        <th align="right" class="print font10 red">{p_sueldo_empleados}</th>
        <th align="right" class="print font10 red">{p_sueldo_encargado}</th>
        <th align="right" class="print font10 red">{p_panaderos}</th>
        <th align="right" class="print font10 red">{p_gastos}</th>
        <th align="right" class="print font10 green">{p_efectivo}</th>
        <th align="right" class="print font10">{p_clientes}</th>
      </tr>
    </table>
  </div>
</div>
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
