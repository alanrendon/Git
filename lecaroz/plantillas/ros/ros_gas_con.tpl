<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Gastos</p>
<form name="form" method="get" action="./ros_gas_con.php">
<table class="tabla">
  <tr>
    <th class="vtabla"><input name="xcia" type="radio" value="una" checked>
    Por compa&ntilde;&iacute;a </th>
    <th class="vtabla"><input name="tipo" type="radio" value="gastos" checked>
    Desgloce de gastos </th>
  </tr>
  <tr>
    <th class="vtabla"><input name="xcia" type="radio" value="todas">
      Todas las compa&ntilde;&iacute;as </th>
    <th class="vtabla"><input name="tipo" type="radio" value="totales">
      Totales del mes </th>
  </tr>
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a:
      <input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5"></th>
    <th class="vtabla">Mes:    
      <select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select>
      &nbsp;       A&ntilde;o:
      <input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5" maxlength="4"></th>
  </tr>
</table>
<p>
  <input name="enviar" class="boton" type="submit" value="Generar listado">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado_all_gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Gastos Generales</p>
<table width="70%" class="tabla">
  <tr>
    <th colspan="5" class="tabla" scope="col">{num_cia_all} - {nombre_cia_all} </th>
    </tr>
  <tr>
    <th class="tabla" scope="col">Fecha</th>
	<th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Descripci&oacute;n del gasto </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : cia_all -->
  <tr>
    <th colspan="5" class="tabla">{num_cia_all} - {nombre_cia_all} </th>
  </tr>
  <!-- START BLOCK : codigo_all -->
  <!-- START BLOCK : fila_all -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{fecha_all}</td>
	<td class="vtabla">{cod_gasto_all}</td>
    <td class="vtabla">{nombre_gasto_all}</td>
    <td class="vtabla">{concepto_all}</td>
    <td class="rtabla">{importe_all}</td>
  </tr>
  <!-- END BLOCK : fila_all -->
  <tr>
    <th class="rtabla" colspan="4">Total de gastos</th>
	<th class="rtabla">{total_gasto_all}</th>
  </tr>
  <!-- END BLOCK : codigo_all -->
  <!-- END BLOCK : cia_all -->
  <tr>
  	<th class="rtabla" colspan="4">Totales</th>
	<th class="rtabla"><font size="+1">{gran_total_all}</font></th>
  </tr>
</table>
</td>
</tr>
</table>
<!-- START BLOCK : listado_all_gastos -->

