<!-- START BLOCK : tipo_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Cat&aacute;logo de Movimientos Bancarios </p>
<form name="form" method="get" action="ban_cmb_con.php">
<table class="tabla">
  <tr>
    <th class="vtabla">Cat&aacute;logo</th>
    <td class="vtabla"><select name="tabla" id="tabla" class="insert">
      <option value="catalogo_mov_bancos">BANORTE</option>
      <option value="catalogo_mov_santander">SANTANDER</option>
    </select></td>
  </tr>
  <tr>
    <th class="vtabla">Generar listado por: </th>
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
<p class="title">Cat&aacute;logo de Movimientos Bancarios </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">C&oacute;digo en banco </th>
    <th class="tabla" scope="col">Descripci&oacute;n</th>
    <th class="tabla" scope="col">Tipo Movimiento </th>
    <th class="tabla" scope="col">Entra Balance </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla">{cod_mov}</td>
    <td class="vtabla">
	<!-- START BLOCK : cod_banco -->
	{cod_banco}
	<!-- END BLOCK : cod_banco -->
	</td>
    <td class="vtabla">{descripcion}</td>
    <td class="tabla">{tipo_mov}</td>
    <td class="tabla">{entra_bal}</td>
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
