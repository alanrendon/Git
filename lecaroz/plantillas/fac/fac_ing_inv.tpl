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
<td align="center" valign="middle"><p class="title">Productos pendientes de ingresar a inventario</p>
  <form action="./fac_ing_inv.php" method="get" name="form" id="form">
  <input name="temp" type="hidden">
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
        <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select()" size="3" maxlength="3"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Fecha de ingreso a inventario <font size="-2">(ddmmaa)</font> </th>
        <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10"></td>
      </tr>
    </table>
    <p>
      <input type="button" class="boton" value="Siguiente" onClick="validar(form)">
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.fecha.value.length < 8) {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Productos pendientes de ingresar a inventario</p>
  <form action="./fac_ing_inv.php" method="post" name="form">
  <input name="fecha" type="hidden" value="{fecha}">
  <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="7" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th class="tabla"><input type="checkbox" onClick="seleccionar({ini},{fin},this)"></th>
      <th colspan="2" class="tabla">Producto</th>
      <th class="tabla">Cantidad</th>
      <th class="tabla">Precio Unidad </th>
      <th class="tabla">No. Factura</th>
      <th class="tabla">Fecha Factura </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="rtabla">{codmp}</td>
      <td class="vtabla">{nombremp}</td>
      <td class="rtabla">{cantidad}</td>
      <td class="rtabla">{precio_unidad}</td>
      <td class="tabla">{num_fact}</td>
      <td class="tabla">{fecha}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <td colspan="7">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./fac_ing_inv.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente" onClick="validar(form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function seleccionar(ini, fin, check) {
		if (check.form.id.length == undefined)
			check.form.id.checked = check.checked ? true : false;
		else
			for (i=ini; i<fin; i++)
				check.form.id[i].checked = check.checked ? true : false;
	}
	
	function validar(form) {
		var count = 0;
		
		if (form.id.length == undefined)
			count += form.id.checked ? 1 : 0;
		else
			for (i=0; i<form.id.length; i++)
				if (form.id[i].checked)
					count++;
		
		if (count == 0) {
			alert("Debe seleccionar al menos una entrada");
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				return false;
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
