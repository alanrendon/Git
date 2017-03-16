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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Archivos de Tranferencias Electr&oacute;nicas</p>
  <form action="" method="get" name="f">
    <input name="tmp" type="hidden" id="tmp">
    <input name="gen" type="hidden" id="gen" value="1">    
    <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Proveedor</th>
    <td class="vtabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[1].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[2].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[3].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[4].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[5].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[6].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[7].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[8].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[9].select()" size="4">
	<input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[0].select()" size="4"></td>
  </tr>
</table></form>
<p>
    <input type="button" class="boton" value="Consultar" onClick="document.location='./ban_tra_pen.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Generar" onClick="generar()">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function generar() {
	document.f.target = "que"
	document.f.action = "./ban_gen_tra.php";
	//var win = window.open("./ban_gen_tra.php?gen=1&num_pro=" + document.f.num_pro.value,"que","left=362,top=284,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=300,height=200");
	var win = window.open("","que","left=362,top=284,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=300,height=200");
	document.f.submit();
	win.focus();
}

window.onload = document.f.num_pro[0].select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">No hay transferencias pendientes </p>
  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  </p></td>
</tr>
</table>
<!-- END BLOCK : no_result -->
<!-- START BLOCK : archivos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table border="2" bordercolor="#FF0000">
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; font-weight: bold;"><p>Solo dar click sobre el enlace para guardar el archivo.</p>
      <p>Atte. Carlos </p></td>
  </tr>
</table>
  <br>
  <table class="tabla">
  <!-- START BLOCK : int -->
  <tr>
    <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num}</td>
	<!--<td class="tabla" style="font-size: 12pt; font-weight: bold;"><a href="trans/trans_int_{folio}_{num}.txt" target="_blank">Transferencia mismo Banco</a></td>-->
	<td class="tabla" style="font-size: 12pt; font-weight: bold;"><a href="download.php?file=trans/trans_int_{folio}_{num}.txt">Transferencia mismo Banco</a></td>
  </tr>
  <!-- END BLOCK : int -->
  <!-- START BLOCK : extra -->
  <tr>
    <td colspan="2" class="tabla" style="font-size: 12pt; font-weight: bold;">&nbsp;</td>
  </tr>
  <!-- END BLOCK : extra -->
  <!-- END BLOCK : ext -->
  <tr>
    <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num}</td>
    <!--<td colspan="2" class="tabla" style="font-size: 12pt; font-weight: bold;"><a href="trans/trans_ext_{folio}_{num}.txt" target="_blank">Transferencia otros Bancos</a></td>-->
	<td colspan="2" class="tabla" style="font-size: 12pt; font-weight: bold;"><a href="download.php?file=trans/trans_ext_{folio}_{num}.txt">Transferencia otros Bancos</a></td>
  </tr>
  <!-- END BLOCK : ext -->
</table>
<p>
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
</p>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function list(folio) {
	window.opener.document.location = "./ban_gen_tra.php?folio=" + folio;
}

window.onload = list({folio});
-->
</script>
<!-- END BLOCK : archivos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td class="rprint_encabezado">AF {folio} </td>
  </tr>
  <tr>
    <td width="20%" class="print_encabezado">USER: {user} <br>
      PASS: {pass} </td>
    <td width="60%" class="print_encabezado" align="center">Trasferencias Electr&oacute;nicas<br>
    <span style="font-size: 12pt;">{num_pro} {nombre}</span></td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Facturas</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{facturas}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="5" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : listado -->
<!-- START BLOCK : concentrado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">{fecha}</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td class="rprint_encabezado">AF {folio} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Transferencias Electr&oacute;nicas </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : pro -->
  <tr>
    <th colspan="7" class="vprint" scope="col" style="font-size: 10pt;">{num_pro} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Facturas</th>
    <th class="print" scope="col">Tipo</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_con -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{facturas}</td>
    <td class="print">{tipo}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : fila_con -->
  <tr>
    <th colspan="6" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
  </tr>
  <!-- END BLOCK : pro -->
  <tr>
    <th colspan="6" class="rprint">Gran Total </th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : concentrado -->
<!-- START BLOCK : totales -->
<table width="100%">
  <tr>
    <td class="print_encabezado">{fecha}</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td class="rprint_encabezado">AF {folio} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Transferencias Electr&oacute;nicas </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Tipo</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : total -->
  <tr>
    <td class="vprint">{num_pro} {nombre} </td>
    <td class="print">{tipo}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : total -->
  <tr>
    <th colspan="2" class="rprint">Transferencias Internas </th>
    <th class="rprint_total">{total_int}</th>
  </tr>
  <tr>
    <th colspan="2" class="rprint">Transferencias Externas </th>
    <th class="rprint_total">{total_ext}</th>
  </tr>
  <tr>
    <th colspan="2" class="rprint">Total de Transferencias</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : totales -->
</body>
</html>
