<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Factura de proveedores varios</title>
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
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/fac/FacturaProveedoresVarios.js"></script>
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
		<div id="titulo">Factura de proveedores varios</div>
		<div id="captura" align="center">
		<form action="FacturasProveedoresVarios.php" method="post" name="factura_form" class="FormValidator" id="factura_form">
				<table class="table">
					<thead>
						<tr>
							<th colspan="3">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2" class="bold">Compa&ntilde;&iacute;a</td>
							<td>
								<input name="num_cia" type="text" class="validate toPosInt center" id="num_cia" size="1" />
								<input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Proveedor</td>
							<td>
								<input name="num_pro" type="text" class="validate toPosInt center" id="num_pro" value="{num_pro}" size="1" />
								<input name="nombre_pro" type="text" class="disabled" id="nombre_pro" size="30" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Factura</td>
							<td>
								<input name="num_fact" type="text" class="validate focus onlyNumbersAndLetters toUpper" id="num_fact" size="10" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Fecha</td>
							<td>
								<input name="fecha" type="text" class="validate focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Gasto</td>
							<td>
								<input name="codgastos" type="text" class="validate focus toPosInt center" id="codgastos" value="{codgastos}" size="1" />
								<input name="desc" type="text" class="disabled" id="desc" size="30" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Concepto</td>
							<td>
								<input name="concepto" type="text" class="validate toUpper cleanText" id="concepto" value="{concepto}" size="50" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold">Tipo</td>
							<td>
								<select name="tipo_factura" id="tipo_factura">
									<option value="0" selected="selected">FACTURA</option>
									<option value="1">RECIBO HONORARIOS</option>
									<option value="2">RECIBO RENTA</option>
									<option value="3">OTROS</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="bold"><input name="aclaracion" type="checkbox" class="checkbox" id="aclaracion" value="1" />Aclaraci&oacute;n</td>
							<td>
								<textarea name="observaciones" cols="50" rows="5" class="validate toText toUpper cleanText" id="observaciones"></textarea>
							</td>
						</tr>
						<tr id="row_anio" style="display:none;">
							<td rowspan="2" class="bold">Agua</td>
							<td class="bold">A&ntilde;o</td>
							<td>
								<input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" maxlength="4" />
							</td>
						</tr>
						<tr id="row_bimestre" style="display:none;">
							<td class="bold">Bimestre</td>
							<td>
								<input name="bimestre" type="text" class="validate focus toPosInt center" id="bimestre" size="1" />
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="3">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
				<br />
				<table class="table">
					<thead>
						<tr>
							<th colspan="2">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="bold">Subtotal</td>
							<td align="right">
								<input name="subtotal" type="text" class="validate focus numberPosFormat right red" precision="2" id="subtotal" size="16" />
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td class="bold">
								<input type="checkbox" id="aplicar_ieps" />
								I.E.P.S.
								<span style="float:right;">
									<input name="por_ieps" type="text" class="validate focus numberPosFormat right red" precision="2" id="por_ieps" size="4" value="8.00" />%
								</span>
							</td>
							<td align="right">
								<input name="ieps" type="text" class="right red" precision="2" id="ieps" size="16" value="0.00" readonly="" />
							</td>
						</tr>
						<tr>
							<td class="bold">
								I.E.P.S. Libre
							</td>
							<td align="right">
								<input name="ieps_libre" type="text" class="validate focus numberPosFormat right red" precision="2" id="ieps_libre" size="16" value="0.00" />
							</td>
						</tr>
						<tr>
							<td class="bold">
								<input type="checkbox" id="aplicar_iva" />
								I.V.A.
								<span style="float:right;">
									<input name="por_iva" type="text" class="validate focus numberPosFormat right red" precision="2" id="por_iva" size="4" value="16.00" />%
								</span>
							</td>
							<td align="right">
								<input name="iva" type="text" class="right red" id="iva" size="16" readonly="readonly" value="0.00" />
							</td>
						</tr>
						<tr>
							<td class="bold">
								<input type="checkbox" id="aplicar_ret_iva" />
								Retenci&oacute;n I.V.A.
								<span style="float:right;">
									<input name="por_ret_iva" type="text" class="validate focus numberPosFormat right blue" precision="2" id="por_ret_iva" size="4" value="10.6666667" />%
								</span>
							</td>
							<td align="right">
								<input name="ret_iva" type="text" class="right blue" id="ret_iva" size="16" readonly="readonly" value="0.00" />
							</td>
						</tr>
						<tr>
							<td class="bold">
								<input type="checkbox" id="aplicar_ret_isr" />
								Retenci&oacute;n I.S.R.
								<span style="float:right;">
									<input name="por_ret_isr" type="text" class="validate focus numberPosFormat right blue" precision="2" id="por_ret_isr" size="4" value="10.00" />%
								</span>
							</td>
							<td align="right">
								<input name="ret_isr" type="text" class="right blue" id="ret_isr" size="16" readonly="readonly" value="0.00" />
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td class="bold">Otros conceptos</td>
							<td>
								<input name="concepto_otros" type="text" class="validate toUpper cleanText" id="concepto_otros" size="30" />
							</td>
						</tr>
						<tr>
							<td class="bold">Importe otros <small>(no graba impuestos)<small></td>
							<td align="right">
								<input name="importe_otros" type="text" class="validate focus numberPosFormat right orange" id="importe_otros" size="16" />
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td class="bold">Total</td>
							<td align="right">
								<input name="total" type="text" class="right font14 bold" id="total" size="16" readonly="readonly" value="0.00" />
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<p>
					<button type="button" id="borrar">Borrar</button>
					&nbsp;&nbsp;
					<button type="button" id="guardar">Guardar</button>
				</p>
			</form>
		</div>
	</div>
	<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>
</body>
</html>
