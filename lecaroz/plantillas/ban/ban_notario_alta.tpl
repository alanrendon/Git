<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.nombre.value == "") {
			alert('Debe especificar un nombre para el notario');
			document.form.nombre.select();
		}
		else if(document.form.num_notario.value==""){
			alert("Debe especificar el numero público de notario");
			document.form.num_notario.select();
		}
		else 
			document.form.submit();
	}
	
	function borrar() {
			document.form.reset();
			document.form.nombre.select();
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">ALTA DE NOTARIO </p>
<form name="form" action="./ban_notario_alta.php" method="get" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="vtabla">C&oacute;digo de Notario </th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="codigo" type="text" class="insert" id="codigo" size="5" maxlength="5" value="{id}"></td>
  </tr>
  <tr>
    <th class="vtabla">Nombre</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre" type="text" class="vinsert" id="nombre" size="50" maxlength="70"></td>
  </tr>
  <tr>
    <th class="vtabla">Notario P&uacute;blico N&uacute;mero</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="num_notario" type="text" class="insert" id="num_notario" size="5"></td>
  </tr>
</table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Alta de Notario" onclick='valida_registro()'>&nbsp;&nbsp;
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar" onclick='borrar()'>
  </p>
  </form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.nombre.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->