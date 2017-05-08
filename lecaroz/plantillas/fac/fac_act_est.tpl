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
<td align="center" valign="middle"><p class="title">Cambiar Estado de Trabajadores</p>
  <form action="./fac_act_est.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) return false;" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="col">Altas pendientes </th>
      <td class="vtabla" scope="col"><input name="tipo" type="radio" value="alta" checked></td>
    </tr>
    <tr>
      <th class="vtabla">Bajas pendientes</th>
      <td class="vtabla"><input name="tipo" type="radio" value="baja"></td>
    </tr>
  </table>  
  <p>
    <input type="submit" class="boton" value="Siguiente"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select()</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Trabajadores con Estado de {estado} </p>
  <form action="./fac_act_est.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <input name="tipo" type="hidden" value="{tipo}">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">N&uacute;mero y Nombre del Empleado</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha de Aviso</th>
      <th class="tabla" scope="col">D&iacute;as<br>
        Transcurridos </th>
      <th class="tabla" scope="col">Cambiar <br>
        Estado </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{num_emp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{dias}</td>
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location = './fac_act_est.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
