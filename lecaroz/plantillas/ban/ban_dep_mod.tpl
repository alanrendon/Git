<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Modificar Dep&oacute;sito</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cerrar -->
<input name="cerrar" type="button" class="boton" onClick="window.opener.document.location.reload();self.close();" value="Cerrar">
<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function actualiza_mov(cod_mov, nombre) {
		// Arreglo con los nombres de las compañías
		mov = new Array();
		<!-- START BLOCK : nombre_mov -->
		mov[{cod_mov}] = '{nombre_mov}';
		<!-- END BLOCK : nombre_mov -->
		
		if (parseInt(cod_mov.value) > 0) {
			if (mov[parseInt(cod_mov.value)] == null) {
				alert("El código no. "+parseInt(cod_mov.value)+" no esta en el catálogo de movimientos bancarios");
				cod_mov.value = document.form.temp.value;
				cod_mov.focus();
				return false;
			}
			else {
				cod_mov.value = parseFloat(cod_mov.value);
				nombre.value  = mov[parseInt(cod_mov.value)];
				return;
			}
		}
		else if (cod_mov.value == "") {
			cod_mov.value = document.form.temp.value;
			return false;
		}
	}
	
	function valida_registro() {
		if (document.form.fecha_mov.value == "") {
			alert("Debe especificar la fecha de depósito");
			document.form.fecha_mov.select();
			return false;
		}
		else if (document.form.cod_mov.value <= 0) {
			alert("Debe especificar el código del movimiento");
			document.form.cod_mov.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
	}
</script>
<p class="title">Modificar Dep&oacute;sito</p>
<form name="form" method="post" action="./ban_dep_mod.php?tabla={tabla}">
<input name="id" type="hidden" value="{id}">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla">{num_cia} - {nombre_cia} </td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Fecha Deposito <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha_mov" type="text" class="insert" id="fecha_mov" onChange="isDate(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.cod_mov.select();
else if (event.keyCode == 38) form.cod_mov.select();" value="{fecha_mov}" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Fecha Conciliaci&oacute;n</th>
    <td class="vtabla">{fecha_con}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Importe</th>
    <td class="vtabla">{importe}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Concepto</th>
    <td class="vtabla">{concepto}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Codigo de Movimiento </th>
    <td class="vtabla"><input name="cod_mov" type="text" class="insert" id="cod_mov" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_mov(this,form.nombre_mov)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha_mov.select();
else if (event.keyCode == 38) form.fecha_mov.select();" value="{cod_mov}" size="3" maxlength="3">
    <input name="nombre_mov" type="text" disabled="true" class="vnombre" id="nombre_mov" value="{nombre_mov}"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar Ventana" onClick="self.close()">
  <input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro()" value="Modificar">
</p>
</form>
<!-- END BLOCK : modificar -->
</body>
</html>
