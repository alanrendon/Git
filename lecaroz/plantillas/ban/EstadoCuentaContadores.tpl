<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Estado de cuenta</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/ban/EstadoCuentaContadores.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
}
.logo_banco {
	padding: 0px 0px 0px 18px;
	background-repeat: no-repeat;
}
.logo_banco_1 {
	background-image: url(imagenes/Banorte16x16.png);
}
.logo_banco_2 {
	background-image: url(imagenes/Santander16x16.png);
}
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Estado de cuenta</div>
	<div id="captura" align="center">
		<form name="inicio" class="FormValidator" id="inicio">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" scope="col">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold">Compañía(s)</td>
						<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Banco</td>
						<td><select name="banco" id="banco" class="logo_banco">
								<option value="" class="logo_banco"></option>
								<option value="1" class="logo_banco logo_banco_1">BANORTE</option>
								<option value="2" class="logo_banco logo_banco_2">SANTANDER</option>
							</select></td>
					</tr>
					<tr>
						<td class="bold">Periodo</td>
						<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
							al
							<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
					</tr>
					<tr>
						<td class="bold">Conciliado</td>
						<td><input name="conciliado1" type="text" class="validate focus toDate center" id="conciliado1" size="10" maxlength="10" />
							al
							<input name="conciliado2" type="text" class="validate focus toDate center" id="conciliado2" size="10" maxlength="10" /></td>
					</tr>
					<tr>
						<td class="bold">Opciones</td>
						<td><input name="depositos" type="checkbox" id="depositos" value="1" checked="checked" />
							<span class="bold blue">Depositos</span><br />
							<input name="cargos" type="checkbox" id="cargos" value="1" checked="checked" />
							<span class="bold red">Cargos</span><br />
							<br />
							<input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
							<span class="bold green">Pendientes</span><br />
							<input name="conciliados" type="checkbox" id="conciliados" value="1" checked="checked" />
							<span class="bold orange">Conciliados</span></td>
					</tr>
					<tr>
						<td class="bold">Proveedor(es)</td>
						<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Folio(s)</td>
						<td><input name="folios" type="text" class="validate toInterval" id="folios" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Gasto(s)</td>
						<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Importe(s) o rango(s)</td>
						<td><input name="importes" type="text" class="validate toIntervalFloats" id="importes" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Código(s)</td>
						<td><select name="codigos[]" size="10" multiple="multiple" id="codigos" style="width:100%;">
							</select></td>
					</tr>
					<tr>
						<td class="bold">Concepto</td>
						<td><input name="concepto" type="text" class="validate toText cleanText toUpper" id="concepto" size="40" maxlength="1000" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<p>
				<input type="button" name="consultar" id="consultar" value="Consultar" />
			</p>
		</form>
	</div>
</div>
<div id="modificar_wrapper" style="display:none;">
	<form action="" method="get" name="modificar" class="FormValidator" id="modificar">
		<input name="id" type="hidden" id="id" value="{id}" />
		<table class="table">
			<thead>
				<tr>
					<th colspan="2" align="left" scope="col"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Información del movimiento</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold">Compañía</td>
					<td><input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" />
						<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="30" />
						<input name="cuenta_cia" type="text" disabled="disabled" id="cuenta_cia" size="10" /></td>
				</tr>
				<tr>
					<td class="bold">Acreditado a</td>
					<td><input name="num_cia_sec" type="text" class="validate focus toPosInt right" id="num_cia_sec" size="3" />
						<input name="nombre_cia_sec" type="text" disabled="disabled" id="nombre_cia_sec" size="30" />
						<input name="cuenta_cia_sec" type="text" disabled="disabled" id="cuenta_cia_sec" size="10" /></td>
				</tr>
				<tr>
					<td class="bold">Banco</td>
					<td><select name="banco" class="logo_banco" id="banco">
							<option value="1" class="logo_banco logo_banco_1">BANORTE</option>
							<option value="2" class="logo_banco logo_banco_2">SANTANDER</option>
						</select></td>
				</tr>
				<tr>
					<td class="bold">Fecha</td>
					<td><input name="fecha" type="text" class="validate focus toDate center green" id="fecha" size="10" maxlength="10" /></td>
				</tr>
				<tr>
					<td class="bold">Conciliado</td>
					<td class="orange"><input name="conciliado" type="text" class="center orange" id="conciliado" size="10" maxlength="10" readonly="readonly" /></td>
				</tr>
				<tr>
					<td class="bold">Importe</td>
					<td><input type="hidden" name="tipo_mov" id="tipo_mov" />
						<input name="importe" type="text" class="validate focus numberPosFormat right" precision="2" id="importe" size="12" /></td>
				</tr>
				<tr>
					<td class="bold">Código</td>
					<td><select name="cod_mov" id="cod_mov">
						</select></td>
				</tr>
				<tr>
					<td class="bold">Concepto</td>
					<td><input name="concepto" type="text" id="concepto" style="width:100%;" size="40" maxlength="1000" /></td>
				</tr>
				<tr>
					<td colspan="2" class="bold">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" class="bold"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Información de cheque/transferencia</th>
				</tr>
				<tr>
					<td class="bold">Folio</td>
					<td><input name="folio" type="text" class="right" id="folio" size="10" readonly="readonly" /></td>
				</tr>
				<tr>
					<td class="bold">Beneficiario</td>
					<td id="beneficiario">&nbsp;</td>
				</tr>
				<tr>
					<td class="bold">Gasto</td>
					<td id="beneficiario"><input name="gasto" type="text" class="validate focus toPosInt right" id="gasto" size="3" />
						<input name="descripcion_gasto" type="text" disabled="disabled" id="descripcion_gasto" size="40" /></td>
				</tr>
				<tr>
					<td colspan="2" class="bold">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" class="bold"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Información de arrendamiento</th>
				</tr>
				<tr>
					<td class="bold">Arrendatario</td>
					<td><select name="arrendatario" id="arrendatario">
						</select></td>
				</tr>
				<tr>
					<td class="bold">Recibo de arrendamiento</td>
					<td><select name="recibo_renta" id="recibo_renta">
						</select></td>
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
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
