<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Movimientos de Banco Autorizados </p>
<table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">C&oacute;digo de movimiento </th>
    <th class="tabla" scope="col">Importe autorizado </th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{cod_mov}</td>
    <td class="vtabla">{descripcion}</td>
    <th class="rtabla">{importe}</th>
    <th class="rtabla"><input type="button" class="boton" value="Modificar" onClick="modificar({id})">
      &nbsp;
      <input type="button" class="boton" value="Borrar" onClick="borrar({id})"></th>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function modificar(id) {
		window.open('./ban_mau_minimod.php?id='+id,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480');
		return;
	}
	
	function borrar(id) {
		window.open('./ban_mau_minidel.php?id='+id,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200');
		return;
	}
</script>