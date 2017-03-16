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
		<th scope="col" class="tabla">Compa&ntilde;ia
		  <input name="cia" type="text" class="insert" id="cia" size="5" onKeyDown="if(event.keyCode==13) form.proveedor.select();"> </th>
		<th scope="col" class="tabla">Proveedor
		  <input name="proveedor" type="text" class="insert" id="proveedor" onKeyDown="if(event.keyCode==13) form.enviar.focus();" value="{num_proveedor}" size="5"> </th>
	  </tr>
	  <tr class="tabla">
		<td scope="row" colspan="2" class="tabla"> Desde 
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

function modificar(id,num_cia,codgastos,num_proveedor,num_fact,cheque,fecha,importe) {
	var mod = window.open("./fac_gas_minimod.php?id="+id+"&num_cia="+num_cia+"&num_fact="+num_fact+"&num_proveedor="+num_proveedor+"&cheque="+cheque+"&importe="+importe+"&codgastos="+codgastos+"&fecha="+fecha,null,"width=300,height=215,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0,top=100,left=400");
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr class="tabla"> 
<td align="center" valign="middle">
<p class="title">CONSULTA DE FACTURAS</p>
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
		<th colspan="2" class="tabla">Proveedor</th>
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
		<th class="tabla">Factura</th>
		<th class="tabla">Fecha</th>
		<th class="tabla">Importe</th>
		<th class="tabla">Fecha de pago </th>
		<th class="tabla">Cheque</th>
		<th class="tabla">Descripción</th>
		<th class="tabla" colspan="2">Codigo de gasto </th>
	    <th class="tabla">&nbsp;</th>
	  </tr>
	<!-- START BLOCK : rows -->
	  <tr class="tabla"  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="tabla">{num_fact}</td>
		<td class="tabla">{fecha1}</td>
		<td class="tabla">{importe}</td>
		<td class="tabla">{fecha_pago}</td>
		<td class="tabla">{num_cheque}</td>
		<td class="tabla">{descripcion}</td>
		<td class="tabla">{codgasto}</td>
		<td class="tabla">{gasto_desc}</td>
	    <td class="tabla"><input type="button" name="mod{i}" value="M" class="boton" onClick="modificar({id},{num_cia},{codgasto},{num_proveedor},{num_fact},{num_cheque},{fech1},{importe1});" {bloquea}></td>
	  </tr>
	<!-- END BLOCK : rows -->
	  <tr class="tabla"  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	    <th class="tabla">{num_fact}</th>
	    <th class="vtabla">&nbsp;</th>
	    <th class="tabla">{importe}</th>
	    <th colspan="5" class="vtabla">&nbsp;</th>
	    </tr>
	  <tr class="tabla"  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	    <th class="vtabla" colspan="8">M=Modificar c&oacute;digo de gasto </th>
	    </tr>
	</table>
<p>
    <input name="button" type="button" class="boton" onclick='parent.history.back()' value="Regresar">
	</p>
	</td>
</tr>
</table>

<!-- END BLOCK : facturas -->
