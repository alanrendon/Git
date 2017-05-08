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
<td align="center" valign="middle"><p class="title">Listado de Gastos de Caja Fijos</p>
  <form action="./bal_gas_caj_fij_lis.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) this.blur()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><select name="cod_gastos" class="insert" id="cod_gastos">
        <option value="" selected></option>
		<!-- START BLOCK : cod_gastos -->
		<option value="{id}">{id} {descripcion}</option>
		<!-- END BLOCK : cod_gastos -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_cia.value != "" && form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Gastos de Caja Fijos </p>
    <table class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Comentario</th>
      <th class="print" scope="col">Tipo</th>
      <th class="print" scope="col">Balance</th>
      <th class="print" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="vprint">{cod_gastos} {descripcion}</td>
      <td class="vprint">{comentario}</td>
      <td class="print">{tipo_mov}</td>
      <td class="print">{balance}</td>
      <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th colspan="6" class="rprint">Total</th>
	  <th class="rprint_total">{total}</th>
	  </tr>
  </table>
  </td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
