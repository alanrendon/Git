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
<!-- START BLOCK : cerrar -->

<!-- END BLOCK : cerrar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de Trabajadores</p>
<form action="./fac_tra_altas.php" method="post" name="form">
<input name="temp" type="hidden">
<input name="aguinaldo" type="hidden">
<input name="admin" type="hidden" value="{admin}">
<input name="user" type="hidden" value="{iduser}">
<input name="tmp" type="hidden" id="tmp">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td colspan="5" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia_emp.select();" value="{num_cia}" size="3" maxlength="3" {num_cia_readonly}>
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <th class="vtabla">Esta en</th>
    <td colspan="5" class="vtabla"><input name="num_cia_emp" type="text" class="insert" id="num_cia_emp" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia_emp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) nombre.select();" value="{num_cia}" size="3" maxlength="3" {num_cia_readonly}>
      <input name="nombre_cia_emp" type="text" disabled="true" class="vnombre" id="nombre_cia_emp" value="{nombre_cia}" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <td colspan="6" class="tabla" id="mensaje">&nbsp;</td>
  </tr>
  <tr>
    <th class="vtabla">Nombre</th>
    <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onChange="buscarEmp()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_paterno.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode == 39) fecha_nac.select();" size="20" maxlength="20"></td>
    <th class="vtabla">Fecha de Nacimiento <font size="-2">(ddmmaa)</font> </th>
    <td colspan="3" class="vtabla"><input name="fecha_nac" type="text" class="insert" id="fecha_nac" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) lugar_nac.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode == 37) nombre.select();" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Paterno </th>
    <td class="vtabla"><input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onChange="buscarEmp()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_materno.select();
else if (event.keyCode == 39) lugar_nac.select();
else if (event.keyCode == 38) nombre.select();" size="20" maxlength="20"></td>
    <th class="vtabla">Lugar de Nacimiento </th>
    <td colspan="3" class="vtabla"><input name="lugar_nac" type="text" class="vinsert" id="lugar_nac" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) rfc.select();
else if (event.keyCode == 37) ap_materno.select();
else if (event.keyCode == 38) fecha_nac.select();" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Materno </th>
    <td class="vtabla"><input name="ap_materno" type="text" class="vinsert" id="ap_materno" onChange="buscarEmp()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha_nac.select();
else if (event.keyCode == 40) rfc.select();
else if (event.keyCode == 38) ap_paterno.select();" size="20" maxlength="20"></td>
    <th class="vtabla">Sexo</th>
    <td colspan="3" class="vtabla"><input name="sexo" type="radio" value="FALSE" checked>
      Hombre&nbsp;&nbsp;
      <input name="sexo" type="radio" value="TRUE">
      Mujer</td>
  </tr>
  <tr>
    <th colspan="6">&nbsp;</th>
  </tr>
  <tr>
    <th class="vtabla">RFC</th>
    <td class="vtabla"><input name="rfc" type="text" class="vinsert" id="rfc" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) homo_clave.select();
else if (event.keyCode == 38) ap_materno.select();
else if (event.keyCode == 40) colonia.select();" size="13" maxlength="13"></td>
    <th class="vtabla">Calle y N&uacute;mero </th>
    <td colspan="3" class="vtabla"><input name="calle" type="text" class="vinsert" id="calle" onKeyDown="if (event.keyCode == 13) colonia.select();
else if (event.keyCode == 37) homo_clave.select();
else if (event.keyCode == 38) lugar_nac.select();
else if (event.keyCode == 40) cod_postal.select();" size="50" maxlength="50"></td>
  </tr>
  <tr>
    <th class="vtabla">Colonia</th>
    <td class="vtabla"><input name="colonia" type="text" class="vinsert" id="colonia" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cod_postal.select();
else if (event.keyCode == 38) rfc.select();
else if (event.keyCode == 40) del_mun.select();" size="40" maxlength="40"></td>
    <th class="vtabla">C&oacute;digo Postal </th>
    <td colspan="3" class="vtabla"><input name="cod_postal" type="text" class="vinsert" id="cod_postal" onKeyDown="if (event.keyCode == 13) del_mun.select();
else if (event.keyCode == 37) colonia.select();
else if (event.keyCode == 38) calle.select();
else if (event.keyCode == 40) entidad.select();" size="5" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla">Delegaci&oacute;n o Municipio</th>
    <td class="vtabla"><input name="del_mun" type="text" class="vinsert" id="del_mun" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) entidad.select();
else if (event.keyCode == 38) colonia.select();
else if (event.keyCode == 40) salario.select();" size="40" maxlength="40"></td>
    <th class="vtabla">Entidad Federativa</th>
    <td colspan="3" class="vtabla"><input name="entidad" type="text" class="vinsert" id="entidad" onKeyDown="if (event.keyCode == 13) salario.select();
