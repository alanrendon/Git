<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pedidos Manuales sin Proveedor</p>
  <form action="./ped_sis_man_sp.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="tipo" type="hidden" id="tipo">    
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Listado" onClick="validar(this.form, 2)">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Captura" onClick="validar(this.form, 1)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form, tipo) {
	if (tipo == 1 && form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else {
		form.tipo.value = tipo;
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pedidos Manuales sin Proveedor</p>
  <form action="./ped_sis_man_sp.php" method="post" name="form">
<input name="tmp" type="hidden" id="tmp">  
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <tr>
    <td class="tabla" style="font-size: 14pt; font-weight: bold;">{num_cia} {nombre} </td>
  </tr>
</table>

  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Existencia</th>
      <th class="tabla" scope="col">Pedido</th>
	  <th class="tabla" scope="col">Unidad</th>
    </tr>
    <!-- START BLOCK : pro -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla" style="color: #{color};"><input name="codmp[]" type="hidden" id="codmp" value="{codmp}">
        <input name="nombre[]" type="hidden" id="nombre" value="{nombre}">
        <input name="unidad[]" type="hidden" id="unidad" value="{unidad}">
        {codmp}</td>
      <td class="vtabla" style="color: #{color};">{nombre}</td>
      <td class="rtabla" style="color: #{color};">{existencia}</td>
      <td class="tabla"><input name="pedido[]" type="text" class="rinsert" id="pedido" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) pedido[{next}].select(); else if (event.keyCode == 38) pedido[{back}].select();" value="{pedido}" size="8"></td>
	  <td class="vtabla" style="color: #{color}; font-weight: bold;">{unidad}</td>
    </tr>
	<!-- END BLOCK : pro -->
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Pedido</th>
      <th class="tabla" scope="col">Unidad</th>
    </tr>
	<!-- START BLOCK : extra -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) pedido[{i}].select()" value="{codmp}" size="3">
        <input name="nombre[]" type="text" class="vnombre" id="nombre" value="{nombre}" size="30" readonly="true"></td>
      <td class="tabla"><input name="pedido[]" type="text" class="rinsert" id="pedido" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) codmp[{next}].select()" value="{pedido}" size="8"></td>
      <td class="vtabla"><input name="unidad[]" type="text" class="vnombre" id="unidad" value="{unidad}" size="8" readonly="true"></td>
    </tr>
	<!-- END BLOCK : extra -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ped_sis_man_sp.php'">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Capturar" onClick="validar()">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, mp = new Array();

<!-- START BLOCK : mp -->
mp[{codmp}] = new Array();
mp[{codmp}]['nombre'] = "{nombre}";
mp[{codmp}]['unidad'] = "{unidad}";
<!-- END BLOCK : mp -->

function cambiaPro(i) {
	if (form.codmp[i].value == "") {
		form.nombre[i].value = "";
		form.pedido[i].value = "";
		form.unidad[i].value = "";
	}
	else if (mp[form.codmp[i].value] != null) {
		form.nombre[i].value = mp[form.codmp[i].value]['nombre'];
		form.unidad[i].value = mp[form.codmp[i].value]['unidad'];
	}
	else {
		alert("EL producto no se encuentra en el catálogo");
		form.codmp[i].value = form.tmp.value;
		form.codmp[i].select();
	}
}

function validar() {
	/*var total = 0;
	
	for (var i = 0; i < form.codmp.length; i++)
		total += get_val(form.codmp[i]) > 0 && get_val(form.pedido[i]) > 0 ? 1 : 0;
	
	if (total == 0) {
		alert("Debe hacer al menos un pedido");
		form.pedido[0].select();
		return false;
	}
	else */if (confirm("¿Son correctos los datos?"))
		form.submit();
	else
		form.pedido[0].select();
}

window.onload = form.pedido[0].select();
-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Pedidos</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print">Producto</th>
    <th colspan="2" class="print">Pedido</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="rprint" style="font-size: 10pt;">{codmp}</td>
    <td class="vprint" style="font-size: 10pt;">{nombre}</td>
    <td class="rprint" style="font-size: 10pt;">{pedido}</td>
    <td class="vprint" style="font-size: 10pt;">{unidad}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
