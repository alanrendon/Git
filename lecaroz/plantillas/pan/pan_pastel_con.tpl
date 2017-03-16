<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Revisi&oacute;n y modificaci&oacute;n de facturas de pastel </p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.num_cia.value == "" || parseInt(document.form.num_cia.value) <= 0)
	{
		alert("Compañía erronea");
		document.form.num_cia.select();
	}
	else
	document.form.submit();
}
</script>
<form name="form" method="get" action="./pan_pastel_con.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr class="tabla">
      <th scope="col" class="tabla">N&uacute;mero de compa&ntilde;&iacute;a
        <input name="num_cia" type="text" class="insert" id="num_cia" size="5" onKeyDown="if(event.keyCode==13)document.form.enviar.focus();"> </th>
      </tr>
    <tr class="tabla">
      <td class="vtabla"><p>
        <label>
        <input name="consulta" type="radio" value="0" checked>
  Por autorizar </label>
        <br>
        <label>
        <input type="radio" name="consulta" value="1">
  Actualizadas</label>
        <br>
      </p></td>
      </tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="Enviar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : autorizados -->
<script language="JavaScript" type="text/JavaScript">
	function modificar_pastel(let_folio,num_remi,num_cia,idmodifica) {
		window.open('./pan_pastel_minimod.php?let_folio='+let_folio+'&num_remi='+num_remi+'&num_cia='+num_cia+'&idmodifica='+idmodifica,'borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=600,top=100,left=400');
		return;
	}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Consulta de solicitud de modificaciones</p>
	<form name="form" method="post" action="./pan_pastel_con.php">
	<table class="tabla">
	  <tr class="tabla">
		<th scope="col" class="tabla" colspan="6">{num_cia}&#8212;{nom_cia}</th>
	  </tr>
	  <tr class="tabla">
	    <th class="tabla">X</th>
		<th class="tabla">N&uacute;mero de factura </th>
		<th class="tabla">Fecha de Solicitud</th>
	    <th class="tabla">Descripción</th>
		<th class="tabla">Fecha de Autorizaci&oacute;n </th>
		<th class="tabla">Modificar</th>
	  </tr>
	  <!-- START BLOCK : rows -->
	  <tr class="tabla">
	    <td class="tabla"><input name="id_sol[]" type="checkbox" id="id_sol" value="{id_sol}" /></td>
		<td class="tabla">{let_folio}&nbsp;{num_remi}</td>
		<td class="tabla">{fecha_solicitud}</td>
	    <td class="tabla">{descripcion}</td>
		<td class="tabla">{fecha_autorizacion}</td>
		<td class="tabla">
		<!-- START BLOCK : modificar -->
		<input type="button" value="M" class="boton" onClick="modificar_pastel('{let_remi}',{num_remi},{num_cia},{id});">
		<!-- END BLOCK : modificar -->		</td>
	  </tr>
	  <!-- END BLOCK : rows -->
	</table>
	<p>
	<input name="regresar" type="button" value="Regresar" class="boton" onClick="parent.history.back();">
	&nbsp;&nbsp;
	<input type="button" class="boton" onclick="borrar()" value="Borrar" />
	</p>
	
	</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function borrar() {
	if (confirm('¿Desea borrar las solicitudes seleccionadas?'))
		document.form.submit();
}
//-->
</script>
<!-- END BLOCK : autorizados -->

<!-- START BLOCK : actualizados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" valign="top">
      <p class="title">Revisión de Facturas pendientes, autorizadas y modificadas </p>
        <table class="tabla">
          <tr class="tabla">
            <th scope="col" class="tabla" colspan="4">{num_cia}&#8212;{nom_cia}</th>
          </tr>
          <tr class="tabla">
            <th class="tabla">N&uacute;mero de factura </th>
            <th class="tabla">Fecha de Solicitud</th>
            <th class="tabla">Fecha de Autorizaci&oacute;n </th>
            <th class="tabla">Fecha Modificaci&oacute;n</th>
          </tr>
          <!-- START BLOCK : reg-->
          <tr class="tabla">
            <td class="tabla">{letra_folio1}&nbsp;{num_remi1}</td>
            <td class="tabla">{fecha_solicitud1}</td>
            <td class="tabla">{fecha_autorizacion1}</td>
            <td class="tabla">{fecha_modificacion1}</td>
          </tr>
          <!-- END BLOCK : reg -->
        </table><br>
	<input name="regresar" type="button" value="Regresar" class="boton" onClick="parent.history.back();">		
  </td>
  </tr>
</table>
<!-- END BLOCK : actualizados -->