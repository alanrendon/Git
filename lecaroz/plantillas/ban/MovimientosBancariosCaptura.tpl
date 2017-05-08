<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Captura de movimientos bancarios</title>
	<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/lecaroz/menus/stm31.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/ban/MovimientosBancariosCaptura.js"></script>
	<style type="text/css">
		.info-table {
			border-collapse: collapse;
			border: solid 1px #000;
			background-color: #fff;
		}
		.info-table td,
		.info-table th {
			border: solid 1px #000;
		}
		.info-table th {
			background-color: #999;
		}
	</style>
	<script type="text/javascript">
		var current_date = '{fecha}';
	</script>
</head>

<body>

	<div id="contenedor">
		<div id="titulo">Captura de movimientos bancarios</div>
		<div id="captura" align="center">
			<form name="captura_form" class="FormValidator" id="captura_form">
				<table class="table">
					<thead>
						<tr>
							<th colspan="2">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="bold font12">Banco</td>
							<td>
								<select name="banco" id="banco" class="bold font12">
									<option value="1">BANORTE</option>
									<option value="2">SANTANDER</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="bold font12">Tipo de movimiento</td>
							<td>
								<select name="tipo" id="tipo" class="bold font12">
									<option value="FALSE" class="blue">ABONOS</option>
									<option value="TRUE" class="red">CARGOS</option>
								</select>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br />
				<table class="table" id="captura_table">
					<thead>
						<tr>
							<th>Compa&ntilde;&iacute;a</th>
							<th>Fecha</th>
							<th>C&oacute;digo</th>
							<th>Importe</th>
							<th>Concepto</th>
							<th>Facturas</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<p>
					<button type="button" id="registrar">Registrar</button>
				</p>
			</form>
		</div>
	</div>

	<div id="agregar_factura_wrapper" style="display:none;">
		<form class="FormValidator" id="buscar_factura_form">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Compa&ntilde;&iacute;a</td>
						<td>
							<input type="text" class="validate focus toPosInt right" id="num_cia_fac" size="3" />
							<input type="text" id="nombre_cia_fac" disabled="" size="30" />
						</td>
					</tr>
					<tr>
						<td>Factura</td>
						<td>
							<input type="text" class="validate focus toPosInt" id="num_fact" size="10" />
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>

	<div id="reporte_wrapper" style="display:none; width:800px; height:600px;">
		<iframe id="reporte_frame" src="" style="width:100%; height:100%;"></iframe>
	</div>

	<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>

</body>
</html>
