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
<td align="center" valign="middle"><p class="title">Comparativo de Saldos Anual</p>
  <form action="./ban_com_anu.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) next.focus()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" onClick="validar(form)" value="Siguiente"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		var now = new Date();
		
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else if (form.anio.value > now.getFullYear()) {
			alert("El año no debe ser mayor al año actual");
			form.anio.value = now.getFullYear();
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
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
    <td width="60%" class="print_encabezado" align="center">Comparativo de Saldos al A&ntilde;o {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : bloque -->
  <table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col"><font size="+1">{titulo}</font></th>
  </tr>
</table>

  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Saldo al <br>
      1 de Enero </th>
      <th class="print" scope="col">Saldo al <br>
      {dia} de {mes} </th>
      <th class="print" scope="col">Saldo Proveedores<br> 
      al 1 de Enero </th>
      <th class="print" scope="col">Saldo Proveedores<br> 
      al {dia} de {mes} </th>
      <th class="print" scope="col">General - Balance </th>
      <th class="print" scope="col">Gastos no <br>
      incluidos </th>
      <th class="print" scope="col">Diferencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{saldo_ini}</td>
      <td class="rprint">{saldo_dia}</td>
      <td class="rprint">{saldo_pro_ini}</td>
      <td class="rprint">{saldo_pro}</td>
      <td class="rprint">{gen_bal}</td>
	  <td class="rprint">{no_incluidos}</td>
	  <td class="rprint">{result}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rprint">Total</th>
      <th class="rprint_total">{saldo_ini}</th>
      <th class="rprint_total">{saldo_dia}</th>
      <th class="rprint_total">{saldo_pro_ini}</th>
      <th class="rprint_total">{saldo_pro}</th>
      <th class="rprint_total">{gen_bal}</th>
      <th class="rprint_total">{no_incluidos}</th>
      <th class="rprint_total">{result}</th>
    </tr>
  </table>
  <br>
  <!-- END BLOCK : bloque -->
<!-- END BLOCK : listado -->
</body>
</html>
