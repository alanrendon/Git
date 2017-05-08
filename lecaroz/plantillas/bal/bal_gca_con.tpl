<!-- START BLOCK : tipo_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Catalogo de Gastos de Oficina </p>
<form name="form" method="get" action="bal_gca_con.php">
<table class="tabla">
  <tr>
    <th class="vtabla">Generar Consulta por: </th>
    <td class="vtabla"><input name="tipo" type="radio" value="nombre">
      Nombre <br>
      <input name="tipo" type="radio" value="codigo"> 
      C&oacute;digo </td>
  </tr>
</table>
<p>
<input type="submit" class="boton" value="Generar listado">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : tipo_listado -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<script language="javascript" type="text/javascript">
	function imprimir(boton) {
		boton.style.visibility = 'hidden';
		window.print();
		alert("Imprimiendo...");
		boton.style.visibility = 'visible';
	}
</script>
<p class="title">Gastos de Caja </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Descripci&oacute;n</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla">{num_gasto}</td>
    <td class="vtabla">{descripcion}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
<input type="button" class="boton" value="Imprimir listado" onClick="imprimir(this)">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
