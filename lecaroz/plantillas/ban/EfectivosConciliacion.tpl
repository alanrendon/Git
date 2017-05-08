<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Conciliación de efectivos</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
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
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/ban/EfectivosConciliacion.js"></script>
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
	<div id="titulo">Conciliación de efectivos</div>
	<div id="captura" align="center"></div>
</div>
<div id="modificar_deposito_wrapper" style="display:none;">
	<form action="" method="post" name="modificar_deposito" class="FormValidator" id="modificar_deposito">
		<input type="hidden" name="id_deposito" id="id_deposito" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th scope="col">Fecha</th>
					<th scope="col">Conciliado</th>
					<th scope="col">Banco</th>
					<th scope="col">C&oacute;digo</th>
					<th scope="col">Concepto</th>
					<th scope="col">Importe</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"><input name="fecha_deposito" type="text" class="validate focus toDate center" id="fecha_deposito" size="10" maxlength="10" /></td>
					<td id="conciliado_deposito">&nbsp;</td>
					<td align="center"><img src="/lecaroz/imagenes/Banorte16x16.png" name="banco" width="16" height="16" id="banco" /></td>
					<td align="center"><select name="codigo_deposito" id="codigo_deposito">
							<option value="{value}">{text}</option>
						</select></td>
					<td id="concepto_deposito">&nbsp;</td>
					<td align="right" id="importe_deposito">&nbsp;</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="dividir_deposito_wrapper" style="display:none;">
	<form action="" method="post" name="dividir_deposito" class="FormValidator" id="dividir_deposito">
		<input type="hidden" name="id_deposito_dividir" id="id_deposito_dividir" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th scope="col">Depósito</th>
					<th align="right" id="deposito_dividir" scope="col">0.00</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="right">1</td>
					<td align="center"><input name="importe_deposito_dividir[]" type="text" class="validate focus numberPosFormat right" precision="2" id="importe_deposito_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="right">2</td>
					<td align="center"><input name="importe_deposito_dividir[]" type="text" class="validate focus numberPosFormat right" precision="2" id="importe_deposito_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="right">3</td>
					<td align="center"><input name="importe_deposito_dividir[]" type="text" class="validate focus numberPosFormat right" precision="2" id="importe_deposito_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="right">4</td>
					<td align="center"><input name="importe_deposito_dividir[]" type="text" class="validate focus numberPosFormat right" precision="2" id="importe_deposito_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="right">5</td>
					<td align="center"><input name="importe_deposito_dividir[]" type="text" class="validate focus numberPosFormat right" precision="2" id="importe_deposito_dividir" size="10" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="bold">Total</td>
					<td align="right" class="bold" id="total_deposito_dividido">0.00</td>
				</tr>
				<tr>
					<td class="bold">Resto</td>
					<td align="right"><span class="bold" id="resto_deposito_dividir">0.00</span></td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="cambiar_deposito_wrapper" style="display:none;">
	<form action="" method="post" name="cambiar_deposito" class="FormValidator" id="cambiar_deposito">
		<input type="hidden" name="id_deposito_cambiar" id="id_deposito_cambiar" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th scope="col">Acreditado a</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"><input name="num_cia_sec" type="text" class="validate focus toPosInt right" id="num_cia_sec" size="3" />
						<input name="nombre_cia_sec" type="text" disabled="disabled" id="nombre_cia_sec" size="30" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="bold">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="carta_deposito_wrapper" style="display:none;">
	<form action="" method="post" name="carta_deposito" class="FormValidator" id="carta_deposito">
		<input type="hidden" name="id_deposito_carta" id="id_deposito_carta" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th colspan="2" scope="row">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold" scope="row">Compañía destino</td>
					<td><input name="num_cia_destino" type="text" class="validate focus toPosInt right" id="num_cia_destino" size="3" />
						<input name="nombre_cia_destino" type="text" disabled="disabled" id="nombre_cia_destino" size="30" /></td>
				</tr>
				<tr>
					<td class="bold" scope="row">Contacto en el banco</td>
					<td><input name="contacto" type="text" class="validate toText cleanText toUpper" id="contacto" size="30" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" scope="row">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="cometra_deposito_wrapper" style="display:none;">
	<form action="" method="post" name="cometra_deposito" class="FormValidator" id="cometra_deposito">
		<input type="hidden" name="id_deposito_cometra" id="id_deposito_cometra" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th colspan="2" scope="row">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold" scope="row">Compañía destino</td>
					<td><input name="num_cia_destino_cometra" type="text" class="validate focus toPosInt right" id="num_cia_destino_cometra" size="3" />
						<input name="nombre_cia_destino_cometra" type="text" disabled="disabled" id="nombre_cia_destino_cometra" size="30" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" scope="row">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="modificar_oficina_wrapper" style="display:none;">
	<form action="" method="post" name="modificar_oficina" class="FormValidator" id="modificar_oficina">
		<input type="hidden" name="id_oficina" id="id_oficina" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">Fecha</th>
					<th scope="col">Importe</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"><input name="num_cia_oficina" type="text" class="validate focus toPosInt right" id="num_cia_oficina" size="3" />
						<input name="nombre_cia_oficina" type="text" disabled="disabled" id="nombre_cia_oficina" size="30" /></td>
					<td align="center"><input name="fecha_oficina" type="text" class="validate focus toDate center" id="fecha_oficina" size="10" maxlength="10" /></td>
					<td align="right" id="importe_oficina">&nbsp;</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="dividir_oficina_wrapper" style="display:none;">
	<form action="" method="post" name="dividir_oficina" class="FormValidator" id="dividir_oficina">
		<input type="hidden" name="id_oficina_dividir" id="id_oficina_dividir" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th id="fecha_dividir" scope="col">&nbsp;</th>
					<th align="right" id="oficina_dividir" scope="col">0.00</th>
				</tr>
				<tr>
					<td colspan="2" scope="col">&nbsp;</td>
				</tr>
				<tr>
					<th scope="col">Fecha</th>
					<th scope="col">Importes</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"><input name="fecha_oficina_dividir[]" type="text" class="validate focus toDate center" id="fecha_oficina_dividir" size="10" maxlength="10" /></td>
					<td align="center"><input name="importe_oficina_dividir[]" type="text" class="validate focus numberFormat right" precision="2" id="importe_oficina_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="center"><input name="fecha_oficina_dividir[]" type="text" class="validate focus toDate center" id="fecha_oficina_dividir" size="10" maxlength="10" /></td>
					<td align="center"><input name="importe_oficina_dividir[]" type="text" class="validate focus numberFormat right" precision="2" id="importe_oficina_dividir" size="10" /></td>
				</tr>
				<tr>
					<td align="center"><input name="fecha_oficina_dividir[]" type="text" class="validate focus toDate center" id="fecha_oficina_dividir" size="10" maxlength="10" /></td>
					<td align="center"><input name="importe_oficina_dividir[]" type="text" class="validate focus numberFormat right" precision="2" id="importe_oficina_dividir" size="10" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="bold">Total</td>
					<td align="right" class="bold" id="total_oficina_dividido">0.00</td>
				</tr>
				<tr>
					<td class="bold">Resto</td>
					<td align="right"><span class="bold" id="resto_oficina_dividir">0.00</span></td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
