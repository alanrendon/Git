<form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
      <tr>
        <th scope="row">Banco</th>
      </tr>
	  <tr>
        <td class="linea_off" style="font-size:14pt;font-weight:bold;"><input name="banco" type="hidden" id="banco" value="{banco}" />
        {nombre_banco}</td>
      </tr>
	  </tr>
  </table>
  <br />
  <table class="tabla_captura">
      <tr class="linea_off">
        <th height="19" align="left" scope="row">Generar Bonificaciones </th>
        <td align="left" scope="row"><input name="bonificaciones" type="checkbox" class="checkbox" id="bonificaciones" value="1" />
          Si</td>
      </tr>
  </table>
	<br />
	<table class="tabla_captura">
      <tr>
        <th scope="col">C&oacute;digo en banco </th>
        <th scope="col">Asociar con </th>
        <th scope="col">Concepto</th>
        <th scope="col">Tipo de movimiento </th>
      </tr>
      <!-- START BLOCK : cod_banco -->
	  <tr id="row" class="linea_{color}">
        <td><input name="cod_banco[]" type="hidden" id="cod_banco" value="{cod_banco}" />
        {cod_banco} {concepto} </td>
        <td><select name="cod_mov[]" id="cod_mov" style="width:100%;">
		  <!-- START BLOCK : cod_mov -->
          <option value="{cod}"{selected}>{cod} {concepto}</option>
		  <!-- END BLOCK : cod_mov -->
        </select>        </td>
        <td align="center"><input name="concepto[]" type="text" class="cap toText toUpper clean" id="concepto" size="30" maxlength="1000"></td>
        <td align="center"><select name="tipo_bonificacion[]" id="tipo_bonificacion">
          <!-- START BLOCK : cod_banco_abono -->
		  <option value="-1" selected="selected">NO APLICA</option>
		  <!-- END BLOCK : cod_banco_abono -->
		  <!-- START BLOCK : cod_banco_cargo -->
		  <option value="35" selected="selected">COMISION</option>
          <option value="34">I.V.A.</option>
		  <!-- END BLOCK : cod_banco_cargo -->
        </select>
		</td>
      </tr>
	  <!-- END BLOCK : cod_banco -->
  </table>
    <br />
    <table class="tabla_captura">
      <tr>
	    <th colspan="4" align="left" style="font-size:8pt;" scope="col"><input name="checkall" type="checkbox" class="checkbox" id="checkall" checked="checked" />
        Seleccionar todo </th>
      </tr>
	  <!-- START BLOCK : cia -->
	  <tr>
        <th colspan="4" align="left" style="font-size:12pt;" scope="col">{num_cia} {nombre_cia} </th>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <th>Fecha</th>
        <th>Concepto</th>
        <th>Importe</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr id="row" class="linea_{color}">
        <td><input name="id[]" type="checkbox" class="checkbox" id="id" value="{id}" checked="checked" /></td>
        <td align="center">{fecha}</td>
        <td>{cod_banco} {concepto}</td>
        <td align="right" style="color:{color}">{importe}</td>
      </tr>
	  <!-- END BLOCK : row -->
      <tr>
        <th colspan="3" align="right">Total Depositos </th>
        <th align="right" style="font-size:12pt;color:#00C;">{total_depositos}</th>
      </tr>
      <tr>
        <th colspan="3" align="right">Total Comisiones </th>
        <th align="right" style="font-size:12pt;color:#C00;">{total_comisiones}</th>
      </tr>
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
    </table>
    <p>
      <input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" /> 
      &nbsp;&nbsp;
      <input name="conciliar" type="button" class="boton" id="conciliar" value="Conciliar" />
    </p>
	</form>