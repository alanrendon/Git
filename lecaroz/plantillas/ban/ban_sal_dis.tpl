<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Disponibilidad en Bancos</p>
  <form action="./ban_sal_dis.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="submit" value="Siguiente" class="boton">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td width="20%" rowspan="2" class="rprint_encabezado">{hora}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Disponibilidad en Bancos<br>
      al {dia} de {mes} de {anio} </td>
    </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">Cia</th>
      <th class="print" scope="col">Cuenta</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Saldo en Banco </th>
      <th class="print" scope="col">Pendientes</th>
      <th class="print" scope="col">Retenidos</th>
      <th class="print" scope="col">Diferencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="print">{cuenta}</td>
      <td class="vprint">{nombre}</td>
      <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('./ban_esc_con.php?listado=cia&num_cia={num_cia}&cuenta={cuenta_url}&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&tipo=todos&cerrar=1','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768')">{saldo}</td>
      <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('./ban_che_pen.php?num_cia={num_cia}&cuenta={cuenta_url}','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600')">{pendientes}</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">{diferencia}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" value="Regresar" class="boton" onClick="document.location='./ban_sal_dis.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->

<!-- START BLOCK : no_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay pendientes </font></strong></p>
  <p><strong><font color="#FF0000" face="Geneva, Arial, Helvetica, sans-serif">
    <input type="button" value="Regresar" class="boton" onClick="document.location='./ban_sal_dis.php'">
  </font></strong></p></td>
</tr>
</table>
<!-- END BLOCK : no_listado -->
</body>
</html>
