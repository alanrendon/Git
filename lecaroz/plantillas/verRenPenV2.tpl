<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rentas Pendientes de Cobro</title>

<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Rentas Pendientes de Cobro
 {anio}</p>
  <table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Local</th>
    <th class="print" scope="col">Arrendatario</th>
    <th class="print" scope="col">Giro</th>
    <th class="print" scope="col">Oct{ant} </th>
    <th class="print" scope="col">Nov{ant}</th>
    <th class="print" scope="col">Dic{ant}</th>
    <th class="print" scope="col">Ene</th>
    <th class="print" scope="col">Feb</th>
    <th class="print" scope="col">Mar</th>
    <th class="print" scope="col">Abr</th>
    <th class="print" scope="col">May</th>
    <th class="print" scope="col">Jun</th>
    <th class="print" scope="col">Jul</th>
    <th class="print" scope="col">Ago</th>
    <th class="print" scope="col">Sep</th>
    <th class="print" scope="col">Oct</th>
    <th class="print" scope="col">Nov</th>
    <!--<th class="print" scope="col">Dic</th>-->
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">[{cod}] {num} {local}</td>
    <td class="vprint">{arr} {nombre} </td>
    <td class="vprint">{giro}</td>
    <td class="print">{mes_ant_10}</td>
    <td class="print">{mes_ant_11}</td>
    <td class="print">{mes_ant_12}</td>
    <td class="print">{mes1}</td>
    <td class="print">{mes2}</td>
    <td class="print">{mes3}</td>
    <td class="print">{mes4}</td>
    <td class="print">{mes5}</td>
    <td class="print">{mes6}</td>
    <td class="print">{mes7}</td>
    <td class="print">{mes8}</td>
    <td class="print">{mes9}</td>
    <td class="print">{mes10}</td>
    <td class="print">{mes11}</td>
    <!--<td class="print">{mes12}</td>-->
  </tr>
  <!-- END BLOCK : fila -->
</table>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
</p></td>
</tr>
</table>
</body>
</html>
