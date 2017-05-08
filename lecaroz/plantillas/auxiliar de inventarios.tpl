<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form" method="get" action="bal_aux_con.php">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="textfield" type="text" class="insert" size="5" maxlength="5"></td>
    <th class="vtabla">Mes</th>
    <td class="vtabla"><select name="select" class="insert">
      <option selected>---------------</option>
      <option value="1">ENERO</option>
      <option value="2">FEBRERO</option>
      <option value="3">MARZO</option>
      <option value="4">ABRIL</option>
      <option value="5">MAYO</option>
      <option value="6">JUNIO</option>
      <option value="7">JULIO</option>
      <option value="8">AGOSTO</option>
      <option value="9">SEPTIEMBRE</option>
      <option value="10">OCTUBRE</option>
      <option value="11">NOVIEMBRE</option>
      <option value="12">DICIEMBRE</option>
    </select></td>
  </tr>
  <tr>
    <td colspan="2" class="vtabla"><input name="listado" type="radio" value="cia" checked>
      Por compa&ntilde;&iacute;a<br>
      <input name="listado" type="radio" value="todas">
      Todas las compa&ntilde;&iacute;as </td>
    <td colspan="2" class="vtabla"><input name="tipo" type="radio" value="desglozado" checked>
      Desglozado<br>
      <input name="tipo" type="radio" value="totales">
      Totales</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<p>
  <input name="enviar" type="submit" id="enviar" value="Generar listado">
</p>
</form>

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
  <tr>
    <th colspan="10" class="vprint">{codmp} {nombremp} </th>
  </tr>
  <tr>
    <th colspan="7" class="vprint">Saldo anterior: </th>
    <th class="rprint">{unidades_anteriores}</th>
    <th class="rprint">{valores_anteriores}</th>
    <th class="rprint">{costo_anterior}</th>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{costo_unitario}</td>
    <td class="rprint">{unidades_entrada}</td>
    <td class="rprint">{valores_entrada}</td>
    <td class="rprint">{unidades_salida}</td>
    <td class="rprint">{valores_salida}</td>
    <td class="rprint">{unidades_existencia}</td>
    <td class="rprint">{valores_existencia}</td>
    <td class="rprint">{costo_promedio}</td>
  </tr>
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
</table>

</body>
</html>
