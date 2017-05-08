<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Trabajadores</p>
  <form action="./fac_tra_mod.php" method="get" name="form"><table class="tabla">
    <tr>
      <th colspan="2" class="tabla">Criterios de b&uacute;squeda </th>
      </tr>
    <tr>
      <td class="vtabla">Compa&ntilde;&iacute;a</td>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_emp.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <td class="vtabla">N&uacute;mero de Empleado </td>
      <td class="vtabla"><input name="num_emp" type="text" class="insert" id="num_emp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_paterno.select();
else if (event.keyCode == 38) num_cia.select();" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <td class="vtabla">Nombre</td>
      <td class="vtabla">Apellido Paterno: 
        <input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) ap_materno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20">
        &nbsp;&nbsp;&nbsp;Apellido Materno: 
        <input name="ap_materno" type="text" class="vinsert" id="ap_materno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) nombre.select();
else if (event.keyCode == 37) ap_paterno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20">
        &nbsp;&nbsp;&nbsp;Nombre(s): 
        <input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia.select();
else if (event.keyCode == 37) ap_materno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function inicio() {
		{mensaje}
		document.form.num_cia.select();
	}
	
	function valida_registro(form) {
		if (form.num_cia.value <= 0 && form.num_emp.value <= 0 && form.nombre.value == "" && form.ap_paterno.value == "" && form.ap_materno.value == "") {
			alert("Debe especificar al menos un criterio de búsqueda");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = inicio();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : lista -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Trabajadores </p>
  <p><font face="Arial, Helvetica, sans-serif">Resultados de la b&uacute;squeda</font> </p>
  <form action="./fac_tra_mod.php" method="get" name="form">
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">&nbsp;</th>
        <th class="tabla" scope="col">Nombre</th>
        <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
        <th class="tabla" scope="col">Turno</th>
        <th class="tabla" scope="col">Puesto</th>
      </tr>
      <!-- START BLOCK : fila -->
      <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla"><input name="id" type="radio" value="{id}" onClick="form.next.disabled = false"></td>
        <td class="vtabla">{nombre}</td>
        <td class="vtabla">{nombre_cia}</td>
        <td class="tabla">{turno}</td>
        <td class="tabla">{puesto}</td>
      </tr>
      <!-- END BLOCK : fila -->
    </table>
    <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_tra_mod.php'">
&nbsp;&nbsp;    
<input name="next" type="submit" disabled="true" class="boton" id="next" value="Siguiente">
  </p>
  </form></td>
</tr>
</table>
<!-- END BLOCK : lista -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Trabajadores</p>
<form action="./fac_tra_mod.php" method="post" name="form">
<input name="temp" type="hidden">
<input name="id" type="hidden" id="id" value="{id}">
<input name="num_cia_ant" type="hidden" id="num_cia_ant" value="{num_cia}">
<input name="tmp" type="hidden" id="tmp">
<table class="tabla">
  <tr>
    <th class="vtabla">N&uacute;mero de Empleado </th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_emp" type="text" class="insert" id="num_emp" value="{num_emp}" size="5" maxlength="5" readonly="true"></td>
    <td colspan="5" class="vtabla" {bgcolor}><strong>{mensaje}</strong></td>
  </tr>
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td colspan="6" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia_emp.select();" value="{num_cia}" size="3" maxlength="3" {readonly}>
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <th class="vtabla">Esta en</th>
    <td colspan="6" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_cia_emp" type="text" class="insert" id="num_cia_emp" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia_emp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) nombre.select();" value="{num_cia_emp}" size="3" maxlength="3" {readonly}>
        <input name="nombre_cia_emp" type="text" disabled="true" class="vnombre" id="nombre_cia_emp" value="{nombre_cia_emp}" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <th colspan="7">&nbsp;</th>
  </tr>
  <tr>
    <th class="vtabla">Nombre</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_paterno.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode == 39) fecha_nac.select();" value="{nombre}" size="20" maxlength="20"></td>
    <th class="vtabla">Fecha de Nacimiento <font size="-2">(ddmmaa)</font> </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="fecha_nac" type="text" class="insert" id="fecha_nac" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) lugar_nac.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode == 37) nombre.select();" value="{fecha_nac}" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Paterno </th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_materno.select();
