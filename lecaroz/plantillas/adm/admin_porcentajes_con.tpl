<!-- START BLOCK : obtener_datos -->
<link href="/styles/imp.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Porcentajes</P>
<form name="form" method="get" action="./admin_porcentajes_con.php">
<input name="temp" type="hidden" value="">
  <table class="tabla">
  <tr class="tabla">
  <th class="tabla" colspan="2">Mes 
		<select name="mes" size="1" class="insert" id="mes">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		
    A&ntilde;o
    <input name="anio" type="text" class="insert" value="{anio_actual}" size="5"> </th>
  </tr>
  <tr class="tabla">
    <td class="vtabla">
        <label>
        <input name="tipo_con" type="radio" value="1" checked onChange="form.cont.value=1;">
  Porcentajes de Accionistas</label>
        <br>
        <label>
        <input type="radio" name="tipo_con" value="0" onChange="form.cont.value=0;">
  Porcentajes de Distribuciones</label>
	  <input name="con" type="hidden" class="insert" id="con" value="0" size="5" maxlength="5">	</td>
  </tr>
  <tr class="tabla">
    <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
      Efectivo<br>
      <input name="tipo" type="radio" value="2">
      General</td>
  </tr>
</table>
  <p>
  <input type="button" name="enviar" class="boton" value="Consultar" onclick='document.form.submit();'>
  </p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_todos -->
<table width="100%"  height="99%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
	<table width="100%" class="print2" rules="rows">
	  <tr class="print2">
		<th scope="col" class="print2" width="20%">Cia</th>
		<th scope="col" class="print2"width="10%">TOTAL</th>
		<!-- START BLOCK : accionistas -->
		<th scope="col" class="print2" width="8%">{accionista}</th>
		<!-- END BLOCK : accionistas -->
	  </tr>

		<!-- START BLOCK : rows1 -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vprint3">
		{num_cia}&nbsp;&nbsp;{nom_cia}
		</td>
		<td class="rprint3">{importe}</td>
		<!-- START BLOCK : porcentaje -->
		<td class="rprint2">{porcentaje}</td>
		<!-- END BLOCK : porcentaje -->
	  </tr>
		<!-- END BLOCK : rows1 -->
<!-- START BLOCK : totales -->
	<tr class="print2">
		<td class="print2"><strong>
		TOTAL
		</strong></td>
		<td class="rprint2"><font size="2"><strong>
		{total_compania}
		</strong></font></td>
		<!-- START BLOCK : totales_accionistas -->
		<td class="rprint2">{total_accionista}</td>
		<!-- END BLOCK : totales_accionistas -->
	</tr>
<!-- END BLOCK : totales -->
	</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_todos -->
