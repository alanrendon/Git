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
<td align="center" valign="middle"><p class="title">Compra Directa</p>
  <form action="./pan_com_dir_v2.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		if (cia[parseInt(form.num_cia.value)] == null) {
			alert("Compañía "+parseInt(form.num_cia.value)+" no es tuya");
			form.num_cia.value = "";
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Compra Directa </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <th class="tabla">{num_cia} - {nombre_cia} </th>
      <th class="tabla">{fecha}</th>
    </tr>
  </table>  
  <br>
  <form action="./pan_com_dir_v2.php" method="post" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="fecha" type="hidden" value="{fecha}">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Contenido</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_mp(this,nombre_mp[{i}])" onKeyDown="if (event.keyCode == 13) cantidad[{i}].select()" size="4" maxlength="4">
        <input name="nombre_mp[]" type="text" disabled="true" class="vnombre" id="nombre_mp" size="30" maxlength="30"></td>
      <td class="tabla"><input name="cantidad[]" type="text" class="insert" id="cantidad" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcular_total(form)" onKeyDown="if (event.keyCode == 13) contenido[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="contenido[]" type="text" class="insert" id="contenido" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcular_total(form)" onKeyDown="if (event.keyCode == 13) codmp[{next}].select()" size="10" maxlength="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" value="0.00" size="10" maxlength="10"></th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./pan_com_dir_v2.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente" onClick="validar(form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_mp(codmp, nombre) {
		mp = new Array();// Materias primas
		<!-- START BLOCK : mp -->
		mp[{codmp}] = '{nombre}';
		<!-- END BLOCK : mp -->
				
		if (parseInt(codmp.value) > 0) {
			if (mp[parseInt(codmp.value)] == null) {
				alert("Código "+codmp.value+" no esta en el inventario de la compañía");
				codmp.value = temp.value;
				codmp.focus();
				return false;
			}
			else {
				nombre.value = mp[parseInt(codmp.value)];
				return true;
			}
		}
		else if (codmp.value == "") {
			codmp.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function calcular_total(form) {
		var total_value = 0;
		
		for (i = 0; i < form.codmp.length; i++)
			if (form.codmp[i].value > 0 && form.cantidad[i].value > 0 && form.importe[i].value > 0)
				total_value += parseFloat(form.importe[i].value);
		
		form.total.value = total_value.toFixed(2);
	}
	
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.codmp[0].select();
	}
	
	window.onload = document.form.codmp[0].select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
