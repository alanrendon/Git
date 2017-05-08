<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Generador de Archivos y Listados</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/cometra/GenerarArchivosCometra.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Generador de Archivos y Listados</div>
	<div id="captura" align="center">
		<table class="tabla_captura">
			<tr>
				<th align="left" scope="row">Banco</th>
				<td class="font14 bold">{banco}</td>
			</tr>
		</table>
		<br />
		<table class="tabla_captura">
			<tr>
				<th scope="col">Compa&ntilde;&iacute;a</th>
				<th scope="col">Cuenta</th>
				<th scope="col">Fecha</th>
				<th scope="col">C&oacute;digo</th>
				<th scope="col">Concepto</th>
				<th scope="col">Comprobante</th>
				<th scope="col">Importe</th>
				<th scope="col">Separar</th>
				<th scope="col">Total</th>
			</tr>
			<!-- START BLOCK : comprobante -->
			<!-- START BLOCK : deposito -->
			<tr id="row" class="linea_{color}">
				<td><a name="{id}" id="{id}"></a>
					<input name="data[]" type="hidden" id="data" value="{data}" comprobante="{comprobante}" index="{index}" />
					{num_cia} {nombre_cia}</td>
				<td align="center">{cuenta}</td>
				<td align="center">{fecha}</td>
				<td>{cod_mov} {descripcion}</td>
				<td>{concepto}</td>
				<td align="center"{tipo_comprobante}>{comprobante}</td>
				<td align="right"{color_importe}>{importe}</td>
				<td align="right"><a href="#" title="{index}" id="separar" class="green enlace">{separar}</a></td>
				<td id="total_{index}" align="right"{color_importe}>{total}</td>
			</tr>
			<!-- END BLOCK : deposito -->
			<tr>
				<th colspan="6" align="right">Total comprobante</th>
				<th align="right">{importe}</th>
				<th id="separar_{comprobante}" align="right">{separar}</th>
				<th id="total_{comprobante}" align="right">{total}</th>
			</tr>
			<tr class="linea_{color}">
				<td colspan="9">&nbsp;</td>
			</tr>
			<!-- END BLOCK : comprobante -->
			<tr>
				<th colspan="6" align="right">Total de comprobantes </th>
				<th align="right" class="font14">{importe}</th>
				<th id="desglose_separar" align="right" class="font14">{separar}</th>
				<th id="desglose_total" align="right" class="font14">{total}</th>
			</tr>
		</table>
		<br />
		<table class="tabla_captura">
			<tr>
				<th scope="col">Compa&ntilde;&iacute;a</th>
				<th scope="col">Cuenta</th>
				<th scope="col">Fecha</th>
				<th scope="col">Comprobante</th>
				<th scope="col">Importe</th>
				<th scope="col">Separar</th>
				<th scope="col">Total</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr id="row" class="linea_{color}">
				<td>{num_cia} {nombre_cia} </td>
				<td align="center">{cuenta}</td>
				<td align="center">{fecha}</td>
				<td align="center"{tipo_comprobante}>{comprobante}</td>
				<td align="right">{importe}</td>
				<td id="general_separar_{comprobante}" align="right" class="green">{separar}</td>
				<td id="general_total_{comprobante}" align="right">{total}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th colspan="4" align="right">Total General ({num_comprobantes}) </th>
				<th align="right" class="font14">{importe}</th>
				<th id="general_separar" align="right" class="font14">{separar}</th>
				<th id="general_total" align="right" class="font14">{total}</th>
			</tr>
		</table>
		<br />
		<table class="tabla_captura">
			<tr>
				<th colspan="7" scope="col">FALTANTES, SOBRANTES, CHEQUES Y CANCELACION DE DEPOSITOS</th>
			</tr>
			<tr>
				<th scope="col">Compa&ntilde;&iacute;a</th>
				<th scope="col">Cuenta</th>
				<th scope="col">Fecha</th>
				<th scope="col">C&oacute;digo</th>
				<th scope="col">Concepto</th>
				<th scope="col">Comprobante</th>
				<th scope="col">Importe</th>
			</tr>
			<!-- START BLOCK : otro -->
			<tr id="row" class="linea_{color}">
				<td>{num_cia} {nombre_cia}</td>
				<td align="center">{cuenta}</td>
				<td align="center">{fecha}</td>
				<td>{cod_mov} {descripcion}</td>
				<td>{concepto}</td>
				<td align="center">{comprobante}</td>
				<td align="right"{color_importe}>{importe}</td>
			</tr>
			<!-- END BLOCK : otro -->
			<tr>
				<th colspan="6" align="right">Total General </th>
				<th align="right" class="font14">{total_otros}</th>
			</tr>
		</table>
		<!-- START BLOCK : otros -->
		<br />
		<table class="tabla_captura">
			<tr>
				<th colspan="7" scope="col">{otros}</th>
			</tr>
			<tr>
				<th scope="col">Compa&ntilde;&iacute;a</th>
				<th scope="col">Cuenta</th>
				<th scope="col">Fecha</th>
				<th scope="col">C&oacute;digo</th>
				<th scope="col">Concepto</th>
				<th scope="col">Comprobante</th>
				<th scope="col">Importe</th>
			</tr>
			<!-- START BLOCK : mov -->
			<tr id="row" class="linea_{color}">
				<td>{num_cia} {nombre_cia}</td>
				<td align="center">{cuenta}</td>
				<td align="center">{fecha}</td>
				<td>{cod_mov} {descripcion}</td>
				<td>{concepto}</td>
				<td align="center">{comprobante}</td>
				<td align="right"{color_importe}>{importe}</td>
			</tr>
			<!-- END BLOCK : mov -->
			<tr>
				<th colspan="6" align="right">Total General </th>
				<th align="right" class="font14">{total}</th>
			</tr>
		</table>
		<!-- END BLOCK : otros -->
		<br />
		<table class="tabla_captura">
			<tr class="linea_off">
				<th align="left" scope="row">Cheques ({num_cheques})</th>
				<td class="bold font14 green right">{cheques}</td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">Efectivo</th>
				<td class="bold font14 blue right">{efectivo}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">Total General ({num_comprobantes})</th>
				<td class="bold font14 right">{total_general}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">Total Separado</th>
				<td class="bold font14 right">{total_separado}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">Total Depositado</th>
				<td class="bold font14 right">{total_depositado}</td>
			</tr>
		</table>
		<p class="font14 bold red">NOTA 1: Las modificaciones que realice en los importes a separar solo son v&aacute;lidos mientras permanezca en esta pantalla, saliendo o cambiando a otra pantalla sin haber registrado los movimientos en el estado de cuenta anulara todos los cambios.</p>
		<p class="font14 bold red">NOTA 2: Los cambios en el archivo de Excel, en el archivo de Banorte y la impresi&oacute;n de comprobantes de Cometra se ver&aacute;n reflejados una vez que se hayan registrado los movimientos en el estado de cuenta.</p>
		<p>
			<input name="archivo" type="button" id="archivo" value="Generar archivo de Excel" />
&nbsp;&nbsp;
<input type="button" name="archivo_banorte" id="archivo_banorte" value="Generar archivo de Banorte" />
&nbsp;&nbsp;
<input type="button" name="reporte_faltantes" id="reporte_faltantes" value="Reporte de sobrantes y faltantes" />
&nbsp;&nbsp;
<input type="button" name="imprimir_comprobantes" id="imprimir_comprobantes" value="Imprimir comprobantes de Cometra" />
&nbsp;&nbsp;
			<input name="registrar" type="button" id="registrar" value="Registrar en el estado de cuenta" />
		</p>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
