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
<td align="center" valign="middle"><p class="title">Desglosar Pedidos entre Proveedores </p>
  <form action="./ped_des_ped_cap.php" method="post" name="form"><table class="tabla">
    <!-- START BLOCK : result -->
	<tr>
      <th class="tabla">Productos</th>
    </tr>
    <!-- START BLOCK : producto -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="vtabla"><input name="pro[]" type="checkbox" id="pro" value="{codmp}">
        {nombre}</td>
    </tr>
	<!-- END BLOCK : producto -->
	<!-- END BLOCK : result -->
	<!-- START BLOCK : no_result -->
    <tr>
      <td class="tabla">No hay pedidos capturados </td>
    </tr>
	<!-- END BLOCK : no_result -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"{disabled}>
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function validar() {
	var cont = 0;
	
	if (form.pro.length == undefined)
		cont += form.pro.checked ? 1 : 0;
	else
		for (var i = 0; i < form.pro.length; i++)
			cont += form.pro[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert("Debe seleccionar al menos un producto");
		return false;
	}
	else
		form.submit();
}
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : productos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Desglosar Pedidos entre Proveedores</p>
  <form action="./ped_des_ped_cap.php" method="post" name="form"><input name="tmp" type="hidden" id="tmp"><table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Proveedores</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla">{codmp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">
	    <!-- START BLOCK : prov -->
		<input name="id[]" type="hidden" id="id" value="{id}">
		<input name="codmp[]" type="hidden" id="codmp" value="{codmp}">
		<input name="nombre[]" type="hidden" id="nombre" value="{nombre_mp}">
        <input name="num_pro[]" type="hidden" id="num_pro" value="{num_pro}">
        <input name="contenido[]" type="hidden" id="contenido" value="{contenido}">
        <input name="unidad[]" type="hidden" id="unidad" value="{unidad}">
        <input name="porc[]" type="text" class="insert" id="porc" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) {
if (porc.length == undefined) this.blur();
else porc[{next}].select();
}" value="{porc}" size="3">
        {nombre} <span style="color: DarkBlue; ">({presentacion})</span>{br}
		<!-- END BLOCK : prov -->
		</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ped_des_ped_cap.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente" onClick="validar()"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function validar() {
	if (form.porc.length == undefined) {
		if ((!isNaN(parseInt(form.porc.value)) ? parseInt(form.porc.value) : 0) < 100) {
			alert("La suma de los porcentajes para el producto debe ser igual al 100%");
			form.porc.select();
			return false;
		}
		else if (confirm("¿Desea generar los pedidos?"))
			form.submit();
		else {
			form.porc.select();
			return false;
		}
	}
	else {
		var codmp = null;
		for (var i = 0; i < form.porc.length; i++) {
			if (codmp != form.codmp[i].value) {
				if (codmp != null && total < 100) {
					alert("Producto: " + form.nombre[i - 1].value + "\nLa suma de los porcentajes debe ser 100%");
					form.porc[i - 1].select();
					return false;
				}
				
				codmp = form.codmp[i].value;
				
				total = 0;
			}
			total += !isNaN(parseInt(form.porc[i].value)) ? parseInt(form.porc[i].value) : 0;
		}
		if (codmp != null && total < 100) {
			alert("Producto: " + form.nombre[i - 1].value + "\nLa suma de los porcentajes debe ser 100%");
			form.porc[i - 1].select();
			return false;
		}
		
		if (confirm("¿Desea generar los pedidos?"))
			form.submit();
		else {
			form.porc[0].select();
			return false;
		}
	}
}

window.onload = form.porc.length == undefined ? form.porc.select() : form.porc[0].select();
-->
</script>
<!-- END BLOCK : productos -->
</body>
</html>
