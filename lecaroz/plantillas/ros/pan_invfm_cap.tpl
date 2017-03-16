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
<form action="pan_invfm_cap.php" method="get" name="form">
<table class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" size="3" maxlength="3">
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
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : inventario -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">{nombre_cia}</P>

<script language="JavaScript" type="text/JavaScript">
function diferencias(inventario,existencia,diferencia){
var inv=parseFloat(inventario);
var exis;
if (existencia=='') exis=0;
else exis=parseFloat(existencia);
var resul=0;
diferencia.value=exis-inv;
}

function valida_registro()
{
if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();		
}


</script>


<form action="insert_invfm_cap.php?tabla={tabla}" method="post" name="form">

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
      <input name="fecha{i}" type="text" value="{fecha}">
      <input name="precio_unidad{i}" type="hidden" value="{precio_unidad}"></th>
    <td class="vtabla">{nombre_mp}</td>
    <td class="tabla">{existencia}<input name="existencia{i}" type="hidden" value="{existencia}"></td>
    <td class="tabla">&nbsp;<input name="inventario{i}" type="text" class="insert" id="inventario{i}" onKeyDown="if(event.keyCode == 13){document.form.inventario{next}.select();}" value="0" size="10" onChange="diferencias(this.value,form.existencia{i}.value,form.diferencia{i});"></td>
    <td class="tabla"><input name="diferencia{i}" type="text" class="nombre" id="diferencia{i}" value="0" size="10" readonly></td>
  </tr>
 <!-- END BLOCK : rows -->

</table>
<br>
<br>
<input type="button" name="enviar22" class="boton" value="Capturar" onclick='valida_registro()'>
<input name="enviar" type="button" class="boton" id="enviar" onclick='parent.history.back()' value="Regresar">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : inventario -->