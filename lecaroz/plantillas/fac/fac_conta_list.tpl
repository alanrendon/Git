<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
if(document.form.cia.value=="" || document.form.cia.value < 0){
	alert("Por favor revise la compañía");
	document.form.cia.select();
}
else if(document.form.proveedor.value=="" || document.form.proveedor.value < 0){
	alert("Por favor revise el proveedor");
	document.form.proveedor.select();
}
else if(document.form.anio.value=="" || document.form.anio.value < 0){
	alert("Revise el año");
	document.form.anio.select();
}
else 
	document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CONSULTA DE FACTURAS</p>
	<form action="./fac_fmp_con.php" method="get" name="form">
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th scope="col" class="tabla">
		
		    <label>
		    <input type="radio" name="cia" value="0">
  Compañía</label>

		
		  <input name="cia" type="text" class="insert" id="cia" size="5" onKeyDown="if(event.keyCode==13) form.proveedor.select();"> </th>
		<th scope="col" class="tabla">		    <label>
		    <input type="radio" name="cia" value="1">
  Todas las compañías</label>		   </th>
	  </tr>
	  <tr class="tabla">
		<td scope="row" colspan="2" class="tabla"> Mes 
		  <select name="mes" size="1" class="insert">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		  del 
		  <input name="anio" type="text" class="insert" id="cia3" value="{anio_actual}" size="5"></td>
	  </tr>
	</table>
	<p>
	<input type="button" name="enviar" class="boton" value="Continuar" onclick='valida()'>
	</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">window.onload=document.form.cia.select();</script>

</td>
</tr>
</table>
	<script language="JavaScript" type="text/JavaScript">window.onload=form.cia.select()</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : facturas -->
<script language="JavaScript" type="text/JavaScript">

	function modificar(id,codgasto,mes_factura,anio_factura,mes_corriente,anio_corriente,num_cia,num_proveedor,num_fact,cheque) {
	
		if(codgasto==33)
		{
			alert("No puedes cambiar este código");
			return;
		}
		else if(mes_factura==mes_corriente && anio_factura == anio_corriente)
		{
			var mod = window.open("./fac_gas_minimod.php?id="+id+"&num_cia="+num_cia+"&num_fact="+num_fact+"&num_proveedor="+num_proveedor+"&cheque="+cheque,null,"width=300,height=215,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0,top=100,left=400");
		}
		else
		{
			alert("No puede cambiar el código por la fecha de la factura y la fecha actual \n no coinciden en mes y año");
			return;
		}
	}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr class="tabla"> 
<td align="center" valign="middle">
<p class="title"><br>
	 </p>
	<p>
    <input name="button" type="button" class="boton" onclick='parent.history.back()' value="Regresar">
	</p>
	</td>
</tr>
</table>

<!-- END BLOCK : facturas -->
