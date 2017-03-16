<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado del cat&aacute;logo de Materias Primas</p>
<form name="form" action="./fac_mp_con.php?tabla={tabla}" method="get" onKeyDown="if(event.keyCode==13) document.form.enviar.focus();">
<table class="tabla">
	<tr class"tabla">
		<th class="tabla">Por tipo </th>
		<th class="tabla">Ordenar</th>
	    </tr>
	<tr class"tabla">
		<td class="vtabla" align="center"><p>
		  <label>
		  <input name="tipo_mat" type="radio" value="0" checked onChange="form.materia.value=0;">
  Materia Prima</label>
		  <br>
		  <label>
		  <input type="radio" name="tipo_mat" value="1" onChange="form.materia.value=1;">
  Material de Empaque</label>
  <br>
		  <label>
		  <input type="radio" name="tipo_mat" value="2" onChange="form.materia.value=2;">
  Control</label>
		<input name="materia" type="hidden" value="0">
		  </p></td>
		<td class="vtabla" align="center"><p>
		  <label>
		  <input name="tipo_orden" type="radio" value="0" checked onChange="form.orden.value=0;">
  Alfabéticamente</label>
		  <br>
		  <label>
		  <input type="radio" name="tipo_orden" value="1" onChange="form.orden.value=1;">
  Por código</label>
		  <input name="orden" type="hidden" value="0">
		  <br>
		  </p></td>
  		</tr>
	<tr class"tabla">
	  <td class="vtabla" align="center" colspan="2">
	  
	    <p>
	      <label>
	      <input name="tipo_control" type="radio" value="0" checked onChange="form.control.value=0;">
  Controladas</label>
	      <br>
	      <label>
	      <input type="radio" name="tipo_control" value="1"  onChange="form.control.value=1;">
  No Controladas</label>
	<input name="control" type="hidden" id="control" value="0">
	      </p>
		  
	  </td>
	  </tr>
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
LISTADO DE {concepto} 
</p>
<form name="form">
  <table width="100%" class="print">
    <tr class="print">
      <th class="print" scope="col"><strong>C&oacute;digo</strong></th>
      <th class="print" scope="col"><strong>Nombre</strong></th>
      <th class="print" scope="col"><strong>Unidad de consumo </strong></th>
      <th class="print" scope="col"><strong>Controlada </strong></th>
      <th class="print" scope="col"><strong>Presentaci&oacute;n</strong></th>
      <th class="print" scope="col"><strong>Pedido autom&aacute;tico </strong></th>
      <th class="print" scope="col"><strong>% de incremento </strong></th>
      <th class="print" scope="col"><strong>Entregas a fin de mes </strong></th>
      </tr>
<!-- START BLOCK : rows -->
	  <!-- START BLOCK : empaque -->
  	  <tr class="print">
		  <th class="print" colspan="8">
		  		<strong><font size="1" color="#FFFF00">MATERIAL DE EMPAQUE</font></strong>
		  </th>
	  </tr>
	  <!-- END BLOCK : empaque -->

    <tr class="print">
      <td class="print">{codmp}</td>
      <td class="vprint">{nombre}</td>
      <td class="vprint">{unidadconsumo}</td>
      <td class="print">{controlada}</td>
      <td class="vprint">{presentacion}</td>
      <td class="print">{pedido}</td>
      <td class="print">{porcentaje}</td>
      <td class="print">{entregas}</td>
      </tr>
	  
<!-- END BLOCK : rows -->
  </table>
  </form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->