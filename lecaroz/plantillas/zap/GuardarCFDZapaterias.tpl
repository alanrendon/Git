<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Guardar CFD's de proveedores</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/zap/GuardarCFDZapaterias.js"></script>
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
	<div id="titulo">Guardar CFD's de proveedores</div>
	<div id="captura" align="center">
		<form name="datos" class="FormValidator" id="datos">
			<input name="datos" type="hidden" id="datos" value="" />
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" scope="row">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="left" scope="row">Archivo XML</td>
						<td><input name="xml_file[]" type="file" id="xml_file" size="30" multiple /></td>
					</tr>
					<tr>
						<td align="left" scope="row">Archivo PDF</td>
						<td><input name="pdf_file[]" type="file" id="pdf_file" size="30" multiple disabled="disabled" /></td>
					</tr>
					<tr>
						<td align="left" scope="row">Estatus</td>
						<td id="status">&nbsp;</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" scope="row">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<p>
				<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
				&nbsp;&nbsp;
				<input type="button" name="guardar" id="guardar" value="Guardar archivos" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>
</body>
</html>
