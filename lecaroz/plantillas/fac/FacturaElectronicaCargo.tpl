<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Factura Electr&oacute;nica para Clientes</title>

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
<script type="text/javascript" src="jscripts/fac/FacturaElectronicaCargo.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Factura Electr&oacute;nica de Cargo</div>
  <div id="captura" align="center">
    <form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
          <input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="61" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Fecha</th>
          <td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
        </tr>
        <tr>
        	<td colspan="2" align="left" scope="row">&nbsp;</td>
        	</tr>
        <tr>
          <th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Datos del cliente </th>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Nombre</th>
          <td><input name="nombre_cliente" type="text" class="valid toText toUpper cleanText" id="nombre_cliente" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">R.F.C.</th>
          <td><input name="rfc" type="text" class="valid Focus toRFC toUpper" id="rfc" size="13" maxlength="13" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Calle</th>
          <td><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="35" maxlength="100" /> 
            No. Ext.: 
            <input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" size="5" maxlength="20" /> 
            No. Int.: 
            <input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" size="5" maxlength="20" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Colonia</th>
          <td><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Localidad</th>
          <td><input name="localidad" type="text" class="valid toText toUpper cleanText" id="localidad" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Referencia</th>
          <td><input name="referencia" type="text" class="valid toText toUpper cleanText" id="referencia" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
          <td><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Estado</th>
          <td><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Pa&iacute;s</th>
          <td><input name="pais" type="text" class="valid toText toUpper cleanText" id="pais" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">C&oacute;digo postal </th>
          <td><input name="codigo_postal" type="text" class="valid onlyNumbers" id="codigo_postal" size="5" maxlength="20" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Correo electr&oacute;nico </th>
          <td><input name="email_cliente" type="text" class="valid Focus toEmail" id="email_cliente" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Observaciones</th>
          <td><textarea name="observaciones" cols="50" rows="5" class="valid toText toUpper cleanText" id="observaciones" style="width:98%;"></textarea></td>
        </tr>
      </table>
      <br />
      <table class="tabla_captura">
		<tr>
          <th align="left" scope="col"><img src="imagenes/info.png" width="16" height="16" /> Datos de factura </th>
          <th align="left" scope="col">&nbsp;</th>
        </tr>
        <tr>
          <th scope="col">Descripci&oacute;n</th>
          <th scope="col">I.V.A.</th>
        </tr>
		<tbody id="Conceptos">
        <tr>
          <td align="center"><input name="descripcion[]" type="text" class="valid toText toUpper cleanText" id="descripcion" size="80" maxlength="200" /></td>
          <td align="center"><input name="iva[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="iva" size="10" /></td>
        </tr>
		</tbody>
        <tr>
        	<th align="right">I.V.A 
        		<input name="piva" type="text" class="valid Focus numberPosFormat right" precision="2" id="piva" value="1.00" size="5" maxlength="5" />
        		%</th>
        	<th align="center"><input name="total_iva" type="text" class="right bold font12" id="total_iva" size="10" readonly="true" /></th>
        	</tr>
        <tr>
          <th align="right">Total</th>
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