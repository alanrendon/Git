<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title"><font size="+2">Consulta de Movimientos de Reservas</font></p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.bandera.value == 0 && (document.form.cod_reserva.value <=0 || document.form.cod_reserva.value==""))
		alert("Tienes que meter un código de reserva");
	else if (document.form.bandera.value == 1 && (document.form.num_cia.value <=0 || document.form.num_cia.value==""))
		alert("Tienes que meter una compañía");
	else document.form.submit();
}
</script>

<form action="./bal_res_con.php" method="get" name="form" id="form">

<input name="temp" type="hidden">
  <table border="1" class="tabla">
	
	<tr class="tabla">
      <th class="tabla" colspan="2">
	  AÑO 
	    <input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5">
        <input name="bandera" type="hidden" class="insert" value="0" size="5">
</th>
    </tr>
    <tr class="tabla">
      <td class="vtabla">
	  <input name="tipo" type="radio" value="reserva" checked onchange="document.form.bandera.value=0;">
	  Codigo Reserva<br>
  	  <input name="tipo" type="radio" value="cia" onchange="document.form.bandera.value=1;">
  	  Número Compañía
	  </td>
	  <td class="tabla">
	  <input name="cod_reserva" type="text" class="insert" id="cod_reserva" size="5" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp)"><br>
      <input name="num_cia" type="text" size="5" class="insert" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp)">
</td>

    </tr>
  </table>
<p>
  <input class="boton" name="enviar" type="button" value="Consultar" onClick="valida();">
</p>
<script language="javascript" type="text/javascript">window.onload = document.form.cod_reserva.select();</script>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_reserva -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table border="1" class="tabla">
  <tr class="tabla">
    <th class="tabla"><font size="+1">{nom_reserva}</font></th>
  </tr>
</table>






<table border="1">
  <tr class="tabla">
    <th rowspan="2" align="center" class="print" colspan="2">Compañia</th>
    <th colspan="12" class="print" align="center">MES</th>
    <th class="print" rowspan="2">Pagado</th>
    <th class="print" rowspan="2">Total</th>
  </tr>
  <tr class="tabla">
    <th class="print">Enero</th>
    <th class="print">Febrero</th>
    <th class="print">Marzo</th>
    <th class="print">Abril</th>
    <th class="print">Mayo</th>
    <th class="print">Junio</th>
    <th class="print">Julio</th>
    <th class="print">Agosto</th>
    <th class="print">Septiembre</th>
    <th class="print">Octubre</th>
    <th class="print">Noviembre</th>
    <th class="print">Diciembre</th>
  </tr>

<!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vprint">{num_cia}</th>
    <th class="vprint">{nombre_corto}</th>
    <td class="rprint">{importe1}</td>
    <td class="rprint">{importe2}</td>
    <td class="rprint">{importe3}</td>
    <td class="rprint">{importe4}</td>
    <td class="rprint">{importe5}</td>
    <td class="rprint">{importe6}</td>
    <td class="rprint">{importe7}</td>
    <td class="rprint">{importe8}</td>
    <td class="rprint">{importe9}</td>
    <td class="rprint">{importe10}</td>
    <td class="rprint">{importe11}</td>
    <td class="rprint">{importe12}</td>
<!-- START BLOCK : pagado -->
    <td class="rprint"><strong>{pagado}</strong></td>
    <th class="rprint">{total}</th>
<!-- END BLOCK : pagado -->
  </tr>
<!-- END BLOCK : rows -->
</table>
<br>
</td>
</tr>
</table>
<!-- END BLOCK : listado_reserva -->

<!-- START BLOCK : listado_compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table border="1" class="tabla">
  <tr class="tabla">
    <th class="tabla"><font size="+1">{nom_cia}</font></th>
  </tr>
</table>

<table border="1" class="tabla">
  <tr class="tabla">
    <th class="tabla">Mes</th>
	<!-- START BLOCK : reserva -->
    <th class="tabla">{nom_res}</th>
	<!-- END BLOCK : reserva -->
  </tr>

	<!-- START BLOCK : meses -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla">{nombre_mes}</th>
	<!-- START BLOCK : importes -->
    <td class="tabla">{importe}</td>
	<!-- END BLOCK : importes -->
  </tr>
	<!-- END BLOCK : meses -->

  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla">Pagado</th>
	<!-- START BLOCK : pagado2 -->
    <td class="tabla">{pagado2}</td>
	<!-- START BLOCK : pagado2 -->
  </tr>
  <tr class="tabla">
    <th class="tabla">Total</th>
	<!-- START BLOCK : total -->
    <th class="tabla">{total}</th>
	<!-- START BLOCK : total -->
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_compania -->
