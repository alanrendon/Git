<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contratos de trabajadores proximos a vencer</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/AlertaTrabajadoresContratosProximosVencer.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<p class="bold font14 center noDisplay">Contratos de trabajadores proximos a vencer</p>
<form method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	<!-- START BLOCK : admin -->
	<table align="center" class="print" id="empleados">
		<tr>
			<th colspan="5" align="left" class="print font16" scope="col">{admin}</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="5" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th class="print">#</th>
			<th class="print">Empleado</th>
			<th class="print">Fecha Inicio</th>
			<th class="print">Fecha Término</th>
			<th class="print">Renovar</th>
		</tr>
		<!-- START BLOCK : empleado -->
		<tr>
			<td align="right" class="print"><input name="id[]" type="hidden" id="id" value="{id}" />
				{num_emp}</td>
			<td class="print">{empleado}</td>
			<td align="center" class="print">{fecha_inicio}</td>
			<td align="center" class="print">{fecha_termino}</td>
			<td align="center" class="print"><input name="meses[]" type="text" class="valid Focus toPosInt center" id="meses" size="3" />
				mes(es) o
				<input name="ind[]" type="checkbox" id="ind" value="{id}" />
				Indeterminado</td>
		</tr>
		<!-- END BLOCK : empleado -->
		<tr>
			<td colspan="5" class="print">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	{salto} 
	<!-- END BLOCK : admin -->
	<p class="noDisplay center">
		<input type="button" name="cerrar" id="cerrar" value="Cerrar" />
		&nbsp;&nbsp;
		<input name="renovar" type="button" id="renovar" value="Renovar contratos" />
		&nbsp;&nbsp;
		<input type="button" name="email" id="email" value="Enviar avisos por email" />
	</p>
</form>
</body>
</html>