else if (event.keyCode == 39) lugar_nac.select();
else if (event.keyCode == 38) nombre.select();" value="{ap_paterno}" size="20" maxlength="20"></td>
    <th class="vtabla">Lugar de Nacimiento </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="lugar_nac" type="text" class="vinsert" id="lugar_nac" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) rfc.select();
else if (event.keyCode == 37) ap_materno.select();
else if (event.keyCode == 38) fecha_nac.select();" value="{lugar_nac}" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Materno </th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="ap_materno" type="text" class="vinsert" id="ap_materno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha_nac.select();
else if (event.keyCode == 40) rfc.select();
else if (event.keyCode == 38) ap_paterno.select();" value="{ap_materno}" size="20" maxlength="20"></td>
    <th class="vtabla">Sexo</th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="sexo" type="radio" value="FALSE" {sexo_false}>
      Hombre&nbsp;&nbsp;
      <input name="sexo" type="radio" value="TRUE" {sexo_true}>
      Mujer</td>
  </tr>
  <tr>
    <th colspan="7">&nbsp;</th>
  </tr>
  <tr>
    <th class="vtabla">RFC</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="rfc" type="text" class="vinsert" id="rfc" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) homo_clave.select();
else if (event.keyCode == 38) ap_materno.select();
else if (event.keyCode == 40) colonia.select();" value="{rfc}" size="13" maxlength="13"></td>
    <th class="vtabla">Calle y N&uacute;mero </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="calle" type="text" class="vinsert" id="calle" onKeyDown="if (event.keyCode == 13) colonia.select();
else if (event.keyCode == 37) homo_clave.select();
else if (event.keyCode == 38) lugar_nac.select();
else if (event.keyCode == 40) cod_postal.select();" value="{calle}" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <th class="vtabla">Colonia</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="colonia" type="text" class="vinsert" id="colonia" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cod_postal.select();
else if (event.keyCode == 38) rfc.select();
else if (event.keyCode == 40) del_mun.select();" value="{colonia}" size="40" maxlength="40"></td>
    <th class="vtabla">C&oacute;digo Postal </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="cod_postal" type="text" class="vinsert" id="cod_postal" onKeyDown="if (event.keyCode == 13) del_mun.select();
else if (event.keyCode == 37) colonia.select();
else if (event.keyCode == 38) calle.select();
else if (event.keyCode == 40) entidad.select();" value="{cod_postal}" size="5" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla">Delegaci&oacute;n o Municipio</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="del_mun" type="text" class="vinsert" id="del_mun" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) entidad.select();
else if (event.keyCode == 38) colonia.select();
else if (event.keyCode == 40) salario.select();" value="{del_mun}" size="40" maxlength="40"></td>
    <th class="vtabla">Entidad Federativa</th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="entidad" type="text" class="vinsert" id="entidad" onKeyDown="if (event.keyCode == 13) salario.select();
else if (event.keyCode == 37) del_mun.select();
else if (event.keyCode == 38) cod_postal.select();
else if (event.keyCode == 40) fecha_ingreso.select();" value="{entidad}" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr>
    <th class="vtabla">Puesto</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="cod_puestos" class="insert" id="cod_puestos">
      <!-- START BLOCK : puesto -->
      <option value="{id}" {selected}>{id} - {nombre}</option>
      <!-- END BLOCK : puesto -->
    </select></td>
    <th class="vtabla">Fecha de Alta <font size="-2">(ddmmaa)</font> </th>
    <td colspan="2" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="fecha_alta" type="text" class="insert" id="fecha_alta" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) fecha_alta_imss.select();
