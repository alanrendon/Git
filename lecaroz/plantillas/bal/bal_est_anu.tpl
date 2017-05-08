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
<td align="center" valign="middle"><p class="title">Estimaci&oacute;n Anual </p>
  <form action="./bal_est_anu.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[5].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[6].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[7].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[8].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[9].select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select() else if (event.keyCode == 40) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.anio.value.length < 4) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia[0].select();
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
    <td width="60%" class="print_encabezado" align="center">Estimaci&oacute;n Anual </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Saldo <br>
    Inicial <br>
    Banco</th>
    <th class="print" scope="col">Saldo<br>
      Inicial<br>
      Proveedores</th>
    <th class="print" scope="col">Diferencia<br>
      Saldos<br>
      Iniciales</th>
    <th class="print" scope="col">Saldo <br>
    Actual<br>
    Banco</th>
    <th class="print" scope="col">Saldo<br>
      Actual<br>
      Proveedores</th>
    <th class="print" scope="col">Diferencia<br>
      Saldos</th>
    <th class="print" scope="col">Diferencia en<br> 
      Saldos
      Total</th>
    <th class="print" scope="col">General</th>
    <th class="print" scope="col">O</th>
    <th class="print" scope="col">O - G </th>
    <th class="print" scope="col">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{saldo_ini}</td>
    <td class="rprint">{saldo_ini_pro}</td>
    <td class="rprint">{dif_ini}</td>
    <td class="rprint">{saldo}</td>
    <td class="rprint">{sal_pro}</td>
    <td class="rprint">{dif_sal}</td>
    <td class="rprint">{dif_tot}</td>
    <td class="rprint">{bal}</td>
    <td class="rprint">{tmp}</td>
    <td class="rprint">{og}</td>
    <td class="rprint">{dif}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
