<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
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

function valida_registro(mes,anio)
{
	if(parseInt(document.form.anio.value) > parseInt(anio))
	{
		alert("Año incorrecto");
	}

	if(parseInt(document.form.mes.value) > parseInt(mes))
	{
		alert("Mes incorrecto");
	}

	if(document.form.num_cia.value == "")
	{
		alert("Ingrese un número de Compañia");
		document.form.num_cia.focus();
	}
	else if(document.form.mes.value == "")
	{
		alert("Ingrese el mes");
		document.form.mes.select();
	}
	else if(document.form.anio.value == "")
	{
		alert("Ingrese el año");
		document.form.anio.select();
	}

	else
		document.form.submit();
		
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA INVENTARIO FIN DE MES</P>
<form action="./pan_invfm_cap.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" size="3" maxlength="3" onKeyDown="if(event.keyCode==13)document.form.mes.select();">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">Mes 
        <input name="mes" type="text" class="insert" id="mes" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if(event.keyCode==13)document.form.anio.select();" value="{mes}" size="3"> 
        A&ntilde;o 
        <input name="anio" type="text" class="insert" id="anio" onKeyDown="if(event.keyCode==13)document.form.enviar2.focus();" value="{anio}" size="5"></td>
    </tr>
  </table>

<p>
<input type="button" name="enviar2" class="boton" value="Capturar" onclick='valida_registro({mes},{anio});'>
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select()
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : inventario -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA INVENTARIO FIN DE MES</P>
<p class="title">{nombre_cia}<br>{fecha}</P>
<form action="./insert_invfm_cap.php?tabla={tabla}" method="post" name="form">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<table class="tabla">
  <tr class="tabla">
    <th colspan="2" class="tabla">Código y Nombre de Materia Prima </th>
    <th class="tabla">Existencia</th>
    <th class="tabla">Inventario</th>
    <th class="tabla">Diferencias</th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla">{codmp}<input name="codmp{i}" type="hidden" value="{codmp}"><input name="num_cia{i}" type="hidden" value="{num_cia}">
      <input name="fecha{i}" type="hidden" value="{fecha}">
      <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}"></th>
    <td class="vtabla">{nombre_mp}</td>
    <td class="rtabla">{fexistencia}
      <input name="existencia{i}" type="hidden" value="{existencia}"></td>
    <td class="tabla"><input name="inventario{i}" type="text" class="rinsert" id="inventario{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) diferencia(this,existencia{i},diferencia{i},temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) inventario{next}.select();
else if (event.keyCode == 38) inventario{back}.select();" value="{inventario}" size="10"></td>
    <th class="tabla"><input name="diferencia{i}" type="text" class="rnombre" id="diferencia{i}" value="{diferencia}" size="10" readonly></th>
  </tr>
 <!-- END BLOCK : rows -->

</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo y Nombre de Materia Prima </th>
    <th class="tabla" scope="col">Inventario</th>
  </tr>
  <!-- START BLOCK : new_mp -->
  <tr>
    <td class="vtabla"><input name="new_codmp{i}" type="text" class="insert" id="new_codmp{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_mp(this,nombre_mp{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) new_inventario{i}.select();
else if (event.keyCode == 38) new_codmp{back}.select();
else if (event.keyCode == 40) new_codmp{next}.select();" size="4" maxlength="4">
      <input name="new_num_cia{i}" type="hidden" id="new_num_cia{i}" value="{num_cia}">
      <input name="new_fecha{i}" type="hidden" id="new_fecha{i}" value="{fecha}">
      <input name="nombre_mp{i}" type="text" disabled="true" class="vnombre" id="nombre_mp{i}" size="40" maxlength="30"></td>
    <td class="tabla"><input name="new_inventario{i}" type="text" class="rinsert" id="new_inventario{i}" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) new_codmp{next}.select();
else if (event.keyCode == 37) new_codmp{i}.select();
else if (event.keyCode == 38) new_inventario{back}.select();
else if (event.keyCode == 40) new_inventario{next}.select();" size="10" maxlength="10"></td>
  </tr>
  <!-- END BLOCK : new_mp -->
</table>


<p>
<input type="button" class="boton" onclick='history.back()' value="Regresar">
&nbsp;&nbsp;
<input type="button" class="boton" value="Capturar" onClick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
function diferencia(inventario,existencia,diferencia,temp) {
	var value_inventario = !isNaN(parseFloat(inventario.value))?parseFloat(inventario.value):0;
	var value_existencia = !isNaN(parseFloat(existencia.value))?parseFloat(existencia.value):0;
	var value_diferencia = !isNaN(parseFloat(diferencia.value))?parseFloat(diferencia.value):0;
	var value_temp       = !isNaN(parseFloat(temp.value))?parseFloat(temp.value):0;
	
	if (value_inventario != 0) {
		if (value_temp > 0)
			value_diferencia += value_temp;
		
		value_diferencia = value_existencia - value_inventario;
		diferencia.value = value_diferencia.toFixed(2);
	}
	else {
		if (value_temp > 0)
			value_diferencia += value_temp;
		
		diferencia.value = value_diferencia.toFixed(2);
	}
}

function actualiza_mp(codmp, nombre) {
	mp = new Array();// Materias primas
	<!-- START BLOCK : mp -->
	mp[{codmp}] = '{nombre}';
	<!-- END BLOCK : mp -->
			
	if (codmp.value > 0) {
		if (mp[codmp.value] == null) {
			alert("Código "+codmp.value+" no esta en el catálogo de materias primas");
			codmp.value = "";
			nombre.value  = "";
			codmp.focus();
		}
		else {
			nombre.value   = mp[codmp.value];
		}
	}
	else if (codmp.value == "") {
		codmp.value = "";
		nombre.value  = "";
	}
}

function valida_registro()
{
if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();		
}
	
	window.onload = document.form.inventario0.select();
</script>
<!-- END BLOCK : inventario -->