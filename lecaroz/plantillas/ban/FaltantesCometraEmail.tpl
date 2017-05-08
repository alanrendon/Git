<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Faltantes de Cometra al {fecha}</title>
<style type="text/css">
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>
<p style="font-size:14pt; font-weight:bold;">Faltantes de Cometra al {fecha}
</p>
<table style="border-collapse:collapse; border:solid 1px #000;">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="6" align="left" style="border:solid 1px #000; background-color:#CCC;">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th style="border:solid 1px #000; background-color:#CCC;">Fecha</th>
		<th style="border:solid 1px #000; background-color:#CCC;">Comprobante</th>
		<th style="border:solid 1px #000; background-color:#CCC;">Dep&oacute;sito</th>
		<th style="border:solid 1px #000; background-color:#CCC;">Faltante</th>
		<th style="border:solid 1px #000; background-color:#CCC;">Sobrante</th>
		<th style="border:solid 1px #000; background-color:#CCC;">Concepto</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td style="border:solid 1px #000;">{fecha}</td>
		<td align="right" style="border:solid 1px #000;">{comprobante}</td>
		<td align="right" style="border:solid 1px #000;">{deposito}</td>
		<td align="right" style="color:#00C; border:solid 1px #000;">{faltante}</td>
		<td align="right" style="color:#C00; border:solid 1px #000;">{sobrante}</td>
		<td style="border:solid 1px #000;">{concepto}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th align="right" style="border:solid 1px #000; background-color:#CCC;">Totales</th>
		<th align="right" style="border:solid 1px #000; background-color:#CCC;">{comprobante}</th>
		<th align="right" style="border:solid 1px #000; background-color:#CCC;">{deposito}</th>
		<th align="right" style="color:#00C; border:solid 1px #000; background-color:#CCC;">{faltante}</th>
		<th align="right" style="color:#C00; border:solid 1px #000; background-color:#CCC;">{sobrante}</th>
		<th style="color:#C00; border:solid 1px #000; background-color:#CCC;">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="3" align="right" style="border:solid 1px #000; background-color:#CCC;">Diferencia</th>
		<th colspan="2" align="center" style="border:solid 1px #000; background-color:#CCC;">{diferencia} &#8212; {tipo}</th>
		<th style="border:solid 1px #000; background-color:#CCC;">&nbsp;</th>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p>&nbsp;</p>
</body>
</html>
