<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Factura electr&oacute;nica para clientes</title>

<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/calendar.css" rel="stylesheet" type="text/css" />

<style>

.icono {
	opacity: 0.6;
}

.icono:hover {
	opacity: 1;
	cursor: pointer;
}

.cliente_td {
}

.cliente_td:hover {
	cursor: pointer;
}

</style>

<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5-compat.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/Calendar.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/fac/FacturaElectronicaPanaderias.js"></script>

</head>

<body>
<div id="contenedor">
	<div id="titulo">Factura Electr&oacute;nica</div>
	<div id="captura" align="center">
		<form action="" method="post" name="fe_form" class="FormValidator" id="fe_form">
			<input name="fecha" type="hidden" value="{fecha}" />
			<input name="tipo" type="hidden" value="2" />
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Compa&ntilde;&iacute;a</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<select name="num_cia" id="num_cia" style="width:100%;">
								<!-- START BLOCK : num_cia -->
								<option value="{num_cia}">{num_cia} {nombre_cia}</option>
								<!-- END BLOCK : num_cia -->
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Datos del cliente</th>
					</tr>
					<tr>
						<th align="left" scope="row">R.F.C.</th>
						<td>
							<input name="rfc" type="text" class="validate focus toRFC toUpper" id="rfc" size="13" maxlength="13" />
							<img id="buscar" src="iconos/magnify.png" class="icono" width="16" height="16" />
							<span id="rfc_status"></span>
						</td>
					</tr>
					<tr>
						<th align="left" scope="row">Nombre</th>
						<td><input name="nombre_cliente" type="text" class="validate toText toUpper cleanText" id="nombre_cliente" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Calle</th>
						<td><input name="calle" type="text" class="validate toText toUpper cleanText" id="calle" size="35" maxlength="100" />
							No. Ext.:
							<input name="no_exterior" type="text" class="validate toText toUpper cleanText" id="no_exterior" size="5" maxlength="20" />
							No. Int.:
							<input name="no_interior" type="text" class="validate toText toUpper cleanText" id="no_interior" size="5" maxlength="20" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Colonia</th>
						<td><input name="colonia" type="text" class="validate toText toUpper cleanText" id="colonia" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Localidad</th>
						<td><input name="localidad" type="text" class="validate toText toUpper cleanText" id="localidad" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Referencia</th>
						<td><input name="referencia" type="text" class="validate toText toUpper cleanText" id="referencia" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
						<td><input name="municipio" type="text" class="validate toText toUpper cleanText" id="municipio" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Estado</th>
						<td>
							<select name="estado" id="estado">
								<!-- START BLOCK : estado -->
								<option value="{estado}">{estado}</option>
								<!-- END BLOCK : estado -->
							</select>
						</td>
					</tr>
					<tr>
						<th align="left" scope="row">Pa&iacute;s</th>
						<td><input name="pais" type="text" class="validate toText toUpper cleanText" id="pais" style="width:100%;" maxlength="100" value="MEXICO" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">C&oacute;digo postal </th>
						<td><input name="codigo_postal" type="text" class="validate onlyNumbers" id="codigo_postal" size="5" maxlength="20" /></td>
					</tr>
					<tr>
						<th align="left" scope="row">Correo electr&oacute;nico </th>
						<td><input name="email_cliente" type="text" class="validate focus toEmail" id="email_cliente" style="width:100%;" maxlength="100" /></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Datos de facturaci&oacute;n</th>
					</tr>
					<tr>
						<th align="left" scope="row">Tipo de pago</th>
						<td>
							<select name="tipo_pago" id="tipo_pago">
								<!-- <option value="4"></option> -->
								<option value="B">EFECTIVO</option>
								<option value="1">TRANSFERENCIA BANCARIA</option>
								<option value="2">CHEQUE</option>
								<option value="K">TARJETA DE CREDITO</option>
								<option value="5">NO IDENTIFICADO</option>
							</select>
						</td>
					</tr>
					<tr>
						<th align="left" scope="row">Cuenta de pago</th>
						<td><input name="cuenta_pago" type="text" class="validate focus onlyNumbers" id="cuenta_pago" size="20" maxlength="20" /></td>
					</tr>
					<tr>
					<th align="left" scope="row">Condiciones de pago</th>
						<td>
							<select name="condiciones_pago" id="condiciones_pago">
								<option value="0"></option>
								<option value="1">CONTADO</option>
								<option value="2">CREDITO</option>
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
			<table class="table">
				<thead>
					<tr>
						<th colspan="7" align="left" scope="col"><img src="imagenes/info.png" width="16" height="16" /> Datos de factura </th>
					</tr>
					<tr>
						<th scope="col">Descripci&oacute;n</th>
						<th scope="col">Cantidad</th>
						<th scope="col">Precio</th>
						<th scope="col">Unidad</th>
						<th scope="col">Aplicar<br />I.E.P.S. 8%</th>
						<th scope="col">Aplicar<br />
							I.V.A. 16%</th>
						<th scope="col">Importe</th>
					</tr>
				</thead>
				<tbody id="conceptos">
					<tr>
						<td align="center"><input name="descripcion[]" type="text" class="validate toText toUpper cleanText" id="descripcion" value="" size="30" /></td>
						<td align="center"><input name="cantidad[]" type="text" class="validate focus numberPosFormat right" precision="2" id="cantidad" size="5" /></td>
						<td align="center"><input name="precio[]" type="text" class="validate focus numberPosFormat right" precision="2" id="precio" size="8" /></td>
						<td align="center"><input name="unidad[]" type="text" class="validate onlyText toUpper cleanText" id="unidad" size="10" maxlength="50" /></td>
						<td align="center">
							<input name="aplicar_ieps[]" type="checkbox" id="aplicar_ieps" value="0" />
							<input name="ieps[]" type="hidden" id="ieps" value="0" />
						</td>
						<td align="center"><input name="aplicar_iva[]" type="checkbox" id="aplicar_iva" value="0" /></td>
						<td align="center"><input name="importe[]" type="text" class="right" id="importe" size="10" readonly="true" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" align="right">Subtotal</th>
						<th align="center"><input name="subtotal" type="text" class="right bold font12" id="subtotal" size="10" readonly="true" /></th>
					</tr>
					<tr>
						<th colspan="6" align="right">I.E.P.S</th>
						<th align="center"><input name="total_ieps" type="text" class="right bold font12" id="total_ieps" size="10" readonly="true" /></th>
					</tr>
					<tr>
						<th colspan="6" align="right">I.V.A</th>
						<th align="center"><input name="iva" type="text" class="right bold font12" id="iva" size="10" readonly="true" /></th>
					</tr>
					<tr>
						<th colspan="6" align="right">Total</th>
						<th align="center"><input name="total" type="text" class="right bold font12" id="total" size="10" /></th>
					</tr>
				</tfoot>
			</table>
			<p>
				<input name="registrar" type="button" id="registrar" value="Registrar Factura Electr&oacute;nica" />
			</p>
		</form>
	</div>
</div>

<div id="datos_cliente_wrapper" style="display:none;">
	<p>
		Se encontraron los siguientes datos de clientes con R.F.C. <span id="rfc_span" class="bold"></span>
	</p>
	<div style="margin:20px 10px 10px 10px; padding:4px; width:600px; height:300px; overflow:auto;">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2" align="left" scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody id="clientes"></tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

</body>
</html>