else if (event.keyCode == 37) del_mun.select();
else if (event.keyCode == 38) cod_postal.select();
else if (event.keyCode == 40) fecha_alta.select();" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <th class="vtabla">Puesto</th>
    <td class="vtabla"><select name="cod_puestos" class="insert" id="cod_puestos">
        <!-- START BLOCK : puesto -->
        <option value="{id}">{id} - {nombre}</option>
        <!-- END BLOCK : puesto -->
    </select></td>
    <th class="vtabla">Fecha de Alta <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha_alta" type="text" class="insert" id="fecha_alta" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_afiliacion.select();
else if (event.keyCode == 38) entidad.select();
else if (event.keyCode == 37) salario_integrado.select();" value="{fecha}" size="10" maxlength="10" {fecha_readonly}></td>
    <th class="vtabla">Permanente</th>
    <td class="vtabla"><input name="no_baja" type="checkbox" id="no_baja" value="1">
      Si</td>
  </tr>
  <tr>
    <th class="vtabla">Horario</th>
    <td class="vtabla"><select name="cod_horario" class="insert" id="cod_horario">
        <!-- START BLOCK : horario -->
        <option value="{id}">{id} - {nombre}</option>
        <!-- END BLOCK : horario -->
    </select></td>
    <th class="vtabla">Recibe Aguinaldo </th>
    <td class="vtabla"><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE">
      Si</td>
    <th class="vtabla">Tipo Aguinaldo</th>
    <td class="vtabla"><select name="tipo" class="insert" id="tipo">
      <option value="0" selected>NORMAL</option>
      <option value="1">A 1 A&Ntilde;O</option>
      <option value="2">A 3 MESES</option>
    </select>    </td>
  </tr>
  <tr>
    <th class="vtabla">Turno</th>
    <td class="vtabla"><select name="cod_turno" class="insert" id="cod_turno">
        <!-- START BLOCK : turno -->
        <option value="{id}">{id} - {nombre}</option>
        <!-- END BLOCK : turno -->
    </select></td>
    <th class="vtabla">N&uacute;mero de afiliaci&oacute;n </th>
    <td colspan="3" class="vtabla"><input name="num_afiliacion" type="text" class="vinsert" id="num_afiliacion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) uniforme.select();
else if (event.keyCode == 38) fecha_alta_imss.select();
else if (event.keyCode == 37) salario.select();" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <th class="vtabla">Salario</th>
    <td class="vtabla"><input name="salario" type="text" class="rinsert" id="salario" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) salario_integrado.select();
else if (event.keyCode == 38) del_mun.select();" size="10" maxlength="10">
    SI
      <input name="salario_integrado" type="text" class="rinsert" id="salario_integrado" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) fecha_alta.select();
else if (event.keyCode == 38) del_mun.select();" size="10" maxlength="10"></td>
    <th class="vtabla">Cr&eacute;dito Infonavit </th>
    <td colspan="3" class="vtabla"><input name="credito_infonavit" type="radio" value="TRUE">
      Si&nbsp;&nbsp;
      <input name="credito_infonavit" type="radio" value="FALSE" checked>
      No</td>
  </tr>
  <tr>
    <th class="vtabla">Observaciones</th>
    <td colspan="5" class="vtabla"><textarea name="observaciones" rows="3" class="insert" id="observaciones" style="width: 100%;"></textarea></td>
    </tr>
  <tr>
    <td colspan="6" class="vtabla">&nbsp;</td>
    </tr>
  <tr>
    <th class="vtabla">Uniforme</th>
    <td class="vtabla"><input name="uniforme" type="text" class="insert" id="uniforme" onFocus="tmp.value=this.value;this.select()" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) deposito_bata.select(); else if (event.keyCode == 38) salario.select()" size="10" maxlength="10"></td>
    <th class="vtabla">Talla</th>
    <td colspan="3" class="vtabla"><select name="talla" class="insert" id="talla">
      <option value="" selected></option>
	  <option value="1">CHICA</option>
      <option value="2">MEDIANA</option>
      <option value="3">GRANDE</option>
      <option value="4">EXTRA GRANDE</option>
    </select>    </td>
    </tr>
  <tr>
    <th class="vtabla">Dep&oacute;sito por bata </th>
    <td class="vtabla"><input name="control_bata" type="checkbox" id="control_bata" value="1">
      Si</td>
    <th class="vtabla">Monto dep&oacute;sito </th>
    <td colspan="3" class="vtabla"><input name="deposito_bata" type="text" class="insert" id="deposito_bata" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select(); else if (event.keyCode == 38) salario.select()" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
