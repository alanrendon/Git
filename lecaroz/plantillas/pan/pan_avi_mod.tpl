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
<td align="center" valign="middle"><p class="title">Correcci&oacute;n de Av&iacute;o</p>
  <form action="./pan_avi_mod.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) fecha.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) num_cia.select();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"{disabled}> 
    </p>
  <!-- START BLOCK : leyenda -->
  <p style="font-size:14pt; color:red; font-weight:bold; font-family:Arial, Helvetica, sans-serif;">No puede modificar movientos de avio, vaya con el supervisor a cargo.</p>
  <!-- END BLOCK : leyenda -->
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(form.num_cia.value) > 0) {
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
			else {
				form.submit();
				return;
			}
		}
		else if (form.num_cia.value == "") {
			form.num_cia.value = "";
			return false;
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Correcci&oacute;n de Av&iacute;o</p>

  <table class="tabla">
  <tr>
    <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
    <td class="vtabla" scope="col">
      <font size="+1"><strong>{num_cia} - {nombre_cia}</strong></font> </td>
    <th class="vtabla" scope="col">Fecha</th>
    <td class="vtabla" scope="col">
      <font size="+1"><strong>{fecha}</strong></font></td>
  </tr>
</table>
  <br>
  <table width="100%" class="tabla">
    <tr>
      <th width="87" height="36" class="tabla" scope="col">Producto</th>
      <th width="83" class="tabla" scope="col">Existencia<br>
        anterior </th>
      <th width="85" class="tabla" scope="col">Entrada</th>
      <th width="85" class="tabla" scope="col">FD</th>
      <th width="85" class="tabla" scope="col">FN</th>
      <th width="85" class="tabla" scope="col">BD</th>
      <th width="85" class="tabla" scope="col">Repostero</th>
      <th width="85" class="tabla" scope="col">Piconero</th>
      <th width="85" class="tabla" scope="col">Gelatinero</th>
      <th width="85" class="tabla" scope="col">Despacho</th>
      <th class="tabla" scope="col">Existencia<br>
        final</th>
    </tr>
	<tr>
      <td height="400" colspan="11" scope="row">
	  <iframe src="pan_avi_mod_table.php?num_cia={num_cia}&fecha={fecha}" name="avi_mod" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="auto" frameborder="0"></iframe>
	  </td>
     </tr>
  </table>
  <p align="center">    <input type="button" class="boton" value="Regresar" onclick="document.location='./pan_avi_mod.php'">
&nbsp;&nbsp;&nbsp;    
<input type="button" class="boton" name="enviar" value="Corregir consumos" onclick="valida_registro()" onDblClick="return false">
</p>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		var mensaje = "Al corregir los datos se creara un registro de modificación de avio.\n¿Son correctos los datos?";
		
		if (confirm(mensaje))
			window.avi_mod.document.form.submit();
		else
			return false;
	}
</script>
<!-- END BLOCK : hoja -->
</body>
</html>
