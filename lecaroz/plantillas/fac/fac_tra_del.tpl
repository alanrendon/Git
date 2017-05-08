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
<td align="center" valign="middle"><p class="title">Baja de Trabajadores</p>
  <form action="./fac_tra_del.php" method="get" name="form"><table class="tabla">
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
<td align="center" valign="middle"><p class="title">Baja de Trabajadores </p>
  <p><font face="Arial, Helvetica, sans-serif">Resultados de la b&uacute;squeda</font> </p>
  <form action="./fac_tra_del.php" method="post" name="form">
  <input name="numfilas" type="hidden" value="{numfilas}">
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">&nbsp;</th>
        <th class="tabla" scope="col">Nombre</th>
        <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
        <th class="tabla" scope="col">Turno</th>
        <th class="tabla" scope="col">Puesto</th>
        <th class="tabla" scope="col">Prestamo</th>
        <th class="tabla" scope="col">Pensi&oacute;n</th>
      </tr>
      <!-- START BLOCK : fila -->
      <tr bgcolor="{color}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla"><input name="id{i}" type="checkbox" {disabled} id="id{i}" onClick="form.next.disabled = false" value="{id}">
          <input name="afiliado{i}" type="hidden" id="afiliado{i}" value="{afiliado}"></td>
        <td class="vtabla">{nombre}</td>
        <td class="vtabla">{nombre_cia}</td>
        <td class="tabla">{turno}</td>
        <td class="tabla">{puesto}</td>
        <td class="rtabla">{prestamo}</td>
        <td class="tabla"><input name="pension{i}" type="checkbox" id="pension{i}" onClick="form.next.disabled = false" value="{id}"></td>
      </tr>
      <!-- END BLOCK : fila -->
    </table>
    <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_tra_del.php'">
&nbsp;&nbsp;    
<input name="next" type="submit" disabled="true" class="boton" id="next" value="Baja">
  </p>
  </form></td>
</tr>
</table>
<!-- END BLOCK : lista -->

</body>
</html>
