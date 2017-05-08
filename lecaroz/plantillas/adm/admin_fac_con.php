<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consultar notas de pastel pendientes de pago </p>
<form name="form" method="get" action="/plantillas/pan/./pan_fpan_con.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">
        <label> Hasta el dia: </label></th>
    </tr>
    <!-- START BLOCK : lista -->
	<tr class="tabla">
      <td class="tabla" colspan="2"><input name="fecha" type="text" class="insert" id="fecha" size="10" maxlength="10">
	  </td>
	</tr>
	<!-- END BLOCK : lista -->
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar" {disabled}>
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.cia.select();</script>
</td>
</tr>
<tr>
  <td align="center" valign="middle">&nbsp;</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : facturas -->
<script language="JavaScript" type="text/JavaScript">
	function detalle_factura(letra,inicio,fini,cia,numf,id){
		window.open('./pan_bloc_detalle.php?letra='+letra+'&inicio='+inicio+'&final='+fini+'&cia='+cia+'&folios='+numf+'&id='+id,'borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=800,left=300, top=100');
		return;
	}

	function borrar_bloc(id) {
		window.open('./pan_bloc_minidel.php?id='+id,'borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=500');
		return;
	}


</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Facturas pendientes de pago</p>
<table class="tabla">
<!-- START BLOCK : cias -->
  <tr class="tabla">
    <th class="vtabla" colspan="5">
	{num_cia} {nombre_cia}
	</th>
  </tr>
 <!-- END BLOCK : cias -->
  <tr class="tabla">
    <th class="tabla" colspan="2">Folio</th>
    <th class="tabla">Total factura </th>
    <th class="tabla">Pendiente</th>
    <th class="tabla">Fecha de pago </th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla">
    <td class="tabla">{let_folio}</td>
	<td class="tabla">{num_fact}</td>
    <td class="tabla">{total}</td>
    <td class="tabla">{resta}</td>
    <td class="tabla">{fecha_entrega}</td>
  </tr>
<!-- END BLOCK : rows -->  
</table>

<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : facturas -->