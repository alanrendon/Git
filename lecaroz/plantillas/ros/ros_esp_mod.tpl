<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar notas especiales</p>
<form action="./ros_esp_mod.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Compa&ntilde;&iacute;a que reparte materia prima </th>
  </tr>
  <tr class="tabla">
    <td class="tabla">
	<select name="num_cia" class="insert">
	<!-- START BLOCK : cias -->
	  <option value="{num_cia}">{num_cia}&#8212;{nombre_cia}</option>
	<!-- END BLOCK : cias -->
    </select></td>
  </tr>
</table>
<br>

<input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="document.form.submit();">

</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar control de facturas especiales para la compa&ntilde;&iacute;a <br>
   {num_cia} - {nombre_cia}</p>
<form action="./ros_esp_mod.php" method="post" name="form">
<input name="cia" type="hidden" id="cia" value="{num_cia}">
<input name="contador" type="hidden" id="contador" value="{count}">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2">Compañía</th>
	<th scope="col" class="tabla">Borrar</th>
  </tr>
  <!-- START BLOCK : cias1 -->
  <tr class="tabla">
    <th class="rtabla"><input type="hidden" size="5" value="{num_cia}" name="num_exp{i}">
      {num_cia}</th>
    <td class="vtabla">{nombre_cia}</td>
	<td class="tabla"><input name="eliminar{i}" type="checkbox" value="0" onChange="if(this.checked==false) form.borrado{i}.value=0; else form.borrado{i}.value=1"> 
	<input type="hidden" size="5" value="0" name="borrado{i}"></td>
  </tr>
  <!-- END BLOCK : cias1 -->
  <tr>
	<td class="tabla" colspan="3"><input name="borrar" type="checkbox" value="0" onChange="if(this.checked==false) form.borrado_general.value=0; else form.borrado_general.value=1; "> Borrar todo el control
      <input type="hidden" size="5" value="0" name="borrado_general">
	</td>
  </tr>
  
</table>
<br>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
&nbsp;
<input name="enviar" type="button" value="Enviar" onClick="document.form.submit();" class="boton">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : modificar -->
