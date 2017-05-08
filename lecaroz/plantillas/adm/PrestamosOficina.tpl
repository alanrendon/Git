<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Prestamos en Oficina</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/adm/PrestamosOficina.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Prestamos en Oficina</div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n general</th>
        </tr>
        <tr>
          <th align="left" scope="row">N&uacute;mero</th>
          <td colspan="3"><input name="num_proveedor" type="text" class="valid Focus toPosInt center" id="num_proveedor" size="5" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Nombre</th>
          <td colspan="3"><input name="nombre" type="text" class="valid onlyText toUpper cleanText" id="nombre" size="50" maxlength="100" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">R.F.C.</th>
          <td colspan="3"><input name="rfc" type="text" class="valid Focus toRFC toUpper cleanText" id="rfc" size="13" maxlength="13" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo de persona </th>
          <td colspan="3"><input name="tipopersona" type="radio" value="TRUE" />
            F&iacute;sica
            <input name="tipopersona" type="radio" value="FALSE" checked="checked" />
            Moral</td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo de proveedor </th>
          <td colspan="3"><select name="idtipoproveedor" id="idtipoproveedor">
            <!-- START BLOCK : tipo_proveedor -->
			<option value="{id}">{tipo}</option>
			<!-- END BLOCK : tipo_proveedor -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Calle</th>
          <td colspan="3"><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="50" maxlength="200" />
            No. Ext.:
              <input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" size="5" maxlength="20" />
              No. Int.: 
              <input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" size="5" maxlength="20" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Colonia</th>
          <td colspan="3"><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" size="50" maxlength="200" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Localidad</th>
          <td colspan="3"><input name="localidad" type="text" class="valid toText toUpper cleanText" id="localidad" size="50" maxlength="200" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Referencia</th>
          <td colspan="3"><input name="referencia" type="text" class="valid toText toUpper cleanText" id="referencia" size="50" maxlength="200" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
          <td colspan="3"><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" size="50" maxlength="200" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Estado</th>
          <td colspan="3"><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" size="50" maxlength="200" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Pa&iacute;s</th>
          <td colspan="3"><select name="pais" id="pais">
            <!-- START BLOCK : pais -->
			<option value="{pais}"{selected}>{pais}</option>
			<!-- END BLOCK : pais -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">C&oacute;digo postal </th>
          <td colspan="3"><input name="codigo_postal" type="text" class="valid onlyNumbers cleanText" id="codigo_postal" size="5" maxlength="20" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Contacto</th>
          <td colspan="3"><input name="contacto" type="text" id="contacto" size="50" maxlength="100" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Tel&eacute;fono</th>
          <td colspan="3"><input name="telefono1" type="text" class="valid Focus toPhoneNumber" id="telefono1" size="20" maxlength="20" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Tel&eacute;fono</th>
          <td colspan="3"><input name="telefono2" type="text" class="valid Focus toPhoneNumber" id="telefono2" size="20" maxlength="20" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Fax</th>
          <td colspan="3"><input name="fax" type="text" class="valid Focus toPhoneNumber" id="fax" size="20" maxlength="20" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Correo electr&oacute;nico </th>
          <td colspan="3"><input name="email" type="text" class="valid Focus toEmail" id="email" size="50" maxlength="50" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Observaciones</th>
          <td colspan="3"><textarea name="observaciones" cols="50" rows="5" class="valid toText toUpper cleanText" id="observaciones"></textarea></td>
        </tr>
        <tr>
          <td colspan="4" align="left" scope="row">&nbsp;</td>
        </tr>
        <tr>
          <th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de pago </th>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo de documentaci&oacute;n </th>
          <td colspan="3"><input name="tipo_doc" type="radio" value="2" checked="checked" />
            Factura
            <input name="tipo_doc" type="radio" value="1" />
            Remisi&oacute;n</td>
        </tr>
        <tr>
          <th align="left" scope="row">Validar facturas </th>
          <td colspan="3"><input name="verfac" type="checkbox" id="verfac" value="TRUE" checked="checked" />
            Si</td>
        </tr>
        <tr>
          <th align="left" scope="row">Restar a compras </th>
          <td colspan="3"><input name="restacompras" type="checkbox" id="restacompras" value="TRUE" checked="checked" />
            Si</td>
        </tr>
        <tr>
          <th align="left" scope="row">Prioridad</th>

          <td colspan="3"><input name="prioridad" type="radio" value="FALSE" checked="checked" />
            Baja
            <input name="prioridad" type="radio" value="TRUE" />
            Alta</td>
        </tr>
        <tr>
          <th align="left" scope="row">Forma de pago </th>
          <td colspan="3"><input name="idtipopago" type="radio" value="1" checked="checked" />
            Cr&eacute;dito
            <input name="idtipopago" type="radio" value="2" />
            Contado</td>
        </tr>
        <tr>
          <th align="left" scope="row">D&iacute;as de credito </th>
          <td colspan="3"><input name="diascredito" type="text" class="valid Focus toPosInt center" id="diascredito" size="3" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo de pago </th>
          <td colspan="3"><input name="trans" type="radio" value="FALSE" checked="checked" />
          Cheque
          <input name="trans" type="radio" value="TRUE" />
          Transferencia</td>
        </tr>
        <tr>
          <th align="left" scope="row">Para abono a cuenta </th>
          <td colspan="3"><input name="para_abono" type="checkbox" id="para_abono" value="1" checked="checked" />
          Si</td>
        </tr>
        <tr>
          <th align="left" scope="row">Banco </th>
          <td colspan="3"><select name="idbanco" id="idbanco">
		    <option value=""></option>
			<!-- START BLOCK : banco -->
            <option value="{id}">{nombre}</option>
			<!-- END BLOCK : banco -->
          </select>
		  </td>
        </tr>
        <tr>
          <th align="left" scope="row">Sucursal</th>
          <td colspan="3"><input name="sucursal" type="text" class="valid Focus onlyNumbers" id="sucursal" size="4" maxlength="4" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Entidad</th>
          <td colspan="3"><select name="IdEntidad" id="IdEntidad">
            <option value=""></option>
			<!-- START BLOCK : entidad -->
            <option value="{id}">{entidad}</option>
			<!-- END BLOCK : entidad -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Plaza Banxico </th>
          <td colspan="3"><input name="plaza_banxico" type="text" class="valid Focus onlyNumbers" id="plaza_banxico" size="5" maxlength="5" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Referencia</th>
          <td colspan="3"><input name="referencia_bancaria" type="text" class="valid Focus toText" id="referencia_bancaria" size="10" maxlength="10" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Cuenta (11 d&iacute;gitos) </th>
          <td colspan="3"><input name="cuenta" type="text" class="valid Focus onlyNumbers textClean" id="cuenta" size="11" maxlength="11" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">CLABE (18 d&iacute;gitos) </th>
          <td colspan="3"><input name="clabe" type="text" class="valid Focus onlyNumbers textClean" id="clabe" size="18" maxlength="18" /></td>
        </tr>
        <!-- START BLOCK : extra -->
		<tr>
          <td colspan="4" align="left" scope="row">&nbsp;</td>
        </tr>
        <tr>
          <th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de contacto </th>
        </tr>
        <tr>
          <th align="left" scope="row">Contacto</th>
          <td colspan="3"><input name="contacto1" type="text" class="valid toText toUpper cleanText" id="contacto1" size="50" maxlength="255" /></td>
        </tr>
		<tr>
          <th align="left" scope="row">Contacto</th>
          <td colspan="3"><input name="contacto2" type="text" class="valid toText toUpper cleanText" id="contacto2" size="50" maxlength="255" /></td>
        </tr>
		<tr>
          <th align="left" scope="row">Contacto</th>
          <td colspan="3"><input name="contacto3" type="text" class="valid toText toUpper cleanText" id="contacto3" size="50" maxlength="255" /></td>
        </tr>
		<tr>
          <th align="left" scope="row">Contacto</th>
          <td colspan="3"><input name="contacto4" type="text" class="valid toText toUpper cleanText" id="contacto4" size="50" maxlength="255" /></td>
        </tr>
		<tr>
		  <td colspan="4" align="left" scope="row">&nbsp;</td>
	    </tr>
		<tr>
		  <th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de descuentos </th>
	    </tr>
		<tr>
		  <th align="left" scope="row">Descuento</th>
		  <td><input name="desc1" type="text" class="valid Focus numberPosFormat right" precision="2" id="desc1" size="5" />
		    %</td>
	      <th align="left">Concepto</th>
	      <td><input name="cod_desc1" type="text" class="valid Focus toPosInt center" id="cod_desc1" size="3" />
          <input name="con_desc1" type="text" id="con_desc1" size="30" readonly="true" />
          <input name="tipo_desc1" type="text" disabled="disabled" id="tipo_desc1" size="10" /></td>
		</tr>
		<tr>
		  <th align="left" scope="row">Descuento</th>
		  <td><input name="desc2" type="text" class="valid Focus numberPosFormat right" precision="2" id="desc2" size="5" />
		    %</td>
	      <th align="left">Concepto</th>
	      <td><input name="cod_desc2" type="text" class="valid Focus toPosInt center" id="cod_desc2" size="3" />
          <input name="con_desc2" type="text" id="con_desc2" size="30" readonly="true" />
          <input name="tipo_desc2" type="text" disabled="disabled" id="tipo_desc2" size="10" /></td>
		</tr>
		<tr>
		  <th align="left" scope="row">Descuento</th>
		  <td><input name="desc3" type="text" class="valid Focus numberPosFormat right" precision="2" id="desc3" size="5" />
		    %</td>
	      <th align="left">Concepto</th>
	      <td><input name="cod_desc3" type="text" class="valid Focus toPosInt center" id="cod_desc3" size="3" />
          <input name="con_desc3" type="text" id="con_desc3" size="30" readonly="true" />
          <input name="tipo_desc3" type="text" disabled="disabled" id="tipo_desc3" size="10" /></td>
		</tr>
		<tr>
		  <th align="left" scope="row">Descuento</th>
		  <td><input name="desc4" type="text" class="valid Focus numberPosFormat right" precision="2" id="desc4" size="5" />
		    %</td>
	      <th align="left">Concepto</th>
	      <td><input name="cod_desc4" type="text" class="valid Focus toPosInt center" id="cod_desc4" size="3" />
          <input name="con_desc4" type="text" id="con_desc4" size="30" readonly="true" />
          <input name="tipo_desc4" type="text" disabled="disabled" id="tipo_desc4" size="10" /></td>
		</tr>
		<!-- END BLOCK : extra -->
      </table>
      <br />
      <p>
        <input name="alta" type="button" class="boton" id="alta" value="Alta" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>