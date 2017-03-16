<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Estatus de Remisiones</p>
  <form action="./zap_rem_con.php" method="get" name="form" id="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_fact.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Remisi&oacute;n</th>
      <td class="vtabla"><input name="num_fact" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13) fecha1.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : c -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num}] = '{nombre}';
<!-- END BLOCK : p -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null)
		f.nombre_pro.value = pro[get_val(f.num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : detalle_remision -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Zapater&iacute;as Elite </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Detalle de Pagos de Remisi&oacute;n </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Remisi&oacute;n</th>
    <th class="print" scope="col">Fecha Factura </th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Original</th>
    <th class="print" scope="col">Autorizado</th>
  </tr>
  <tr>
    <td class="vprint">{num_cia} {nombre_cia} </td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="print">{num_fact}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{total}</td>
    <td class="print">{ori}</td>
    <td class="print">{aut}</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th colspan="3" class="print" scope="col">Dep&oacute;sitos</th>
  </tr>
  <tr>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : pago -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : pago -->
  <tr>
    <th colspan="2" class="rprint">Resto</th>
    <th class="rprint_total">{resto}</th>
  </tr>
</table>
<!-- END BLOCK : detalle_remision -->
<!-- START BLOCK : pendientes_proveedor -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Zapater&iacute;as Elite </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Remisiones Pendientes de Pago <br />
    {num_pro} {nombre_pro} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : bloque_cia -->
  <tr>
    <th colspan="5" class="vprint" scope="col" style="font-size:12pt;">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="col">Remisi&oacute;n</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">Anticipo</th>
    <th class="print" scope="col">Resto</th>
  </tr>
  <!-- START BLOCK : fact -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{num_fact}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{anticipo}</td>
    <td class="rprint">{resto}</td>
  </tr>
  <!-- END BLOCK : fact -->
  <tr>
    <th colspan="2" class="rprint">Totales</th>
    <th class="rprint_total">{importe}</th>
    <th class="rprint_total">{anticipo}</th>
    <th class="rprint_total">{resto}</th>
  </tr>
  <tr>
    <td colspan="5" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : bloque_cia -->
  <tr>
    <th colspan="2" class="rprint">Gran Total </th>
    <th class="rprint_total">{importe}</th>
    <th class="rprint_total">{anticipo}</th>
    <th class="rprint_total">{resto}</th>
  </tr>
</table>
<!-- END BLOCK : pendientes_proveedor -->
<!-- START BLOCK : pendientes_cias -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de Remisiones por Compa&ntilde;&iacute;a </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a </th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">Anticipo</th>
    <th class="print" scope="col">Resto</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{anticipo}</td>
    <td class="rprint">{resto}</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint_total">{importe}</th>
    <th class="rprint_total">{anticipo}</th>
    <th class="rprint_total">{resto}</th>
  </tr>
</table>
<!-- END BLOCK : pendientes_cias -->
</body>
</html>
