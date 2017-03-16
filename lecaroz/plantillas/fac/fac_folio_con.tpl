<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida_registro()
{
	if (document.form.num_fact.value=="" || document.form.num_fact.value<=0){
		alert("Revise el número de factura");
		document.form.num_fact.select();
		}
	else
		document.form.submit();
}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">B&uacute;squeda de Facturas </p>
<form name="form" action="./fac_folio_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr class"tabla">
		<th class="tabla">Número de Factura </th>
		<td class="tabla">
		<input class="insert" name="num_fact" type="text" id="num_fact" size="10" maxlength="10" onChange="actualiza_fecha()">
		</td>
	    </tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="valida_registro();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_fact.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : busqueda -->
<script language="JavaScript" type="text/JavaScript">
//<a href="altas.asp?id=<%=rs("id")%>&cate_id=<%=rs("cate_id")%>&estado=<%=rs("reqEstado")%>" class="ArialDesplegado">Detalles</a>

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <p class="title">Resultados para la búsqueda de la factura <br>{num_fact} </p>
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Compa&ntilde;&iacute;a</th>
    <th class="tabla">Proveedor</th>
    <th class="tabla">Fecha movimiento</th>
    <th class="tabla">Total de la factura </th>
    </tr>
	<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{num_cia}&#8212;{nombre_cia}</td>
    <td class="tabla">{num_proveedor}&#8212;{nombre_proveedor}</td>
    <td class="tabla">{fecha}</td>
    <th class="tabla">{total_fac}</th>
  </tr>
 	<!-- END BLOCK : rows -->
</table>
  <p>
    <input name="regresar" type="buttom" id="regresar" value="Regresar" class="boton" onClick="parent.history.back();">
  </p></td>
</tr>
</table>
<!-- END BLOCK : busqueda -->