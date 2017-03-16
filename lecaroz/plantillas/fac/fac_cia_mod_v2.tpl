<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Compa&ntilde;&iacute;a </p>
  <form action="./fac_cia_mod_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) siguiente.focus()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function validar() {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Compa&ntilde;&iacute;a</p>
  <form action="./fac_cia_mod_v2.php" method="post" name="form"><input name="tmp" type="hidden" id="tmp">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) nombre.select()" value="{num_cia}" size="3" maxlength="3" readonly="true"></td>
      <th class="vtabla">Tipo de persona</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="persona_fis_moral" type="radio" value="FALSE" {per_f}>
        Moral&nbsp;&nbsp;
        <input name="persona_fis_moral" type="radio" value="TRUE" {per_t}>
        F&iacute;sica</td>
    </tr>
    <tr>
      <th class="vtabla">Nombre</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre" type="text" class="vinsert" id="nombre" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) nombre_corto.select()" value="{nombre}" size="30" maxlength="100"></td>
      <th class="vtabla">No. IMSS </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_imss" type="text" class="vinsert" id="no_imss" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) no_infonavit.select()" value="{no_imss}" size="25" maxlength="25"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre Corto </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre_corto" type="text" class="vinsert" id="nombre_corto" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) telefono.select()" value="{nombre_corto}" size="25" maxlength="25"></td>
      <th class="vtabla">No. Infonavit </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_infonavit" type="text" class="vinsert" id="no_infonavit" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) no_cta_cia_luz.select()" value="{no_infonavit}" size="15" maxlength="15"></td>
    </tr>
    <tr>
      <th rowspan="3" class="vtabla">Direcci&oacute;n</th>
      <td rowspan="3" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><textarea name="direccion" cols="28" rows="3" wrap="VIRTUAL" class="insert" id="direccion">{direccion}</textarea></td>
      <th class="vtabla">Delegaci&oacute;n del IMSS </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="iddelimss" class="insert" id="iddelimss">
        <!-- START BLOCK : imss -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : imss -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Sub-Delegaci&oacute;n del IMSS </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idsubdelimss" class="insert" id="idsubdelimss">
        <!-- START BLOCK : subimss -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : subimss -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">No. Cuenta de Compa&ntilde;&iacute;a de Luz</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_cta_cia_luz" type="text" class="vinsert" id="no_cta_cia_luz" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) sub_cuenta_deudores.select()" value="{cuenta_luz}" size="25" maxlength="25">
        <input name="luz_esp" type="checkbox" id="luz_esp" value="TRUE" {luz_esp}>
        Especial</td>
    </tr>
    <tr>
      <th class="vtabla">Tel&eacute;fono</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="telefono" type="text" class="vinsert" id="telefono" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) email.select()" value="{telefono}" size="15" maxlength="15"></td>
      <th class="vtabla">Medidor de Agua </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="med_agua" type="radio" value="TRUE" {agua_t}>
        Si
          &nbsp;
          <input name="med_agua" type="radio" value="FALSE" {agua_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla">e-m@il</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="email" type="text" class="vinsert" id="email" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) rfc.select()" value="{email}" size="30" maxlength="50"></td>
      <th class="vtabla">Sub-Cuenta de Deudores</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="sub_cuenta_deudores" type="text" class="vinsert" id="sub_cuenta_deudores" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia_primaria.select()" value="{subcuenta}" size="13" maxlength="13"></td>
    </tr>
    <tr>
      <th class="vtabla">R.F.C.</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="rfc" type="text" class="vinsert" id="rfc" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) cod_gasolina.select()" value="{rfc}" size="20" maxlength="20"></td>
      <th class="vtabla">Homoclave</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_cia_primaria" type="text" class="insert" id="num_cia_primaria" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_proveedor.select()" value="{num_cia_pri}" size="3" maxlength="3">        </td>
    </tr>
    <tr>
      <th class="vtabla">Aplica I.V.A. </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="aplica_iva" type="radio" value="TRUE" {iva_t}>
        Si&nbsp;&nbsp;
        <input name="aplica_iva" type="radio" value="FALSE" {iva_f}>
        No</td>
      <th class="vtabla">Representada por </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cliente_cometra.select()" value="{num_pro}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo Gasolina </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="cod_gasolina" type="text" class="vinsert" id="cod_gasolina" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_imss.select()" value="{cod_gasolina}" size="10" maxlength="10"></td>
      <th class="vtabla">No. Cliente de Cometra </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="cliente_cometra" type="text" class="vinsert" id="cliente_cometra" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) dia_ven_luz.select()" value="{cliente_cometra}" size="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Periodo de pago de luz </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="periodo_pago_luz" type="radio" value="1"{per_1}>
        Mensual
          <input name="periodo_pago_luz" type="radio" value="2"{per_2}>
          Bimestral</td>
      <th class="vtabla">Aviso de saldo bajo </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="aviso_saldo" type="radio" value="FALSE" {saldo_f}>
        No
          <input name="aviso_saldo" type="radio" value="TRUE" {saldo_t}>
          Si</td>
    </tr>
    <tr>
      <th class="vtabla"><p>D&iacute;a de vencimiento de recibo de luz </p>
        </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="dia_ven_luz" type="text" class="insert" id="dia_ven_luz" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) por_bg.select()" value="{dia_ven_luz}" size="2" maxlength="2"></td>
      <th class="vtabla">Tipo mes vencimiento recibo bimestral </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="bim_par_imp_luz" type="radio" value="0"{par_0}>
        Par
          <input name="bim_par_imp_luz" type="radio" value="1"{par_1}>
          Impar</td>
    </tr>
	<tr>
    	<th class="vtabla">Turno de COMETRA</th>
    	<td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="turno_cometra" type="radio" id="turno_cometra_2" value="1" {turno_cometra_1}>
    		Matutino
    			<input type="radio" name="turno_cometra" id="turno_cometra_1" value="2" {turno_cometra_2}>
    			Vespertino</td>
    	<th class="vtabla">&nbsp;</th>
    	<td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">&nbsp;</td>
    	</tr>
	<tr>
		<td colspan="4" class="vtabla">&nbsp;</td>
		</tr>
	<tr>
    <th class="vtabla">% BG</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_bg" type="text" class="rinsert" id="por_bg" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_efectivo.select()" value="{por_bg}" size="6" maxlength="6"></td>
    <th class="vtabla">% Efectivo</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_efectivo" type="text" class="rinsert" id="por_efectivo" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_bg_1.select()" value="{por_efectivo}" size="6" maxlength="6"></td>
  </tr>
  <tr>
    <th class="vtabla">% BG 1</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_bg_1" type="text" class="rinsert" id="por_bg_1" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_efectivo_1.select()" value="{por_bg_1}" size="6" maxlength="6"></td>
    <th class="vtabla">% Efectivo 1</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_efectivo_1" type="text" class="rinsert" id="por_efectivo_1" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_bg_2.select()" value="{por_efectivo_1}" size="6" maxlength="6"></td>
  </tr>
  <tr>
    <th class="vtabla">% BG 2</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_bg_2" type="text" class="rinsert" id="por_bg_2" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_efectivo_2.select()" value="{por_bg_2}" size="6" maxlength="6"></td>
    <th class="vtabla">% Efectivo 2</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_efectivo_2" type="text" class="rinsert" id="por_efectivo_2" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_bg_3.select()" value="{por_efectivo_2}" size="6" maxlength="6"></td>
  </tr>
  <tr>
    <th class="vtabla">% BG 3</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_bg_3" type="text" class="rinsert" id="por_bg_3" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_efectivo_3.select()" value="{por_bg_3}" size="6" maxlength="6"></td>
    <th class="vtabla">% Efectivo 3</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_efectivo_3" type="text" class="rinsert" id="por_efectivo_3" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_bg_4.select()" value="{por_efectivo_3}" size="6" maxlength="6"></td>
  </tr>
  <tr>
    <th class="vtabla">% BG 4</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_bg_4" type="text" class="rinsert" id="por_bg_4" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) por_efectivo_4.select()" value="{por_bg_4}" size="6" maxlength="6"></td>
    <th class="vtabla">% Efectivo 4</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="por_efectivo_4" type="text" class="rinsert" id="por_efectivo_4" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this, 2,tmp)" onKeyDown="if (event.keyCode == 13) clabe_banco.select()" value="{por_efectivo_4}" size="6" maxlength="6"></td>
  </tr>
    <tr>
      <td colspan="4" class="vtabla">&nbsp;</td>
      </tr>
    <tr>
      <th class="vtabla">CLABE Banorte </th>
      <td class="vtabla"><input name="clabe_banco" type="text" class="insert" id="clabe_banco" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_plaza.select()" value="{banco1}" size="3" maxlength="3">
        <input name="clabe_plaza" type="text" class="insert" id="clabe_plaza" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_cuenta.select()" value="{plaza1}" size="3" maxlength="3">
        <input name="clabe_cuenta" type="text" class="insert" id="clabe_cuenta" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_identificador.select()" value="{cuenta1}" size="11" maxlength="11">
        <input name="clabe_identificador" type="text" class="insert" id="clabe_identificador" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_banco2.select()" value="{id1}" size="1" maxlength="1"></td>
      <th class="vtabla">CLABE Santander </th>
      <td class="vtabla"><input name="clabe_banco2" type="text" class="insert" id="clabe_banco2" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_plaza2.select()" value="{banco2}" size="3" maxlength="3">
        <input name="clabe_plaza2" type="text" class="insert" id="clabe_plaza2" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_cuenta2.select()" value="{plaza2}" size="3" maxlength="3">
        <input name="clabe_cuenta2" type="text" class="insert" id="clabe_cuenta2" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) clabe_identificador2.select()" value="{cuenta2}" size="11" maxlength="11">
        <input name="clabe_identificador2" type="text" class="insert" id="clabe_identificador2" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{id2}" size="1" maxlength="1"></td>
    </tr>
    <tr>
      <td colspan="4" class="vtabla">&nbsp;</td>
      </tr>
    <tr>
      <th class="vtabla">Contador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idcontador" class="insert" id="idcontador">
        <!-- START BLOCK : contador -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : contador -->
      </select></td>
      <th class="vtabla">Administrador</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idadministrador" class="insert" id="idadministrador">
        <!-- START BLOCK : administrador -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : administrador -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Auditor</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idauditor" class="insert" id="idauditor">
        <!-- START BLOCK : auditor -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : auditor -->
      </select></td>
      <th class="vtabla">Aseguradora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idaseguradora" class="insert" id="idaseguradora">
        <!-- START BLOCK : aseguradora -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : aseguradora -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Sindicato</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idsindicato" class="insert" id="idsindicato">
        <!-- START BLOCK : sindicato -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : sindicato -->
      </select></td>
      <th class="vtabla">Operadora</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="idoperadora" class="insert" id="idoperadora">
        <!-- START BLOCK : operadora -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : operadora -->
      </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./fac_cia_mod_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		f.num_cia.select();
		return false;
	}
	else if (f.nombre.value.length < 3) {
		alert("Debe especificar el nombre de la compañía");
		f.nombre.select();
		return false;
	}
	else if (f.nombre_corto.value.length < 3) {
		alert("Debe especificar el nombre corto de la compañía");
		f.nombre_corto.select();
		return false;
	}
	else if (f.clabe_cuenta.value.length > 0 && f.clabe_cuenta.value.length < 11) {
		alert("El número de cuenta debe ser de 11 dígitos. Rellenar con ceros a la izquierda");
		f.clabe_cuenta.select();
		return false;
	}
	else if (f.clabe_cuenta2.value.length > 0 && f.clabe_cuenta2.value.length < 11) {
		alert("El número de cuenta debe ser de 11 dígitos. Rellenar con ceros a la izquierda");
		f.clabe_cuenta2.select();
		return false;
	}
	else if (confirm("Son correctos los datos")) {
		if (f.num_cia_primaria.value <= 0)
			f.num_cia_primaria.value = f.num_cia.value;
		f.submit();
	}
	else
		return false;
}

window.onload = f.num_cia.select();
-->
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
