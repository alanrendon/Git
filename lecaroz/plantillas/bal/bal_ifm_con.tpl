<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">GENERAR HOJAS DE INVENTARIOS </P>
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

<form action="./bal_ifm_con.php" method="get" name="form">
<table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="2">Generar hoja de inventario por </th>

    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <label>
        <input type="radio" name="tipo_con" value="0" checked onChange="document.form.temp.value=0">
  		Compañía</label>
        <input type="hidden" name="temp">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
      <td class="tabla" align="center"><p>
        <label>
        <input type="radio" name="tipo_con" value="1" onChange="document.form.temp.value=1">
  Todas las compañía</label>
        <br>
      </p></td>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="2" align="center"><p>
        <label>
        <input name="tipo_consulta" type="radio" value="0" checked>
  Hoja de inventario</label>

        <label>
        <input type="radio" name="tipo_consulta" value="1">
  Recibo de avío</label>
        <br>
      </p></td>

    </tr>
  </table>

<p>
<input type="button" name="enviar2" class="boton" value="Consultar" onclick='valida_registro()'>
</p>
<p>
  <input type="button" name="enviar2" class="boton" value="Generar archivo PALM" onclick="document.location= './bal_palm_cap.php'">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : inventario -->



<!-- START BLOCK : comp -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</strong> </P>
<p class="title"><strong>LISTADO DE MATERIAS PRIMAS PARA INVENTARIOS CORRESPONDIENTES A {nombre_mes} <br>{num_cia}&nbsp;{nombre_cia}</strong></P>
<table width="90%" border="0" cellpadding="0">
  <tr class="tabla">
    <th class="tabla" width="65%" colspan="2"><font size="+1">Nombre</font></th>
    <th class="tabla" width="25%"><font size="+1">Existencia</font></th>
    <th class="tabla" width="10%"><font size="+1">Unidad</font></th>
  </tr>
<!-- START BLOCK : rows -->
<!-- START BLOCK : empaque -->
	<tr class="tabla">
	<td class="tabla" colspan="4">
	<font size="+1"><strong>MATERIAL DE EMPAQUE</strong></font>
	</td>
	</tr>
<!-- END BLOCK : empaque -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	<th class="tabla"><font size="+1">{codmp}</font></th> 
    <th class="vtabla"><font size="+1">{nombre_mp}</font></th>
    <td class="tabla"><font size="+1">&nbsp;</font></td>
    <td class="vtabla"><font size="+1">{unidad}</font></td>
  </tr>
 <!-- END BLOCK : rows -->
</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : comp -->

<!-- END BLOCK : inventario -->

<!-- START BLOCK : recibo_avio -->
<table width="100%"  height="49%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
  <p>&nbsp;</p>
  <p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
  <p class="encabezado">PANIFICADORA {num_cia}&nbsp;{nombre}</p>
  <p class="encabezado">AVISO RECIBIDO EL DIA ULTIMO DE CADA MES<br>ESTOS PRODUCTOS NO SE TOMARON EN CUENTA EN EL INVENTARIO</p>
<p>
<table width="70%" border="0" >
  <tr class="tabla">
    <td class="tabla">CANTIDAD</td>
    <td class="tabla">DESCRIPCION</td>
    <td class="tabla">PROVEEDOR</td>
  </tr>
</table>
  </td>
</tr>
</table>
<!-- START BLOCK : jump -->
<br style="page-break-after:always;">
<!-- END BLOCK : jump -->
<!-- END BLOCK : recibo_avio -->

