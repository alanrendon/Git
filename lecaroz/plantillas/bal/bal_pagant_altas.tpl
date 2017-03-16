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
<td align="center" valign="middle"><p class="title">Alta de Pagos Anticipados</p>
  <form action="./bal_pagant_altas.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">&nbsp;</th>
      <th colspan="2" class="tabla" scope="col">Inicia</th>
      <th colspan="2" class="tabla" scope="col">Termina</th>
	  <th class="tabla" scope="col">&nbsp;</th>
	  <th class="tabla" scope="col">&nbsp;</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) mes1[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" size="30" readonly="true"></td>
      <td class="tabla"><input name="mes1[]" type="text" class="insert" id="mes1" onFocus="temp.value=this.value" onChange="if (isInt(this,temp) && this.value >= 1 && this.value <= 12) evaluarMes(this,anio1[{i}],mes2[{i}],anio2[{i}]); else this.value=temp.value" onKeyDown="if (event.keyCode == 13) anio1[{i}].select()" value="{mes}" size="2" maxlength="2"></td>
      <td class="tabla"><input name="anio1[]" type="text" class="insert" id="anio1" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) evaluarMes(mes1[{i}],this,mes2[{i}],anio2[{i}]); else this.value=temp.value" onKeyDown="if (event.keyCode == 13) mes2[{i}].select()" value="{anio}" size="4" maxlength="4"></td>
      <td class="tabla"><input name="mes2[]" type="text" class="insert" id="mes2" onFocus="temp.value=this.value" onChange="if (isInt(this,temp) && this.value >= 1 && this.value <= 12) evaluarMes(mes1[{i}],anio1[{i}],this,anio2[{i}]); else this.value=temp.value" onKeyDown="if (event.keyCode == 13) anio2[{i}].select()" value="{mes}" size="2" maxlength="2"></td>
      <td class="tabla"><input name="anio2[]" type="text" class="insert" id="anio2" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) evaluarMes(mes1[{i}],anio1[{i}],mes2[{i}],this); else this.value=temp.value" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" value="{anio}" size="4" maxlength="4"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="30" maxlength="100"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function evaluarMes(mes1,anio1,mes2,anio2) {
		var m1 = !isNaN(parseInt(mes1.value)) ? parseInt(mes1.value) : false;
		var y1 = !isNaN(parseInt(anio1.value)) ? parseInt(anio1.value) : false;
		var m2 = !isNaN(parseInt(mes2.value)) ? parseInt(mes2.value) : false;
		var y2 = !isNaN(parseInt(anio2.value)) ? parseInt(anio2.value) : false;
		
		var now = new Date({year},{month},{day});
		var year = now.getFullYear();
		var month = now.getMonth();
		var day = now.getDay();
		
		if (m1 && y1 && m2 && y2) {
			if (y1 < year) {
				alert("No puede especificar rangos de años pasados");
				anio1.value = year;
				anio1.select();
				return false;
			}
			else if (y1 > y2) {
				alert("El año de inicio debe ser menor al año de termino");
				anio2.value = anio1.value;
				anio2.select();
				return false;
			}
			else if (m1 == month - 1 && day > 5) {
				alert("No puede capturar del mes anterior");
				mes1.value = month;
				mes2.value = month;
				mes1.select();
				return false;
			}
			else if (m1 < month - 1) {
				alert("No puede capturar meses anteriores");
				mes1.value = month;
				mes2.value = month;
				mes1.select();
				return false;
			}
			else if (m1 > m2 && y1 == y2) {
				alert("El mes inicial debe ser mayor al mes de terminación");
				mes2.value = mes1.value;
				mes2.select();
				return false;
			}
		}
		else
			alert("error");
	}
	
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
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia[0].select();
	}
	
	window.onload = document.form.num_cia[0].select();
</script>
</body>
</html>
