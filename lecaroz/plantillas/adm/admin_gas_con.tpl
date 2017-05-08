<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CONSULTA DE GASTOS CANCELADOS </p>

<form name="form" method="get" action="./admin_gas_con.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">
        <label>CONSULTAR POR: </label></th>
      <th class="tabla">Mes de revisi&oacute;n </th>
    </tr>
    <tr class="tabla">
      <td class="vtabla">
        <p>
          <label>
          <input name="status" type="radio" value="0" checked>
  No revisados </label>
          <br>
          <label>
          <input type="radio" name="status" value="1">
  Revisados</label>
        </p></td>
      <td class="vtabla">
	  <select name="mes" class="insert">
	  <!-- START BLOCK : mes -->
	    <option value="{mes}" {selected}>{nombre_mes}</option>
	  <!-- END BLOCK : mes -->
      </select>
	  
	  <input name="anio" type="text" class="insert" value="{anio}" size="5">
	  
	  </td>
    </tr>
  </table>
  <br>
  <input name="temp" type="hidden" value="{temp}">
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.cia.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : no_revisados -->
<script language="JavaScript" type="text/JavaScript">
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Gastos cancelados por revisar </p>

<form name="form" action="./actualiza_admin_gas.php" method="post">
<input name="contador" type="hidden" value="{contador}">
<table class="tabla">
<!-- START BLOCK : cia -->
  <tr>
    <th colspan="5" class="tabla" scope="col"><font size="2"><strong>{num_cia} &nbsp;{nombre_cia}</strong></font></th>
	<th colspan="5" class="tabla" scope="col"><font size="2"><strong>{operadora}</strong></font></th>
  </tr>
  <tr>
    <th class="tabla">Fecha de cancelaci&oacute;n </th>
    <th class="tabla" colspan="2">Gasto</th>
	<th class="tabla">Concepto del gasto </th>
    <th class="tabla">Concepto de cancelaci&oacute;n </th>
    <th class="tabla">Importe</th>
    <th class="tabla">Revisado</th>
    
  </tr>
 <!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{fecha_can}
      <input name="id{i}" type="hidden" id="id{i}" value="{id}"></td>
    <td class="rtabla">{codgastos}</td>
    <td class="vtabla">{nombre_gasto}</td>
    <td class="vtabla">{concepto_gasto}</td>
    <td class="vtabla">{concepto_can}</td>
    <td class="rtabla">{importe}</td>
    <td class="tabla"><input name="revisa" type="checkbox" id="revisa" value="1" checked onChange="if(this.checked==true) document.form.revisado.value=1; else document.form.revisado.value=0;">
      <input name="revisado{i}" type="hidden" id="revisado{i}" value="1" size="5"></td>
  </tr>
  <!-- END BLOCK : rows -->
  <!-- END BLOCK : cia -->  

</table>
<br>


  <input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">&nbsp;&nbsp;
  <input name="enviar" type="button" id="enviar" value="Enviar" class="boton" onClick="document.form.submit();">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : no_revisados -->


<!-- START BLOCK : revisados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" valign="top">
      <p class="title">LISTADO DE GASTOS CANCELADOS CORRESPONDIENTES A <br>
        {mes} DEL {anio}
      </p>
        <table class="print">
          <!-- START BLOCK : cia1 -->
          <tr>
            <th colspan="5" class="print" scope="col"><font size="2"><strong>{num_cia} &nbsp;{nombre_cia}</strong></font></th>
          </tr>
          <tr>
            <th class="print">Fecha de cancelaci&oacute;n </th>
            <th class="print" colspan="2">Gasto</th>
            <th class="print">Descripcion</th>

            <th class="print">&nbsp;&nbsp;Importe&nbsp;&nbsp;</th>
          </tr>
          <!-- START BLOCK : rows1 -->
          <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
            <td class="print">{fecha_can}
            <td class="rprint">{codgastos}</td>
            <td class="vprint">{nombre_gasto}</td>
            <td class="vprint">{descripcion}</td>
            <td class="rprint">&nbsp;&nbsp;{importe}</td>
          </tr>
          <!-- END BLOCK : rows1 -->
		            <!-- END BLOCK : cia1 -->

        </table>
</td>
 </tr>
</table>
<!-- END BLOCK : revisados -->
