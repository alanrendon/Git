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
<td align="center" valign="middle"><p class="title">Porcentajes de Producto por Proveedor</p>
  <form action="./ped_por_pro.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Materia Prima </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.codmp.value <= 0) {
			alert("Debe especificar el código de materia prima");
			form.codmp.select();
			return false;
		}
		else
			form.submit();
	}
	
	document.form.codmp.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : porcentajes -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Porcentajes de Producto por Proveedor</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Materia Prima </th>
    </tr>
    <tr>
      <th class="tabla">{codmp} {nombre_mp} </th>
    </tr>
  </table>  
  <br>
  <form action="./ped_por_pro.php" method="post" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Contenido</th>
      <th class="tabla" scope="col">Unidad</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Porcentaje</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="rtabla"><input name="id{i}" type="hidden" id="id{i}" value="{id}">
        {num_proveedor}</td>
      <td class="vtabla">{nombre}</td>
      <td class="rtabla">{contenido}</td>
      <td class="vtabla">{unidad}</td>
      <td class="rtabla">{precio}</td>
      <td class="tabla"><input name="porcentaje{i}" type="text" class="insert" id="porcentaje{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) calcula_100(this,suma,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) porcentaje{next}.select();
else if (event.keyCode == 38) porcentaje{back}.select();" value="{porcentaje}" size="5" maxlength="5"></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th colspan="5" class="rtabla">Total</th>
	  <th class="tabla"><input name="suma" type="text" class="nombre" id="suma" value="{total}" size="5" maxlength="5"></th>
	  </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './ped_por_pro.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Actualizar" onClick="valida_registro(form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function calcula_100(por, sum, temp) {
		var por_value = !isNaN(parseInt(por.value)) ? parseInt(por.value) : 0;
		var sum_value = !isNaN(parseInt(sum.value)) ? parseInt(sum.value) : 0;
		var temp_value = !isNaN(parseInt(temp.value)) ? parseInt(temp.value) : 0;
		
		sum_value = sum_value - temp_value;
		
		if (sum_value + por_value <= 100)
			sum.value = sum_value + por_value;
		else {
			alert("Ha revasado el 100% de distribucion");
			por.value = temp.value;
			por.select();
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.suma.value != 100) {
			alert("La suma de los porcentajes debe ser igual a 100");
			form.porcentaje0.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los porcentajes?"))
				form.submit();
			else
				form.porcentaje0.select();
	}
	
	document.form.porcentaje0.select();
</script>
<!-- END BLOCK : porcentajes -->
</body>
</html>
