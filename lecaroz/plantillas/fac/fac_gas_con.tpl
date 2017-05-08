<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else {
				document.form.submit();
			}
	}

	function borrar() {
		if (confirm("¿Desea borrar la pantalla?")) {
			document.form.reset();
			document.form.cia.select();
		}
		else
			document.form.cia.select();
	}
	
</script>

<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Tanques de Gas </P>
<form name="form" method="get" action="./fac_gas_con.php" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Número de Compañía <input name="cia" type="text" id="cia" size="5" maxlength="5" class="insert"></th>
  </tr>
</table>

  
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>

  </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CONSULTA DE TANQUES DE GAS</p>
<form name="form" method="post" action="./fac_gas_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">COMPA&Ntilde;&Iacute;A</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
		  <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
		<input name="cont" type="hidden" id="cont" value="{count}">		</td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero de Tanque </th>
      <th class="tabla" align="center">Nombre</th>
      <th class="tabla" align="center">Capacidad</th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="tabla">{num_tanque} 
	  
	    <input name="id{i}" type="hidden" value="{id}">
	    <span class="rtabla">
	    <input name="num_tanque{i}" type="hidden" id="num_tanque{i}" value="{num_tanque}">
	    </span></th>
	  <td class="vtabla">{nombre}
        <input name="nombre{i}" type="hidden" value="{nombre}">
      </td>
      <td class="rtabla">{capacidad}
        <input name="capacidad{i}" type="hidden" value="{capacidad}">
      </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.modificar{i}.value=1; else if(this.checked==false)document.form.modificar{i}.value=0;">
	  <input type="hidden" name="modificar{i}" value="0">
	  </td>
    </tr>
	<!-- END BLOCK : rows -->
</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