else if (event.keyCode == 38) entidad.select();
else if (event.keyCode == 37) salario.select();" value="{fecha_alta}" size="10" maxlength="10">
        <input name="fecha_alta_imss" type="hidden" id="fecha_alta_imss" value="{fecha_alta_imss}">    </td>
    <th class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">Permanente</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="no_baja" type="checkbox" id="no_baja" value="1" {no_baja_checked}>
      Si</td>
  </tr>
  <tr>
    <th class="vtabla">Horario</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="cod_horario" class="insert" id="cod_horario">
      <!-- START BLOCK : horario -->
      <option value="{id}" {selected}>{id} - {nombre}</option>
      <!-- END BLOCK : horario -->
    </select></td>
    <th class="vtabla">Recibe Aguinaldo </th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE" {aguinaldo_checked}>
      Si</td>
    <th class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">Tipo Aguinaldo</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="tipo" class="insert" id="tipo">
      <option value="0"{tipo_0}>NORMAL</option>
      <option value="1"{tipo_1}>A 1 A&Ntilde;O</option>
      <option value="2"{tipo_2}>A 3 MESES</option>
    </select>    </td>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="imp_alta" type="checkbox" id="imp_alta" value="TRUE" {carta_checked}>
      Imprimir carta de alta en IMSS</td>
  </tr>
  <tr>
    <th class="vtabla">Turno</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><select name="cod_turno" class="insert" id="cod_turno">
      <!-- START BLOCK : turno -->
      <option value="{id}" {selected}>{id} - {nombre}</option>
      <!-- END BLOCK : turno -->
    </select></td>
    <th class="vtabla">N&uacute;mero de afiliaci&oacute;n </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_afiliacion" type="text" class="vinsert" id="num_afiliacion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select();
else if (event.keyCode == 38) fecha_alta_imss.select();
else if (event.keyCode == 37) salario.select();" value="{num_afiliacion}" size="25" maxlength="25">
        <input name="num_afiliacion_ant" type="hidden" id="num_afiliacion_ant" value="{num_afiliacion}"></td>
  </tr>
  <tr>
    <th class="vtabla">Salario</th>
    <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="salario" type="text" class="rinsert" id="salario" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) salario_integrado.select();
else if (event.keyCode == 38) del_mun.select();" value="{salario}" size="10" maxlength="10">
      SI
      <input name="salario_integrado" type="text" class="rinsert" id="salario_integrado" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) fecha_alta.select();
else if (event.keyCode == 38) del_mun.select();" value="{salario_integrado}" size="10" maxlength="10"></td>
    <th class="vtabla">Cr&eacute;dito Infonavit </th>
    <td colspan="4" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="credito_infonavit" type="radio" value="TRUE" {infonavit_true}>
      Si&nbsp;&nbsp;
      <input name="credito_infonavit" type="radio" value="FALSE" {infonavit_false}>
      No</td>
  </tr>
  <tr>
    <th class="vtabla">Observaciones</th>
    <td colspan="6" class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><textarea name="observaciones" rows="3" class="insert" id="observaciones" style="width: 100%;">{observaciones}</textarea></td>
  </tr>
  <tr>
    <td colspan="7" class="vtabla">&nbsp;</td>
  </tr>
  <tr>
    <th class="vtabla">Uniforme</th>
    <td class="vtabla"><input name="uniforme" type="text" class="insert" id="uniforme" onFocus="tmp.value=this.value;this.select()" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) deposito_bata.select(); else if (event.keyCode == 38) salario.select()" value="{uniforme}" size="10" maxlength="10"></td>
    <th class="vtabla">Talla</th>
    <td colspan="4" class="vtabla"><select name="talla" class="insert" id="talla">
      <option value=""{talla_}></option>
      <option value="1"{talla_1}>CHICA</option>
      <option value="2"{talla_2}>MEDIANA</option>
      <option value="3"{talla_3}>GRANDE</option>
      <option value="4"{talla_4}>EXTRA GRANDE</option>
    </select>    </td>
  </tr>
  <tr>
    <th class="vtabla">Dep&oacute;sito por bata </th>
    <td class="vtabla"><input name="control_bata" type="checkbox" id="control_bata" value="1"{control_bata_checked}>
      Si</td>
    <th class="vtabla">Monto dep&oacute;sito </th>
    <td colspan="4" class="vtabla"><input name="deposito_bata" type="text" class="insert" id="deposito_bata" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select(); else if (event.keyCode == 38) salario.select()" value="{deposito_bata}" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="history.back()">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Modificar" onClick="valida_registro(form)">
</p>
</form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_compania(num_cia, nombre) {
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.nombre.value == "") {
			alert("Debe especificar el nombre del trabajador");
			form.nombre.select();
			return false;
		}
		else if (form.ap_paterno.value == "") {
			alert("Debe especificar el apellido paterno");
			form.ap_paterno.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
