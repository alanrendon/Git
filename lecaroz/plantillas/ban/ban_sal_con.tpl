<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <th width="60%" class="tabla" align="center">Listado de Saldos <br>
      al {dia} de {mes} de {anio} </th>
    </tr>
</table>
<!-- START BLOCK : listado -->
<p class="title"><font size="+3">{titulo}</font></p>
<table width="100%" cellpadding="0" cellspacing="0" class="print">
  <tr>
    <th class="print" scope="col">Cia.</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Saldo en Bancos </th>
    <th class="print" scope="col">Saldo en Libros</th>
    <th class="print" scope="col">Pendientes</th>
    <th class="print" scope="col">Saldo Proveedores </th>
    <th class="print" scope="col">&Uacute;ltima factura </th>
    <th class="print" scope="col">Perdidas anteriores </th>
    <th class="print" scope="col">Devoluciones<br> 
      de IVA </th>
    <th class="print" scope="col">Promedio Efectivo </th>
    <th class="print" scope="col">Otros Dep&oacute;sitos</th>
    <th class="print" scope="col">D&iacute;as</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{num_cia}</td>
    <!-- <td class="print">{cuenta}</td> -->
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint"><font color="#{color_saldo_bancos}">{saldo_bancos}</font></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('./ban_esc_con.php?listado=cia&num_cia={num_cia}&cuenta=1&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&tipo=todos&cerrar=1','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768')"><strong><font color="#{color_saldo_libros}">{saldo_libros}</font></strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('./ban_esc_con.php?listado=cia&num_cia={num_cia}&cuenta=1&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&tipo=retiros&cod_mov=5&cerrar=1','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768');">{pendientes}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('ban_prov1_saldo.php?cia={num_cia}&id=','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768');"><strong><font color="#0000FF">{saldo_pro}</font></strong></td>
    <td class="print" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('ban_prov1_saldo.php?id={id_ultima_fac}','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=250');">{ultima_fac}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{devoluciones}</td>
    <td class="rprint">{pro_efectivo}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="window.open('./ban_dot_con.php?num_cia={num_cia}&mes={mes}&anio={anio}&tipo=desglozado','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768')">{efectivo}</td>
    <td class="print"><strong><font color="#0000FF">{dias}</font></strong></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th colspan="2" class="print">Totales</th>
    <th class="rprint_total">{total_saldo_bancos}</th>
    <th class="rprint_total">{total_saldo_libros}</th>
    <th class="rprint_total">{total_pendientes}</th>
    <th class="rprint_total">{total_saldo_pro}</th>
    <th class="print">&nbsp;</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total">{total_devoluciones}</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total">{total_efectivo}</th>
    <th class="print">&nbsp;</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
<br>
<table class="print">
  <tr>
    <th class="print" scope="col">Saldo en Bancos </th>
    <th class="print" scope="col">Saldo en Libros </th>
    <th class="print" scope="col">Pendientes</th>
    <th class="print" scope="col">Saldo Proveedores </th>
    <th class="print" scope="col">Devoluciones de IVA </th>
    <th class="print" scope="col">Otros Dep&oacute;sitos</th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{total_saldo_bancos}</font></th>
    <th class="print"><font size="+1">{total_saldo_libros}</font></th>
    <th class="print"><font size="+1">{total_pendientes}</font></th>
    <th class="print"><font size="+1">{total_saldo_pro}</font></th>
    <th class="print"><font size="+1">{total_devoluciones}</font></th>
    <th class="print"><font size="+1">{total_efectivo}</font></th>
  </tr>
</table>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function estado_cuenta() {
		window.open("./ban_esc_con.php?listado=cia&num_cia={num_cia}&fecha1=01%2F{mes}%2F{anio}&fecha2={dia}%2F{mes}%2F{anio}&tipo=todos","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
</script>
</body>
</html>