<!-- START BLOCK : boton_cerrar -->
<input type="button" class="boton" value="Cerrar" onClick="self.close()">&nbsp;&nbsp;
<!-- END BLOCK : boton_cerrar -->
  <input name="Button" type="button" class="boton" value="Alta" onClick="valida_registro(form)">
</p></form></td>
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
	
	function antiguedad(fecha_alta) {
		 //calculo la fecha de hoy
		hoy = new Date();
		
		//calculo la fecha que recibo
		//La descompongo en un array
		var fecha = fecha_alta.split("/");
		//si el array no tiene tres partes, la fecha es incorrecta
		if (fecha.length != 3)
			return false;
		
		//compruebo que los ano, mes, dia son correctos
		var ano = parseInt(fecha[2]);
		if (isNaN(ano))
			return false;
		
		var mes = parseInt(fecha[1]);
		if (isNaN(mes))
			return false;
		
		var dia = parseInt(fecha[0]);
		if (isNaN(dia))
			return false;
		
		//si el año de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
		if (ano <= 99)
			ano += 1900;
		
		//resto los años de las dos fechas
		var ant = hoy.getFullYear() - ano - 1; // - 1 porque no se si ha cumplido años ya este año
		
		//si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido
		if (hoy.getMonth() + 1 - mes < 0) // + 1 porque los meses empiezan en 0
			return ant;
		if (hoy.getMonth() + 1 - mes > 0)
			return ant + 1;
	
		// entonces es que eran iguales. miro los dias
		// si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
		if (hoy.getUTCDate() - dia >= 0)
			return ant + 1;
		
		return ant;
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
		else if (form.num_afiliacion.value.length > 0 && form.fecha_nac.value.length < 8) {
			alert("Debe especificar la fecha de nacimiento del trabajador");
			form.fecha_nac.select();
			return false;
		}
		else if (!(form.user.value == 1 || form.user.value == 4) && form.num_afiliacion.value.length > 0 && antiguedad(form.fecha_nac.value) < 18) {
			alert("La edad de la persona es menor a 18 años");
			form.fecha_nac.select();
			return false;
		}
		else if (form.admin.value == 1) {
			if (antiguedad(form.fecha_alta.value) > 0) {
				var temp = prompt("Introdusca el ultimo aguinaldo del empleado");
				var aguinaldo = !isNaN(parseFloat(temp)) ? parseFloat(temp) : 0;
				if (aguinaldo == 0) {
					if (confirm("¿Desea continuar sin ingresar el último aguinaldo?")) {
						form.aguinaldo.value = "";
						if (confirm("¿Son correctos los datos?")) {
							form.submit();
						}
						else {
							form.num_cia.select();
							return false;
						}
					}
					else {
						form.num_cia.select();
						return false;
					}
				}
				else {
					form.aguinaldo.value = aguinaldo.toFixed(2);
					if (confirm("¿Son correctos los datos?")) {
						form.submit();
					}
					else {
						form.num_cia.select();
						return false;
					}
				}
			}
			else {
				form.aguinaldo.value = "";
				if (confirm("¿Son correctos los datos?")) {
					form.submit();
				}
				else {
					form.num_cia.select();
					return false;
				}
			}
		}
		else if (confirm("¿Son correctos los datos?")) {
			form.aguinaldo.value = "";
			form.submit();
		}
		else {
			form.num_cia.select();
		}
	}
	
	function buscarEmp() {
		if (form.nombre.value.length > 0 && form.ap_paterno.value.length > 0 && form.ap_materno.value.length > 0) {
			var myConn = new XHConn();
			
			if (!myConn)
				alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
			
			// Pedir datos
			myConn.connect('./fac_tra_altas.php', 'GET', 'n=' + form.nombre.value + '&ap=' + form.ap_paterno.value + '&am=' + form.ap_materno.value, validarEmp);
		}
	}
	
	var validarEmp = function(oXML) {
		var result = oXML.responseText;
		
		if (result.length > 0) {
			alert(result);
		}
	}
	
	<!-- START BLOCK : num_emp -->
	function num_emp() {
		var cadena = "Compañía: {num_cia}\nEmpleado: {nombre}\nNúmero asignado: {num_emp}";
		alert(cadena);
		document.form.num_cia.select();
	}
	
	window.onload = num_emp();
	<!-- END BLOCK : num_emp -->
	<!-- START BLOCK : seleccionar -->	
	window.onload = document.form.num_cia.select();
	<!-- END BLOCK : seleccionar -->
	<!-- START BLOCK : cerrar -->
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
	<!-- END BLOCK : cerrar -->
</script>
</body>
</html>
