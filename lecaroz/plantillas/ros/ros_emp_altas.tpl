<!-- START BLOCK : empleados -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Empleados
  dados de Alta por Do&ntilde;a Liz </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Num. Empleado </th>
      <th class="tabla" scope="col">Nombre</th>
    </tr>
    <!-- START BLOCK : emp -->
	<tr>
      <td class="tabla">{num_emp}</td>
      <td class="vtabla">{nombre}</td>
    </tr>
	<!-- END BLOCK : emp -->
  </table>  <p>
    <input type="button" class="boton" value="Cerrar" onClick="window.opener.document.location.reload();self.close();">
  </p></td>
</tr>
</table>
<!-- END BLOCK : empleados -->

<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function actualiza_prestamos() {
		window.opener.document.location.reload();
		parent.close();
	}
	
	window.onload = actualiza_prestamos();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : alta -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de Empleados</p>
<form name="form" method="post" action="./ros_emp_altas.php?tabla={tabla}">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla">{num_cia} - {nombre_cia} </td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Apellido Paterno </th>
    <th class="tabla" scope="col">Apellido Materno </th>
    <th class="tabla" scope="col">Puesto</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="nombre{i}" type="text" class="vinsert" id="nombre{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.ap_paterno{i}.select();
else if (event.keyCode == 37) form.num_emp{i}.select();
else if (event.keyCode == 38) form.nombre{back}.select();
else if (event.keyCode == 40) form.nombre{next}.select();" value="{nombre}" size="20" maxlength="20"></td>
    <td class="tabla"><input name="ap_paterno{i}" type="text" class="vinsert" id="ap_paterno{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.ap_materno{i}.select();
else if (event.keyCode == 37) form.nombre{i}.select();
else if (event.keyCode == 38) form.ap_paterno{back}.select();
else if (event.keyCode == 40) form.ap_paterno{next}.select();" value="{ap_paterno}" size="20" maxlength="20"></td>
    <td class="tabla"><input name="ap_materno{i}" type="text" class="vinsert" id="ap_materno{i}" onKeyDown="if (event.keyCode == 13) form.num_emp{next}.select();
else if (event.keyCode == 39) form.num_emp{i}.select();
else if (event.keyCode == 37) form.ap_paterno{i}.select();
else if (event.keyCode == 38) form.ap_materno{back}.select();
else if (event.keyCode == 40) form.ap_materno{next}.select();" value="{ap_materno}" size="20" maxlength="20"></td>
    <td class="tabla"><select name="cod_puestos{i}" class="insert" id="cod_puestos{i}">
      <option value="9" {9}>ENCAR. ROSTICERIA</option>
      <option value="10" {10}>EMPL. ROSTICERIA</option>
      <option value="11" {11}>SEGUNDO ROSTICERO</option>
    </select></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>

<p>
  <input type="button" class="boton" onClick="parent.close();" value="Cerrar ventana">&nbsp;&nbsp;<input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : alta -->
