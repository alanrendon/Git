<!-- tabla proveedores menu facturas y proveedores -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form1" method="post" action="">
  <table class="tabla">
    <tr>
      <th class="vtabla">Numero de proveedor </th>
      <td class="vtabla"><input name="textfield" type="text" size="5" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre</th>
      <td class="vtabla"><input name="textfield2" type="text" class="insert" size="40"></td>
    </tr>
    <tr>
      <th class="vtabla">Direccion (1) </th>
      <td class="vtabla"><input name="textfield3" type="text" class="insert" size="60"></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo</th>
      <td class="vtabla"><p>
        <label>
        <input name="RadioGroup1" type="radio" value="radio" checked>
  Credito</label>
     
        <label>
        <input type="radio" name="RadioGroup1" value="radio">
  Contado</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Porcentaje descuento autorizado</th>
      <td class="vtabla"><input name="textfield8" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Dias de credito autorizado</th>
      <td class="vtabla"><input name="textfield9" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Telefonos</th>
      <td class="vtabla"><input name="textfield7" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Resta a compras </th>
      <td class="vtabla"><p>
        <label>
        <input name="RadioGroup2" type="radio" value="radio" checked>
  Si</label>
        
        <label>
        <input type="radio" name="RadioGroup2" value="radio">
  No</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Proveedor u otros </th>
      <td class="vtabla"><select name="select" size="1">
        <option value="0">0-Proveedor</option>
        <option value="1">1-Otros</option>
        <option value="2">2-Proveedor empaque</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Tiempo entrega mercancia </th>
      <td class="vtabla"><input name="textfield10" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo persona </th>
      <td class="vtabla"><p>
        <label>
        <input name="RadioGroup3" type="radio" value="radio" checked>
  Moral</label>
       
        <label>
        <input type="radio" name="RadioGroup3" value="radio">
  Fisica</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Clave bancaria estandar </th>
      <td class="vtabla"><input name="textfield11" type="text" class="insert"></td>
    </tr>
  </table>
</form>
</td>
</tr>
</table>