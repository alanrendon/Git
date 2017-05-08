<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un numero para la compañía');
			document.form.campo0.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?")) {
				if (document.form.homoclave.value > 0)
					document.form.campo27.value = "TRUE";
				document.form.submit();
			}
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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<form action="./alta_catalogos.php?tabla={tabla}" method="post" name="form" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
    <caption class="tabla">Datos de la Compa&ntilde;&iacute;a</caption>
    <tr>
      <th class="vtabla">N&uacute;mero de compa&ntilde;&iacute;a</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo0" type="text" class="insert" size="5" maxlength="5" value="{id}"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre de compa&ntilde;&iacute;a</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo1" type="text" class="insert" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre corto</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo11" type="text" class="insert" size="50" maxlength="25"></td>
    </tr>
    <tr>
      <th class="vtabla">Direcci&oacute;n</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><textarea name="campo2" cols="34" rows="3" wrap="VIRTUAL" class="insert"></textarea></td>
    </tr>
    <tr>
      <th class="vtabla">Tel&eacute;fono</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo6" type="text" class="insert" size="15" maxlength="15"></td>
    </tr>
	<tr>
      <th class="vtabla">e-m@il</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo26" type="text" class="insert" size="50" maxlength="50"></td>
    </tr>

      <th class="vtabla">RFC</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo3" type="text" class="insert" size="15" maxlength="13"></td>
    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos de Cuenta Bancaria</caption>
    <tr>
      <th class="vtabla">Banco</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo25" class="insert" id="campo25">
          <option value="{valuebanco}" selected>{idbanco} - {namebanco}</option>
		  <!-- START BLOCK : banco -->
          <option value="{valuebanco}">{idbanco} - {namebanco}</option>
          <!-- END BLOCK : banco -->
        </select>
      </td>
      <th class="vtabla">CLABE</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <input name="campo21" type="text" class="insert" id="campo21" size="3" maxlength="3">
        |<input name="campo22" type="text" class="insert" id="campo22" size="3" maxlength="3">
        |<input name="campo23" type="text" class="insert" id="campo23" size="11" maxlength="11">
        |<input name="campo24" type="text" class="insert" id="campo24" size="1" maxlength="1">
    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos Generales</caption>
    <tr>
      <th class="vtabla">No. IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo4" type="text" class="insert" size="16" maxlength="15"></td>
      <th class="vtabla">No. cuenta Compa&ntilde;&iacute;a de Luz</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo9" type="text" class="insert" size="13" maxlength="13"></td>
    </tr>
   <tr>
      <th class="vtabla">Delegaci&oacute;n del IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo16" class="insert">
          <option value="{valuedelimss}" selected>{iddelimss} - {namedelimss}</option>
		  <!-- START BLOCK : delimss -->
          <option value="{valuedelimss}">{iddelimss} - {namedelimss}</option>
          <!-- END BLOCK : delimss -->
        </select>
      </td>
      <th class="vtabla">Contrato Recolecci&oacute;n</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo7" type="text" class="insert" size="13" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Sub-Delegaci&oacute;n del IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo19" class="insert">
          <option value="{valuesubdelimss}" selected>{idsubdelimss} - {namesubdelimss}</option>
		  <!-- START BLOCK : subdelimss -->
          <option value="{valuesubdelimss}">{idsubdelimss} - {namesubdelimss}</option>
          <!-- END BLOCK : subdelimss -->
        </select>
      </td>
      <th class="vtabla">Sub-cuenta deudores</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo8" type="text" class="insert" size="13" maxlength="3" value="0"></td>
    </tr>
    <tr>
      <th class="vtabla">No. Infonavit</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo5" type="text" class="insert" size="15" maxlength="15"></td>
      <th class="vtabla">Tipo de persona</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo10" type="radio" value="false" checked>&nbsp;Moral&nbsp;&nbsp;<input name="campo10" type="radio" value="true">&nbsp;F&iacute;sica</td>
    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">C&oacute;digos</caption>
    <tr>
      <th class="vtabla">C&oacute;digo Contador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo15" class="insert">
          <option value="{valuecontador}" selected>{idcontador} - {namecontador}</option>
		  <!-- START BLOCK : contador -->
          <option value="{valuecontador}">{idcontador} - {namecontador}</option>
          <!-- END BLOCK : contador -->
        </select>
      </td>
      <th class="vtabla">C&oacute;digo Administrador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo12" class="insert">
          <option value="{valueadministrador}" selected>{idadministrador} - {nameadministrador}</option>
		  <!-- START BLOCK : administrador -->
          <option value="{valueadministrador}">{idadministrador} - {nameadministrador}</option>
          <!-- END BLOCK : administrador -->
        </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Auditor</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo14" class="insert">
          <option value="{valueauditor}" selected>{idauditor} - {nameauditor}</option>
		  <!-- START BLOCK : auditor -->
          <option value="{valueauditor}">{idauditor} - {nameauditor}</option>
          <!-- END BLOCK : auditor -->
        </select>
      </td>
      <th class="vtabla">C&oacute;digo Aseguradora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo13" class="insert">
          <option value="{valueaseguradora}" selected>{idaseguradora} - {nameaseguradora}</option>
		  <!-- START BLOCK : aseguradora -->
          <option value="{valueaseguradora}">{idaseguradora} - {nameaseguradora}</option>
          <!-- END BLOCK : aseguradora -->
        </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Sindicato</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo18" class="insert">
          <option value="{valuesindicato}" selected>{idsindicato} - {namesindicato}</option>
		  <!-- START BLOCK : sindicato -->
          <option value="{valuesindicato}">{idsindicato} - {namesindicato}</option>
          <!-- END BLOCK : sindicato -->
        </select>
      </td>
    <th class="vtabla">C&oacute;digo Gasolina</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo20" type="text" class="insert" size="10" maxlength="10" value="0"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Operadora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="campo17" class="insert">
          <option value="{valueoperadora}" selected>{idoperadora} - {nameoperadora}</option>
		  <!-- START BLOCK : operadora -->
          <option value="{valueoperadora}">{idoperadora} - {nameoperadora}</option>
          <!-- END BLOCK : operadora -->
        </select>
      </td>
	  <th class="vtabla">Homoclave</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="homoclave" type="text" class="insert" size="5" maxlength="3" value="0"><input type="hidden" name="campo27" value="FALSE"><input type="hidden" name="campo28" value="TRUE"></td>
    </tr>
    <tr>
      <th class="vtabla">Aplica IVA </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo30" type="radio" value="TRUE">
        Si
          <input name="campo30" type="radio" value="FALSE">
          No</td>
      <th class="vtabla">N&uacute;mero Accionista</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo31" type="text" id="campo31" value="0" size="4" maxlength="4"></td>
    </tr>

</table>
<p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Alta de Compa&ntilde;&iacute;a" onclick='valida_registro()'><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>
</td>
</tr>
</table>
