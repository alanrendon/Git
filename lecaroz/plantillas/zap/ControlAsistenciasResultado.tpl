<form name="Datos" class="formulario" id="Datos"><table class="tabla_captura">
      <tr class="linea_off">
        <th align="left">Compa&ntilde;&iacute;a</th>
        <td style="font-size:12pt;font-weight:bold;">{num_cia} {nombre_cia} </td>
      </tr>
      <tr class="linea_on">
        <th align="left">Periodo</th>
        <td style="font-size:12pt;font-weight:bold;">{fecha1} al {fecha2} </td>
      </tr>
    </table>
    <br />
    <table class="tabla_captura">
      <tr>
        <th colspan="2">Empleado</th>
        <!-- START BLOCK : th -->
		<th>{dia_mes}<br />{dia_semana}</th>
		<!-- END BLOCK : th -->
        <th style="color:#00C;">A</th>
        <th style="color:#C00;">F</th>
        <th style="color:#0C0;">I</th>
        <th style="color:#F90;">D</th>
        <th style="color:#60C;">V</th>
        <th>T</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr class="linea_{row_color}">
	    <td align="right">{num_emp}</td>
        <td>{nombre_emp}</td>
        <!-- START BLOCK : td -->
		<td align="center"><select name="status{row}[]" style="font-weight:bold;color:#{color}" id="status{row}" alt="'row':'{row}','idemp':'{idemp}','fecha':'{fecha}'">
          <option value="1" style="color:#00C;font-weight:bold;"{1}>A</option>
          <option value="2" style="color:#C00;font-weight:bold;"{2}>F</option>
          <option value="3" style="color:#0C0;font-weight:bold;"{3}>I</option>
          <option value="4" style="color:#F90;font-weight:bold;"{4}>D</option>
          <option value="5" style="color:#60C;font-weight:bold;"{5}>V</option>
        </select>        </td>
		<!-- END BLOCK : td -->
        <td align="center"><input name="A[]" type="text" class="disabled alignCenter" style="color:#00C;" id="A" value="{1}" size="2"></td>
        <td align="center"><input name="F[]" type="text" class="disabled alignCenter" style="color:#C00;" id="F" value="{2}" size="2"></td>
        <td align="center"><input name="I[]" type="text" class="disabled alignCenter" style="color:#0C0;" id="I" value="{3}" size="2"></td>
        <td align="center"><input name="D[]" type="text" class="disabled alignCenter" style="color:#F90;" id="D" value="{4}" size="2"></td>
        <td align="center"><input name="V[]" type="text" class="disabled alignCenter" style="color:#60C;" id="V" value="{5}" size="2"></td>
        <td align="center"><input name="T[]" type="text" class="disabled alignCenter bold" id="T" value="{T}" size="2"></td>
	  </tr>
	  <!-- END BLOCK : row -->
      <tr>
        <th colspan="{span}">&nbsp;</th>
        <th><input name="TA" type="text" class="disabled alignCenter bold" style="color:#00C;" id="TA" value="{1}" size="2"></th>
        <th><input name="TF" type="text" class="disabled alignCenter bold" style="color:#C00;" id="TF" value="{2}" size="2"></th>
        <th><input name="TI" type="text" class="disabled alignCenter bold" style="color:#0C0;" id="TI" value="{3}" size="2"></th>
        <th><input name="TD" type="text" class="disabled alignCenter bold" style="color:#F90;" id="TD" value="{4}" size="2"></th>
        <th><input name="TV" type="text" class="disabled alignCenter bold" style="color:#60C;" id="TV" value="{5}" size="2"></th>
		<th><input name="TT" type="text" class="disabled alignCenter bold" id="TT" value="{T}" size="2"></th>
      </tr>
    </table>
    <br>
    <table class="tabla_captura">
      <tr class="linea_off">
        <td style="color:#00C;font-weight:bold;" scope="row">A</td>
        <td>Asistencia</td>
      </tr>
      <tr class="linea_on">
        <td style="color:#C00;font-weight:bold;" scope="row">F</td>
        <td>Falta</td>
      </tr>
      <tr class="linea_off">
        <td style="color:#0C0;font-weight:bold;" scope="row">I</td>
        <td>Incapacidad</td>
      </tr>
      <tr class="linea_on">
        <td style="color:#F90;font-weight:bold;" scope="row">D</td>
        <td>Descanso</td>
      </tr>
      <tr class="linea_off">
        <td style="color:#60C;font-weight:bold;" scope="row">V</td>
        <td>Vacaciones</td>
      </tr>
    </table>
    <p>
      <input name="regresar" type="button" class="boton" id="regresar" value="Regresar" />
    </p></form>