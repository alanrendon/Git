<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
if(document.form.cia.value=="" || document.form.cia.value < 0){
	alert("Por favor revise la compañía");
	document.form.cia.select();
}
else if(document.form.cheque.value=="" || document.form.cheque.value < 0){
	alert("Por favor revise el proveedor");
	document.form.proveedor.select();
}
else 
	document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">BÚSQUEDA DE CHEQUES</p>
	<form action="./ban_cheq_bus.php" method="get" name="form">
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th scope="col" class="tabla">Compa&ntilde;ia
		  <input name="cia" type="text" class="insert" id="cia" size="5" onKeyDown="if(event.keyCode==13) form.cheque.select();"> </th>
		<th scope="col" class="tabla">Numero de cheque
		  <input name="cheque" type="text" class="insert" id="cheque" onKeyDown="if(event.keyCode==13) form.enviar.focus();" size="5"> </th>
	  </tr>
	</table>
	<p>
	<input type="button" name="enviar" class="boton" value="Continuar" onclick='valida()'>
	</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">window.onload=document.form.cia.select()</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : cheque -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr class="tabla"> 
<td align="center" valign="middle">
<p class="title">CONSULTA DE CHEQUE </p>
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
		<th class="tabla" colspan="2">A nombre de </th>
	  </tr>
	  <tr class="tabla">
		<th class="tabla">{num_cia}</th>
		<td class="tabla">{nom_cia}</td>
		<th class="tabla">{num_proveedor}</th>
		<td class="tabla">{nom_proveedor}</td>
	  </tr>
	</table>
	<br>
	<table class="tabla">
	  <tr class="tabla">
		<th class="tabla">Folio</th>
		<th class="tabla">Fecha elaboración</th>
		<th class="tabla">Fecha de conciliación</th>
		<th class="tabla">Facturas</th>
		<th class="tabla">Concepto</th>
		<th class="tabla">Estado</th>
	    <th class="tabla" colspan="2">Gasto</th>
		<th class="tabla">Importe</th>

	  </tr>
	  <tr class="tabla"  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="tabla">{folio}</td>
		<td class="tabla">{fecha}</td>
		<td class="tabla">{fecha_con}</td>
		<td class="tabla">{facturas}</td>
		<td class="tabla">{concepto}</td>
		<td class="tabla">{estado}</td>
	    <td class="tabla">{codgasto}</td>
		<td class="tabla">{gasto}</td>
		<td class="tabla">{importe}</td>

	  </tr>
	</table>
<p>
    <input name="button" type="button" class="boton" onclick='parent.history.back()' value="Regresar">
	</p>
	</td>
</tr>
</table>

<!-- END BLOCK : cheque -->
