<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado del cat&aacute;logo de Gastos </p>
<form name="form" action="./fac_gastos_con.php?tabla={tabla}" method="get" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();">
<table class="tabla">
	<tr class"tabla">
		<th class="tabla">C&oacute;digo Estado Resultados </th>
		<th class="tabla">Ordenar</th>
	    </tr>
	<tr class"tabla">
		<td class="vtabla" align="center"><p>
		  <label>
		  <input name="tipo_mat" type="radio" value="0" checked onChange="form.estado.value=0;">
  No incluidos </label>
		  <br>
		  <label>
		  <input type="radio" name="tipo_mat" value="1" onChange="form.estado.value=1;">Gastos de operaci&oacute;n</label><br>
		  <label>
		  <input type="radio" name="tipo_mat" value="2" onChange="form.estado.value=2;">Gastos generales </label><br>
		  <label>
		  <input type="radio" name="tipo_mat" value="3" onChange="form.estado.value=3;">TODOS</label>
		  <input name="estado" type="hidden" id="estado" value="0" size="5">
		  </p></td>
		<td class="vtabla" align="center"><p>
		  <label>
		  <input name="tipo_orden" type="radio" value="0" checked onChange="form.orden.value=0;">
  Alfabéticamente</label>
		  <br>
		  <label>
		  <input type="radio" name="tipo_orden" value="1" onChange="form.orden.value=1;">
  Por código</label>
		  <input name="orden" type="hidden" value="0" size="5">
		  <br>
		  </p></td>
  		</tr>
<!--		
	<tr class"tabla">
	  <td class="vtabla" align="center" colspan="2">
	    <p>
	      <label><input name="tipo_control" type="radio" value="0" checked onChange="form.tipo.value=0;">Variables</label>
	      <br>
	      <label><input type="radio" name="tipo_control" value="1"  onChange="form.tipo.value=1;">Fijos</label>
		  <input name="tipo" type="hidden" id="tipo" value="0" size="5">
	      </p>
	  </td>
	  </tr>
-->
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="document.form.submit();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado">
OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. Y C.V.<br>
LISTADO DE GASTOS {concepto} 
</p>

  <table class="print">
    <tr class="print">
      <th class="print" scope="col"><strong>C&oacute;digo</strong></th>
      <th class="print" scope="col"><strong>Nombre</strong></th>
	  <!-- START BLOCK : estado -->
	      <th class="print" scope="col"><strong>C&oacute;digo estado de resultados </strong></th>
	  <!-- END BLOCK : estado -->
	      <th class="print" scope="col"><strong>Tipo de Gasto</strong></th>
          <th class="print" scope="col">Aplicaci&oacute;n del Gasto </th>
    </tr>
<!-- START BLOCK : rows -->
    <tr class="print" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{codgastos}</td>
      <td class="vprint">{nombre}</td>
	  <!-- START BLOCK : edo_resul -->
      <td class="vprint">{edo_resul}</td>
	  <!-- END BLOCK : edo_resul -->
      <td class="print">{tipo}</td>
      <td class="print">{ap}</td>
    </tr>
	  
<!-- END BLOCK : rows -->
  </table>

</td>
</tr>
</table>
<!-- END BLOCK : listado -->