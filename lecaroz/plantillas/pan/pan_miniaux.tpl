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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<table class="tabla">
  <tr>
    <th rowspan="2" class="tabla" scope="col">Fecha</th>
    <th rowspan="2" class="tabla" scope="col">Concepto</th>
    <th rowspan="2" class="tabla" scope="col">Costo<br>
    Unitario </th>
    <th colspan="2" class="tabla" scope="col">Entradas</th>
    <th colspan="2" class="tabla" scope="col">Salidas</th>
    <th colspan="2" class="tabla" scope="col">Existencias</th>
    <th rowspan="2" class="tabla" scope="col">Costo<br>
    Promedio </th>
    <th rowspan="2" class="tabla" scope="col">Diferencia<br>
      de Costo</th>
  </tr>
  <tr>
    <th class="tabla" scope="col">Unidades</th>
    <th class="tabla" scope="col">Valores</th>
    <th class="tabla" scope="col">Unidades</th>
    <th class="tabla" scope="col">Valores</th>
    <th class="tabla" scope="col">Unidades</th>
    <th class="tabla" scope="col">Valores</th>
  </tr>
  <!-- START BLOCK : mp -->
  <tr>
    <th colspan="11" class="vtabla">{codmp} {nombremp} </th>
  </tr>
  <tr>
    <th colspan="7" class="vtabla">Saldo anterior: </th>
    <th class="rtabla">{unidades_anteriores}</th>
    <th class="rtabla">{valores_anteriores}</th>
    <th class="rtabla">{costo_anterior}</th>
    <th class="tabla">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{fecha}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla"><strong>{costo_unitario}</strong></td>
    <td class="tabla">{unidades_entrada}</td>
    <td class="rtabla">{valores_entrada}</td>
    <td class="rtabla">{unidades_salida}</td>
    <td class="rtabla">{valores_salida}</td>
    <td class="rtabla">{unidades_existencia}</td>
    <td class="rtabla">{valores_existencia}</td>
    <td class="rtabla"><strong>{costo_promedio}</strong></td>
    <td class="rtabla">{diferencia_costo}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rtabla">Total de movimientos y saldo actual: </th>
    <th class="tabla">{total_unidades_entrada}</th>
    <th class="rtabla">{total_valores_entrada}</th>
    <th class="rtabla">{total_unidades_salida}</th>
    <th class="rtabla">{total_valores_salida}</th>
    <th class="rtabla">{total_unidades}</th>
    <th class="rtabla">{total_valores}</th>
    <th class="rtabla">{ultimo_costo_promedio}</th>
    <th class="rtabla">{total_diferencia}</th>
  </tr>
  <!-- END BLOCK : mp -->
</table>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
</p>
</td>
</tr>
</table>
</body>
</html>
