<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else {
			document.form.submit();
			return;
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar Compañía</p>
<form name="form" method="get" action="./fac_cia_mod.php">
<table class="tabla">
  <tr>
    <th scope="row" class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onChange="if (this.value == '') return false;
else if (parseInt(this.value) > 0) {
var temp=parseInt(this.value); this.value=temp;}
else this.value='';" size="5" maxlength="3">
    </td>
    </tr>
</table>
<p>
<input type="button" value="Siguiente" class="boton" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : modificar -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar un numero para la compañía');
			document.form.num_cia.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?")) {
				if (parseInt(document.form.homoclave.value) > 0)
					document.form.homo_clave.value = "TRUE";
				else
					document.form.homo_clave.value = "FALSE";
				document.form.submit();
			}
			else
				document.form.num_cia.select();
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
<p class="title">Modificar Compañía</p>
<form action="./fac_cia_mod.php?actualizar=1" method="post" name="form">
<table class="tabla">
    <caption class="tabla">Datos de la Compa&ntilde;&iacute;a</caption>
    <tr>
      <th class="vtabla">N&uacute;mero de compa&ntilde;&iacute;a</th>
      <th class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_cia" type="hidden" value="{num_cia}">{num_cia}</th>
    </tr>
    <tr>
      <th class="vtabla">Nombre de compa&ntilde;&iacute;a</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.nombre_corto.select();
else if (event.keyCode == 38) form.homoclave.select();" value="{nombre}" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre corto</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre_corto" type="text" class="vinsert" id="nombre_corto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.direccion.select();
else if (event.keyCode == 38) form.nombre.select();" value="{nombre_corto}" size="50" maxlength="25"></td>
    </tr>
    <tr>
      <th class="vtabla">Direcci&oacute;n</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><textarea name="direccion" cols="34" rows="3" wrap="VIRTUAL" class="insert" id="direccion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.telefono.select();
	  else if (event.keyCode == 38) form.nombre_corto.select();">{direccion}</textarea></td>
    </tr>
    <tr>
      <th class="vtabla">Tel&eacute;fono</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="telefono" type="text" class="vinsert" id="telefono" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.email.select();
else if (event.keyCode == 38) form.direccion.select();" value="{telefono}" size="15" maxlength="15"></td>
    </tr>
	<tr>
      <th class="vtabla">e-m@il</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="email" type="text" class="vinsert" id="email" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.rfc.select();
else if (event.keyCode == 38) form.telefono.select();" value="{email}" size="50" maxlength="50"></td>
    </tr>
	<tr>
      <th class="vtabla">RFC</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="rfc" type="text" class="vinsert" id="rfc" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.clabe_banco.select();
else if (event.keyCode == 38) form.email.select();" value="{rfc}" size="15" maxlength="13"></td>
    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos de Cuenta Bancaria</caption>
    <tr>
      <th class="vtabla">Banco</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idbancos" class="insert">
		  <!-- START BLOCK : banco -->
          <option value="{idbanco}" {checked}>{idbanco} - {namebanco}</option>
          <!-- END BLOCK : banco -->
        </select>
      </td>
      <th class="vtabla">CLABE</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <input name="clabe_banco" type="text" class="insert" id="clabe_banco" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.no_imss.select();
else if (event.keyCode == 38) form.rfc.select();
else if (event.keyCode == 39) form.clabe_plaza.select();" value="{clabe_banco}" size="3" maxlength="3">
        |<input name="clabe_plaza" type="text" class="insert" id="clabe_plaza" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.no_imss.select();
else if (event.keyCode == 38) form.rfc.select();
else if (event.keyCode == 39) form.clabe_cuenta.select();
else if (event.keyCode == 37) form.clabe_banco.select();" value="{clabe_plaza}" size="3" maxlength="3">
        |<input name="clabe_cuenta" type="text" class="insert" id="clabe_cuenta" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.no_imss.select();
else if (event.keyCode == 38) form.rfc.select();
else if (event.keyCode == 37) form.clabe_plaza.select();
else if (event.keyCode == 39) form.clabe_identificador.select();" value="{clabe_cuenta}" size="11" maxlength="11">
        |<input name="clabe_identificador" type="text" class="insert" id="clabe_identificador" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.no_imss.select();
else if (event.keyCode == 38) form.rfc.select();
else if (event.keyCode == 39) form.no_imss.select();
else if (event.keyCode == 37) form.clabe_cuenta.select();" value="{clabe_identificador}" size="1" maxlength="1"></td>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">Emisor</td>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="emisor" type="text" id="emisor" size="5" maxlength="5" class="insert"></td>
    </tr>
</table>
 <br>
<table class="tabla">
    <caption class="tabla">Datos Generales</caption>
    <tr>
      <th class="vtabla">No. IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_imss" type="text" class="vinsert" id="no_imss" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 39) form.no_cta_cia_luz.select();
else if (event.keyCode == 38 || event.keyCode == 37) form.clabe_banco.select();" value="{no_imss}" size="16" maxlength="15"></td>
      <th class="vtabla">No. cuenta Compa&ntilde;&iacute;a de Luz</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_cta_cia_luz" type="text" class="vinsert" id="no_cta_cia_luz" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.contrato_recoleccion.select();
