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
<!-- START BLOCK : pregunta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <p class="title">&iquest;Se imprimieron correctamente los cheques?
  </p>
  <p>
    <input type="button" class="boton" value="Si" onClick="document.location='./ban_che_error.php?ok=1'">
&nbsp;&nbsp;
<input type="button" class="boton" value="No" onClick="document.location='./ban_che_error.php?error=1'"> 
</p></td>
</tr>
</table>
<!-- END BLOCK : pregunta -->
<!-- START BLOCK : error -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p><strong><font face="Arial, Helvetica, sans-serif">Seleccione una opci&oacute;n</font></strong></p>
<form action="./ban_che_error.php" method="get"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">
<input name="opcion" type="radio" value="1" checked>
        Recorrer folios </th>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="opcion" type="radio" value="2">
        Reimprimir cheques </th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_error.php'">
    &nbsp;&nbsp;    
<input type="submit" class="boton" value="Siguiente >>"> 
    </p></form></td>
</tr>
</table>
<!-- END BLOCK : error -->
<!-- START BLOCK : saltar_folios_1 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recorrer Folios</p>
  <form action="./ban_che_error.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false;">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de folios a recorrer</th>
      <td class="vtabla"><input name="num_folios" type="text" class="insert" id="num_folios" onFocus="temp.value=this.value" onChange="isInt(this)" onKeyDown="if (event.keyCode == 13) sig.focus()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_error.php?error=1'">
&nbsp;&nbsp;    
<input name="sig" type="button" class="boton" id="sig" onClick="validar(this.form)" value="Siguiente >>">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_folios.value <= 0) {
			alert("Debe especificar cuantos folios son los que se van a recorrer");
			form.num_folios.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_folios.select();
</script>
<!-- END BLOCK : saltar_folios_1 -->
<!-- START BLOCK : saltar_folios_2 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recorrer Folios</p>
  <p><strong><font face="Arial, Helvetica, sans-serif">Favor de capturar los folios a recorrer</font></strong></p>
  <form action="./ban_che_error.php" method="post" name="form">
  <input name="num_cheque1" type="hidden" value="{num_cheque1}">
  <input name="num_cheque2" type="hidden" value="{num_cheque2}">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Folios</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cheque[]" type="text" class="insert" id="num_cheque" onFocus="temp.value=this.value" onChange="if (isInt(this)) rango(this,temp)" onKeyDown="if (event.keyCode == 13 && num_cheque.length != undefined) num_cheque[{next}].select()" size="8" maxlength="8"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_error.php?opcion=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Corregir Folios" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function rango(num_cheque, temp) {
		if (num_cheque.value == "") {
			return false;
		}
		else if (parseInt(num_cheque.value) < parseInt(form.num_cheque1.value) || parseInt(num_cheque.value) > parseInt(form.num_cheque2.value)) {
			alert("Los folios deben de estar dentro del rango de " + form.num_cheque1.value + " a " + form.num_cheque2.value);
			num_cheque.value = temp.value;
			return false;
		}
	}
	
	function validar() {
		var ok = true;
		
		if (form.num_cheque.length == undefined) {
			ok = parseInt(form.num_cheque.value) > 0 ? true : false;
		}
		else {
			for (i = 0; i < form.num_cheque.length; i++) {
				if (form.num_cheque[i].value == "" || form.num_cheque[i].value <= 0) {
					ok = false;
				}
			}
		}
		
		if (!ok) {
			alert("Debe de especificar todos los folios a recorrer");
			return false;
		}
		else {
			if (confirm("¿Son correctos todos los datos?")) {
				form.submit();
			}
		}
	}
	
	window.onload = form.num_cheque.length == undefined ? form.num_cheque.select() : form.num_cheque[0].select();
</script>
<!-- END BLOCK : saltar_folios_2 -->
<!-- START BLOCK : reimprimir -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Reimprimir Cheques</p>
  <form action="./ban_che_error.php" method="post" name="form">
  <input name="folio1" type="hidden" value="{folio1}" disabled>
  <input name="folio2" type="hidden" value="{folio2}" disabled>
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Reimprimir a partir del folio </th>
      <td class="vtabla"><input name="num_cheque_reim" type="text" class="insert" id="num_cheque_reim" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) reim.focus()" size="8" maxlength="8"></td>
      </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_error.php?error=1'">
&nbsp;&nbsp;
<input name="reim" type="button" class="boton" id="reim" onClick="validar()" value="Reimprimir">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function validar() {
		if (form.num_cheque_reim.value < form.folio1.value || form.num_cheque_reim.value > form.folio2.value) {
			alert("El rango debe estar entre " + form.folio1.value + " y " + form.folio2.value);
			form.num_cheque_reim.value = form.temp.value;
			form.num_cheque_reim.select();
			return false;
		}
		else if (confirm("¿Esta seguro de que desea reimprimir los cheques?")) {
			if (confirm("Regrese los cheques a la impresora y presione ACEPTAR para empezar a imprimirlos")) {
				form.submit();
			}
			else {
				form.num_cheque_reim.select();
				return false;
			}
		}
	}
	
	window.onload = form.num_cheque_reim.select();
</script>
<!-- END BLOCK : reimprimir -->
</body>
</html>
