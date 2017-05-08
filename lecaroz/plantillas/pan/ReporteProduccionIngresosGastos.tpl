<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Reporte de Producci&oacute;n, Ingresos y Gastos</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ReporteProduccionIngresosGastos.js"></script>

<style type="text/css" media="screen">
.Reporte {
	height: auto;
}

table {
	border-collapse: collapse;
}

th {
	font-size: 10pt;
	border: solid 1px #000;
	background-color: #73A8B7;
}

td {
	font-size: 10pt;
	border: solid 1px #000;
}

td.bold {
	font-weight: bold;
}

td.blue {
	color: #00C;
}

td.green {
	color: #060;
}

td.red {
	color: #C00;
}

th.blue {
	color: #00C;
}

th.green {
	color: #060;
}

th.red {
	color: #C00;
}
</style>

<style type="text/css" media="print">
.Reporte {
	height: 130mm;
}

table {
	border-collapse: collapse;
}

th {
	font-size: 7pt;
}

td {
	font-size: 7pt;
}

td.bold {
	font-weight: normal;
}

td.blue {
	color: #000;
}

td.green {
	color: #000;
}

td.red {
	color: #000;
}

th.blue {
	color: #000;
}

th.green {
	color: #000;
}

th.red {
	color: #000;
}
</style>

</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">REPORTE DE PRODUCCI&Oacute;N, INGRESOS Y GASTOS AL  {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center">
      <tr>
        <th colspan="2" scope="col">D&iacute;a</th>
        <th scope="col">Producci&oacute;n</th>
        <th scope="col">Venta<br />
        Puerta</th>
        <th scope="col">Abonos</th>
        <th scope="col">Otros</th>
        <th scope="col">Ingresos</th>
        <th scope="col">Raya</th>
        <th scope="col">Sueldo<br />
        Empleados</th>
        <th scope="col">Sueldo<br />
          Encargado</th>
        <th scope="col">Panaderos</th>
        <th scope="col">Gastos</th>
        <th scope="col">Efectivo</th>
        <th scope="col">Clientes</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr>
        <td>{dial}</td>
        <td> {dian}</td>
        <td align="right" class="green">{produccion}</td>
        <td align="right" class="blue">{venta_puerta}</td>
        <td align="right" class="blue">{abono}</td>
        <td align="right" class="blue">{otros}</td>
        <td align="right" class="blue">{ingresos}</td>
        <td align="right" class="red">{raya}</td>
        <td align="right" class="red">{sueldo_empleados}</td>
        <td align="right" class="red">{sueldo_encargado}</td>
        <td align="right" class="red">{panaderos}</td>
        <td align="right" class="red">{gastos}</td>
        <td align="right" class="green">{efectivo}</td>
        <td align="right">{clientes}</td>
	  </tr>
	  <!-- END BLOCK : row -->
      <tr>
        <th colspan="2">&nbsp;</th>
        <th align="right" class="green">{produccion}</th>
        <th align="right" class="blue">{venta_puerta}</th>
        <th align="right" class="blue">{abono}</th>
        <th align="right" class="blue">{otros}</th>
        <th align="right" class="blue">{ingresos}</th>
        <th align="right" class="red">{raya}</th>
        <th align="right" class="red">{sueldo_empleados}</th>
        <th align="right" class="red">{sueldo_encargado}</th>
        <th align="right" class="red">{panaderos}</th>
        <th align="right" class="red">{gastos}</th>
        <th align="right" class="green">{efectivo}</th>
        <th align="right">{clientes}</th>
      </tr>
      <tr>
        <th colspan="2">&nbsp;</th>
        <th align="right" class="green">{p_produccion}</th>
        <th align="right" class="blue">{p_venta_puerta}</th>
        <th align="right" class="blue">{p_abono}</th>
        <th align="right" class="blue">{p_otros}</th>
        <th align="right" class="blue">{p_ingresos}</th>
        <th align="right" class="red">{p_raya}</th>
        <th align="right" class="red">{p_sueldo_empleados}</th>
        <th align="right" class="red">{p_sueldo_encargado}</th>
        <th align="right" class="red">{p_panaderos}</th>
        <th align="right" class="red">{p_gastos}</th>
        <th align="right" class="green">{p_efectivo}</th>
        <th align="right">{p_clientes}</th>
      </tr>
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
