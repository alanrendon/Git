<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">INVENTARIO ROSTICERIA</P>
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
	if(document.form.temp.value== 0 && (document.form.num_cia.value <=0 || document.form.num_cia.value == ""))
	{
		alert("Ingrese un número de Compañia");
		document.form.num_cia.focus();
	}
	else
		document.form.submit();
		
}
</script>

<form action="ros_ifm_con.php" method="get" name="form">
<table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="3">Generar hoja de inventario por </th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <label>
        <input type="radio" name="tipo_con" value="0" checked onChange="document.form.temp.value=0">
  		Compañía</label>
        <input type="hidden" name="temp">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">      </td>
      <td class="tabla" align="center"><p>
        <label>
        <input type="radio" name="tipo_con" value="1" onChange="document.form.temp.value=1">
  Todas las compañía</label>
        <br>
      </p></td>
      <td class="tabla" align="center">Administrador
        <select name="idadmin" class="insert" id="idadmin">
          <option value="" selected="selected"></option>
          <!-- START BLOCK : idadmin -->
		  <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : idadmin -->
        </select>
        </td>
    </tr>
  </table>

<p>
<input type="button" name="enviar2" class="boton" value="Consultar" onclick='valida_registro()'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : inventario -->
<!-- START BLOCK : comp -->
<table width="102%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<p class="title">{nombre_cia}</P>
<p class="title">LISTADO DE MATERIAS PRIMAS PARA INVENTARIOS CORRESPONDIENTES A {nombre_mes}</P>
<p class="title">&nbsp; </P>
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
<table width="70%" class="tabla">
  <tr class="tabla">
    <th class="tabla" width="55%"><font size="+1">Nombre</font></th>
    <th class="tabla" width="35%"><font size="+1">Existencia</font></th>
    <th class="tabla" width="10%"><font size="+1">Unidad</font></th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla"><font size="+1">{nombre_mp}</font></th>
    <td class="tabla"><font size="+1">&nbsp;</font></td>
    <td class="vtabla"><font size="+1">{unidad}</font></td>
  </tr>
 <!-- END BLOCK : rows -->

</table>
<br>
<table width="600">
  <tr>
    <th width="40%" scope="row">&nbsp;</th>
    <td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td width="40%">&nbsp;</td>
  </tr>
  <tr>
    <th scope="row" style="border-top-width: 1px; border-top-style: solid;">FIRMA POLLERO </th>
    <td>&nbsp;</td>
    <th style="border-top-width: 1px; border-top-style: solid;">FIRMA SUPERVISOR </th>
  </tr>
</table></td>
</tr>
</table>
<!-- END BLOCK : comp -->
<!-- END BLOCK : inventario -->