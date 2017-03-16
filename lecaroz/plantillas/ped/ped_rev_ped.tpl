<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Sistema Pedidos de Panader&iacute;as</p>
  <form action="./ped_rev_ped.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><select name="num_cia" class="insert" id="num_cia">
        <!-- START BLOCK : cia -->
		<option value="{num_cia}">{num_cia} {nombre}</option>
		<!-- END BLOCK : cia -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="if (get_val(num_cia) > 0) this.form.submit()" />
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pedidos de Panader&iacute;as </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <tr>
    <td class="tabla" style="font-size:14pt; font-weight:bold;">{num_cia} {nombre_cia} </td>
  </tr>
</table>
<br />
  <form action="./ped_rev_ped.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col"><img src="./menus/delete.gif" width="16" height="16" /></th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Producto</th>
	  <th class="tabla" scope="col">Unidad</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Observaciones</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
	  <td class="vtabla"><input name="elim{i}" type="checkbox" id="elim{i}" value="{id}" />
	    <input name="id[]" type="hidden" id="id" value="{id}" /></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{producto}</td>
	  <td class="vtabla">{unidad}</td>
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod({i})" onkeydown="movCursor(event.keyCode,cantidad{index},null,cantidad{index},codmp{back},codmp{next})" value="{codmp}" size="3" />
        <input name="nombre_mp[]" type="text" disabled="disabled" class="vnombre" id="nombre_mp" value="{nombre_mp}" /></td>
      <td class="tabla"><input name="cantidad[]" type="text" class="rinsert" id="cantidad" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,num_pro{index},codmp{index},num_pro{index},cantidad{back},cantidad{next})" value="{cantidad}" size="8" /></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro({i})" onkeydown="movCursor(event.keyCode,obs{index},cantidad{index},obs{index},num_pro{back},num_pro{next})" value="{num_pro}" size="3" />
        <input name="nombre_pro[]" type="text" disabled="disabled" class="vnombre" id="nombre_pro" value="{nombre_pro}" /></td>
      <td class="tabla"><input name="obs[]" type="text" class="vinsert" id="obs" onkeydown="movCursor(event.keyCode,codmp{next},num_pro{index},null,obs{back},obs{next})" value="{obs}" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='./ped_rev_ped.php'" />
    &nbsp;&nbsp;
    <input type="button" class="boton" onclick="validar()" value="Siguiente" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, pro = new Array(), cod = new Array();
<!-- START BLOCK : pro -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : pro -->
<!-- START BLOCK : mp -->
cod[{cod}] = '{nombre}';
<!-- END BLOCK : mp -->

function cambiaPro(i) {
	var num_pro = f.num_pro.length == undefined ? f.num_pro : f.num_pro[i];
	var nombre_pro = f.nombre_pro.length == undefined ? f.nombre_pro : f.nombre_pro[i];
	
	if (num_pro.value == '' || num_pro.value == '0') {
		num_pro.value = '';
		nombre_pro.value = '';
	}
	else if (pro[get_val(num_pro)] != null)
		nombre_pro.value = pro[get_val(num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		num_pro.value = f.tmp.value;
		num_pro.select();
	}
}

function cambiaCod(i) {
	var codmp = f.codmp.length == undefined ? f.codmp: f.codmp[i];
	var nombre_mp = f.nombre_mp.length == undefined ? f.nombre_mp : f.nombre_mp[i];
	
	if (codmp.value == '' || codmp.value == '0') {
		codmp.value = '';
		nombre_mp.value = '';
	}
	else if (cod[get_val(codmp)] != null)
		nombre_mp.value = cod[get_val(codmp)];
	else {
		alert('El producto no se encuentra en el catálogo');
		codmp.value = f.tmp.value;
		codmp.select();
	}
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = f.codmp.length == undefined ? f.codmp.select() : f.codmp[0].select();
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
