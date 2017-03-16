

<script type="text/javascript" language="JavaScript">
	
function valida_registro() {


if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
}
	
function borrar() {
	if (confirm("¿Desea borrar la pantalla?")) {
		document.form.reset();
		document.form.num_cia.select();
	}
	else
		document.form.num_cia.select();
}
	</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA DIRECTA DE EFECTIVOS</P>
<!-- tabla importe_efectivos menu efectivos -->
<form name="form" method="post" action="insert_ros_efe_cap.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="tabla">Num. Compa&ntilde;&iacute;a </th>
      <th class="tabla">Dia</th>
      <th class="tabla">Importe</th>
      
    </tr>
	<!-- START BLOCK : rows -->
    <tr>
      <td class="tabla" align="center">
        <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" size="5">
      </td>
      <td class="tabla">
        <input name="fecha{i}" type="text" class="insert" id="fecha{i}" onChange="if (document.form.fecha{i}.value > 31 || document.form.fecha{i}.value < 1) {alert('El dia tiene que comprender del 1 a 31'); 	document.form.fecha{i}.select();return false;}" size="5" maxlength="2">
   </td>
      <td class="tabla" align="center">
        <input name="importe{i}" type="text" class="insert" id="importe{i}" size="15" >
   </td>
      
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Efectivos" onclick='valida_registro()'><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>
</td>
</tr>
</table>