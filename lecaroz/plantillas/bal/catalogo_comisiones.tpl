

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
	<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
	
	
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA AL CATALOGO DE COMISIONES</P>
<!-- tabla importe_efectivos menu efectivos -->
<form name="form" method="post" action="insert_cat_comisiones.php?tabla={tabla}">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla">Codigo<br>Materia prima</th>
      <th class="tabla">Comisi&oacute;n ($)</th>
      
    </tr>
	<!-- START BLOCK : rows -->
    <tr>
      <td class="tabla" align="center">
        <input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="5" maxlength="3" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp)">
   </td>
      <td class="tabla" align="center">
        <input name="comision{i}" type="text" class="insert" id="comision{i}" size="15" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp)">
</td>
      
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>&nbsp;
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>

</td>
</tr>
</table>