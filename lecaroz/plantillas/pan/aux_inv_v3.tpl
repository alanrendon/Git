<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr align="center" valign="middle">
    <td>
      <p class="title">AUXILIAR DE INVENTARIOS DE MATERIA PRIMA</p>
      <form action="./aux_inv_v3.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
        <table class="tabla">
          <tr>
            <th class="vtabla">Compa&ntilde;&iacute;a</th>
            <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codmp.select();" size="5" maxlength="5"></td>
          </tr>
          <tr>
            <th class="vtabla">Materia Prima </th>
            <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp3" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select();" size="3" maxlength="3"></td>
          </tr>
          <tr>
            <th class="vtabla">Mes</th>
            <td class="vtabla"><select name="mes" class="insert" id="select">
              <option value="1" {1}>ENERO</option>
              <option value="2" {2}>FEBRERO</option>
              <option value="3" {3}>MARZO</option>
              <option value="4" {4}>ABRIL</option>
              <option value="5" {5}>MAYO</option>
              <option value="6" {6}>JUNIO</option>
              <option value="7" {7}>JULIO</option>
              <option value="8" {8}>AGOSTO</option>
              <option value="9" {9}>SEPTIEMBRE</option>
              <option value="10" {10}>OCTUBRE</option>
              <option value="11" {11}>NOVIEMBRE</option>
              <option value="12" {12}>DICIEMBRE</option>
            </select></td>
          </tr>
          <tr>
            <th class="vtabla">A&ntilde;o</th>
            <td class="vtabla"><input name="anio" type="text" class="insert" id="anio2" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
          </tr>
          <tr>
            <th class="vtabla">Listado</th>
            <td class="vtabla"><input name="listado" type="radio" value="desglozado">
              Desglosado<br>
              <input name="listado" type="radio" value="totales" checked>
              Totales</td>
          </tr>
          <tr>
            <th class="vtabla">Filtros</th>
            <td class="vtabla"><input name="controlada" type="radio" value="todas" checked>
              Todas<br>
              <input name="controlada" type="radio" value="si">
              Controladas<br>
              <input name="controlada" type="radio" value="no">
              No Controladas...<br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="tipo" type="radio" value="todas" checked>
              Todas<br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="tipo" type="radio" value="mp">
              Materia Prima<br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="tipo" type="radio" value="me">
              Material de Empaque  </td>
          </tr>
          <tr>
            <th class="vtabla">&iquest;Incluir diferencias?</th>
            <td class="vtabla"><input name="dif" type="checkbox" id="dif" value="TRUE" checked>
              Si</td>
          </tr>
          <tr>
            <th class="vtabla">&iquest;Incluir Gas?</th>
            <td class="vtabla"><input name="gas" type="checkbox" id="gas" value="TRUE" checked>
            Si</td>
          </tr>
        </table>
        <p>
          <input type="submit" value="Siguiente" class="boton">
        </p>
    </form></td>
  </tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado_des -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Auxiliar de Inventario</p>
<!-- START BLOCK : cia_des -->
<table class="print">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">A&ntilde;o</th>
  </tr>
  <tr>
    <td class="tabla"><strong>{num_cia} - {nombre_cia} </strong></td>
    <td class="tabla"><strong>{mes}</strong></td>
    <td class="tabla"><strong>{anio}</strong></td>
  </tr>
</table>  
<br>
<table cellpadding="0" cellspacing="0" class="print">
  <!-- START BLOCK : mp_des -->
  <tr>
    <th rowspan="2" class="print" scope="col">Fecha</th>
    <th rowspan="2" class="print" scope="col">Concepto</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
    Unitario </th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="3" class="print" scope="col">Salidas</th>
    <th colspan="2" class="print" scope="col">Existencias</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
    Promedio </th>
    <th rowspan="2" class="print" scope="col">Diferencia<br>
      de Costo</th>
  </tr>
  <tr>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Turno</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
  </tr>
  <tr>
    <th colspan="7" class="vprint">{codmp} {nombremp} </th>
    <th class="rprint">Saldo anterior: </th>
    <th class="rprint">{unidades_anteriores}</th>
    <th class="rprint">{valores_anteriores}</th>
    <th class="rprint">{costo_anterior}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila_des -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint"><strong>{costo_unitario}</strong></td>
    <td class="rprint"><font color="#0000FF">{unidades_entrada}</font></td>
    <td class="rprint"><font color="#0000FF">{valores_entrada}</font></td>
    <td class="print">{turno}</td>
    <td class="rprint"><font color="#FF0000">{unidades_salida}</font></td>
    <td class="rprint"><font color="#FF0000">{valores_salida}</font></td>
    <td class="rprint">{unidades}</td>
    <td class="rprint">{valores}</td>
    <td class="rprint"><strong>{costo_promedio}</strong></td>
    <td class="rprint">{diferencia_costo}</td>
  </tr>
  <!-- END BLOCK : fila_des -->
  <tr>
    <th colspan="3" class="rprint">Total de movimientos y saldo actual: </th>
    <th class="rprint"><font size="-1" color="#0000FF">{unidades_entrada}</font></th>
    <th class="rprint"><font size="-1" color="#0000FF">{valores_entrada}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1" color="#FF0000">{unidades_salida}</font></th>
    <th class="rprint"><font size="-1" color="#FF0000">{valores_salida}</font></th>
    <th class="rprint"><font size="-1">{unidades}</font></th>
    <th class="rprint"><font size="-1">{valores}</font></th>
    <th class="rprint"><font size="-1">{costo_promedio}</font></th>
    <th class="rprint"><font size="-1">{total_diferencia}</font></th>
  </tr>
  <tr>
    <td colspan="12">&nbsp;</td>
    </tr>
  <!-- END BLOCK : mp_des -->
