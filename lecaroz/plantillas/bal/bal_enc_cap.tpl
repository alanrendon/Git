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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Control de Encargados para Fin de Mes </p>
  <form action="./bal_enc_cap.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
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
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this)" onKeyDown="if (event.keyCode == 13) return false" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value < 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.anio.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Control de Encargados para Fin de Mes</p>
  <form action="./bal_enc_cap.php" method="post" name="form">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <th class="tabla"><input name="mes" type="hidden" id="mes" value="{mes}">
        {mes_escrito}</th>
      <th class="tabla"><input name="anio" type="hidden" id="anio" value="{anio}">
        {anio}</th>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Inici&oacute;</th>
      <th class="tabla" scope="col">Termin&oacute;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
	  <td class="vtabla"><input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}">
	    {num_cia}</td>
      <td class="vtabla">{nombre_cia} </td>
      <td class="tabla"><input name="nombre_inicio{i}" type="text" class="vinsert" id="nombre_inicio{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) nombre_fin{i}.select();
else if (event.keyCode == 38) nombre_inicio{back}.select();
else if (event.keyCode == 40) nombre_inicio{next}.select();" value="{nombre_inicio}" size="60" maxlength="60"></td>
      <td class="tabla"><input name="nombre_fin{i}" type="text" class="vinsert" id="nombre_fin{i}" onKeyDown="if (event.keyCode == 13) nombre_inicio{next}.select();
else if (event.keyCode == 37) nombre_inicio{i}.select();
else if (event.keyCode == 38) nombre_fin{back}.select();
else if (event.keyCode == 40) nombre_fin{next}.select();" value="{nombre_fin}" size="60" maxlength="60"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location = './bal_enc_cap.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.nombre_inicio.select();
	}
	
	window.onload = document.form.nombre_inicio0.select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
