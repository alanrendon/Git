<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de efectivos mensuales</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/ban/EfectivosReporteMensualNormal.js?v={rand}"></script>
<style id="screen-style" type="text/css" media="screen">
.hoja {
	width: 100%;
}

.reporte {
	float: left;
	margin: 10px 10px;
}

.tabla {
	width: 100%;
}

.celda {
	font-size: 12pt;
	border: 1px solid white;
}

.lcelda {
	border-left: 1px solid black;
}

.rcelda {
	border-right: 1px solid black;
}

.bcelda {
	border-bottom: 1px solid black;
}

.cabecera {
	font-size: 12pt;
}
</style>

<style id="print-style" type="text/css" media="print">
.hoja {
	width: 240mm;
	min-height: 200mm;
}

.reporte {
	width: 112mm;
	float: left;
	margin: 0 3mm;
}

.tabla {
	width: 100%;
}

.celda {
	border: 1px solid white;
}

.lcelda {
	border-left: 1px solid black;
}

.rcelda {
	border-right: 1px solid black;
}

.bcelda {
	border-bottom: 1px solid black;
}

.cabecera {
}
</style>
</head>

<body>
<!-- START BLOCK : hoja -->
<div class="hoja">
	<!-- START BLOCK : reporte -->
	<div class="reporte">
		<table align="center" class="print tabla">
			<tr>
				<th colspan="8" align="center" class="print font12" scope="col"><span style="float:left;">{num_cia}</span><span style="float:right;">{num_cia}</span><br />
					{nombre}<br />
					{alias}<br />
					<span style="float:right;" class="font8">{periodo}</span></th>
			</tr>
			<tr>
				<th class="print cabecera">Día</th>
				<th class="print cabecera">Efectivo</th>
				<th class="print cabecera">Depósito</th>
				<th class="print cabecera">Mayoreo</th>
				<th class="print cabecera">Oficina</th>
				<th class="print cabecera">Faltantes</th>
				<th class="print cabecera">Diferencia</th>
				<th class="print cabecera">Total<br />
					Depósitos</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr>
				<td align="center" class="celda lcelda bold {bcelda}">{dia}</td>
				<td align="right" class="celda bold green {bcelda}">{efectivo}</td>
				<td align="right" class="celda bold blue {bcelda}">{deposito}</td>
				<td align="right" class="celda bold blue {bcelda}">{mayoreo}</td>
				<td align="right" class="celda bold yellow {bcelda}">{oficina}</td>
				<td align="right" class="celda bold {color_faltante} {bcelda}">{faltante}</td>
				<td align="right" class="celda bold {color_diferencia} {bcelda}">{diferencia}</td>
				<td align="right" class="celda rcelda bold blue {bcelda}">{total}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th align="right" class="print cabecera">Tot.</th>
				<th align="right" class="print cabecera">{efectivo}</th>
				<th align="right" class="print cabecera blue">{deposito}</th>
				<th align="right" class="print cabecera blue">{mayoreo}</th>
				<th align="right" class="print cabecera yellow">{oficina}</th>
				<th align="right" class="print cabecera {color_faltante}">{faltante}</th>
				<th align="right" class="print cabecera {color_diferencia}">{diferencia}</th>
				<th align="right" class="print cabecera blue">{total}</th>
			</tr>
			<tr>
				<th align="right" class="print cabecera">Prom.</th>
				<th align="right" class="print cabecera">{pefectivo}</th>
				<th align="right" class="print cabecera blue">{pdeposito}</th>
				<th align="right" class="print cabecera blue">{pmayoreo}</th>
				<th align="right" class="print cabecera yellow">{poficina}</th>
				<th align="right" class="print cabecera yellow">&nbsp;</th>
				<th align="right" class="print cabecera">&nbsp;</th>
				<th align="right" class="print cabecera blue">{ptotal}</th>
			</tr>
		</table>
	</div>
	<!-- END BLOCK : reporte -->
</div>
{salto}
<!-- END BLOCK : hoja -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
	<!-- START BLOCK : boton_email -->
	<input name="email" type="button" id="email" value="Enviar por correo electr&oacute;nico" data-request="{data-request}" />
	<!-- END BLOCK : boton_email -->
</p>
</body>
</html>
