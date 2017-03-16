<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Resgistro de facturas electr&oacute;nicas que no entraron en sistema</title>
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
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Request.File.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturaElectronicaManualSoloRegistrar.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Resgistro de facturas electr&oacute;nicas que no entraron en sistema</div>
	<div id="captura" align="center">
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<input name="fecha_timbrado" type="hidden" id="fecha_timbrado" />
			<input name="uuid" type="hidden" id="uuid" />
			<input name="no_certificado_digital" type="hidden" id="no_certificado_digital" />
			<input name="no_certificado_sat" type="hidden" id="no_certificado_sat" />
			<input name="sello_cfd" type="hidden" id="sello_cfd" />
			<input name="sello_sat" type="hidden" id="sello_sat" />
			<input name="cadena_original" type="hidden" id="cadena_original" />
			<table class="tabla_captura">
				<tr class="linea_on">
					<th align="left" scope="row">Archivo XML</th>
					<td><input name="xml_file[]" type="file" id="xml_file" size="30" multiple /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Archivo PDF</th>
					<td>
						<input name="tipo_pdf" type="radio" id="tipo_pdf_generar" value="generar" checked="checked" /> Generar PDF desde el sistema<br />
						<input name="tipo_pdf" type="radio" id="tipo_pdf_cargar" value="cargar" /> Cargar PDF: <input name="pdf_file[]" type="file" id="pdf_file" size="30" multiple disabled="disabled" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
					<td>
						<input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" readonly="readonly" />
						<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="61" />
						<input name="rfc_cia" type="hidden" id="rfc_cia" />
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Serie</th>
					<td><input name="serie" type="text" class="valid Focus onlyLetters" id="serie" value="" size="5" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Folio</th>
					<td><input name="folio" type="text" class="valid Focus toPosInt" id="folio" value="" size="10" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Hora</th>
					<td><input name="hora" type="text" class="valid Focus toTimeHMS center" id="hora" value="{hora}" size="8" maxlength="8" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Tipo</th>
					<td>
						<input name="tipo" type="radio" id="tipo_1" value="1" disabled="disabled" />
						Venta<br />
						<input name="tipo" type="radio" id="tipo_2" value="2" disabled="disabled" />
						Cliente<br />
						<input name="tipo" type="radio" id="tipo_5" value="5" disabled="disabled" />
						Arrendamiento<br />
						<input name="tipo" type="radio" id="tipo_6" value="6" disabled="disabled" />
						Otro
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tipo de pago</th>
					<td>
						<select name="tipo_pago" id="tipo_pago">
							<!-- <option value="4" selected="selected"></option> -->
							<option value="B">EFECTIVO</option>
							<option value="1">TRANSFERENCIA ELECTRONICA</option>
							<option value="2">CHEQUE</option>
							<option value="K">TARJETA DE CREDITO</option>
							<option value="V">MONEDERO ELECTRONICO</option>
							<option value="W">DINERO ELECTRONICO</option>
							<option value="X">VALES DE DESPENSA</option>
							<option value="Y">TARJETA DE DEBITO</option>
							<option value="Z">TARJETA DE SERVICIOS</option>
						</select>
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Cuenta de pago</th>
					<td><input name="cuenta_pago" type="text" class="valid Focus onlyNumbers" id="cuenta_pago" size="20" maxlength="20" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Condiciones de pago</th>
					<td>
						<select name="condiciones_pago" id="condiciones_pago">
							<option value="0" selected="selected"></option>
							<option value="1">CONTADO</option>
							<option value="2">CREDITO</option>
						</select>
					</td>
				</tr>
				<tbody id="renta_block" style="display:none;">
					<tr>
						<td colspan="2" align="left" scope="row">&nbsp;</td>
					</tr>
					<tr>
						<th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" />Datos de renta</th>
					</tr>
					<tr class="linea_off">
						<th align="left" scope="row">Inmobiliaria</th>
						<td>
							<input name="cuenta_predial" type="hidden" id="cuenta_predial" />
							<select name="arrendador" id="arrendador"></select>
						</td>
					</tr>
					<tr class="linea_on">
						<th align="left" scope="row">Arrendatario</th>
						<td>
							<select name="arrendatario" id="arrendatario"></select>
						</td>
					</tr>
					<tr class="linea_off">
						<th align="left" scope="row">A&ntilde;o</th>
						<td><input name="anio_renta" type="text" class="valid Focus toPosInt center" id="anio_renta" value="{anio_renta}" size="4" maxlength="4" /></td>
					</tr>
					<tr class="linea_on">
						<th align="left" scope="row">Mes</th>
						<td>
							<select name="mes_renta" id="mes_renta">
								<option value="1">ENERO</option>
								<option value="2">FEBRERO</option>
								<option value="3">MARZO</option>
								<option value="4">ABRIL</option>
								<option value="5">MAYO</option>
								<option value="6">JUNIO</option>
								<option value="7">JULIO</option>
								<option value="8">AGOSTO</option>
								<option value="9">SEPTIEMBRE</option>
								<option value="10">OCTUBRE</option>
								<option value="11">NOVIEMBRE</option>
								<option value="12">DICIEMBRE</option>
							</select>
						</td>
					</tr>
				</tbody>
				<tr>
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" />Datos del cliente</th>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Nombre</th>
					<td><input name="nombre_cliente" type="text" class="valid toText toUpper cleanText" id="nombre_cliente" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">R.F.C.</th>
					<td><input name="rfc" type="text" class="valid Focus toRFC toUpper" id="rfc" size="13" maxlength="13" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Calle</th>
					<td><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="35" maxlength="100" readonly="readonly" />
						No. Ext.:
						<input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" size="5" maxlength="20" readonly="readonly" />
						No. Int.:
						<input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" size="5" maxlength="20" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Colonia</th>
					<td><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Localidad</th>
					<td><input name="localidad" type="text" class="valid toText toUpper cleanText" id="localidad" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Referencia</th>
					<td><input name="referencia" type="text" class="valid toText toUpper cleanText" id="referencia" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
					<td><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Estado</th>
					<td><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Pa&iacute;s</th>
					<td><input name="pais" type="text" class="valid toText toUpper cleanText" id="pais" style="width:98%;" maxlength="100" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">C&oacute;digo postal </th>
					<td><input name="codigo_postal" type="text" class="valid onlyNumbers" id="codigo_postal" size="5" maxlength="20" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Correo electr&oacute;nico </th>
					<td><input name="email_cliente" type="text" class="valid Focus toEmail" id="email_cliente" style="width:98%;" maxlength="100" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" nowrap="nowrap" scope="row">Observaciones
					<input name="long_obs" type="checkbox" id="long_obs" value="1" />
					<span class="font6">(versi&oacute;n larga)</span></th>
					<td><textarea name="observaciones" cols="50" rows="5" class="valid toText toUpper" id="observaciones" style="width:98%;"></textarea></td>
				</tr>
			</table>
			<br />
			<table class="tabla_captura">
				<tr>
					<th colspan="7" align="left" scope="col"><img src="imagenes/info.png" width="16" height="16" />Datos de factura</th>
				</tr>
				<tr>
					<th colspan="7" align="left" scope="col"><img src="/lecaroz/imagenes/plus16x16.png" name="expand" width="16" height="16" align="top" id="expand" /><span id="expand_desc">Expandir descripciones</span><input name="tipo_reporte" type="hidden" id="tipo_reporte" value="1" /></th>
				</tr>
				<tr>
					<th scope="col">Descripci&oacute;n</th>
					<th scope="col">Cantidad</th>
					<th scope="col">Precio</th>
					<th scope="col">Unidad</th>
					<th scope="col">Aplicar<br />I.V.A.</th>
					<th scope="col">Aplicar<br />I.E.P.S.</th>
					<th scope="col">Importe</th>
				</tr>
				<tbody id="Conceptos">
					<tr class="linea_off">
						<td align="center"><input name="descripcion[]" type="text" class="valid toText toUpper cleanText" id="descripcion" value="" size="30" readonly="readonly" /></td>
						<td align="center"><input name="cantidad[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad" size="5" readonly="readonly" /></td>
						<td align="center"><input name="precio[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="precio" size="8" readonly="readonly" /></td>
						<td align="center"><input name="unidad[]" type="text" class="valid onlyText toUpper cleanText" id="unidad" size="10" maxlength="50" readonly="readonly" /></td>
						<td align="center"><input name="aplicar_iva[]" type="checkbox" id="aplicar_iva" value="0" /></td>
						<td align="center"><input name="aplicar_ieps[]" type="checkbox" id="aplicar_ieps" value="0" /></td>
						<td align="center"><input name="importe[]" type="text" class="right" id="importe" size="10" readonly="true" /></td>
					</tr>
				</tbody>
				<tr>
					<th colspan="6" align="right">Subtotal</th>
					<th align="center"><input name="subtotal" type="text" class="right bold font12" id="subtotal" size="10" readonly="true" /></th>
				</tr>
				<tr>
					<th colspan="6" align="right">I.E.P.S.</th>
					<th align="center"><input name="ieps" type="text" class="right bold font12" id="ieps" size="10" readonly="true" /></th>
				</tr>
				<tr>
					<th colspan="6" align="right">I.V.A</th>
					<th align="center"><input name="iva" type="text" class="right bold font12" id="iva" size="10" readonly="true" /></th>
				</tr>
				<tr>
					<th colspan="6" align="right">Retenci&oacute;n I.V.A</th>
					<th align="center"><input name="retencion_iva" type="text" class="right bold font12" id="retencion_iva" size="10" readonly="true" /></th>
				</tr>
				<tr>
					<th colspan="6" align="right">Retenci&oacute;n I.S.R.</th>
					<th align="center"><input name="retencion_isr" type="text" class="right bold font12" id="retencion_isr" size="10" readonly="true" /></th>
				</tr>
				<tr>
					<th colspan="6" align="right">Total</th>
					<th align="center"><input name="total" type="text" class="right bold font12" id="total" size="10" /></th>
				</tr>
			</table>
			<p>
				<input name="registrar" type="button" id="registrar" value="Registrar Factura Electr&oacute;nica" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
