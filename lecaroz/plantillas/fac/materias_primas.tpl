
<!-- tabla materiaprima menu provedores y facturas -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="catalogomatprima" method="post" action="insercion.php?tabla=catalogomatprima">
<table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo de materia prima</th>
      <td class="vtabla"><input type="text" name="0" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre materia prima </th>
      <td><input name="1" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Unidad consumo </th>
      <td class="vtabla"><select name="2" size="1">
        <option value="1">1-pieza</option>
        <option value="2">2-kilo</option>
        <option value="3">3-litro</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo</th>
      <td class="vtabla"><select name="3" size="1">
        <option value="1">1-Materia Prima</option>
        <option value="2">2-Material empaque</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Materia prima controlada </th>
      <td class="vtabla"><p>
        <label>
        <input name="4" type="radio" value="TRUE" checked>
  Si</label>

        <label>
        <input type="radio" name="4" value="FALSE">
  No</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Presentaci&oacute;n</th>
      <td class="vtabla"><select name="5" size="1">
        <option value="1">1-pieza</option>
        <option value="2">2-kilo</option>
        <option value="3">3-litro</option>
        <option value="4">4-bulto</option>
        <option value="5">5-caja</option>
        <option value="6">6-cubeta</option>
        <option value="7">7-millar</option>
        <option value="8">8-balon</option>
        <option value="9">9-bolsa</option>
        <option value="10">10-garrafon</option>
        <option value="11">11-frasco</option>
        <option value="12">12-atado</option>
        <option value="13">13-paquete</option>
        <option value="14">14-rollo</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Proceso autom&aacute;tico</th>
      <td class="vtabla"><p>
        <label>
        <input name="6" type="radio" value="TRUE" checked>
  Si</label>
        
        <label>
        <input type="radio" name="6" value="FALSE">
  No</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">% de incremento al promedio </th>
      <td class="vtabla"><input name="7" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de entregas para el pedido de fin de mes </th>
      <td><input name="8" type="text" class="insert"></td>
    </tr>
</table>
<br>
<input type="submit" value="Enviar">
</form>
</td>
</tr>
</table>