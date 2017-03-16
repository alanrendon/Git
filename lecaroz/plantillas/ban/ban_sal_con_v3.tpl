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
<td align="center" valign="middle"><p class="title">Listado de Saldos Contables </p>
  <form action="./ban_sal_con_v3.php" method="get"><table class="tabla">
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
<table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Cia</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Saldo<br>
    Bancos</th>
    <th class="print" scope="col">Saldo <br>
    Libros</th>
    <th class="print" scope="col">Cheques Proveedores<br>
    Pendientes</th>
    <th class="print" scope="col">Saldo <br>
      Proveedores</th>
    <th class="print" scope="col">Libros -<br>
    	Proveedores</th>
    <th class="print" scope="col">Inventario<br>
    	Inicial</th>
    <th class="print" scope="col">Perdidas <br>
    Anteriores </th>
    <th class="print" scope="col">Devoluciones<br />de I.V.A.</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','{dia}','{mes}','{anio}',1)"><strong>{saldo_libros}</strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','','','',2)">{pendientes}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_pro}</td>
    <td class="rprint">{libros_pro}</td>
    <td class="rprint">{inv}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{dev_iva}</td>
    </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : fila_con -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint"><strong>{saldo_libros}</strong></td>
    <td class="rprint" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','','','',2)">{pendientes}</td>
    <td class="rprint" style="color: #0000CC; font-weight: bold;" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="saldoPro('','{num_cia}')">{saldo_pro}</td>
    <td class="rprint">{libros_pro}</td>
    <td class="rprint">{inv}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{dev_iva}</td>
    </tr>
  <!-- END BLOCK : fila_con -->
  <!-- START BLOCK : fila_zap -->
  <!-- END BLOCK : fila_zap -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="2" class="rprint">Totales</th>
    <th class="rprint_total">{saldo_bancos}</th>
    <th class="rprint_total">{saldo_libros}</th>
    <th class="rprint_total">{pendientes}</th>
    <th class="rprint_total">{saldo_pro}</th>
    <th class="rprint_total">{libros_pro}</th>
    <th class="rprint_total">{inv}</th>
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total">{dev_iva}</th>
    </tr>
  <!-- END BLOCK : total -->
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
    <th class="print" scope="col">Libros - Proveedores </th>
    <th class="print" scope="col">Inventario Inicial </th>
    <th class="print" scope="col">Devoluciones de IVA </th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{gt_saldo_bancos}</font></th>
    <th class="print"><font size="+1">{gt_saldo_libros}</font></th>
    <th class="print"><font size="+1">{gt_pendientes}</font></th>
    <th class="print"><font size="+1">{gt_saldo_pro}</font></th>
    <th class="print"><font size="+1">{gt_libros_pro}</font></th>
    <th class="print"><font size="+1">{gt_inv}</font></th>
    <th class="print"><font size="+1">{gt_dev_iva}</font></th>
  </tr>
</table>
<!-- END BLOCK : gran_total -->
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
