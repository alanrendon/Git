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
<script type="text/javascript" src="jscripts/ban/DiferenciaInteresesImpuestosAnualReporte.js"></script>

</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte">
  <div class="NombreReporte" align="center" style="margin-bottom:20px;">Diferencia de Intereses e Impuestos {anio} </div>
  <div class="Datos">
    <table width="98%" align="center" class="print">
      <tr>
        <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
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
        <th class="print" scope="col">Total</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr id="row">
        <td class="print">{num_cia} {nombre_cia} </td>
        <td align="right" class="print{color1}">{1}</td>
        <td align="right" class="print{color2}">{2}</td>
        <td align="right" class="print{color3}">{3}</td>
        <td align="right" class="print{color4}">{4}</td>
        <td align="right" class="print{color5}">{5}</td>
        <td align="right" class="print{color6}">{6}</td>
        <td align="right" class="print{color7}">{7}</td>
        <td align="right" class="print{color8}">{8}</td>
        <td align="right" class="print{color9}">{9}</td>
        <td align="right" class="print{color10}">{10}</td>
        <td align="right" class="print{color11}">{11}</td>
        <td align="right" class="print{color12}">{12}</td>
        <td align="right" class="print bold">{total}</td>
      </tr>
		<!-- END BLOCK : row -->
		<!-- START BLOCK : totales -->
	  <tr>
	  	<th align="right" class="print">Totales</th>
	  	<th align="right" class="print">{1}</th>
	  	<th align="right" class="print">{2}</th>
	  	<th align="right" class="print">{3}</th>
	  	<th align="right" class="print">{4}</th>
	  	<th align="right" class="print">{5}</th>
	  	<th align="right" class="print">{6}</th>
	  	<th align="right" class="print">{7}</th>
	  	<th align="right" class="print">{8}</th>
	  	<th align="right" class="print">{9}</th>
	  	<th align="right" class="print">{10}</th>
	  	<th align="right" class="print">{11}</th>
	  	<th align="right" class="print">{12}</th>
	  	<th align="right" class="print">{total}</th>
  	</tr>
	  <!-- END BLOCK : totales -->
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
