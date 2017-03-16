<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida_registro()
{
	document.form.submit();
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Facturas por Proveedor </p>
<form name="form" action="./fac_fac_con.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
<table class="tabla">
	<tr class"tabla">
		<th class="tabla">Número de Compañía </th>
		<td class="tabla">
		<input class="insert" name="num_cia" type="text" id="num_cia" size="10" maxlength="3" onChange="actualiza_fecha()">
		</td>
	    <th class="tabla">Número de proveedor </th>
	    <td class="tabla"><input class="insert" name="num_proveedor" type="text" id="num_proveedor" size="10" maxlength="3" onChange="actualiza_fecha1()"></td>
	</tr>
	<tr class"tabla">
		<td class="tabla" align="center">
			Mes</td>
		<td class="tabla">
		<select name="mes" size="1" class="insert">
		  <option value="1" {1}>Enero</option>
		  <option value="2" {2}>Febrero</option>
		  <option value="3" {3}>Marzo</option>
		  <option value="4" {4}>Abril</option>
		  <option value="5" {5}>Mayo</option>
		  <option value="6" {6}>Junio</option>
		  <option value="7" {7}>Julio</option>
		  <option value="8" {8}>Agosto</option>
		  <option value="9" {9}>Septiembre</option>
		  <option value="10" {10}>Octubre</option>
		  <option value="11" {11}>Noviembre</option>
		  <option value="12" {12}>Diciembre</option>
		</select>

		</td>
		<td class="tabla" align="center">
		A&ntilde;o</td>
  		<td class="tabla">
			<input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="10" maxlength="4">
		</td>

	</tr>
</table>
<p>
	<input class="boton" name="enviar" type="button" value="Consultar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<script language="JavaScript" type="text/JavaScript">
//<a href="altas.asp?id=<%=rs("id")%>&cate_id=<%=rs("cate_id")%>&estado=<%=rs("reqEstado")%>" class="ArialDesplegado">Detalles</a>

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<form name="form">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="3"><font size="+1">Consulta de Facturas del mes de {mes} del año {anio}</font></th>
    </tr>
    <tr class="tabla">
      <td class="tabla"><font size="+1">Cia: {nombre_cia}</font> </td>
	  <th class="tabla">&nbsp;&nbsp;&nbsp;</th>
	  <td class="tabla"><font size="+1"> Proveedor: {nom_proveedor}</font></td>
    </tr>
  </table>
  <br>
  <br>
  
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Número Factura</th>
    <th class="tabla">Total_Factura</th>
    <th class="tabla">Fecha movimiento</th>
    <th class="tabla">Fecha pago</th>
    <th class="tabla">Numero cheque</th>
    <th class="tabla" colspan="2">Codigo Gasto</th>
    <th class="tabla">Examinar</th>
  </tr>
	<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla">{num_documento}
      <input name="num_documento{i}" type="hidden" id="num_documento{i}" value="{num_documento}"></th>
    <td class="tabla">{costo_total1}
      <input name="costo_total{i}" type="hidden" id="costo_total{i}" value="{costo_total}"></td>
    <td class="tabla">{fecha}
      <input name="fecha{i}" type="hidden" id="fecha{i}" value="{fecha}"></td>
    <td class="tabla">{fecha_pago}
      <input name="fecha_pago{i}" type="hidden" id="fecha_pago{i}" value="{fecha_pago}"></td>
    <td class="tabla">{num_cheque}
      <input name="num_cheque{i}" type="hidden" id="num_cheque{i}" value="{num_cheque}"></td>
    <td class="tabla">{codgasto}
      <input name="codgasto{i}" type="hidden" id="codgasto{i}" value="{codgasto}"></td>
    <td class="tabla">{nom_gasto}</td>
    <td class="tabla"><input type="button" name="examinar{i}" value="Examinar" onClick="manda_registro();"></td>
  </tr>
 	<!-- END BLOCK : rows -->
</table>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->