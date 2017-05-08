<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">GENERAR HOJAS DE INVENTARIOS </P>
<form action="./bal_ifm_con_v2.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) siguiente.select()" size="3" maxlength="3">
      <input name="nombre_cia" type="text" id="nombre_cia2" size="30" disabled class="vnombre"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Administrador</th>
    <td class="vtabla"><select name="admin" id="admin" class="insert">
      <option value=""></option>
      <option value="-1" style="color:#C00; font-weight:bold;">ORDENAR POR ADMINISTRADOR</option>
      <!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
    </select>
    </td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo</th>
    <td class="vtabla"><input name="tipo" type="radio" value="inventario" checked>
      Hoja de inventario
        <input name="tipo" type="radio" value="recibo">
        Recibo de Av&iacute;o </td>
  </tr>
</table>


<p>
<input name="siguiente" type="button" class="boton" id="siguiente" onclick='validar(this.form)' value="Siguiente">
</p>
<p>
  <input type="button" name="enviar2" class="boton" value="Generar archivo PALM" onclick="document.location= './bal_palm_cap.php'">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
	var cia = new Array();
	<!-- START BLOCK : num_cia -->
	cia[{num_cia}] = "{nombre_cia}";
	<!-- END BLOCK : num_cia -->

	function cambiaCia(num, nombre) {
		if (num.value == "")
			nombre.value = "";
		else if (cia[num.value] != null)
			nombre.value = cia[num.value];
		else {
			alert("La compañía no se encuentra en el catálogo");
			num.value = num.form.temp.value;
			num.select();
		}
	}

	function validar(form) {
		form.submit();
	}

	window.onload=document.form.num_cia.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cia -->
<p class="title" align="center"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</strong> </P>
<p class="title" align="center"><strong>LISTADO DE MATERIAS PRIMAS PARA INVENTARIOS CORRESPONDIENTES A {nombre_mes} <br>{num_cia}&nbsp;{nombre_cia}</strong></P>
<!-- START BLOCK : hoja -->
<table width="90%" border="0" align="center" cellpadding="0">
  <!-- START BLOCK : nombre_cia -->
  <tr class="tabla">
    <th class="tabla" colspan="4" style="border-color:#000000 "><font size="+1">{num_cia} {nombre_cia}</font></th>
  </tr>
  <!-- END BLOCK : nombre_cia -->
  <tr class="tabla">
    <th class="tabla" width="65%" colspan="2" style="border-color:#000000 "><font size="+1">Nombre</font></th>
    <th class="tabla" width="25%" style="border-color:#000000 "><font size="+1">Existencia</font></th>
    <th class="tabla" width="10%" style="border-color:#000000 "><font size="+1">Unidad</font></th>
  </tr>
<!-- START BLOCK : fila -->
<!-- START BLOCK : empaque -->
	<tr class="tabla">
	<td class="tabla" colspan="4" style="border-color:#000000 ">
	<font size="+1"><strong>MATERIAL DE EMPAQUE</strong></font>
	</td>
	</tr>
<!-- END BLOCK : empaque -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	<td class="tabla" style="border-color:#000000 "><font size="+1">{codmp}</font></td>
    <td class="vtabla" style="border-color:#000000 "><font size="+1">{nombre_mp}</font></td>
    <td class="tabla" style="border-color:#000000 "><font size="+1">&nbsp;</font></td>
    <td class="vtabla" style="border-color:#000000 "><font size="+1">{unidad}</font></td>
  </tr>
 <!-- END BLOCK : fila -->
</table>
<!-- START BLOCK : notas -->
<table align="center">
  <tr>
    <th align="left" class="vtabla" scope="row">Block de Pastel </th>
    <td>___________________________________</td>
  </tr>
  <tr>
    <th align="left" class="vtabla" scope="row">Block Amarillos </th>
    <td>___________________________________</td>
  </tr>
  <tr>
    <th align="left" class="vtabla" scope="row">Notas Venta Pan </th>
    <td>___________________________________</td>
  </tr>
  <tr>
    <th align="left" class="vtabla" scope="row">Notas Venta Pollo </th>
    <td>___________________________________</td>
  </tr>
  <tr>
    <th align="left" class="vtabla" scope="row">Comprobantes Cometra </th>
    <td>___________________________________</td>
  </tr>
  <tr>
    <th align="left" class="vtabla" scope="row">Folios Efectivo </th>
    <td>___________________________________</td>
  </tr>
</table>
<!-- END BLOCK : notas -->
<!-- START BLOCK : salto_pagina -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto_pagina -->
<!-- START BLOCK : salto_hoja_par -->
<br>
<!-- END BLOCK : salto_hoja_par -->
<!-- END BLOCK : hoja -->
<!-- END BLOCK : cia -->

<!-- START BLOCK : recibo_avio -->
<table width="100%"  height="49%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
  <p>&nbsp;</p>
  <p class="title" align="center">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
  <p class="encabezado" align="center">PANIFICADORA {num_cia}&nbsp;{nombre}</p>
  <p class="encabezado" align="center">AVIO RECIBIDO EL DIA ULTIMO DE CADA MES<br>
    ESTOS PRODUCTOS NO SE TOMARON EN CUENTA EN EL INVENTARIO</p>
<p>
<table width="70%" border="0" align="center" >
  <tr class="tabla">
    <td class="tabla" style="border-color:#000000 ">CANTIDAD</td>
    <td class="tabla" style="border-color:#000000 ">DESCRIPCION</td>
    <td class="tabla" style="border-color:#000000 ">PROVEEDOR</td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- START BLOCK : jump -->
<br style="page-break-after:always;">
<!-- END BLOCK : jump -->
<!-- END BLOCK : recibo_avio -->

