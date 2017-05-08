<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><form action="./fac_tra_minibus.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;ia</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) nombre.select()" size="3" />
      <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="30" /></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Nombre(s)</th>
    <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onkeydown="if (event.keyCode == 13) ap_paterno.select()" size="50" /></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Apellido Paterno </th>
    <td class="vtabla"><input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onkeydown="if (event.keyCode == 13) ap_materno.select()" size="50" /></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Apellido Materno </th>
    <td class="vtabla"><input name="ap_materno" type="text" class="vinsert" id="ap_materno" onkeydown="if (event.keyCode == 13) num_emp.select()" size="50" /></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Num. Empleado </th>
    <td class="vtabla"><input name="num_emp" type="text" class="insert" id="num_emp" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="5" /></td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Buscar" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
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
	f.submit();
}

window.onload = f.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">No</th>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col"> Alta </th>
    <th class="tabla" scope="col">Alta IMSS </th>
    <th class="tabla" scope="col">Baja</th>
    <th class="tabla" scope="col">Baja IMSS </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{num_emp}</td>
    <td class="vtabla">{nombre}</td>
    <td class="vtabla">{num_cia} {nombre_cia} </td>
    <td class="tabla" style="color:#0000CC">{fecha_alta}</td>
    <td class="tabla" style="color:#0000CC">{alta_imss}</td>
    <td class="tabla" style="color:#CC0000">{fecha_baja}</td>
    <td class="tabla" style="color:#CC0000">{baja_imss}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : no_result -->
  <tr>
    <td colspan="7" class="tabla">No hay resultados </td>
    </tr>
  <!-- END BLOCK : no_result -->
</table>
  <p>
    <input type="button" class="boton" value="Regresar" onclick="history.back()" />
  &nbsp;&nbsp;
  <input type="submit" class="boton" value="Cerrar" onclick="self.close()" />
  </p></td>
</tr>
</table>
<!-- END BLOCK : result -->
</body>
</html>