else if (event.keyCode == 38) form.clabe_banco.select();
else if (event.keyCode == 37) form.no_imss.select();
else if (event.keyCode == 39) form.contrato_recoleccion.select();" value="{no_cta_cia_luz}" size="13" maxlength="13"></td>
    </tr>
        <tr>
      <th class="vtabla">Delegaci&oacute;n del IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="iddelimss" class="insert" id="iddelimss">
		  <!-- START BLOCK : delimss -->
          <option value="{iddelimss}" {checked}>{iddelimss} - {namedelimss}</option>
          <!-- END BLOCK : delimss -->
        </select>
      </td>
      <th class="vtabla">Contrato Recolecci&oacute;n</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="contrato_recoleccion" type="text" class="vinsert" id="contrato_recoleccion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.sub_cuenta_deudores.select();
else if (event.keyCode == 38) form.no_cta_cia_luz.select();
else if (event.keyCode == 37) form.no_cta_cia_luz.select();
else if (event.keyCode == 39) form.sub_cuenta_deudores.select();" value="{contrato_recoleccion}" size="13" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Sub-Delegaci&oacute;n del IMSS</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idsubdelimss" class="insert" id="idsubdelimss">
		  <!-- START BLOCK : subdelimss -->
          <option value="{idsubdelimss}" {checked}>{idsubdelimss} - {namesubdelimss}</option>
          <!-- END BLOCK : subdelimss -->
        </select>
      </td>
      <th class="vtabla">Sub-cuenta deudores</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="sub_cuenta_deudores" type="text" class="vinsert" id="sub_cuenta_deudores" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.cod_gasolina.select();
else if (event.keyCode == 38) form.contrato_recoleccion.select();
else if (event.keyCode == 37) form.contrato_recoleccion.select();
else if (event.keyCode == 39) form.cod_gasolina.select();" value="{sub_cuenta_deudores}" size="13" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla">No. Infonavit</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_infonavit" type="text" class="vinsert" id="no_infonavit" value="{no_infonavit}" size="15" maxlength="15"></td>
      <th class="vtabla">Tipo de persona</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  <!-- START BLOCK : persona_moral -->
	  <input name="persona_fis_moral" type="radio" value="false" checked>
      &nbsp;Moral&nbsp;&nbsp;<input name="persona_fis_moral" type="radio" value="true">
      &nbsp;F&iacute;sica
	  <!-- END BLOCK : persona_moral -->
	  <!-- START BLOCK : persona_fisica -->
	  <input name="persona_fis_moral" type="radio" value="false">
      &nbsp;Moral&nbsp;&nbsp;<input name="persona_fis_moral" type="radio" value="true" checked>
      &nbsp;F&iacute;sica
	  <!-- END BLOCK : persona_fisica -->
	  
	  </td>
    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">C&oacute;digos</caption>
    <tr>
      <th class="vtabla">C&oacute;digo Contador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idcontador" class="insert" id="idcontador">
		  <!-- START BLOCK : contador -->
          <option value="{idcontador}" {checked}>{idcontador} - {namecontador}</option>
          <!-- END BLOCK : contador -->
        </select>
      </td>
      <th class="vtabla">C&oacute;digo Administrador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idadministrador" class="insert" id="idadministrador">
		  <!-- START BLOCK : administrador -->
          <option value="{idadministrador}" {checked}>{idadministrador} - {nameadministrador}</option>
          <!-- END BLOCK : administrador -->
        </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Auditor</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idauditor" class="insert" id="idauditor">
		  <!-- START BLOCK : auditor -->
          <option value="{idauditor}" {checked}>{idauditor} - {nameauditor}</option>
          <!-- END BLOCK : auditor -->
        </select>
      </td>
      <th class="vtabla">C&oacute;digo Aseguradora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idaseguradora" class="insert" id="idaseguradora">
		  <!-- START BLOCK : aseguradora -->
          <option value="{idaseguradora}" {checked}>{idaseguradora} - {nameaseguradora}</option>
          <!-- END BLOCK : aseguradora -->
        </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Sindicato</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idsindicato" class="insert" id="idsindicato">
		  <!-- START BLOCK : sindicato -->
          <option value="{idsindicato}" {checked}>{idsindicato} - {namesindicato}</option>
          <!-- END BLOCK : sindicato -->
        </select>
      </td>
    <th class="vtabla">C&oacute;digo Gasolina</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="cod_gasolina" type="text" class="vinsert" id="cod_gasolina" value="{cod_gasolina}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Operadora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <select name="idoperadora" class="insert" id="idoperadora">
		  <!-- START BLOCK : operadora -->
          <option value="{idoperadora}" {checked}>{idoperadora} - {nameoperadora}</option>
          <!-- END BLOCK : operadora -->
        </select>
      </td>
	  <th class="vtabla">Homoclave</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="homoclave" type="text" class="vinsert" id="homoclave" value="{homoclave}" size="5" maxlength="3">
      <input name="homo_clave" type="hidden" id="homo_clave" value="{homo_clave}"></td>
    </tr>
    <tr>
      <th class="vtabla">Aplica IVA </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="aplica_iva" type="radio" value="TRUE" {checked_true}>
        Si&nbsp;&nbsp;
        <input name="aplica_iva" type="radio" value="FALSE" {checked_false}>
        No</td>
      <th class="vtabla">N&uacute;mero de Accionista </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" size="4" maxlength="4" value="{num_proveedor}"></td>
    </tr>
  </tr>
</table>
<p>
    <input type="button" name="" class="boton" value="Regresar" onClick="document.location='fac_cia_mod.php'">
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Modificar Compa&ntilde;&iacute;a" onclick='valida_registro()'><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : modificar -->