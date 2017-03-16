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
<td align="center" valign="middle"><p class="title">Modificar Local</p>
  <form action="./ren_art_mod.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="id" type="hidden" id="id" value="{id}">    
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="num_local" type="text" class="insert" id="num_local" value="{num_local}" size="4" readonly="true"></td>
      <th class="vtabla">Nombre del Arrendatario</th>
      <td class="vtabla"><input name="nombre_arrendatario" type="text" class="vinsert" id="nombre_arrendatario" onKeyDown="if (event.keyCode == 13) rfc.select()" value="{nombre_arrendatario}" size="30" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre Local</th>
      <td class="vtabla"><input name="nombre_local" type="text" class="vinsert" id="nombre_local" onKeyUp="if (event.keyCode == 13) direccion_local.select()" value="{nombre_local}" size="30" maxlength="50"></td>
      <th class="vtabla">R.F.C.</th>
      <td class="vtabla"><input name="rfc" type="text" class="vinsert" id="rfc" onKeyUp="if (event.keyCode == 13) direccion_fiscal.select()" value="{rfc}" size="13" maxlength="13"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Direcci&oacute;n Local</th>
      <td class="vtabla"><textarea name="direccion_local" cols="25" rows="3" class="insert" id="direccion_local" onKeyDown="if (event.keyCode == 13) email.select()">{direccion_local}</textarea></td>
      <th class="vtabla">Direcci&oacute;n Fiscal </th>
      <td class="vtabla"><textarea name="direccion_fiscal" cols="25" rows="3" class="insert" id="direccion_fiscal" onKeyDown="if (event.keyCode == 13) giro.select()">{direccion_fiscal}</textarea></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Correo electr&oacute;nico </th>
      <td colspan="3" class="vtabla"><input name="email" type="text" class="vinsert" id="email" style="width:98%;" value="{email}" maxlength="100" onKeyDown="if(event.keyCode==13)fecha_inicio.select();"></td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><select name="cod_arrendador" class="insert" id="cod_arrendador" style="max-width: 200px;">
        <!-- START BLOCK : arr -->
		<option value="{cod}"{selected}>{cod} {nombre}</option>
		<!-- END BLOCK : arr -->
      </select></td>
      <th class="vtabla">Tipo de Persona </th>
      <td class="vtabla"><input name="tipo_persona" type="radio" value="FALSE"{persona_f}>
        F&iacute;sica
          <input name="tipo_persona" type="radio" value="TRUE"{persona_t}>
          Moral</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Inicio de Contrato</th>
      <td class="vtabla"><input name="fecha_inicio" type="text" class="insert" id="fecha_inicio" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) fecha_final.select()" value="{fecha1}" size="10" maxlength="10"></td>
      <th class="vtabla">Giro</th>
      <td class="vtabla"><input name="giro" type="text" class="vinsert" id="giro" onKeyDown="if (event.keyCode == 13) representante.select()" value="{giro}" size="30" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fin de Contrato</th>
      <td class="vtabla"><input name="fecha_final" type="text" class="insert" id="fecha_final" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) metros.select()" value="{fecha2}" size="10" maxlength="10"></td>
      <th class="vtabla">Bloque</th>
      <td class="vtabla"><input name="bloque" type="radio" value="1"{bloque_1}>
        Interno
          <input name="bloque" type="radio" value="2"{bloque_2}>
          Externo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros</th>
      <td class="vtabla"><input name="metros" type="text" class="rinsert" id="metros" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) metros_cuadrados.select()" value="{metros}" size="4"></td>
      <th class="vtabla">Representante</th>
      <td class="vtabla"><input name="representante" type="text" class="vinsert" id="representante" onKeyDown="if (event.keyCode == 13) nombre_aval.select()" value="{representante}" size="30" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros Cuadrados </th>
      <td class="vtabla"><input name="metros_cuadrados" type="text" class="rinsert" id="metros_cuadrados" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) cuenta_predial.select()" value="{metros_cuadrados}" size="4"></td>
      <th class="vtabla">Aval</th>
      <td class="vtabla"><input name="nombre_aval" type="text" class="vinsert" id="nombre_aval" onKeyDown="if (event.keyCode == 13) bien_avaluo.select()" value="{nombre_aval}" size="30" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta de Predial </th>
      <td class="vtabla"><input name="cuenta_predial" type="text" class="vinsert" id="cuenta_predial" onKeyDown="if (event.keyCode == 13) contacto.select()" value="{cuenta_predial}" size="30" maxlength="30"></td>
      <th class="vtabla">Bien Avaluo </th>
      <td class="vtabla"><input name="bien_avaluo" type="text" class="vinsert" id="bien_avaluo" onKeyDown="if (event.keyCode == 13) telefono.select()" value="{bien_avaluo}" size="30" maxlength="150"></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Contacto</th>
      <td class="vtabla"><input name="contacto" type="text" class="vinsert" id="contacto" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{contacto}" size="30" maxlength="255"></td>
      <th class="vtabla">Tel&eacute;fono de contacto </th>
      <td class="vtabla"><input name="telefono" type="text" class="vinsert" id="telefono" onKeyDown="if (event.keyCode == 13) parrafo.select()" value="{telefono}" size="30" maxlength="50"></td>
    </tr>
	<tr>
	  <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
	  <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) clausula.select()" value="{num_cia}" size="3">
	    <input name="nombre_cia" type="text" disabled class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30"></td>
	  <th class="vtabla">Tipo Local</th>
	  <td class="vtabla"><input type="radio" name="tipo_local" id="radio" value="1"{tipo_local_1}>
	    Comercial
	      <input type="radio" name="tipo_local" id="radio2" value="2"{tipo_local_2}>
	      Vivienda</td>
	  </tr>
	<tr>
	  <th class="vtabla" scope="row">Clausula</th>
	  <td class="vtabla"><input name="clausula" type="text" class="vinsert" id="clausula" onKeyDown="if(event.keyCode==13)nombre_arrendatario.select()" value="{clausula}" size="10" maxlength="50"></td>
	  <th class="vtabla">Parrafo</th>
	  <td class="vtabla"><input name="parrafo" type="text" class="vinsert" id="parrafo" onKeyDown="if(event.keyCode==13)renta_con_recibo.select()" value="{parrafo}" size="10" maxlength="50"></td>
	  </tr>
    <tr>
      <td colspan="4" class="vtabla" scope="row">&nbsp;</td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Renta con Recibo </th>
      <td class="vtabla"><input name="renta_con_recibo" type="text" class="rinsert" id="renta_con_recibo" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) renta_sin_recibo.select()" value="{renta_con_recibo}" size="10"></td>
      <th class="vtabla">Renta sin recibo </th>
      <td class="vtabla"><input name="renta_sin_recibo" type="text" class="rinsert" id="renta_sin_recibo" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) mantenimiento.select()" value="{renta_sin_recibo}" size="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mantenimiento</th>
      <td class="vtabla"><input name="mantenimiento" type="text" class="rinsert" id="mantenimiento" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) agua.select()" value="{mantenimiento}" size="10"></td>
      <th class="vtabla">Agua</th>
      <td class="vtabla"><input name="agua" type="text" class="rinsert" id="agua" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) por_incremento.select()" value="{agua}" size="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Retenci&oacute;n de I.V.A.</th>
      <td class="vtabla"><input name="retencion_iva" type="radio" value="TRUE"{ret_iva_t}>
        Si
          <input name="retencion_iva" type="radio" value="FALSE"{ret_iva_f}>
          No</td>
      <th class="vtabla">Retenci&oacute;n de I.S.R.</th>
      <td class="vtabla"><input name="retencion_isr" type="radio" value="TRUE"{ret_isr_t}>
        Si
          <input name="retencion_isr" type="radio" value="FALSE"{ret_isr_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fianza</th>
      <td class="vtabla"><input name="fianza" type="radio" value="TRUE"{fianza_t}>
        Si
          <input name="fianza" type="radio" value="FALSE"{fianza_f}>
          No</td>
      <th class="vtabla">Incremento Anual </th>
      <td class="vtabla"><input name="incremento_anual" type="radio" value="TRUE"{inc_t}>
        Si
          <input name="incremento_anual" type="radio" value="FALSE"{inc_f}>
          No&nbsp;&nbsp;&nbsp;+%
          <input name="por_incremento" type="text" class="rinsert" id="por_incremento" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) cargo_daños.select()" value="{por_incremento}" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cargo por Da&ntilde;os </th>
      <td class="vtabla"><input name="cargo_daños" type="text" class="rinsert" id="cargo_daños" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) cargo_termino.select()" value="{cargo_daños}" size="10"></td>
      <th class="vtabla">Cargo por T&eacute;rmino</th>
      <td class="vtabla"><input name="cargo_termino" type="text" class="rinsert" id="cargo_termino" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) rentas_en_deposito.select()" value="{cargo_termino}" size="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Recibo Mensual</th>
      <td class="vtabla"><input name="recibo_mensual" type="radio" value="TRUE"{mensual_t}>
        Si
          <input name="recibo_mensual" type="radio" value="FALSE"{mensual_f}>
          No</td>
      <th class="vtabla">Rentas en Dep&oacute;sito </th>
      <td class="vtabla"><input name="rentas_en_deposito" type="text" class="rinsert" id="rentas_en_deposito" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) nombre_local.select()" value="{rentas_en_deposito}" size="10"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function validar() {
	if (f.nombre_local.value.length < 3) {
		alert("Debe especificar el nombre del local");
		f.nombre_local.select();
		return false;
	}
	else if (f.direccion_local.value.length < 3) {
		alert("Debe especificar la dirección del local");
		f.direccion_local.select();
		f.direccion_local.focus();
	}
	else if (f.fecha_inicio.value.length < 8) {
		alert("Debe especificar la fecha de inicio de contrato");
		f.fecha_inicio.select();
		return false;
	}
	else if (f.fecha_final.value.length < 8) {
		alert("Debe especificar la fecha de fin de contrato");
		f.fecha_final.select();
		return false;
	}
	else if (f.nombre_arrendatario.value.length < 3) {
		alert("Debe especificar el nombre del arrendatario");
		f.arrendatario.select();
		return false;
	}
	else if (get_val(f.num_cia) == 0) {
		alert("Debe especificar la compañía");
		f.num_cia.select();
		return false;
	}
	/*else if (f.rfc.value.length < 12) {
		alert("Debe especificar el RFC del arrendatario");
		f.rfc.select();
		return false;
	}*/
	else if (get_val(f.renta_con_recibo) < 0 && get_val(f.mantenimiento) < 0) {
		alert("Debe especificar el importe de la renta o del mantenimiento");
		f.renta_con_recibo.select();
		return false;
	}
	else if (get_val(f.por_incremento) < 0 && get_val(f.por_incremento) > 99.99) {
		alert("El porcentaje de incremento debe estar en el rango de 0 a 100");
		f.por_incremento.select();
		return false;
	}
	else if (confirm("¿Son correctos todos los datos?"))
		f.submit();
	else
		f.nombre_local.select();
}

window.onload = function () { f.nombre_local.select(); showAlert = true; cambiaCia(); }
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
