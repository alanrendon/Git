<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="title">Alta de Tanque de Gas </p>
<script language="javascript" type="text/javascript">
// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
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
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}
	
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
		}
		else if (document.form.num_tanque.value <= 0) {
			alert("Debe especificar el número de tanque para la compañía");
			document.form.num_tanque.select();
		}
		else if (document.form.capacidad.value <= 0) {
			alert("Debe especificar la capacidad del tanque");
			document.form.capacidad.select();
		}
		else {
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_cia.select();
	}
</script>
 <table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="./fac_glp_altas.php?tabla={tabla}" method="post">
<input type="hidden" name="temp" value="">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if (parseInt(this.value) >= 0 || this.value == '')
actualiza_compania(this,form.nombre_cia);
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_tanque.select();" size="5" maxlength="5">
    <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">N&uacute;mero de tanque</th>
    <td class="vtabla"><input name="num_tanque" type="text" class="insert" id="num_tanque3" onFocus="form.temp.value=this.value" onChange="if (this.value == '') return;
else if ((parseInt(this.value) >= 0 && parseInt(this.value) <= 10)) {
var temp=parseInt(this.value); this.value=temp;}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.nombre.select();
else if (event.keyCode == 38) form.num_cia.select();" size="2" maxlength="2"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Nombre</th>
    <td class="vtabla"><input name="nombre" type="text" class="insert" id="nombre" onFocus="form.temp.value=this.value" onChange="this.value=this.value.toUpperCase()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.capacidad.select();
else if (event.keyCode == 38) form.num_tanque.select();" size="2" maxlength="2"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Capacidad</th>
    <td class="vtabla">    <input name="capacidad" type="text" class="insert" id="capacidad" onFocus="form.temp.value=this.value" onChange="if (this.value=='') return;
else if (parseInt(this.value) >= 0) {
var temp=parseInt(this.value); this.value=temp;}
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.enviar.focus();
else if (event.keyCode == 38) form.num_tanque.select();" size="8" maxlength="8"></td>
  </tr>
</table>
<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input type="button" class="boton" value="Borrar" onClick="borrar();">
&nbsp;&nbsp;&nbsp;&nbsp;<img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" value="Alta de tanque" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
</body>
</html>
