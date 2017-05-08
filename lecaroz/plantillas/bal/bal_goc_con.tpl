
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">



<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Gastos pagados por otras compa&ntilde;&iacute;as</p>
<form name="form" method="get" action="./bal_goc_con.php">
<table class="tabla">
  <tr>
    <th class="tabla">Mes</th>
	<td class="vinsert">
		<select name="mes" class="insert" id="mes">
		  <option value="1">ENERO</option>
		  <option value="2">FEBRERO</option>
		  <option value="3">MARZO</option>
		  <option value="4">ABRIL</option>
		  <option value="5">MAYO</option>
		  <option value="6">JUNIO</option>
		  <option value="7">JULIO</option>
		  <option value="8">AGOSTO</option>
		  <option value="9">SEPTIEMBRE</option>
		  <option value="10">OCTUBRE</option>
		  <option value="11">NOVIEMBRE</option>
		  <option value="12">DICIEMBRE</option>
		</select>
	</td>
	
	<th class="tabla">Año</th>
	<td><input name="anio" type="text" value="{anio}" size="5" maxlength="4" class="insert"> </td>
  </tr>
</table>
<br>
<p><img src="./menus/insert.gif" width="16" height="16">  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Gastos pagados por otras compa&ntilde;&iacute;as</p>
<table class="tabla">
  <tr>
    <th class="tabla">Cia. que presto </th>
    <th class="tabla">Concepto</th>
    <th class="tabla">Cia. a la que prestaron </th>
    <th class="tabla">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{nom_cia}</td>
    <td class="tabla">{concepto}</td>
    <td class="tabla">{nom_cia2} </td>
    <td class="tabla">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</td>
</tr>
</table>
<!-- END BLOCK : gastos -->
