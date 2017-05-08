<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<form name="form" method="get" action="./aux_inv1.php">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5"></td>
    <th class="vtabla">Mes</th>
    <td class="vtabla"><select name="mes" class="insert" id="mes">
      <option>---------------</option>
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
    <td colspan="2" class="vtabla"><input name="listado" type="radio" onClick="if (this.checked)
form.num_cia.style.visibility = 'visible'" value="cia" checked>
      Por compa&ntilde;&iacute;a<br>
      <input name="listado" type="radio" onClick="if (this.checked)
form.num_cia.style.visibility = 'hidden'" value="todas">
      Todas las compa&ntilde;&iacute;as </td>
    <td colspan="2" class="vtabla"><input name="tipo" type="radio" onClick="if (this.checked)
form.codmp.style.visibility = 'visible'" value="mp" checked>      
      Desglozado por producto 
      <input name="codmp" type="text" class="insert" id="codmp" size="3" maxlength="3">      <br>      <input name="tipo" type="radio" onClick="if (this.checked)
form.codmp.style.visibility = 'hidden'" value="desglozado">
      Desglozado todos los productos<br>
      <input name="tipo" type="radio" onClick="if (this.checked)
form.codmp.style.visibility = 'hidden'" value="totales">
      Totales</td>
  </tr>
</table>
<p>
  <input name="enviar" type="submit" id="enviar" value="Generar listado">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<form name="form">
<table width="100%">
  <tr>
    <td width="10%" class="print_encabezado">Cia.: {num_cia} </td>
    <td width="80%" class="print_encabezado"><div align="center">{nombre_cia}</div></td>
    <td width="10%" class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado"><div align="center">Auxiliar de Materias Primas<br>
      correspondientes al mes de {mes} de {anio} </div></td>
    <td>&nbsp;</td>
  </tr>
</table>
<br>
<table class="print">
  <tr>
    <th rowspan="2" class="print" scope="col">Fecha</th>
    <th rowspan="2" class="print" scope="col">Concepto</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
    Unitario </th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="2" class="print" scope="col">Salidas</th>
    <th colspan="2" class="print" scope="col">Existencias</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
    Promedio </th>
  </tr>
  <tr>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
  </tr>
  <!-- START BLOCK : mp -->
  <tr>
    <th colspan="10" class="vprint">{codmp} {nombremp} </th>
  </tr>
  <tr>
    <th colspan="7" class="vprint">Saldo anterior: </th>
    <th class="rprint">{unidades_anteriores}</th>
    <th class="rprint">{valores_anteriores}</th>
    <th class="rprint">{costo_anterior}</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint"><strong>{costo_unitario}</strong></td>
    <td class="rprint">{unidades_entrada}</td>
    <td class="rprint">{valores_entrada}</td>
    <td class="rprint">{unidades_salida}</td>
    <td class="rprint">{valores_salida}</td>
    <td class="rprint">{unidades_existencia}</td>
    <td class="rprint">{valores_existencia}</td>
    <td class="rprint"><strong>{costo_promedio}</strong></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rprint">Total de movimientos y saldo actual: </th>
    <th class="rprint">{total_unidades_entrada}</th>
    <th class="rprint">{total_valores_entrada}</th>
    <th class="rprint">{total_unidades_salida}</th>
    <th class="rprint">{total_valores_salida}</th>
    <th class="rprint">{total_unidades}</th>
    <th class="rprint">{total_valores}</th>
    <th class="rprint">{ultimo_costo_promedio}</th>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <!-- END BLOCK : mp -->
</table>
<br style="page-break-before:always;">
<br style="page-break-after:always;">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

<!-- START BLOCK : listado_totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<table width="100%">
  <tr>
    <td width="10%" class="print_encabezado">Cia.: {num_cia} </td>
    <td width="80%" class="print_encabezado"><div align="center">{nombre_cia}</div></td>
    <td width="10%" class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado"><div align="center">Auxiliar de Materias Primas<br>
        correspondientes al mes de {mes} de {anio} </div></td>
    <td>&nbsp;</td>
  </tr>
</table>
<br>
<table class="print">
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
  <tr>
    <td class="vprint">{codmp_total} {nombremp_total}</td>
    <td class="rprint">{unidades_anteriores_total}</td>
    <td class="rprint">{valores_anteriores_total}</td>
    <td class="rprint">{costo_anterior_total}</td>
    <td class="rprint">{total_unidades_entrada_total}</td>
    <td class="rprint">{total_valores_entrada_total}</td>
    <td class="rprint">{total_unidades_salida_total}</td>
    <td class="rprint">{total_valores_salida_total}</td>
    <td class="rprint">{total_unidades_total}</td>
    <td class="rprint">{total_valores_total}</td>
    <td class="rprint">{ultimo_costo_promedio_total}</td>
  </tr>
  <!-- END BLOCK : mp_total -->
  <tr>
    <th class="rprint">Total General </th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{total_valores_anteriores}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{total_valores_entrada_total}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{total_valores_salida_total}</font></th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint"><font size="-1">{total_valores_total}</font></th>
    <th class="rprint">&nbsp;</th>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_totales -->