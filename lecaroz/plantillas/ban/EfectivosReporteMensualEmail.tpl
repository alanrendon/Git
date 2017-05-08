<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de efectivos mensuales</title>
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
				<th class="print cabecera">D&iacute;a</th>
				<th class="print cabecera">Efectivo</th>
				<th class="print cabecera">Dep&oacute;sito</th>
				<th class="print cabecera">Mayoreo</th>
				<th class="print cabecera">Oficina</th>
				<th class="print cabecera">Faltante</th>
				<th class="print cabecera">Diferencia</th>
				<th class="print cabecera">Total<br />Dep&oacute;sitos</th>
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
				<th align="right" class="print cabecera">&nbsp;</th>
				<th align="right" class="print cabecera">&nbsp;</th>
				<th align="right" class="print cabecera blue">{ptotal}</th>
			</tr>
		</table>
	</div>
	<!-- END BLOCK : reporte -->
</div>
{salto}
<!-- END BLOCK : hoja -->
</body>
</html>
