<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Trabajadores</p>
  <form action="./pan_tra_con.php" method="get" name="form">
    <table class="tabla">
      <tr class="tabla">
        <th class="tabla"><label><input name="tipo_con" type="radio" value="0" checked onChange="document.form.aux.value=0;">
          Por compañía</label>	</th>
        <th class="tabla"><label><input type="radio" name="tipo_con" value="1" onChange="document.form.aux.value=1;">Por nombre</label></th>
      </tr>
      <tr class="tabla">
        <td class="tabla"><input name="cia" type="text" class="insert" id="cia" size="4">
          <input name="aux" type="hidden" class="insert" id="aux" value="0" size="4"></td>
        <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" size="50"></td>
      </tr>
    </table>
      <br>
      <input name="Button" type="button" class="boton" value="Siguiente" onClick="valida();">
  </form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();

function valida(){
	if(document.form.aux.value==1 && document.form.nombre.value==""){
		alert("Inserta el nombre a buscar");
		document.form.nombre.select();
	}
	else
		document.form.submit();
}
</script>


</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Listado de Trabajadores</p>

  <table class="tabla">
<!-- START BLOCK : cia -->
    <tr class="tabla">
      <th colspan="7" class="tabla"><strong>{num_cia}&nbsp;{nom_cia}</strong></th>
    </tr>

    <tr class="tabla">
      <td class="tabla" colspan="2">Nombre</td>
      <td class="tabla">Turno</td>
      <td class="tabla">Puesto</td>
      <td class="tabla">Horario</td>
    </tr>
<!-- START BLOCK : rows -->
    <tr class="tabla">
      <th class="tabla">{num_emp}</th>
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">{turno}</td>
      <td class="vtabla">{puesto}</td>
      <td class="vtabla">{horario}</td>
    </tr>
<!-- END BLOCK : rows -->
<!-- END BLOCK : cia -->
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

