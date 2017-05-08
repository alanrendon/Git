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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td colspan="3" class="vtabla"> {num_cia} - {nombre_cia}        </td>
  </tr>
  <tr>
    <th colspan="4">&nbsp;</th>
  </tr>
  <tr>
    <th class="vtabla">Nombre</th>
    <td class="vtabla">{nombre}</td>
    <th class="vtabla">Fecha de Nacimiento <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla">{fecha_nac}</td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Paterno </th>
    <td class="vtabla">{ap_paterno}</td>
    <th class="vtabla">Lugar de Nacimiento </th>
    <td class="vtabla">{lugar_nac}</td>
  </tr>
  <tr>
    <th class="vtabla">Apellido Materno </th>
    <td class="vtabla">{ap_materno}</td>
    <th class="vtabla">Sexo</th>
    <td class="vtabla">{sexo}</td>
  </tr>
  <tr>
    <th colspan="4">&nbsp;</th>
  </tr>
  <tr>
    <th class="vtabla">RFC</th>
    <td class="vtabla">{rfc}          </td>
    <th class="vtabla">Calle y N&uacute;mero </th>
    <td class="vtabla">{calle}</td>
  </tr>
  <tr>
    <th class="vtabla">Colonia</th>
    <td class="vtabla">{colonia}</td>
    <th class="vtabla">C&oacute;digo Postal </th>
    <td class="vtabla">{cod_postal}</td>
  </tr>
  <tr>
    <th class="vtabla">Delegaci&oacute;n o Municipio</th>
    <td class="vtabla">{del_mun}</td>
    <th class="vtabla">Entidad Federativa</th>
    <td class="vtabla">{entidad}</td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <th class="vtabla">Puesto</th>
    <td class="vtabla">{puesto}</td>
    <th class="vtabla">Fecha de Alta IMSS </th>
    <td class="vtabla">{fecha_alta_imss}</td>
  </tr>
  <tr>
    <th class="vtabla">Horario</th>
    <td class="vtabla">{horario}</td>
    <th class="vtabla">Fecha de Baja IMSS </th>
    <td class="vtabla">{fecha_baja_imss}</td>
  </tr>
  <tr>
    <th class="vtabla">Turno</th>
    <td class="vtabla">{turno}</td>
    <th class="vtabla">N&uacute;mero de afiliaci&oacute;n </th>
    <td class="vtabla">{num_afiliacion}</td>
  </tr>
  <tr>
    <th class="vtabla">Salario</th>
    <td class="vtabla">{salario}</td>
    <th class="vtabla">Estatus</th>
    <td class="vtabla">{estatus}</td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  </p></td>
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
</script>
</body>
</html>
