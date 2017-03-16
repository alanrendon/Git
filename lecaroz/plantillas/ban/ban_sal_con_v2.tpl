<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
<tr>
<td align="center" valign="middle"><p class="title">Listado de Saldos</p>
  <form action="./ban_sal_con_v2.php" method="get"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Cuenta</th>
      <td class="vtabla" scope="col"><select name="cuenta" class="insert" id="cuenta">
        <option value="0" selected>CONSOLIDADO</option>
        <option value="1">BANORTE</option>
        <option value="2">SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="col">Administrador</th>
      <td class="vtabla" scope="col"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
    	<th class="vtabla" scope="col">Contador</th>
    	<td class="vtabla" scope="col"><select name="conta" class="insert" id="conta">
    		<option value="" selected></option>
    		<!-- START BLOCK : conta -->
    		<option value="{id}">{nombre}</option>
    		<!-- END BLOCK : conta -->
    		</select></td>
    	</tr>
  </table>
  <p>
    <input type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<!-- START BLOCK : encabezado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td class="rprint_encabezado">{hora}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Saldos ({banco})<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<!-- END BLOCK : encabezado -->
<br>
<table width="100%" align="center">
<tr>
<td width="20%">&nbsp;</td>
<td width="60%">
  <table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col"><font size="+2">{leyenda}</font></th>
  </tr>
</table></td>
<td width="20%" class="rprint_encabezado">{hora}</td>
</tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Cia</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Saldo<br>
    Bancos</th>
    <th class="print" scope="col">Saldo <br>
    Libros</th>
    <th class="print" scope="col">Movimientos<br>
    Pendientes</th>
    <th class="print" scope="col">Movimientos<br>
    pendientes<br>reservados</th>
    <th class="print" scope="col">Saldo <br>
    {leyenda_saldo_pro}</th>
    <!-- START BLOCK : leyenda_saldo_rem -->
	<th class="print" scope="col">Saldo<br>
      Remisiones</th>
	<!-- END BLOCK : leyenda_saldo_rem -->
    <th class="print" scope="col">&Uacute;ltima <br>
    Factura </th>
    <th class="print" scope="col">Inventario<br>
    Inicial</th>
    <th class="print" scope="col">D&iacute;as de<br>consumo</th>
    <th class="print" scope="col">Perdidas <br>
    Anteriores </th>
    <th class="print" scope="col">{title}</th>
    <th class="print" scope="col">Promedio <br>
    Efectivo </th>
    <th class="print" scope="col">Gastos y<br>
    N&oacute;minas</th>
	<th class="print" scope="col">Otros <br>
    Dep&oacute;sitos</th>
    <th class="print" scope="col">D&iacute;as</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint"><a href="javascript:void(0);" title="{nombre_completo}" style="text-decoration:none; color:#000;">{nombre_cia}</a></td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','{dia}','{mes}','{anio}',1)"><strong>{saldo_libros}</strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','','','',2)">{pendientes}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';">{pendientes_cta}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_pro}</td>
    <td class="print" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('{id}','{num_cia}')">{ultima_fac}</td>
    <td class="rprint">{inv}</td>
    <td class="rprint">{dias_consumo}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{dev_iva}</td>
    <td class="rprint">{prom_efe}</td>
    <td class="rprint">{nom}</td>
	<td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="otrosDep('{num_cia}','{mes}','{anio}')">{otros_dep}</td>
    <td class="print" style="color: #0000CC; font-weight: bold;">{dias}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : fila_con -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint"><a href="javascript:void(0);" title="{nombre_completo}" style="text-decoration:none; color:#000;">{nombre_cia}</a></td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint"><strong>{saldo_libros}</strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','','','',2)">{pendientes}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';">{pendientes_cta}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_pro}</td>
    <td class="print" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('{id}','{num_cia}')">{ultima_fac}</td>
    <td class="rprint">{inv}</td>
    <td class="rprint">{dias_consumo}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{dev_iva}</td>
    <td class="rprint">{prom_efe}</td>
    <td class="rprint">{nom}</td>
	<td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="otrosDep('{num_cia}','{mes}','{anio}')">{otros_dep}</td>
    <td class="print" style="color: #0000CC; font-weight: bold;">{dias}</td>
  </tr>
  <!-- END BLOCK : fila_con -->
  <!-- START BLOCK : fila_zap -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint"><a href="javascript:void(0);" title="{nombre_completo}" style="text-decoration:none; color:#000;">{nombre_cia}</a></td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','{dia}','{mes}','{anio}',1)"><strong>{saldo_libros}</strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','','','',2)">{pendientes}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';">{pendientes_cta}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_pro}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_rem}</td>
    <td class="print" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('{id}','{num_cia}')">{ultima_fac}</td>
    <td class="rprint">{inv}</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="facPen('{num_cia}')">{fac_pen}</td>
    <td class="rprint">{prom_efe}</td>
    <td class="rprint">{nom}</td>
	<td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="otrosDep('{num_cia}','{mes}','{anio}')">{otros_dep}</td>
    <td class="print" style="color: #0000CC; font-weight: bold;">{dias}</td>
  </tr>
  <!-- END BLOCK : fila_zap -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="2" class="rprint">Totales</th>
    <th class="rprint_total">{saldo_bancos}</th>
    <th class="rprint_total">{saldo_libros}</th>
    <th class="rprint_total">{pendientes}</th>
    <th class="rprint_total">{pendientes_cta}</th>
    <th class="rprint_total">{saldo_pro}</th>
    <!-- START BLOCK : total_saldo_rem -->
	<th class="rprint_total">{saldo_rem}</th>
	<!-- END BLOCK : total_saldo_rem -->
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total">{inv}</th>
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total">{dev_iva}</th>
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total">{nom}</th>
    <th class="print_total">{otros_dep}</th>
    <th class="print">&nbsp;</th>
  </tr>
  <!-- END BLOCK : total -->
  <!-- START BLOCK : pendientes -->
  <tr>
    <th colspan="5" class="rprint">Por aclarar</th>
    <th class="rprint_total">{status_pasivo_3}</th>
    <th class="rprint_total" colspan="10">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="5" class="rprint">Con factura completa</th>
    <th class="rprint_total">{status_pasivo_2}</th>
    <th class="rprint_total" colspan="10">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="5" class="rprint">Sin factura original</th>
    <th class="rprint_total">{status_pasivo_1}</th>
    <th class="rprint_total" colspan="10">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="5" class="rprint">Factura completa sin saldo</th>
    <th class="rprint_total">{factura_sin_saldo}</th>
    <th class="rprint_total" colspan="10">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="5" class="rprint">Factura completa con saldo</th>
    <th class="rprint_total">{factura_con_saldo}</th>
    <th class="rprint_total" colspan="10">&nbsp;</th>
  </tr>
  <!-- END BLOCK : pendientes -->
