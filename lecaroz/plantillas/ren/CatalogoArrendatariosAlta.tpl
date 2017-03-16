<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
  <table class="tabla_captura">
    <tr class="linea_off">
      <th align="left" scope="row">Inmobiliaria</th>
      <td><input type="hidden" name="idarrendador" id="idarrendador" />
        <input name="arrendador" type="text" class="valid Focus toPosInt center" id="arrendador" size="3" />
        <input name="nombre_arrendador" type="text" disabled="disabled" id="nombre_arrendador" size="60" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Local</th>
      <td><select name="idlocal" id="idlocal">
        </select>
        <input type="hidden" name="tipo" id="tipo" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Bloque</th>
      <td><input name="bloque" type="radio" value="1" checked="checked" />
        Interno
        <input name="bloque" type="radio" value="2" />
        Externo</td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Alias</th>
      <td><input name="alias_arrendatario" type="text" class="valid toText cleanText toUpper" id="alias_arrendatario" size="65" maxlength="100" /></td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n del local</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Tipo</th>
      <td align="left" id="tipo_local" scope="row">&nbsp;</td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Domicilio</th>
      <td align="left" id="domicilio_local" scope="row">&nbsp;</td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Superficie (m&#178;)</th>
      <td align="left" id="superficie_local" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n del arrendatario</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Nombre</th>
      <td><input name="nombre_arrendatario" type="text" class="valid toText cleanText toUpper" id="nombre_arrendatario" size="65" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">R.F.C.</th>
      <td><input name="rfc" type="text" class="valid toRFC toUpper" id="rfc" size="13" maxlength="13" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Tipo de persona</th>
      <td><input name="tipo_persona" type="radio" value="FALSE" checked="checked" />
        F&iacute;sica
        <input type="radio" name="tipo_persona" value="TRUE" />
        Moral</td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Domicilio fiscal</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Calle</th>
      <td><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="40" maxlength="100" />
        No. Ext.:
        <input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" size="5" maxlength="50" />
        No. Int.:
        <input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" size="5" maxlength="50" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Colonia</th>
      <td><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
      <td><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Estado</th>
      <td><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Pais</th>
      <td><input name="pais" type="text" class="valid toText toUpper cleanText" id="pais" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">C&oacute;digo postal</th>
      <td><input name="codigo_postal" type="text" class="valid onlyNumbers" id="codigo_postal" size="5" maxlength="20" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de contacto</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Contacto</th>
      <td><input name="contacto" type="text" class="valid toText cleanText toUpper" id="contacto" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Tel&eacute;fono 1</th>
      <td><input name="telefono1" type="text" class="valid Focus toPhoneNumber" id="telefono1" size="20" maxlength="20" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Tel&eacute;fono 2</th>
      <td><input name="telefono2" type="text" class="valid Focus toPhoneNumber" id="telefono2" size="20" maxlength="20" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Correo electr&oacute;nico</th>
      <td><input name="email" type="text" class="valid Focus toEmail" id="email" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Correo electr&oacute;nico</th>
      <td><input name="email2" type="text" class="valid Focus toEmail" id="email2" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Correo electr&oacute;nico</th>
      <td><input name="email3" type="text" class="valid Focus toEmail" id="email3" size="40" maxlength="100" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de contrato</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Giro de la empresa</th>
      <td><input name="giro" type="text" class="valid toText cleanText toUpper" id="giro" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Representante</th>
      <td><input name="representante" type="text" class="valid toText cleanText toUpper" id="representante" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Fianza</th>
      <td><input name="fianza" type="text" class="valid toText cleanText toUpper" id="fianza" size="40" maxlength="100" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Tipo de fianza</th>
      <td><input name="tipo_fianza" type="text" class="valid toText cleanText toUpper" id="tipo_fianza" size="40" maxlength="200" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Fecha de inicio</th>
      <td><input name="fecha_inicio" type="text" class="valid toDate center" id="fecha_inicio" size="10" maxlength="10" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Fecha de t&eacute;rmino</th>
      <td><input name="fecha_termino" type="text" class="valid toDate center" id="fecha_termino" size="10" maxlength="10" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Dep&oacute;sito en garant&iacute;a</th>
      <td>$
        <input name="deposito_garantia" type="text" class="valid Focus numberPosFormat right" precision="2" id="deposito_garantia" size="10" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n y desglose de renta</th>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Tipo de pago</th>
      <td><select name="tipo_pago" id="tipo_pago">
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
          <!-- <option value="5" selected="selected">NO IDENTIFICADO</option> -->
        </select></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Cuenta de pago</th>
      <td><input name="cuenta_pago" type="text" class="valid Focus onlyNumbers" id="cuenta_pago" size="16" maxlength="16" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Recibo mensual</th>
      <td><input name="recibo_mensual" type="radio" value="TRUE" checked="checked" />
        Si
        <input type="radio" name="recibo_mensual" value="FALSE" />
        No</td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Incremento anual</th>
      <td><input name="incremento_anual" type="radio" value="TRUE" checked="checked" />
        Si
        <input type="radio" name="incremento_anual" value="FALSE" />
        No</td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Porcentaje de incremento</th>
      <td>%
        <input name="porcentaje_incremento" type="text" class="valid Focus numberPosFormat right" precision="2" id="porcentaje_incremento" size="5" maxlength="5" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Renta</th>
      <td>$
        <input name="renta" type="text" class="valid Focus numberPosFormat right blue" id="renta" style="width:150px;" size="14" precision="2" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Mantenimiento</th>
      <td>$
        <input name="mantenimiento" type="text" class="valid Focus numberPosFormat right blue" id="mantenimiento" style="width:150px;" size="14" precision="2" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Subtotal</th>
      <td>$
        <input name="subtotal" type="text" class="right bold blue" id="subtotal" style="width:150px;" size="14" readonly="readonly" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">I.V.A.</th>
      <td>$
        <input name="iva" type="text" class="right bold blue" id="iva" style="width:150px;" size="14" readonly="readonly" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Agua</th>
      <td>$
        <input name="agua" type="text" class="valid Focus numberPosFormat right blue" precision="2" id="agua" style="width:150px;" size="14" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row"><input name="aplicar_retenciones" type="checkbox" id="aplicar_retenciones" value="1" />
        Retenci&oacute;n de I.V.A.</th>
      <td>$
        <input name="retencion_iva" type="text" class="right bold red" id="retencion_iva" style="width:150px;" size="14" readonly="readonly" /></td>
    </tr>
    <tr class="linea_on">
      <th align="left" scope="row">Retenci&oacute;n de I.S.R.</th>
      <td>$
        <input name="retencion_isr" type="text" class="right bold red" id="retencion_isr" style="width:150px;" size="14" readonly="readonly" /></td>
    </tr>
    <tr class="linea_off">
      <th align="left" scope="row">Total</th>
      <td>$
        <input name="total" type="text" class="right bold blue font14" id="total" style="width:150px;" size="14" readonly="readonly" /></td>
    </tr>
  </table>
  <p>
    <input type="button" name="regresar" id="regresar" value="Regresar" />
    &nbsp;&nbsp;
    <input type="button" name="alta" id="alta" value="Alta" />
  </p>
</form>
