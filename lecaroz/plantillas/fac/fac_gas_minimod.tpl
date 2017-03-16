<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->


<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function actualizar() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			document.form.codgastos.select();
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<form name="form" method="get" action="./fac_gas_minimod.php" >
<input name="idmovimiento_gastos" type="hidden" value="{id}">
<input name="parser" type="hidden" value="{valor}">  
<table>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla">{num_cia}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Codigo de Gasto </th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" value="{codgastos}" size="5" maxlength="5">
        <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
		<input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}">
		<input name="num_fact" type="hidden" id="num_fact" value="{num_fact}">
		<input name="num_cheque" type="hidden" id="num_cheque" value="{num_cheque}" size="6">
        <input name="importe" type="hidden" id="importe" value="{importe1}">

        <input name="idfactura" type="hidden" id="idfactura" value="{idfactura}" size="5">
		<input name="idpasivo" type="hidden" id="idpasivo" value="{idpasivo}" size="5">
		<input name="idcheque" type="hidden" id="idcheque" value="{idcheque}" size="6">
        <input name="idpagada" type="hidden" id="idpagada" value="{idpagada}" size="6"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla">
	  {fecha}
	  <input name="fecha" type="hidden" id="fecha" value="{fecha}" size="10" {estado}></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla">{concepto}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"> {importe} </td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input name="modificar" type="button" class="boton" id="modificar" value="Modificar" onClick="actualizar()">
  </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : modificar -->

