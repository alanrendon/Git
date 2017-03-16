<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un numero para el proveedor');
			document.form.campo0.select();
		}
		else if(document.form.campo1.value == "") {
			alert('Debe especificar un nombre para el proveedor');
			document.form.campo1.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.campo0.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.campo0.select();
		}
		else
			document.form.campo0.select();
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./alta_catalogos.php?tabla={tabla}" method="post" name="form" id="form" onKeyDown="if (event.keyCode == 13) valida_registro();">
<table class="tabla">
      <caption class="tabla">Datos del Proveedor</caption>
      <tr>
        <th class="vtabla">N&uacute;mero proveedor</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo0" type="text" class="insert" size="5" maxlength="5" value="{id}">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Nombre</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo1" type="text" class="insert" size="50" maxlength="100">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Direcci&oacute;n</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <textarea name="campo2" cols="50" rows="3" wrap="VIRTUAL" class="insert"></textarea>
        </td>
      </tr>
            <tr>
        <th class="vtabla">Tel&eacute;fono 1</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo6" type="text" class="insert" id="campo6" size="20" maxlength="20">
        </td>
      </tr>
	  <tr>
        <th class="vtabla">Tel&eacute;fono 2</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo7" type="text" class="insert" id="campo7" size="20" maxlength="20">
        </td>
      </tr>
	  <tr>
        <th class="vtabla">Fax</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo8" type="text" class="insert" id="campo8" size="20" maxlength="20">
        </td>
      </tr>
	  <tr>
        <th class="vtabla">e-m@il</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo9" type="text" class="insert" id="campo9" size="50" maxlength="50">
        </td>
      </tr>
      <tr>
        <th class="vtabla">R.F.C.</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo3" type="text" class="insert" size="13" maxlength="17">
        </td>
      </tr>
      <tr>
        <td class="tabla" colspan="2">
          <strong>Prioridad del Proveedor</strong><br>
		  <label>
          <input name="campo20" type="radio" value="true" checked>
  Alta</label>&nbsp;&nbsp;
          
          <label>
          <input type="radio" name="campo20" value="false">
  Baja</label>        </td>
        
      </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos de Cuenta Bancaria</caption>
    <tr>
      <th class="vtabla" colspan="2">Pago Interbancario </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');" colspan="2">
	            <p>
            <label>
            <input name="campo19" type="radio" value="0" checked>
  No</label>
           
            <label>
            <input name="campo19" type="radio" value="1">
  Si</label>
            <br>
          </p>
	  </td>
    </tr>
	<tr>
      <th class="vtabla">Banco</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo14" class="insert" id="campo14">
          <option value="{valuebanco}" selected>{idbanco} - {namebanco}</option>
		  <!-- START BLOCK : banco -->
          <option value="{valuebanco}">{idbanco} - {namebanco}</option>
          <!-- END BLOCK : banco -->
        </select>
      </td>
      <th class="vtabla">CLABE</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo15" type="text" class="insert" id="campo15" size="3" maxlength="3">
      |<input name="campo16" type="text" class="insert" id="campo16" size="3" maxlength="3">
      |<input name="campo17" type="text" class="insert" id="campo17" size="11" maxlength="11">
      |<input name="campo18" type="text" class="insert" id="campo18" size="1" maxlength="1">      </tr>
	<tr>
	  <th class="vtabla" colspan="2">Para abono en cuenta</th>

	  <td class="vtabla" colspan="2"><p>
	    <label>
	    <input name="campo21" type="radio" value="true" checked>
  Si</label>

	    <label>
	    <input type="radio" name="campo21" value="false">
  No</label>
	    <br>
	    </p></td>

	  </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">
    Datos Generales 
    </caption>
      <tr>
        <th class="vtabla">Tipo de pago</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <select name="campo4" class="insert" id="campo4">
            <option value="{valuepago}" selected>{idpago} - {namepago}</option>
            <!-- START BLOCK : pago -->
            <option value="{valuepago}">{idpago} - {namepago}</option>
            <!-- END BLOCK : pago -->
          </select>
        </td>
      </tr>
      <tr>
        <th class="vtabla">D&iacute;as de cr&eacute;dito autorizado</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo5" type="text" class="insert" id="campo5" value="0" size="5" maxlength="5">
        </td>
      </tr>

      <tr>
        <th class="vtabla">Resta a compras</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <p>
            <label>
            <input name="campo10" type="radio" value="0">
  No</label>
           
            <label>
            <input name="campo10" type="radio" value="1" checked>
  Si</label>
            <br>
          </p>
        </td>
      </tr>
      <tr>
        <th class="vtabla">Proveedor u otros</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <select name="campo11" class="insert" id="campo11">
            <option value="{valueproveedor}" selected>{idproveedor} - {nameproveedor}</option>
            <!-- START BLOCK : proveedor -->
            <option value="{valueproveedor}">{idproveedor} - {nameproveedor}</option>
            <!-- END BLOCK : proveedor -->
          </select>
        </td>
      </tr>
      <tr>
        <th class="vtabla">Tiempo entrega mercancia</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
          <input name="campo12" type="text" class="insert" id="campo12" value="0" size="5" maxlength="5">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Tipo de Persona</th>
        <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
            <input name="campo13" type="radio" value="0" checked>&nbsp;Moral&nbsp;&nbsp;<input type="radio" name="campo13" value="1">&nbsp;F&iacute;sica
		</td>
      </tr>
    </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Alta de Proveedor" onclick="valida_registro()"><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
</td>
</tr>
</table>