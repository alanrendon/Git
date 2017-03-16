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
<td align="center" valign="middle"><p class="title">Carta de Gasolina</p>
  <form action="./ban_car_gas.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla">D&iacute;a</th>
      <th class="tabla">Mes</th>
      <th class="tabla">Anio</th>
    </tr>
    <tr>
      <td class="tabla"><input name="dia" type="text" class="insert" id="dia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) anio.select();
else if (event.keyCode == 40) num_cia0.select();" value="{dia}" size="2" maxlength="2"></td>
      <td class="tabla"><select name="mes" class="insert" id="mes">
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
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia0.select();
else if (event.keyCode == 37) dia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla">Compa&ntilde;&iacute;a</th>
      <th class="tabla">Importe</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="cod_gasolina{i}" type="hidden" id="cod_gasolina{i}">
      <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia{i},cod_gasolina{i},importe{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) importe{i}.select();
else if (event.keyCode == 38) num_cia{back}.select();
else if (event.keyCode == 40) num_cia{next}.select();" size="3" maxlength="3">
        <input name="nombre_cia{i}" type="text" class="vnombre" id="nombre_cia{i}" size="30" maxlength="30" readonly="true"></td>
      <td class="tabla"><input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia{next}.select();
else if (event.keyCode == 37 || event.keyCode == 39) num_cia{i}.select();
else if (event.keyCode == 38) importe{back}.select();
else if (event.keyCode == 40) importe{next}.select();" size="10" maxlength="10"></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Generar Carta" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	// Validar y actualizar número y nombre de compañía
	function actualiza_cia(num_cia, nombre, cod_gasolina, importe) {
		// Arreglo con los nombres de las compañías
		cia = new Array();
		gas = new Array();
		imp = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		gas[{num_cia}] = '{cod_gasolina}';
		imp[{num_cia}] = '{importe}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				cod_gasolina.value = "";
				importe.value = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseInt(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				cod_gasolina.value = gas[parseInt(num_cia.value)];
				importe.value = imp[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			cod_gasolina.value = "";
			
			return false;
		}
	}
	
	function valida_registro(form) {
		if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?")) {
				window.open("","carta","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
				form.target = "carta";
				form.submit();
			}
			else
				form.num_cia0.select();
	}
	
	document.form.anio.select();
</script>
</body>
</html>