</table>
{salto}
<!-- END BLOCK : listado -->
<!-- START BLOCK : gran_total -->
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Saldo en Bancos </th>
    <th class="print" scope="col">Saldo en Libros </th>
    <th class="print" scope="col">Pendientes</th>
    <th class="print" scope="col">Saldo Proveedores </th>
    <th class="print" scope="col">Inventario Inicial </th>
    <th class="print" scope="col">Devoluciones de IVA </th>
    <th class="print" scope="col">Otros Dep&oacute;sitos</th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{gt_saldo_bancos}</font></th>
    <th class="print"><font size="+1">{gt_saldo_libros}</font></th>
    <th class="print"><font size="+1">{gt_pendientes}</font></th>
    <th class="print"><font size="+1">{gt_saldo_pro}</font></th>
    <th class="print"><font size="+1">{gt_inv}</font></th>
    <th class="print"><font size="+1">{gt_dev_iva}</font></th>
    <th class="print"><font size="+1">{gt_otros_dep}</font></th>
  </tr>
</table>
<!-- END BLOCK : gran_total -->
<!-- START BLOCK : total_inm -->
<br>
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">#</th>
		<th class="print" scope="col">Inmobiliaria</th>
		<th class="print" scope="col">Saldo<br>
		Bancos</th>
		<th class="print" scope="col">Saldo<br>
		Libros</th>
	</tr>
	<!-- START BLOCK : inm -->
	<tr>
		<td class="rprint">{num_cia}</td>
		<td class="vprint"><a href="javascript:void(0);" title="{nombre_completo}" style="text-decoration:none; color:#000;">{nombre_cia}</a></td>
		<td class="rprint">{saldo_bancos}</td>
		<td class="rprint">{saldo_libros}</td>
	</tr>
	<!-- END BLOCK : inm -->
	<tr>
		<th colspan="2" class="rprint"><font size="+1">Total</font></th>
		<th class="rprint"><font size="+1">{saldo_bancos}</font></th>
		<th class="rprint"><font size="+1">{saldo_libros}</font></th>
	</tr>
</table>
<!-- END BLOCK : total_inm -->
<!-- START BLOCK : functions -->
<script language="javascript" type="text/javascript">
<!--
function estadoCuenta(num_cia, cuenta, dia, mes, anio, tipo) {
	var url = "./ban_esc_con_v2.php";
	var opt;

	if (tipo == 1) {
		opt = "?num_cia=" + num_cia + "&cuenta=" + cuenta + "&fecha1=01/" + mes + "/" + anio + "&fecha2=" + dia + "/" + mes + "/" + anio + "&tipo=0&cerrar=1&noacuenta=1";
	}
	else {
		opt = "?num_cia=" + num_cia + "&cuenta=" + cuenta + "&tipo=0&che_pen=1&cerrar=1&nocon=1&noacuenta=1";
	}

	var ven = window.open(url + opt, "pend", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}

function saldoPro(id, num_cia) {
	var url = "./ban_prov1_saldo.php";
	var opt;

	if (id != "") {
		opt = "?id=" + id + "&cia=" + num_cia;
	}
	else {
		opt = "?cia=" + num_cia + "&id=";
	}
	var ven = window.open(url + opt, "saldopro", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}

function facPen(num_cia) {
	var ven = window.open("./ban_fac_pen.php?num_cia=" + num_cia, "fac_pen", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}

function otrosDep(num_cia, mes, anio) {
	var ven = window.open("./ban_dot_con.php?num_cia=" + num_cia + "&mes=" + mes + "&anio=" + anio + "&tipo=desglozado", "otrosdep", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}
-->
</script>
<!-- END BLOCK : functions -->
</body>
</html>
