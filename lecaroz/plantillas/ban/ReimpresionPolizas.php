<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reimpresión de pólizas</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="menus/stm31.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/Request.File.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/ban/CatalogoSindicatos.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
}
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reimpresión de pólizas</div>
	<div id="captura" align="center">
		<form id="captura_polizas" name="captura_polizas" method="post" action="">
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Compañía</th>
						<th scope="col">Banco</th>
						<th scope="col">Folio</th>
					</tr>
				</thead>
				<tbody id="rows">
					<tr>
						<td><input name="num_cia[]" type="text" class="validate focus toPosInt right" id="num_cia" size="3" />
						<input name="nombre_cia[]" type="text" disabled="disabled" id="nombre_cia" /></td>
						<td><select name="select" id="select">
							<option value="1">BANORTE</option>
							<option value="2">SANTANDER</option>
						</select></td>
						<td><input name="folio[]" type="text" class="validate focus toPosInt right" id="folio[]" size="6" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<p class="FormValidator">
				<input type="button" name="reimprimir" id="reimprimir" value="Reimprimir pólizas" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
