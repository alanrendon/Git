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
<td align="center" valign="middle"><p class="title">Modificar Pedido</p>
  <form action="./ped_ped_mod.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
	<input name="temp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" size="4" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="4" maxlength="4"></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : pedidos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Pedido</p>
  <form action="" method="get" name="form"><table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="9" class="vtabla" scope="col">{num_cia} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla"><input type="checkbox" onClick="checkall(this,{ini},{fin})"></th>
      <th colspan="2" class="tabla">Producto</th>
      <th colspan="2" class="tabla">Proveedor</th>
      <th colspan="2" class="tabla">Pedido</th>
      <th colspan="2" class="tabla">Contenido</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="rtabla">{codmp}</td>
      <td class="vtabla">{nombre_mp}</td>
      <td class="rtabla">{num_pro}</td>
      <td class="vtabla">{nombre_pro}</td>
      <td class="rtabla"><input name="pedido[]" type="text" class="rnombre" id="pedido" onClick="modPedido({i}, {id})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" value="{pedido}" size="8" readonly="true"></td>
      <td class="vtabla">{unidad_pedido}</td>
      <td class="rtabla">{contenido}</td>
      <td class="vtabla">{unidad_consumo}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <td colspan="9" class="tabla">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ped_ped_mod.php'">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Borrar" onClick="borrar()">
</p>
  </form> </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function modPedido(i, id) {
}

function borrar() {
	var cont = 0;
	
	if (form.id.length == undefined)
		cont += form.id.checked ? 1 : 0;
	else
		for (var i = 0; i < form.id.length; i++)
			cont += form.id[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert("Debe seleccionar al menos un registro");
		return false;
	}
	else if (confirm("¿Desea borrar los registros seleccionados?"))
		form.submit();
	else
		return false;
}

function checkall(check, ini, fin) {
	if (form.id.length == undefined)
		form.id.checked = check.checked;
	else
		for (var i = ini; i <= fin; i++)
			form.id[i].checked = check.checked;
}
-->
</script>
<!-- END BLOCK : pedidos -->
</body>
</html>
