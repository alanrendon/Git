<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CAPTURA INVENTARIO FIN DE MES</P>
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

function valida_registro()
{
	if(document.form.num_cia.value <=0)
	{
		alert("Ingrese un número de Compañia");
		document.form.num_cia.focus();
	}
	else
		document.form.submit();
		
}
</script>
<form action="ros_invfm_cap.php" method="get" name="form">
<table class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" onKeyDown="if (event.keyCode == 13) enviar2.focus()" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
    </tr>
  </table>

<p>
<input type="button" name="enviar2" class="boton" value="Capturar" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : inventario -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">{nombre_cia}</P>

<script language="JavaScript" type="text/JavaScript">
function diferencias(inventario,existencia,diferencia,temp) {
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

function valida_registro()
{
if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();		
}


</script>


<form action="./insert_invfm_cap.php?tabla={tabla}&ros=1" method="post" name="form">
<input name="temp" type="hidden">
<input name="numfilas" type="hidden" value="{numfilas}">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Código materia Prima</th>
    <th class="tabla">Nombre</th>
    <th class="tabla">Existencia</th>
    <th class="tabla">Inventario</th>
    <th class="tabla">Diferencias</th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla">{codmp}<input name="codmp{i}" type="hidden" value="{codmp}"><input name="num_cia{i}" type="hidden" value="{num_cia}">
      <input name="fecha{i}" type="hidden" value="{fecha}">
      <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}"></th>
    <td class="vtabla">{nombre_mp}</td>
    <td class="tabla">{fexistencia}<input name="existencia{i}" type="hidden" value="{existencia}"></td>
    <td class="tabla"><input name="inventario{i}" type="text" class="rinsert" id="inventario{i}" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) diferencias(this,existencia{i},diferencia{i},temp);" onKeyDown="if(event.keyCode == 13 || event.keyCode == 40) inventario{next}.select();
else if (event.keyCode == 38) inventario{back}.select();" value="{inventario}" size="10"></td>
    <td class="tabla"><input name="diferencia{i}" type="text" class="rnombre" id="diferencia{i}" value="{diferencia}" size="10" readonly></td>
  </tr>
 <!-- END BLOCK : rows -->

</table>
<br>
<br>
<input name="enviar" type="button" class="boton" id="enviar" onClick='parent.history.back()' value="Regresar">
&nbsp;&nbsp;
<input type="button" name="enviar22" class="boton" value="Capturar" onclick='valida_registro()'>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
window.onload = document.form.inventario0.select();
</script>
<!-- END BLOCK : inventario -->