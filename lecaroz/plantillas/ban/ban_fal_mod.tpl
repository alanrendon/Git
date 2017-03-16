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
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Faltantes de Cometra </p>
  <form action="./ban_fal_mod.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="id" type="hidden" id="id" value="{id}">  
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Dep&oacute;sito</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
    </tr>
	<tr>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30"></td>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onFocus="temp.value=this.value" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) deposito.select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="deposito" type="text" class="rinsert" id="deposito" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) importe.select()" value="{deposito}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) descripcion.select()" value="{importe}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="tipo" class="insert" id="tipo">
        <option value="FALSE" {f}>FALTANTE</option>
        <option value="TRUE" {t}>SOBRANTE</option>
      </select></td>
      <td class="tabla"><h5>
        <input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{descripcion}" size="50" maxlength="100">
      </h5></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function validar(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.importe.value <= 0) {
			alert("Debe especificar el importe");
			form.importe.select();
			return false;
		}
		else if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
