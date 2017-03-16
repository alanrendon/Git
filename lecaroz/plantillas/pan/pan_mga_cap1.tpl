<script type="text/javascript" language="JavaScript">
	
</script>
<!-- tabla movimiento_gastos menu panaderias -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">CAPTURA DE MOVIMIENTO DE GASTOS</p>
<p class="title"><font color="#FF0000" size="+3">{mensaje}</font></p>
<form name="form" method="post" action="insert_pan_mga_cap.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="vtabla">compa&ntilde;ia</th>
      <td class="vtabla"><font size="+2">{num_cia}&#8212;{nombre_cia} </font> </td>
      <th class="vtabla">fecha</th>
      <td class="vtabla"><font size="+2">{fecha}</font></td>
    </tr>
  </table><br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center" colspan="2">C&oacute;digo y descripcion del gasto </th>
      <th class="tabla" align="center">Concepto</th>
      <th class="tabla" align="center">importe</th>
    </tr>
	<!-- START BLOCK : rows -->
	<input name="num_cia{i}" type="hidden" class="insert" value="{num_cia}">
	<input name="fecha{i}" type="hidden" class="insert" value="{fecha}">
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">

	  <td class="vtabla" align="left">
<input name="codgastos{i}" type="hidden" class="insert" id="codgastos{i}" value="{codgastos}">	
<!-- START BLOCK : gasto_ok -->
	{codgastos}
	<!-- END BLOCK : gasto_ok -->
	<!-- START BLOCK : gasto_error -->
	<font color="#{color}"> {codgastos}</font>
	<!-- END BLOCK : gasto_error -->
      </td>
	  <td class="vtabla"> 
	  {descripcion}
	  </td>	
      <td class="vtabla" >
          <input name="concepto{i}" type="hidden" class="insert" id="concepto{i}2" value="{concepto}">
          {concepto}
      </td>
      <th class="tabla" align="right">
          <input name="importe{i}" type="hidden" id="importe{i}" value="{importe}">
		  <input name="captura{i}" type="hidden" id="importe{i}" value="false">
          {importe2}
      </th>
    </tr>
	<!-- END BLOCK : rows -->
	<!-- START BLOCK : totales -->
	<th class="tabla" colspan="3" align="center"><b>Total</b></th>
	<th class="tabla" align="center"> <font size="+2">{total1}</font> <input name="total" type="hidden" id="total" value="{total}"></th>
	<!-- END BLOCK : totales -->
  </table>
  <br>
  <table class="tabla">
    <tr>
      <td class="vtabla">Error c&oacute;dio de gasto </td>
      <td bgcolor="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td class="vtabla">Error limite de gasto </td>
      <td bgcolor="#9900CC">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input name="enviar" type="button" class="boton" id="enviar" onclick="document.location='./pan_mga_cap.php'" value="Regresar">
<!-- START BLOCK : capturar -->
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
    <input type="button" class="boton" value="Capturar datos" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
<!-- END BLOCK : capturar -->
</p>
  </form>
  </td>
</tr>
</table>