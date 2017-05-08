<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<p class="title">Actualizaci&oacute;n de Inventario</p>
<form name="form" method="get" action="./ros_act_inv.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp) && this.value > 100 && this.value < 200)" size="3" maxlength="3"></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Todas</th>
    <th class="vtabla"><input name="tipo" type="radio" value="todas" checked></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Controlada</th>
    <th class="vtabla"><input name="tipo" type="radio" value="controlada"></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">No controlada </th>
    <th class="vtabla"><input name="tipo" type="radio" value="no_controlada"></th>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="valida_registro()">
</p>
</form>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<p class="title">Actualizaci&oacute;n de Inventario</p>
<form name="form" method="post" action="./ros_act_inv.php?accion=1">
<table class="tabla">
  <tr>
  	<th class="vtabla">Compa&ntilde;&iacute;a</th>
	<td class="vtabla"><strong>{num_cia} - {nombre_cia}</strong></td>
    <th class="vtabla">Mes</th>
    <td class="vtabla"><strong>{mes}</strong></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Materia Prima </th>
    <th class="tabla" scope="col">Existencia<br> 
    c&oacute;mputo</th>
    <th class="tabla" scope="col">Existencia<br> 
    f&iacute;sica </th>
    <th class="tabla" scope="col">Faltantes</th>
    <th class="tabla" scope="col">Sobrantes</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <th class="vtabla" scope="row">{mp}</th>
    <td class="tabla">{existencia}</td>
    <td class="tabla">{inventario}</td>
    <td class="tabla">{falta}</td>
    <td class="tabla">{sobra}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
  <input type="button" class="boton" value="Cancelar"> 
&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Actualizar">
</p>
</form>
<!-- END BLOCK : listado -->
</td>
</tr>
</table>