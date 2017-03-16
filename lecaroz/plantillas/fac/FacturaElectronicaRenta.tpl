<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Factura Electr&oacute;nica para Renta</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="../../styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="styles/Popups.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturaElectronicaRenta.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Factura Electr&oacute;nica para Renta</div>
  <div id="captura" align="center">
    <form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
    	<input type="hidden" name="id" id="id" />
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Local</th>
          <td><input name="local" type="text" class="valid Focus toPosInt center" id="local" size="3" />
          <input name="nombre_local" type="text" disabled="disabled" id="nombre_local" size="40" /></td>
        </tr>
        <tr class="linea_on">
        	<th align="left" scope="row">Inmobiliaria</th>
        	<td id="inmobiliaria">&nbsp;</td>
        	</tr>
        <tr class="linea_off">
          <th align="left" scope="row">Arrendatario</th>
          <td id="arrendatario">&nbsp;</td>
        </tr>
        <tr class="linea_on">
        	<td colspan="2" align="left" scope="row">&nbsp;</td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">A&ntilde;o</th>
        	<td><input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
        	</tr>
        <tr class="linea_on">
        	<th align="left" scope="row">Mes</th>
        	<td><select name="mes" id="mes">
        		<option value="1"{1}>ENERO</option>
        		<option value="2"{2}>FEBRERO</option>
        		<option value="3"{3}>MARZO</option>
        		<option value="4"{4}>ABRIL</option>
        		<option value="5"{5}>MAYO</option>
        		<option value="6"{6}>JUNIO</option>
        		<option value="7"{7}>JULIO</option>
        		<option value="8"{8}>AGOSTO</option>
        		<option value="9"{9}>SEPTIEMBRE</option>
        		<option value="10"{10}>OCTUBRE</option>
        		<option value="11"{11}>NOVIEMBRE</option>
        		<option value="12"{12}>DICIEMBRE</option>
        		</select></td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">Concepto</th>
        	<td><input name="concepto" type="text" class="valid toText toUpper cleanText" id="concepto" style="width:98%;" size="40" maxlength="100" /></td>
        	</tr>
        <tr class="linea_on">
        	<td colspan="2" align="left" scope="row">&nbsp;</td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">Renta</th>
        	<td><input name="renta" type="text" class="valid Focus numberPosFormat right blue" precision="2" id="renta" size="12" /></td>
        	</tr>
        <tr class="linea_on">
        	<th align="left" scope="row">Mantenimiento</th>
        	<td><input name="mantenimiento" type="text" class="valid Focus numberPosFormat right blue" precision="2" id="mantenimiento" size="12" /></td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">Subtotal</th>
        	<td><input name="subtotal" type="text" class="right blue bold" id="subtotal" size="12" readonly="readonly" /></td>
        	</tr>
        <tr class="linea_on">
        	<th align="left" scope="row"><input name="aplicar_iva" type="checkbox" id="aplicar_iva" />
        		I.V.A.</th>
        	<td><input name="iva" type="text" class="right blue" id="iva" size="12" readonly="readonly" />
        		<input type="hidden" name="iva_renta" id="iva_renta" />
        		<input type="hidden" name="iva_mantenimiento" id="iva_mantenimiento" /></td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">Agua</th>
        	<td><input name="agua" type="text" class="valid Focus numberPosFormat right blue" precision="2" id="agua" size="12" /></td>
        	</tr>
        <tr class="linea_on">
        	<th align="left" scope="row"><input name="aplicar_retenciones" type="checkbox" id="aplicar_retenciones" value="1" />
        		Retenci&oacute;n I.V.A.</th>
        	<td><input name="retencion_iva" type="text" class="right red" id="retencion_iva" size="12" readonly="readonly" /></td>
        	</tr>
        <tr class="linea_off">
        	<th align="left" scope="row">Retenci&oacute;n I.S.R.</th>
        	<td><input name="retencion_isr" type="text" class="right red" id="retencion_isr" size="12" readonly="readonly" /></td>
        	</tr>
        <tr class="linea_on">
        	<th align="left" scope="row">Total</th>
        	<td><input name="total" type="text" class="right blue bold font12" id="total" size="12" readonly="readonly" /></td>
        	</tr>
      </table>
      <br />
      <p>
      	<input name="registrar" type="button" id="registrar" value="Registrar Factura Electr&oacute;nica" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>