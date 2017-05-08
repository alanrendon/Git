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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Pagos Fijos</p>
  <form action="./ban_lis_che_fij.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codgastos.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="3" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Pagos Fijos </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th colspan="2" class="print" scope="col">Proveedor</th>
    <th colspan="2" class="print" scope="col">Gasto</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">I.V.A.</th>
    <th class="print" scope="col">Ret. I.V.A. </th>
    <th class="print" scope="col">I.S.R.</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{num_proveedor}</td>
    <td class="vprint">{nombre_pro}</td>
    <td class="rprint">{codgastos}</td>
    <td class="vprint">{nombre_gas}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{ret_iva}</td>
    <td class="rprint">{isr}</td>
    <td class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
