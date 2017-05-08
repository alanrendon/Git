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
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar {nombre_mov} </p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="tabla">Cuenta</th>
  </tr>
  <tr>
    <td class="tabla" scope="row">{num_cia} - {nombre_cia} </td>
    <td class="tabla">{cuenta}</td>
  </tr>
</table>
  <br>
  <form action="./ban_esc_minimod.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="id" type="hidden" value="{id}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) importe.select();
else if (event.keyCode == 38) concepto.select();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) concepto.select();
else if (event.keyCode == 38) fecha.select();" value="{importe}" size="10" maxlength="10" {readonly}></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Movimiento </th>
      <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}" {selected}>{cod_mov} - {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) fecha.select();
else if (event.keyCode == 38) importe.select();" value="{concepto}" size="50" maxlength="200"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Afectar al Saldo </th>
      <td class="vtabla"><input name="saldo_libros" type="checkbox" id="saldo_libros" value="TRUE" checked>
        Libros&nbsp;&nbsp;&nbsp;
        <!--
		<input name="saldo_bancos" type="checkbox" id="saldo_bancos" value="TRUE" checked {disabled}>
        Bancos
		-->
		</td>
    </tr>
    <tr>
    	<td colspan="2" class="vtabla" scope="row">&nbsp;</td>
    	</tr>
    <tr>
    	<th class="vtabla" scope="row">Local</th>
    	<td class="vtabla"><select name="local" class="insert" id="local">
    		<option value=""></option>
			<!-- START BLOCK : local -->
			<option value="{value}"{selected}>{text}</option>
			<!-- END BLOCK : local -->
    		</select></td>
    	</tr>
    <tr>
    	<th class="vtabla" scope="row">A&ntilde;o</th>
    	<td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
    	</tr>
    <tr>
    	<th class="vtabla" scope="row">Mes</th>
    	<td class="vtabla"><select name="mes" class="insert" id="mes">
    		<option value=""></option>
			<option value="1"{1}>ENERO</option>
    		<option value="2"{2}>FEBRERO</option>
    		<option value="3"{3}>MARZO</option>
    		<option value="4"{4}>ABRIL</option>
    		<option value="5"{5}>MAYO</option>
    		<option value="6"{6}>JUNIO</option>
    		<option value="7"{7}>JULIO</option>
    		<option value="8"{8}>AGOSTO</option>
    		<option value="9"{9}>SEPTIEMBRE</option>
    		<option value="10"{10}>OCTUBRE</option>
    		<option value="11"{11}>NOVIEMBRE</option>
    		<option value="12"{12}>DICIEMBRE</option>
    		</select></td>
    	</tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.importe.value <= 0) {
			alert("Debe especificar el importe");
			form.importe.select();
			return false;
		}
		else if (form.concepto.value == "") {
			alert("Debe especificar el concepto");
			form.concepto.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.fecha.select();
	}
	
	window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : modificar -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : cerrar_error -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : cerrar_error -->
</body>
</html>
