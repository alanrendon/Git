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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Importes para Facturas de Oficinas</p>
  <form action="./ban_imp_ofi.php" method="post" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Fecha</th>
    <td class="vtabla">      <select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    <th class="vtabla">A&ntilde;o</th>
    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{anio}" size="4" maxlength="4"></td>
  </tr>
</table>
<br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" size="32"></td>
      <td class="tabla"><input name="importe[]" type="text" class="insert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="10" maxlength="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Imponer importe" onClick="imponer(form)">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p>
  </form>
  </td>
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
	
	function imponer(form) {
		var temp = prompt("Importe a imponer:");
		
		var importe = !isNaN(parseFloat(temp)) ? parseFloat(temp) : 0;
		
		if (importe > 0)
			for (i=0; i<form.importe.length; i++)
				form.importe[i].value = importe.toFixed(2);
		else
			return false;
	}
	
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.anio.select();
	}
	
	window.onload = document.form.anio.select();
</script>
</body>
</html>