</table>
<br>
<!-- END BLOCK : cia_des -->
</td>
</tr>
</table>
<!-- END BLOCK : listado_des -->

<!-- START BLOCK : listado_totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Auxiliar de Inventario</p>
<!-- START BLOCK : cia_total -->
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">A&ntilde;o</th>
  </tr>
  <tr>
    <td class="tabla"><strong>{num_cia} - {nombre_cia} </strong></td>
    <td class="tabla"><strong>{mes}</strong></td>
    <td class="tabla"><strong>{anio}</strong></td>
  </tr>
</table>  
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="print">
  <tr>
    <th class="print">&nbsp;</th>
	<th colspan="3" class="print" scope="col">Existencia Inicial </th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="2" class="print" scope="col">Salidas</th>
    <th colspan="3" class="print" scope="col">Existencia Actual </th>
  </tr>
  <tr>
    <th class="print" scope="col">Materia Prima </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo Promedio </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo Promedio </th>
  </tr>
  <!-- START BLOCK : mp_total -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint"><strong>{codmp} {nombremp}</strong></td>
    <td class="rprint">{unidades_anteriores}</td>
    <td class="rprint">{valores_anteriores}</td>
    <td class="rprint">{costo_anterior}</td>
    <td class="rprint"><font color="#0000FF">{unidades_entrada}</font></td>
    <td class="rprint"><font color="#0000FF">{valores_entrada}</font></td>
    <td class="rprint"><font color="#FF0000">{unidades_salida}</font></td>
    <td class="rprint"><font color="#FF0000">{valores_salida}</font></td>
    <td class="rprint">{unidades}</td>
    <td class="rprint">{valores}</td>
    <td class="rprint">{costo_promedio}</td>
  </tr>
  <!-- END BLOCK : mp_total -->
  <tr>
    <th class="rprint">Total General </th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{valores_anteriores}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1" color="#0000FF">{valores_entrada}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1" color="#FF0000">{valores_salida}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{valores}</font></th>
    <th class="rprint">&nbsp;</th>
  </tr>
</table>
<br>
<!-- END BLOCK : cia_total -->
<!-- START BLOCK : totales -->
<table class="print">
  <tr>
    <th class="print" scope="col">Unidades Iniciales </th>
    <th class="print" scope="col">Valores Iniciales </th>
    <th class="print" scope="col">Entradas (unidades) </th>
    <th class="print" scope="col">Entradas (valores) </th>
    <th class="print" scope="col">Salidas (unidades) </th>
    <th class="print" scope="col">Salidas (valores)</th>
    <th class="print" scope="col">Unidades Finales </th>
    <th class="print" scope="col">Valores Finales</th>
  </tr>
  <tr>
    <td class="print"><font size="+1">{unidades_anteriores}</font></td>
    <td class="print"><font size="+1">{valores_anteriores}</font></td>
    <td class="print"><font size="+1" color="#0000FF">{entradas_unidades}</font></td>
    <td class="print"><font size="+1" color="#0000FF">{entradas_valores}</font></td>
    <td class="print"><font size="+1" color="#FF0000">{salidas_unidades}</font></td>
    <td class="print"><font size="+1" color="#FF0000">{salidas_valores}</font></td>
    <td class="print"><font size="+1">{unidades}</font></td>
    <td class="print"><font size="+1">{valores}</font></td>
  </tr>
</table>
<br>
<!-- END BLOCK : totales -->
<table class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Leyendas</th>
    </tr>
  <tr>
    <td bgcolor="#0000CC" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">Controladas</td>
  </tr>
  <tr>
    <td bgcolor="#993300" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">No controladas </td>
  </tr>
  <tr>
    <td bgcolor="#993399" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">Material de Empaque </td>
  </tr>
</table>
</td>
</tr>
</table>
<br>
<!-- END BLOCK : listado_totales -->
</body>
</html>
