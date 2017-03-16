<!-- START BLOCK : numfilas -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">

<p class="title">Depósitos Manuales</p>
<form action="./ban_movimientos.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">N&uacute;mero de Depositos </th>
    <td class="vtabla"><input name="numfilas" type="text" id="numfilas" class="boton" size="3" maxlength="3" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)"></td>
  </tr>
</table>
<p>
  <input type="submit" name="Submit" value="Siguiente" class="boton">
</p>
</form>
<!-- END BLOCK : numfilas -->
<!-- START BLOCK : captura -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.cia.campo0.value <= 0) {
			alert('Debe especificar un numero para la compania');
			document.cia.campo0.focus();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.cia.submit();
			else
				document.cia.campo0.focus();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.cia.reset();
		else
			document.cia.campo0.focus();
	}
</script>
<p class="title">Depósitos Manuales</p>
<!-- movimientos_bancarios -->
<form action="./insert_ban_movimientos.php?tabla=companias" method="post" name="cia" id="cia">
  <table width="200" border="1" class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo Movimiento</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp))" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha_mov{i}.select();
else if (event.keyCode == 37) form.concepto{back}.select();
else if (event.keyCode == 38) form.num_cia{back}.select();
else if (event.keyCode == 40) form.num_cia{next}.select();" size="5" maxlength="5"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="fecha_mov{i}" type="text" class="insert" id="fecha_mov{i}" onChange="isDate(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe{i}.select();
else if (event.keyCode == 37) form.num_cia{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" size="10" maxlength="10"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<select name="cod_mov{i}" class="insert" id="cod_mov{i}">
		<!-- START BLOCK : movimientos -->
			<option value="{id}">{id}-{descripcion}</option>
		<!-- END BLOCK : movimientos -->
      </select>	  </td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37) form.fecha{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" size="12" maxlength="12"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_cia{next}.select();
else if (event.keyCode == 37) form.importe{i}.select();
else if (event.keyCode == 38) form.concepto{back}.select();
else if (event.keyCode == 40) form.concepto{next}.select();" size="50" maxlength="50"></td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p> <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
      <input name="enviar" type="button" class="boton" onClick='valida_registro()' value="Capturar">
    </p>
</form>
<!-- END BLOCK : captura -->