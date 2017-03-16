<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Factura Electr&oacute;nica para Clientes (con consignatario)</title>
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
<script type="text/javascript" src="jscripts/fac/FacturaElectronicaConsignatario.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Factura Electr&oacute;nica para Clientes (con consignatario) </div>
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
          <td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" readonly="true" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Tipo de pago</th>
          <td><select name="tipo_pago" id="tipo_pago">
              <!-- <option value="4" selected="selected"></option> -->
              <option value="B">EFECTIVO</option>
              <option value="1">TRANSFERENCIA BANCARIA</option>
              <option value="2">CHEQUE</option>
              <option value="K">TARJETA DE CREDITO</option>
              <option value="V">MONEDERO ELECTRONICO</option>
              <option value="W">DINERO ELECTRONICO</option>
              <option value="X">VALES DE DESPENSA</option>
              <option value="Y">TARJETA DE DEBITO</option>
              <option value="Z">TARJETA DE SERVICIOS</option>
              <option value="NA">N/A</option>
              <!-- <option value="5">NO IDENTIFICADO</option> -->
            </select></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Cuenta de pago</th>
          <td><input name="cuenta_pago" type="text" class="valid Focus onlyNumbers" id="cuenta_pago" size="20" maxlength="20" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Condiciones de pago</th>
          <td><select name="condiciones_pago" id="condiciones_pago">
              <option value="0" selected="selected"></option>
              <option value="1">CONTADO</option>
              <option value="2">CREDITO</option>
            </select></td>
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
        <tr>
          <td colspan="2" align="left" scope="row">&nbsp;</td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Datos del consignatario </th>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Nombre</th>
          <td><input name="nombre_consignatario" type="text" class="valid toText toUpper cleanText" id="nombre_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">R.F.C.</th>
          <td><input name="rfc_consignatario" type="text" class="valid Focus toRFC toUpper" id="rfc_consignatario" size="13" maxlength="13" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Calle</th>
          <td><input name="calle_consignatario" type="text" class="valid toText toUpper cleanText" id="calle_consignatario" size="35" maxlength="100" />
            No. Ext.:
            <input name="no_exterior_consignatario" type="text" class="valid toText toUpper cleanText" id="no_exterior_consignatario" size="5" maxlength="20" />
            No. Int.:
            <input name="no_interior_consignatario" type="text" class="valid toText toUpper cleanText" id="no_interior_consignatario" size="5" maxlength="20" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Colonia</th>
          <td><input name="colonia_consignatario" type="text" class="valid toText toUpper cleanText" id="colonia_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Localidad</th>
          <td><input name="localidad_consignatario" type="text" class="valid toText toUpper cleanText" id="localidad_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Referencia</th>
          <td><input name="referencia_consignatario" type="text" class="valid toText toUpper cleanText" id="referencia_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
          <td><input name="municipio_consignatario" type="text" class="valid toText toUpper cleanText" id="municipio_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Estado</th>
          <td><input name="estado_consignatario" type="text" class="valid toText toUpper cleanText" id="estado_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">Pa&iacute;s</th>
          <td><input name="pais_consignatario" type="text" class="valid toText toUpper cleanText" id="pais_consignatario" style="width:98%;" maxlength="100" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">C&oacute;digo postal </th>
          <td><input name="codigo_postal_consignatario" type="text" class="valid onlyNumbers" id="codigo_postal_consignatario" size="5" maxlength="20" /></td>
        </tr>
      </table>
      <br />
      <table class="tabla_captura">
        <tr>
          <th colspan="8" align="left" scope="col"><img src="imagenes/info.png" width="16" height="16" /> Datos de factura </th>
        </tr>
        <tr>
          <th scope="col">Descripci&oacute;n</th>
          <th scope="col">Cantidad</th>
          <th scope="col">Precio</th>
          <th scope="col">Unidad</th>
          <th scope="col">N&uacute;mero de<br />
            Pedimento</th>
          <th scope="col">Fecha de<br />
            Entrada</th>
          <th scope="col">Aduana de<br />
            Entrada</th>
          <th scope="col">Importe</th>
        </tr>
        <tbody id="Conceptos">
          <tr>
            <td align="center"><input name="descripcion[]" type="text" class="valid toText toUpper cleanText" id="descripcion" size="30" maxlength="100" /></td>
            <td align="center"><input name="cantidad[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad" size="5" /></td>
            <td align="center"><input name="precio[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="precio" size="8" /></td>
            <td align="center"><input name="unidad[]" type="text" class="valid onlyText toUpper cleanText" id="unidad" size="10" maxlength="50" /></td>
            <td align="center"><input name="numero_pedimento[]" type="text" class="valid Focus onlyNumbers" id="numero_pedimento" size="15" maxlength="15" /></td>
            <td align="center"><input name="fecha_entrada[]" type="text" class="valid Focus toDate" id="fecha_entrada" size="10" maxlength="10" /></td>
            <td align="center"><input name="aduana_entrada[]" type="text" class="valid toText cleanText toUpper" id="aduana_entrada" size="30" maxlength="100" /></td>
            <td align="center">
              <input name="descuento_row[]" type="hidden" id="descuento_row" />
              <input name="importe[]" type="text" class="right" id="importe" size="10" readonly="true" />
            </td>
          </tr>
        </tbody>
        <tr>
          <th colspan="7" align="right">Subtotal</th>
          <th align="center"><input name="subtotal" type="text" class="right bold font12" id="subtotal" size="10" readonly="true" /></th>
        </tr>
        <tr>
          <th colspan="7" align="right">Descuento del
            <input name="porcentaje_descuento" type="text" class="valid Focus numberPosFormat right" precision="2" id="porcentaje_descuento" size="5" />
            %</th>
          <th align="center"><input name="descuento" type="text" class="right bold font12" id="descuento" size="10" readonly="readonly" /></th>
        </tr>
        <tr>
          <th colspan="7" align="right"><input name="aplicar_iva" type="checkbox" id="aplicar_iva" value="16" />
            I.V.A</th>
          <th align="center"><input name="iva" type="text" class="right bold font12" id="iva" size="10" readonly="true" /></th>
        </tr>
        <tr>
          <th colspan="7" align="right">Total</th>
          <th align="center"><input name="total" type="text" class="right bold font12" id="total" size="10" readonly="readonly" /></th>
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
