<!--START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Datos Estad&iacute;sticos de Panader&iacute;as</p>
<form name="form" action="./bal_his_pan.php" method="get">
<input name="temp" type="hidden">
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp))actualiza_compania(this, nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) anio.select();" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) num_cia.select();" value="{anio}" size="10" maxlength="10"></td>
    </tr>
  </table>
  <p>
  <input type="button" class="boton" onclick='valida_registro(form)' value="Siguiente"></p>
</form>
</td>
</tr>
</table>
<script type="text/javascript" language="JavaScript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	function actualiza_compania(num_cia, nombre) {
		cia = new Array();// Materias primas
		<!-- START BLOCK : nom_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nom_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!--END BLOCK : obtener_datos -->


<!--START BLOCK : historico -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Hist&oacute;rico</p>

<form name="form" method="post" action="./bal_his_pan.php">
 <input name="num_cia" type="hidden" value="{num_cia}" id="num_cia">
  <input name="anio" type="hidden" value="{anio}" id="anio">
  <input name="temp" type="hidden">
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <td class="tabla"><font size="+1">{num_cia} {nombre_cia}</font> </td>
      <th class="tabla"><font size="+1">A&ntilde;o</font></th>
      <td class="tabla"><font size="+1">{anio}</font></td>
    </tr>
  </table>
  <p></p>
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla">Mes</th>
      <th class="tabla">Utilidad Neta </th>
      <th class="tabla">Ventas en Puerta </th>
      <th class="tabla">Abono Reparto </th>
      <th class="tabla">Clientes</th>
      <th class="tabla">Ingresos Ext.</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr class="tabla">
      <th class="vtabla" align="center">{nombre_mes}
        <input name="mes{i}" type="hidden" id="mes{i}" value="{mes}">
</th>
      <td class="tabla" align="center"><input name="utilidad{i}" type="text" class="rinsert" id="utilidad{i}" onFocus="temp.value=this.value" onChange="if (isFloat2(this,2,temp)) calcula_total(this,total_utilidad,temp)" onKeyDown="if (event.keyCode == 37) clientes{i}.select();
else if (event.keyCode == 39) venta{i}.select();
else if (event.keyCode == 38) utilidad{back}.select();
else if (event.keyCode == 13 || event.keyCode == 40) utilidad{next}.select();" value="{utilidad}" size="10" maxlength="10"></td>
      <td class="tabla" align="center"><input name="venta{i}" type="text" class="rinsert" id="venta{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcula_total(this,total_venta,temp)" onKeyDown="if (event.keyCode == 37) utilidad{i}.select();
else if (event.keyCode == 39) reparto{i}.select();
else if (event.keyCode == 38) venta{back}.select();
else if (event.keyCode == 13 || event.keyCode == 40) venta{next}.select();" value="{venta}" size="10" maxlength="10"></td>
      <td class="tabla" align="center"><input name="reparto{i}" type="text" class="rinsert" id="reparto{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcula_total(this,total_reparto,temp)" onKeyDown="if (event.keyCode == 37) venta{i}.select();
else if (event.keyCode == 39) clientes{i}.select();
else if (event.keyCode == 38) reparto{back}.select();
else if (event.keyCode == 13 || event.keyCode == 40) reparto{next}.select();" value="{reparto}" size="10" maxlength="10"></td>
      <td class="tabla" align="center"><input name="clientes{i}" type="text" class="rinsert" id="clientes{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcula_total(this,total_clientes,temp);" onKeyDown="if (event.keyCode == 37) reparto{i}.select();
else if (event.keyCode == 39) ingresos{i}.select();
else if (event.keyCode == 38) clientes{back}.select();
else if (event.keyCode == 13 || event.keyCode == 40) clientes{next}.select();" value="{clientes}" size="10" maxlength="10"></td>
      <td class="tabla" align="center"><input name="ingresos{i}" type="text" class="rinsert" id="ingresos{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) calcula_total(this,total_ingresos,temp);" onKeyDown="if (event.keyCode == 37) clientes{i}.select();
else if (event.keyCode == 39) utilidad{i}.select();
else if (event.keyCode == 38) ingresos{back}.select();
else if (event.keyCode == 13 || event.keyCode == 40) ingresos{next}.select();" value="{ingresos}" size="10" maxlength="10"></td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr class="tabla">
      <th class="tabla" align="center">TOTAL</th>
      <td class="tabla"><input name="total_utilidad" type="text" disabled="true" class="rnombre" id="total_utilidad" value="{utilidad}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="total_venta" type="text" disabled="true" class="rnombre" id="total_venta" value="{venta}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="total_reparto" type="text" disabled="true" class="rnombre" id="total_reparto" value="{reparto}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="total_clientes" type="text" disabled="true" class="rnombre" id="total_clientes" value="{clientes}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="total_ingresos" type="text" disabled="true" class="rnombre" id="total_ingresos" value="{ingresos}" size="10" maxlength="10"></td>
    </tr>
  </table>
  <p> 
      <input type="button" class="boton" value="Cancelar" onclick="history.back()">
&nbsp;&nbsp;
    <input type="button" class="boton" onclick='valida_registro(form)' value="Siguiente">
  </p>
</form>

</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.utilidad0.select();
	}
	
	function calcula_total(cantidad, total, temp) {
		var value_cantidad = !isNaN(parseFloat(cantidad.value))?parseFloat(cantidad.value):0;
		var value_total    = !isNaN(parseFloat(total.value))?parseFloat(total.value):0;
		var value_temp     = !isNaN(parseFloat(temp.value))?parseFloat(temp.value):0;
		
		if (value_cantidad != 0) {
			if (value_temp != 0)
				value_total -= value_temp;
			value_total += value_cantidad;
			
			total.value = value_total.toFixed(2);
			return;
		}
		else {
			if (value_temp != 0)
				value_total -= value_temp;
			
			total.value = value_total.toFixed(2);
			return;
		}
	}
	
	window.onload = document.form.utilidad0.select();
</script>
<!-- END BLOCK : historico -->