<!-- START BLOCK : listado_x_cia_gastos -->
<script language="javascript" type="text/javascript">
	function modificar(id) {
		var mod = window.open("./ros_gas_minimod.php?id="+id,null,"width=300,height=215,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0");
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Gastos Generales </p>
<table class="tabla">
<tr>
<th class="tabla"><font size="+1">{num_cia_una} - {nombre_cia_una}</font> </th>
<th class="tabla"><font size="+1">{mes}</font></th>
<th class="tabla"><font size="+1">{anio}</font></th>
</tr>
</table>
<br>
<table class="tabla">
  <!-- START BLOCK : codigo_gasto -->
  <tr>
    <th class="tabla" scope="col">Fecha</th>
	<th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Descripci&oacute;n de gasto </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
	<th class="tabla">&nbsp;</th>
  </tr>

  <!-- START BLOCK : fila_gasto -->
  <tr>
    <td class="tabla">{fecha_gasto}</td>
	<td class="rtabla">{cod_gasto}</td>
    <td class="vtabla">{nombre_gasto}</td>
    <td class="vtabla">{concepto_gasto}</td>
    <td class="rtabla"><strong>{importe_gasto}</strong></td>
	<td class="tabla"><input name="modificar" type="button" class="boton" value="Modificar" onClick="modificar({id})" {disabled}></td>
  </tr>
  <!-- END BLOCK : fila_gasto -->
  <tr>
    <th class="rtabla" colspan="4">Total de gastos</th>
	<th class="rtabla"><strong>{total_gasto}</strong></th>
	<th class="tabla"></th>
  </tr>
  <tr>
    <td class="tabla" colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : codigo_gasto -->
  <tr>
	<th class="rtabla" colspan="4">Totales gastos no incluidos</th>
	<th class="rtabla"><font size="2">{gastos_no_incluidos}</font></th>
	<th class="rtabla">&nbsp;</th>
  </tr>
  <tr>
	<th class="rtabla" colspan="4">Totales gastos de operacion</th>
	<th class="rtabla"><font size="2">{gastos_operacion}</font></th>
	<th class="rtabla">&nbsp;</th>
  </tr>
  <tr>
	<th class="rtabla" colspan="4">Totales gastos generales</th>
	<th class="rtabla"><font size="2">{gastos_generales}</font></th>
	<th class="tabla">&nbsp;</th>
  </tr>

  <tr>
  	<th class="rtabla" colspan="4">Totales</th>
	<th class="rtabla"><font size="+1">{gran_total_gasto}</font></th>
	<th class="tabla">&nbsp;</th>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_x_cia_gastos -->

<!-- START BLOCK : listado_x_cia_totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table width="100%">
  <tr>
    <th width="10%">Cia.:{num_cia}</th>
	<th width="100%">{num_cia} {nombre_cia}</th>
	<th width="10%">Cia.:{num_cia}</th>
  </tr>
  <tr>
    <th width="10%"></th>
	<th width="100%">Gastos del Mes de {mes} de {anio}</th>
	<th width="10%"></th>
  </tr>
</table>
<!-- START BLOCK : gastos_operacion -->
<table class="tabla" width="80%">
  <tr>
    <th class="print" colspan="3">Gastos de Operaci&oacute;n</th>
  </tr>
  <tr>
    <th class="print" scope="col" width="20%">C&oacute;digo</th>
    <th class="print" scope="col" width="40%">Descripci&oacute;n de gasto </th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_totales -->
  <tr>
    <td class="vprint"><b>{cod_total}</b></td>
    <td class="vprint"><b>{nombre_total}</b></td>
    <td class="rprint"><b>{importe_total}</b></td>
  </tr>
  <!-- END BLOCK : fila_totales -->
   <tr>
  	<th class="rprint" colspan="2">Totales</th>
	<th class="rprint_total"><font size="+1">{gran_total_total}</font></th>
  </tr>
</table>
<!-- END BLOCK : gastos_operacion -->
<!-- START BLOCK : gastos_gral -->
<table class="tabla" width="80%">
  <tr>
    <th class="print" colspan="3">Gastos Generales</th>
  </tr>
  <tr>
    <th class="print" scope="col" width="20%">C&oacute;digo</th>
    <th class="print" scope="col" width="40%">Descripci&oacute;n de gasto </th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_totales_gral -->
  <tr>
    <td class="vprint"><b>{cod_total_gral}</b></td>
    <td class="vprint"><b>{nombre_total_gral}</b></td>
    <td class="rprint"><b>{importe_total_gral}</b></td>
  </tr>
  <!-- END BLOCK : fila_totales_gral -->
   <tr>
  	<th class="rprint" colspan="2">Totales</th>
	<th class="rprint_total"><font size="+1">{gran_total_total}</font></th>
  </tr>
</table>
<!-- END BLOCK : gastos_gral -->
<!-- START BLOCK : gastos_otros -->
<table class="tabla" width="80%">
  <tr>
    <th class="print" colspan="3">Gastos que no se Incluyen</th>
  </tr>
  <tr>
    <th class="print" scope="col" width="20%">C&oacute;digo</th>
    <th class="print" scope="col" width="40%">Descripci&oacute;n de gasto </th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_totales_otros -->
  <tr>
    <td class="vprint"><b>{cod_total_otros}</b></td>
    <td class="vprint"><b>{nombre_total_otros}</b></td>
    <td class="rprint"><b>{importe_total_otros}</b></td>
  </tr>
  <!-- END BLOCK : fila_totales_otros -->
   <tr>
  	<th class="rprint" colspan="2">Totales</th>
	<th class="rprint_total"><font size="+1">{gran_total_total}</font></th>
  </tr>
</table>
<!-- END BLOCK : gastos_otros -->
<table class="tabla" width="80%">
   <tr>
  	<th class="rtabla" colspan="2">Gran Total</th>
	<th class="rtabla"><font size="+1">{gran_total}</font></th>
  </tr>
</table>
<br>
</td>
</tr>
</table>
<!-- END BLOCK : listado_x_cia_totales -->
