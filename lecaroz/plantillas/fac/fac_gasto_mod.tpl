<!-- START BLOCK : datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Gastos </p>
  <form method="get" name="form" action="./fac_gasto_mod.php">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Gasto </th>
      <td class="vtabla"><input name="codgasto" type="text" class="insert" id="codgasto" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="5" maxlength="5"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.codgasto.value <= 0) {
			alert("Debe especificar un código");
			document.form.codgasto.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.codgasto.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Gastos </p>
<form name="form" method="post" action="./fac_gasto_mod.php">
<input name="temp" type="hidden">
<table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo</th>
      <td class="vtabla"><input name="codgasto" type="text" class="insert" id="codgasto" value="{codgastos}" size="5" readonly></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre Gasto </th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) form.estado_resultados.select();" value="{nombre}" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo de estado de resultados </th>
      <td class="vtabla">
        <select name="estado_resultados" class="insert" id="estado_resultados">
	  <!-- START BLOCK : estado -->
	  <option value="{valueestado}" {selected}>{nameestado}</option>
          <!-- END BLOCK : estado -->
        </select>
	  </td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de Gasto</th>
      <td class="vtabla"><p>
        <label>
        <input name="tipo" type="radio" value="FALSE" {fijo} onChange="form.tipo_gasto.value='true';">
  Fijo</label>

        <label>
        <input type="radio" name="tipo" value="TRUE" {variable} onChange="form.tipo_gasto.value='false';">
  Variable</label>
        <input name="tipo_gasto" type="hidden" class="insert" id="tipo_gasto" value="{tipo}" size="5">
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Aplicaci&oacute;n de Gasto</th>
      <td class="vtabla"><input name="aplicacion_gasto" type="radio" value="FALSE" {panaderia}>
        Panader&iacute;a
          <input name="aplicacion_gasto" type="radio" value="TRUE" {reparto}>
          Reparto</td>
    </tr>
</table>
<p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Cancelar" onclick='parent.history.back()'> 
    &nbsp;&nbsp;
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Modificar" onclick='valida_registro(document.form)'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.nombre.value == "") {
			alert("Debe especificar el nombre del gasto");
			form.nombre.select();
			return false;
		}
		else
			form.submit();
	}
</script>

<!-- END BLOCK : modificar -->
