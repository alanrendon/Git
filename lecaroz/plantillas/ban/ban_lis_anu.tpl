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
<td align="center" valign="middle"><p class="title">Listado de Bancos Anual</p>
  <form action="./ban_lis_anu.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) anio.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de listado</th>
      <td class="vtabla"><input name="tipo" type="radio" value="cod_mov" checked>
        Por c&oacute;digo de bancario<br>
        <input name="tipo" type="radio" value="cia">
        Por compa&ntilde;&iacute;as  </td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else if (form.tipo[0].checked && form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cod_mov -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td align="right" class="print_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Bancos Anual<br>
      al a&ntilde;o {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top" scope="col">	  <table class="print">
        <tr>
          <th colspan="2" class="print" scope="col">C&oacute;digo</th>
        </tr>
        <!-- START BLOCK : codigo -->
		<tr>
          <td class="print">{cod_mov}</td>
          <td class="vprint">&nbsp;&nbsp;{descripcion}</td>
        </tr>
		<!-- END BLOCK : codigo -->
        <tr>
          <th colspan="2" class="print">Total</th>
        </tr>
      </table>
	  </td>
      <!-- START BLOCK : mes_mov -->
	  <td valign="top" scope="col">	  <table class="print">
        <tr>
          <th class="print" scope="col">{mes}</th>
        </tr>
        <!-- START BLOCK : fila_mov -->
		<tr>
          <td class="rprint">&nbsp;&nbsp;{importe}&nbsp;&nbsp;</td>
        </tr>
		<!-- END BLOCK : fila_mov -->
        <tr>
          <th class="rprint">{total}&nbsp;&nbsp;</th>
        </tr>
      </table>
	  </td>
	  <!-- END BLOCK : mes_mov -->
    </tr>
</table>
<!-- END BLOCK : cod_mov -->

<!-- START BLOCK : cias -->
<table width="100%">
  <tr>
    <td class="print_encabezado">&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V.</td>
    <td align="right" class="print_encabezado">&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Bancos Anual<br>
      al a&ntilde;o {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top" scope="col">	  <table class="print">
        <tr>
          <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
        </tr>
        <!-- START BLOCK : cia -->
		<tr>
          <td class="print">{num_cia}</td>
          <td class="vprint">&nbsp;&nbsp;{nombre_cia}</td>
        </tr>
		<!-- END BLOCK : cia -->
      </table>
	  </td>
      <!-- START BLOCK : mes_cia -->
	  <td valign="top" scope="col">	  <table class="print">
        <tr>
          <th class="print" scope="col">{mes}</th>
        </tr>
        <!-- START BLOCK : fila_cia -->
		<tr>
          <td class="rprint">&nbsp;&nbsp;{importe}&nbsp;&nbsp;</td>
        </tr>
		<!-- END BLOCK : fila_cia -->
      </table>
	  </td>
	  <!-- END BLOCK : mes_cia -->
    </tr>
</table>
<!-- END BLOCK : cias -->
</body>
</html>
