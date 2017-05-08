<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Diferencias de Saldos <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" class="print">
  <tr>
    <th width="10%" class="print" scope="col">Cia</th>
    <th width="15%" class="print" scope="col">N&uacute;mero de Cuenta </th>
    <th width="30%" class="print" scope="col">Nombre</th>
    <th width="15%" class="print" scope="col">Saldo Conciliado </th>
    <th width="15%" class="print" scope="col">Saldo Capturado </th>
    <th width="15%" class="print" scope="col">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print"><a href="./ban_esc_con.php?listado=cia&num_cia={num_cia}&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&cuenta=1&tipo=todos">{num_cia}</a></td>
    <td class="print">{cuenta}</td>
    <td class="vprint">{nombre}</td>
    <td class="rprint">{saldo_con}</td>
    <td class="rprint">{saldo_cap}</td>
    <td class="rprint">{diferencia}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="5" class="rprint_total">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<p>
<input type="button" class="boton" value="Imprimir" onClick="window.print()">
</p></td>
</tr>
</table>
</body>
</html>
